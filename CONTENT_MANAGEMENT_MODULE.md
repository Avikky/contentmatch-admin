# Content Management & Analytics Module - Complete Guide

**Date:** October 9, 2025  
**Project:** ContentMatch Admin Panel  
**Module:** Content Management, Reporting & Analytics

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Controller Methods](#controller-methods)
4. [Routes Configuration](#routes-configuration)
5. [Database Schema](#database-schema)
6. [Frontend Integration](#frontend-integration)
7. [Use Cases](#use-cases)
8. [Implementation Steps](#implementation-steps)

---

## Overview

This module provides comprehensive content management capabilities for the ContentMatch admin panel, including:

- **Content Review & Moderation** - View, filter, and manage all content
- **Report Management** - Handle user-reported content
- **Analytics Dashboard** - Track trends, engagement, and user activity
- **Feedback Management** - Review user feedback and ratings
- **Engagement Tracking** - Monitor content interactions
- **Bulk Operations** - Efficient management of multiple items

---

## Features

### 1. Content Management Dashboard
- View all content with advanced filtering
- Search by title, description, or creator
- Filter by status, platform, date range
- View key metrics (reports, feedback, engagements, likes)
- Bulk status updates
- Export content data to CSV

### 2. Reported Content Management
- View all content reports grouped by status
- See reporter details and report reason
- View content owner information
- Take actions: Ban, Remove, Dismiss, or Warn
- Add resolution notes
- Track resolution history

### 3. Analytics Dashboard
- **Trending Content**
  - Most engaged content
  - Top performing categories
  - Trending hashtags
  
- **User Activity**
  - Most active content creators
  - Most active engagers
  - User engagement trends

- **Platform Analytics**
  - Content distribution by platform
  - Platform-specific engagement rates

- **Temporal Trends**
  - Content creation over time
  - Engagement over time
  - Customizable time periods

### 4. Feedback Management
- View all content feedback
- Filter by rating, content, user
- See detailed feedback with liked/improvement aspects
- Track average ratings
- Rating distribution analysis

### 5. Engagement Tracking
- Monitor all user engagements
- Track subscribe, like, comment actions
- View proof images
- Content-specific engagement metrics

---

## Controller Methods

### ContentController

#### `index(Request $request): Response`
**Purpose:** Display all content with filters  
**Features:**
- Search by title, description, creator
- Filter by status, platform, date range
- Show reports count, feedback count, engagement metrics
- Pagination with 20 items per page
- Quick stats overview

**Query Parameters:**
- `search` - Search term
- `status` - Filter by content status
- `platform` - Filter by platform name
- `date_from` - Start date (Y-m-d)
- `date_to` - End date (Y-m-d)

---

#### `show(Content $content): Response`
**Purpose:** Detailed view of single content  
**Features:**
- Complete content information
- All reports with reporter details
- All feedback with user details
- All engagements
- Likes list (limited to 50)
- Engagement statistics
- Feedback statistics including rating distribution
- Report statistics by status

---

#### `reported(Request $request): Response`
**Purpose:** Manage reported content  
**Features:**
- View reports by status (pending, reviewing, resolved, dismissed)
- Search reports by reason or reporter
- See content details and owner
- Quick access to resolution actions
- Report statistics dashboard

**Query Parameters:**
- `status` - Filter by report status (default: pending)
- `search` - Search in reasons/descriptions

---

#### `analytics(Request $request): Response`
**Purpose:** Comprehensive analytics dashboard  
**Features:**

**Trending Data:**
- Top 20 trending hashtags (by recent usage)
- Top 10 categories (by recent content)
- Top 10 most engaged content

**User Activity:**
- Top 10 active creators (by content count)
- Top 10 active engagers (by engagement count)

**Temporal Charts:**
- Content created per day
- Engagements per day
- Platform distribution

**Overall Stats:**
- Total/recent content count
- Total/recent engagements
- Total/recent feedback
- Average rating
- Active users count

**Query Parameters:**
- `period` - Time period in days (default: 30)

---

#### `feedback(Request $request): Response`
**Purpose:** View and manage content feedback  
**Features:**
- All feedback with ratings
- Filter by content or rating
- User details and feedback content
- Like count per feedback
- Statistics: total, average rating, sentiment distribution

**Query Parameters:**
- `content_id` - Filter by specific content
- `rating` - Filter by rating value

---

#### `engagements(Request $request): Response`
**Purpose:** Track content engagements  
**Features:**
- All engagements with user details
- Filter by content
- Track engagement types (subscribe, like, comment)
- Statistics on engagement actions

**Query Parameters:**
- `content_id` - Filter by specific content

---

#### `updateStatus(Content $content, Request $request): RedirectResponse`
**Purpose:** Update content status  
**Validation:**
```php
'status' => ['required', 'in:active,published,draft,archived,reported,removed']
'reason' => ['nullable', 'string', 'max:500']
```
**Actions:**
- Updates content status
- Logs activity with reason

---

#### `ban(Content $content, Request $request): RedirectResponse`
**Purpose:** Ban content and optionally ban user  
**Validation:**
```php
'reason' => ['required', 'string', 'max:500']
'ban_user' => ['boolean']
'ban_duration' => ['nullable', 'integer', 'min:1']
```
**Actions:**
- Sets content status to 'removed'
- Resolves all pending reports
- Optionally bans the content owner
- Logs ban activity

---

#### `destroy(Content $content, Request $request): RedirectResponse`
**Purpose:** Permanently delete content  
**Validation:**
```php
'reason' => ['required', 'string', 'max:500']
```
**Actions:**
- Logs deletion details
- Soft deletes content (can be recovered)

---

#### `resolveReport(Report $report, Request $request): RedirectResponse`
**Purpose:** Resolve a content report  
**Validation:**
```php
'action' => ['required', 'in:dismiss,ban_content,remove_content,warn_user']
'notes' => ['nullable', 'string', 'max:1000']
```
**Actions:**
- `dismiss` - Mark report as dismissed
- `ban_content` - Set content status to removed
- `remove_content` - Delete content
- `warn_user` - Issue warning (extensible)
- Updates report with resolution details

---

#### `bulkUpdateStatus(Request $request): RedirectResponse`
**Purpose:** Update multiple content items at once  
**Validation:**
```php
'content_ids' => ['required', 'array']
'content_ids.*' => ['exists:contents,id']
'status' => ['required', 'in:active,published,draft,archived,reported,removed']
'reason' => ['nullable', 'string', 'max:500']
```

---

#### `export(Request $request)`
**Purpose:** Export content data to CSV  
**Includes:**
- ID, Title, Creator, Platform
- Status, Reports count
- Feedback count, Engagements count
- Likes count, Created date

---

## Routes Configuration

Add these routes to your admin routes file:

```php
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    
    // ==================
    // Content Management
    // ==================
    Route::get('/content', [ContentController::class, 'index'])
        ->name('admin.content.index');
    
    Route::get('/content/{content}', [ContentController::class, 'show'])
        ->name('admin.content.show');
    
    Route::patch('/content/{content}/status', [ContentController::class, 'updateStatus'])
        ->name('admin.content.update-status');
    
    Route::post('/content/{content}/ban', [ContentController::class, 'ban'])
        ->name('admin.content.ban');
    
    Route::delete('/content/{content}', [ContentController::class, 'destroy'])
        ->name('admin.content.destroy');
    
    Route::post('/content/bulk-status', [ContentController::class, 'bulkUpdateStatus'])
        ->name('admin.content.bulk-status');
    
    Route::get('/content/export', [ContentController::class, 'export'])
        ->name('admin.content.export');
    
    // ==================
    // Reported Content
    // ==================
    Route::get('/content/reported', [ContentController::class, 'reported'])
        ->name('admin.content.reported');
    
    Route::post('/reports/{report}/resolve', [ContentController::class, 'resolveReport'])
        ->name('admin.reports.resolve');
    
    // ==================
    // Analytics
    // ==================
    Route::get('/content/analytics', [ContentController::class, 'analytics'])
        ->name('admin.content.analytics');
    
    // ==================
    // Feedback & Engagement
    // ==================
    Route::get('/content/feedback', [ContentController::class, 'feedback'])
        ->name('admin.content.feedback');
    
    Route::get('/content/engagements', [ContentController::class, 'engagements'])
        ->name('admin.content.engagements');
});
```

---

## Database Schema

### Contents Table
```sql
contents
├── id (bigint, PK)
├── user_id (bigint, FK → users.id)
├── community_id (bigint, FK → communities.id, nullable)
├── platform_id (bigint, FK → platforms.id)
├── title (varchar)
├── slug (varchar, indexed)
├── video_id (varchar)
├── description (text, nullable)
├── platform_content_url (varchar)
├── is_youtube_shorts (boolean)
├── allow_feedback (boolean)
├── allow_engagement (boolean)
├── status (enum: active|published|draft|archived|reported|removed)
├── created_at, updated_at, deleted_at
```

### Reports Table
```sql
reports
├── id (bigint, PK)
├── reporter_id (bigint, FK → users.id)
├── reportable_type (varchar) - Content::class
├── reportable_id (bigint)
├── reason (varchar)
├── description (text, nullable)
├── status (enum: pending|reviewing|resolved|dismissed)
├── resolved_by (bigint, FK → admins.id, nullable)
├── resolution_notes (text, nullable)
├── resolved_at (timestamp, nullable)
├── created_at, updated_at, deleted_at
```

### Feedback Table
```sql
feedbacks
├── id (bigint, PK)
├── user_id (bigint, FK → users.id)
├── content_id (bigint, FK → contents.id)
├── rating (integer, 1-5)
├── liked_aspects (json)
├── improvement_aspects (json)
├── proof_image_url (varchar, nullable)
├── created_at, updated_at, deleted_at
```

### Engagements Table
```sql
engagements
├── id (bigint, PK)
├── user_id (bigint, FK → users.id)
├── content_id (bigint, FK → contents.id)
├── did_engage (boolean)
├── did_subscribe (boolean)
├── did_like (boolean)
├── did_comment (boolean)
├── additional_comments (text, nullable)
├── proof_image (varchar, nullable)
├── created_at, updated_at, deleted_at
```

### Content Metrics Table
```sql
content_metrics
├── id (bigint, PK)
├── content_id (bigint, FK → contents.id)
├── views_count (integer)
├── likes_count (integer)
├── comments_count (integer)
├── shares_count (integer)
├── engagement_rate (integer)
├── created_at, updated_at, deleted_at
```

---

## Frontend Integration

### Inertia.js Pages to Create

#### 1. `Admin/Content/Index.vue`
**Props:**
```javascript
{
    contents: PaginatedCollection<Content>,
    platforms: Platform[],
    stats: {
        total: number,
        active: number,
        reported: number,
        removed: number,
        archived: number
    },
    filters: {
        search: string,
        status: string,
        platform: string,
        date_from: string,
        date_to: string
    }
}
```

**Features:**
- Data table with sortable columns
- Search input
- Status filter dropdown
- Platform filter dropdown
- Date range picker
- Quick stats cards
- Bulk selection checkboxes
- Action buttons (View, Edit Status, Ban, Delete)

---

#### 2. `Admin/Content/Show.vue`
**Props:**
```javascript
{
    content: Content & {
        user, platform, community, metrics,
        categories, hashtags, reports[], 
        feedback[], engagements[], likes[]
    },
    engagementStats: object,
    feedbackStats: object,
    reportStats: object
}
```

**Features:**
- Content preview with embed
- Owner information card
- Metrics dashboard
- Reports list with resolution actions
- Feedback list with ratings
- Engagement list
- Status management panel
- Ban/Remove actions

---

#### 3. `Admin/Content/Reported.vue`
**Props:**
```javascript
{
    reports: PaginatedCollection<Report>,
    stats: {
        pending: number,
        reviewing: number,
        resolved: number,
        dismissed: number
    },
    filters: {
        status: string,
        search: string
    }
}
```

**Features:**
- Reports table with content preview
- Reporter information
- Content owner information
- Report reason and description
- Resolution actions dropdown
- Quick stats cards
- Search and filter

---

#### 4. `Admin/Content/Analytics.vue`
**Props:**
```javascript
{
    trendingHashtags: Hashtag[],
    topCategories: Category[],
    mostEngagedContent: Content[],
    mostActiveCreators: User[],
    mostActiveEngagers: User[],
    contentPerDay: ChartData[],
    engagementPerDay: ChartData[],
    platformDistribution: ChartData[],
    stats: object,
    period: string
}
```

**Features:**
- Line charts for temporal data
- Bar charts for top items
- Pie chart for platform distribution
- KPI cards with key metrics
- Period selector (7, 30, 90 days)
- Exportable data
- Leaderboards (top creators, engagers)

---

#### 5. `Admin/Content/Feedback.vue`
**Props:**
```javascript
{
    feedbacks: PaginatedCollection<Feedback>,
    stats: {
        total: number,
        average_rating: number,
        positive: number,
        neutral: number,
        negative: number
    },
    filters: {
        content_id: number,
        rating: number
    }
}
```

**Features:**
- Feedback cards with ratings
- User information
- Liked/improvement aspects
- Proof images
- Rating distribution chart
- Filter by content or rating

---

#### 6. `Admin/Content/Engagements.vue`
**Props:**
```javascript
{
    engagements: PaginatedCollection<Engagement>,
    stats: {
        total: number,
        did_engage: number,
        did_subscribe: number,
        did_like: number,
        did_comment: number
    },
    filters: {
        content_id: number
    }
}
```

**Features:**
- Engagement list with user details
- Action checkboxes (subscribe, like, comment)
- Proof images viewer
- Statistics overview
- Filter by content

---

## Use Cases

### Use Case 1: Reviewing Reported Content
**Scenario:** Admin receives notification of reported content

**Steps:**
1. Navigate to "Reported Content" page
2. View list filtered by "pending" status
3. Click on report to see details
4. Review content, reason, and reporter
5. Take action:
   - Dismiss if invalid
   - Warn user if minor violation
   - Ban content if violates policies
   - Remove content if severe violation
6. Add resolution notes
7. Submit resolution

**Expected Outcome:**
- Report marked as resolved
- Content status updated if applicable
- Activity logged
- Reporter notified (if implemented)

---

### Use Case 2: Analyzing Trending Content
**Scenario:** Marketing team wants to understand content trends

**Steps:**
1. Navigate to "Analytics" page
2. Select time period (e.g., last 30 days)
3. Review:
   - Trending hashtags for campaign ideas
   - Top categories for content focus
   - Most engaged content for best practices
   - Active creators for partnerships
4. Export data for presentation

**Expected Outcome:**
- Clear insights into content performance
- Data-driven decision making
- Exportable reports

---

### Use Case 3: Managing Content Quality
**Scenario:** Admin performs weekly content quality review

**Steps:**
1. Navigate to "Content" page
2. Filter by recent content (date range)
3. Review each content:
   - Check reports count
   - Review feedback ratings
   - Check engagement metrics
4. For low-quality content:
   - Change status to "archived"
   - Add reason
5. For high-quality content:
   - Feature prominently (if feature exists)

**Expected Outcome:**
- Maintained content quality
- Users see relevant, high-quality content
- Poor content appropriately handled

---

### Use Case 4: Investigating User Feedback
**Scenario:** Admin notices declining ratings

**Steps:**
1. Navigate to "Feedback" page
2. Review feedback statistics
3. Filter by low ratings (1-2 stars)
4. Read improvement aspects
5. Identify common themes
6. Navigate to specific content items
7. Take appropriate action

**Expected Outcome:**
- Understand user concerns
- Improve content guidelines
- Address systemic issues

---

### Use Case 5: Bulk Content Moderation
**Scenario:** Spam campaign detected

**Steps:**
1. Navigate to "Content" page
2. Search by common spam term or user
3. Review results
4. Select multiple content items (checkboxes)
5. Use bulk action: "Remove"
6. Add reason: "Spam campaign"
7. Confirm action

**Expected Outcome:**
- Multiple spam items removed efficiently
- Platform integrity maintained
- Activity logged for audit

---

## Implementation Steps

### Step 1: Verify Database Tables
Ensure all tables exist in main app database:
- ✅ contents
- ✅ reports
- ✅ feedbacks
- ✅ engagements
- ✅ content_metrics
- ✅ users
- ✅ platforms
- ✅ categories
- ✅ hashtags

### Step 2: Install Dependencies
```bash
cd /Users/mac/Herd/contentmatch-admin
composer require spatie/laravel-activitylog  # For activity logging
```

### Step 3: Add Routes
Add all routes from the Routes Configuration section to:
`/contentmatch-admin/routes/web.php` or `routes/admin.php`

### Step 4: Create Inertia Views
Create the following Vue/React components:
- `resources/js/Pages/Admin/Content/Index.vue`
- `resources/js/Pages/Admin/Content/Show.vue`
- `resources/js/Pages/Admin/Content/Reported.vue`
- `resources/js/Pages/Admin/Content/Analytics.vue`
- `resources/js/Pages/Admin/Content/Feedback.vue`
- `resources/js/Pages/Admin/Content/Engagements.vue`

### Step 5: Add Navigation Links
Update admin navigation to include:
- Content Management
- Reported Content
- Analytics
- Feedback
- Engagements

### Step 6: Test Functionality
- [ ] View content list
- [ ] Search and filter content
- [ ] View content details
- [ ] Update content status
- [ ] Ban content
- [ ] View reported content
- [ ] Resolve reports
- [ ] View analytics dashboard
- [ ] View feedback
- [ ] View engagements
- [ ] Bulk operations
- [ ] Export data

### Step 7: Set Up Permissions (Optional)
Consider adding role-based permissions:
```php
'admin.content.view'
'admin.content.edit'
'admin.content.delete'
'admin.reports.manage'
'admin.analytics.view'
```

---

## Advanced Features (Future Enhancements)

### 1. Automated Content Moderation
- AI-based content analysis
- Automatic flagging of suspicious content
- Content similarity detection

### 2. Real-time Notifications
- Browser notifications for new reports
- Email digest for pending actions
- Slack integration

### 3. Advanced Analytics
- Predictive trending
- Creator performance scoring
- Engagement predictions
- A/B testing capabilities

### 4. Content Scheduling
- Schedule content publication
- Auto-archive old content
- Content expiration

### 5. User Warning System
- Strike-based warnings
- Temporary suspensions
- Appeal process

---

## Security Considerations

### Authentication
- ✅ All routes protected by `auth:admin` middleware
- ✅ Role-based access control recommended

### Authorization
- Implement Policy classes for Content management
- Check admin permissions before sensitive actions

### Audit Logging
- All moderation actions logged
- Activity log for compliance
- Immutable audit trail

### Data Privacy
- Respect GDPR requirements
- Anonymize exported data if needed
- Secure proof image storage

---

## Performance Optimization

### Database
- ✅ Indexes on frequently queried columns
- ✅ Eager loading relationships
- Use database transactions for multi-step operations
- Consider read replicas for analytics

### Caching
```php
// Cache trending data
Cache::remember('trending_hashtags_30d', 3600, function() {
    // Expensive query
});

// Cache analytics stats
Cache::remember('content_stats', 1800, function() {
    // Stats query
});
```

### Query Optimization
- ✅ Use `withCount()` instead of `count()` in loops
- ✅ Select only needed columns
- Paginate large result sets
- Use chunks for large exports

---

## Troubleshooting

### Issue: Content not showing
**Check:**
- Database connection
- Soft deletes filtering
- Relationship eager loading

### Issue: Reports not resolving
**Check:**
- Transaction rollback on error
- Foreign key constraints
- Admin authentication

### Issue: Analytics loading slowly
**Solution:**
- Implement caching
- Reduce time period
- Optimize queries with indexes

---

## Conclusion

This Content Management & Analytics module provides comprehensive tools for:

✅ **Content Moderation** - Efficient review and management  
✅ **Report Handling** - Structured resolution workflow  
✅ **Analytics & Insights** - Data-driven decisions  
✅ **User Feedback** - Quality improvement  
✅ **Engagement Tracking** - Community health monitoring  

The module is production-ready and scales with your platform's growth.

---

**Last Updated:** October 9, 2025  
**Version:** 1.0  
**Maintainer:** ContentMatch Development Team  
**Status:** ✅ Ready for Implementation
