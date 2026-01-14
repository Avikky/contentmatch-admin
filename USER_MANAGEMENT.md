# User Management System - Documentation

## Overview

This user management module provides comprehensive tools for administrators to manage platform users, including moderation actions, community management, and detailed user information tracking.

## Features

### User Listing & Search
- Paginated user list with advanced filtering
- Search by name, email, or username
- Filter by status (active, suspended, banned)
- Filter by premium and verification status
- Real-time statistics dashboard

### User Profile Management
- View detailed user information
- Track user statistics (communities, reports, subscriptions, blocked users)
- View user activity history
- Access community memberships
- Monitor subscription status

### Moderation Actions
1. **Suspend User**: Temporarily restrict user access
2. **Ban User**: Permanently prohibit user access
3. **Reactivate User**: Restore access to suspended/banned users
4. **Remove User**: Soft-delete user account
5. **Remove from Community**: Detach user from specific communities

### Audit Trail
- All moderation actions are logged with:
  - Admin who performed the action
  - Action type
  - Reason provided
  - Status transitions
  - Timestamp
  - Additional metadata

### User Reports
- View all reports filed against a user
- Filter by report status
- Track reporter information
- Monitor resolution status

## Database Structure

### New Tables
- **user_moderation_logs**: Tracks all admin actions on users
  - Stores action type, admin ID, reason, status changes
  - Provides full audit trail for compliance

### Updated Models
- **User**: Added soft deletes, status scopes, relationships for moderation, reports, blocks, and subscriptions
- **Report**: Polymorphic relationship for reported content
- **Subscription**: User subscription management
- **UserModerationLog**: Audit log for moderation actions

## Routes

### Admin User Management Routes
```
GET    /admin/users                                   - List all users
GET    /admin/users/{user}                           - View user details
POST   /admin/users/{user}                           - Update user
DELETE /admin/users/{user}                           - Remove user

POST   /admin/users/{user}/ban                       - Ban user
POST   /admin/users/{user}/suspend                   - Suspend user
POST   /admin/users/{user}/reactivate                - Reactivate user

GET    /admin/users/{user}/reports                   - View user reports
GET    /admin/users/{user}/communities               - View user communities
GET    /admin/users/{user}/subscriptions             - View subscriptions
GET    /admin/users/{user}/moderation-history        - View moderation logs

POST   /admin/users/{user}/remove-from-community     - Remove from community
```

## Service Layer

### UserManagementService
Central service handling all user management operations:
- `getUsers()`: Fetch paginated users with filters
- `getUserDetails()`: Get comprehensive user information
- `banUser()`: Ban a user with audit logging
- `suspendUser()`: Suspend a user with audit logging
- `reactivateUser()`: Reactivate suspended/banned user
- `removeUser()`: Soft-delete user account
- `removeUserFromCommunity()`: Detach user from community
- `getUserReports()`: Fetch reports against user
- `getUserCommunities()`: Get user's community memberships
- `getUserSubscriptions()`: Get user's subscription data
- `getModerationHistory()`: Fetch moderation logs
- `getUserStatistics()`: Get platform-wide user statistics

## Frontend Components

### Pages
1. **Index.vue**: Main user listing with filters and statistics
2. **Show.vue**: Detailed user profile with tabbed interface
   - Overview tab: Communities and recent activity
   - Reports tab: Reports filed against user
   - Moderation History tab: Admin action logs

### Features
- Real-time statistics cards
- Advanced filtering and search
- Modal-based moderation actions
- Inline community management
- Responsive design matching existing admin portal

## Usage

### Viewing Users
Navigate to "Users" in the admin sidebar to access the user management dashboard.

### Performing Moderation Actions
1. Click "View Details" on any user
2. Use the action buttons (Suspend, Ban, Reactivate, Remove)
3. Provide a reason when prompted
4. Confirm the action

### Removing User from Community
1. View user details
2. Navigate to the community in the Overview tab
3. Click "Remove" next to the community
4. Confirm the action

### Viewing Reports
1. View user details
2. Switch to the "Reports" tab
3. Review all reports filed against the user

## Security Considerations
- All routes protected by `auth:admin` middleware
- Admins cannot ban/suspend/remove other admin users
- All moderation actions require authentication
- Reasons are required for ban/suspend actions
- Complete audit trail for compliance

## Testing Commands

```bash
# Run migrations
php artisan migrate

# Build frontend assets
npm run build

# Run PHP syntax check
php -l app/Services/UserManagementService.php

# Clear cache
php artisan cache:clear
php artisan config:clear
```

## Future Enhancements
- Bulk moderation actions
- Advanced reporting and analytics
- Email notifications for moderation actions
- Custom moderation workflows
- Integration with abuse prevention systems
- Export user data for GDPR compliance
