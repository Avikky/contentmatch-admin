# Community Management Module

## Overview
This module provides comprehensive community management functionality for administrators, allowing them to view, monitor, and manage all communities on the platform.

## Features Implemented

### 1. Community List View (`/admin/communities`)
- **Route**: `GET /admin/communities`
- **Controller**: `CommunityController@index`
- **View**: `resources/js/Pages/Admin/Communities/Index.vue`

Features:
- Search communities by name, category, or description
- View community avatar, name, and slug
- See community owner information
- View member count, category, status, and visibility
- View creation date
- Click to view full community details
- Paginated results

### 2. Community Detail View (`/admin/communities/{id}`)
- **Route**: `GET /admin/communities/{community}`
- **Controller**: `CommunityController@show`
- **View**: `resources/js/Pages/Admin/Communities/Show.vue`

#### Overview Tab
- Community profile with avatar, name, and description
- Statistics: Total members, admins, moderators, Discord servers
- Community owner information with link to user profile
- Detailed community information (created date, last updated, visibility, status, category)

#### Members Tab
- Complete list of all community members
- Member details: Avatar, name, email
- Member role (admin, moderator, member)
- Member status (active, banned, inactive)
- Join date and last activity date
- Actions:
  - View member profile
  - Ban member from community
  - Remove member from community

#### Admins & Moderators Tab
- Separate sections for community admins and moderators
- Shows avatar, name, username, and email
- Link to view each admin/moderator's profile

#### Discord Servers Tab
- List of all connected Discord servers
- Server information: Name, Server ID, Invite Code
- Active/Inactive status
- Connection date

### 3. Member Management Actions

#### Ban Member
- **Route**: `POST /admin/communities/{community}/members/{user}/ban`
- **Controller**: `CommunityController@banMember`
- Updates member status to 'banned' in the community
- Member remains in database but cannot access community

#### Remove Member
- **Route**: `POST /admin/communities/{community}/members/{user}/remove`
- **Controller**: `CommunityController@removeMember`
- Completely removes member from the community
- Deletes the membership relationship

## Database Relationships

### Community Model Updates
Added new relationships:
- `admins()` - Members with admin role
- `moderators()` - Members with moderator role
- `discordServers()` - Connected Discord servers

Existing relationships:
- `owner()` - Community creator
- `members()` - All community members with pivot data (role, status, join date, last activity)
- `hashtags()` - Associated hashtags
- `engagementMetrics()` - Engagement statistics

## Navigation
- Added "Communities" link to the admin sidebar navigation
- Icon: Group of people icon
- Accessible from any admin page

## UI Components Used
- `AuthenticatedLayout` - Main admin layout
- `Modal` - For confirmation dialogs (ban/remove actions)
- `Link` - Inertia.js Link component for navigation

## Styling
- Consistent with existing admin panel design
- Uses Tailwind CSS
- Responsive design (works on mobile and desktop)
- Color-coded badges for status indicators:
  - Green: Active status
  - Red: Banned status
  - Blue: Public visibility
  - Purple: Private visibility, Admin role
  - Orange: Categories
  - Gray: Archived/Inactive

## Key Features

### Security & Authorization
- All routes protected by `auth:admin` middleware
- Actions require admin authentication
- Proper validation for all operations

### User Experience
- Tabbed interface for easy navigation between different data views
- Search and filter capabilities
- Confirmation modals for destructive actions
- Loading states during processing
- Success/error messages via Inertia.js flash messages
- Pagination for large datasets

### Data Display
- Formatted dates (e.g., "Jan 15, 2024")
- User initials displayed when no avatar
- Community initials displayed when no avatar
- Empty states for lists with no data
- Proper null/undefined handling

## Files Modified/Created

### Routes
- `routes/web.php` - Added community management routes

### Controllers
- `app/Http/Controllers/Admin/CommunityController.php` - Added show(), banMember(), removeMember()

### Models
- `app/Models/Community.php` - Added admins(), moderators(), discordServers() relationships

### Views
- `resources/js/Pages/Admin/Communities/Index.vue` - Complete redesign with proper list view
- `resources/js/Pages/Admin/Communities/Show.vue` - New detailed community view with tabs

### Layouts
- `resources/js/Layouts/AuthenticatedLayout.vue` - Added Communities navigation item

## Usage

1. Navigate to "Communities" from the admin sidebar
2. Use search to find specific communities
3. Click on any community to view details
4. Use tabs to navigate between Overview, Members, Staff, and Discord sections
5. Perform actions on members (view, ban, remove) from the Members tab
6. View community owner, admins, and moderators in their respective sections
7. Monitor Discord server connections

## Future Enhancements (Suggestions)
- Bulk member actions (ban/remove multiple members)
- Export community data
- Community activity analytics
- Member activity timeline
- Discord server management (add/remove/edit)
- Community settings editor
- Audit log for all admin actions
- Advanced filters (by status, visibility, member count range)
