# Admin Community Module Routes

This file documents the recommended routes for the ContentMatch Admin Community Module.

## Current Routes (Assumed)
```php
// In routes/web.php or routes/admin.php

Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    
    // Community Management
    Route::get('/communities', [CommunityController::class, 'index'])->name('admin.communities.index');
    Route::post('/communities', [CommunityController::class, 'store'])->name('admin.communities.store');
    Route::get('/communities/{community}', [CommunityController::class, 'show'])->name('admin.communities.show');
    Route::put('/communities/{community}', [CommunityController::class, 'update'])->name('admin.communities.update');
    Route::delete('/communities/{community}', [CommunityController::class, 'destroy'])->name('admin.communities.destroy');
    
    // Member Management
    Route::delete('/communities/{community}/members/{user}', [CommunityController::class, 'removeMember'])
        ->name('admin.communities.members.remove');
    Route::post('/communities/{community}/members/{user}/ban', [CommunityController::class, 'banMember'])
        ->name('admin.communities.members.ban');
});
```

## Recommended Additional Routes

Add these routes to enhance the community management functionality:

```php
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    
    // ====================
    // Member Role Management
    // ====================
    Route::patch('/communities/{community}/members/{user}/role', [CommunityController::class, 'updateMemberRole'])
        ->name('admin.communities.members.update-role');
    
    Route::patch('/communities/{community}/members/{user}/status', [CommunityController::class, 'toggleMemberStatus'])
        ->name('admin.communities.members.toggle-status');
    
    // ====================
    // Bulk Operations
    // ====================
    Route::post('/communities/{community}/members/bulk-remove', [CommunityController::class, 'bulkRemoveMembers'])
        ->name('admin.communities.members.bulk-remove');
    
    Route::post('/communities/{community}/members/bulk-role', [CommunityController::class, 'bulkUpdateRole'])
        ->name('admin.communities.members.bulk-role');
    
    // ====================
    // Community Analytics
    // ====================
    Route::get('/communities/{community}/analytics', [CommunityController::class, 'analytics'])
        ->name('admin.communities.analytics');
    
    Route::get('/communities/{community}/engagement', [CommunityController::class, 'engagement'])
        ->name('admin.communities.engagement');
    
    // ====================
    // Content Moderation
    // ====================
    Route::get('/communities/{community}/content', [CommunityController::class, 'content'])
        ->name('admin.communities.content');
    
    Route::get('/communities/{community}/reports', [CommunityController::class, 'reports'])
        ->name('admin.communities.reports');
    
    // ====================
    // Export Functions
    // ====================
    Route::get('/communities/export', [CommunityController::class, 'exportCommunities'])
        ->name('admin.communities.export');
    
    Route::get('/communities/{community}/members/export', [CommunityController::class, 'exportMembers'])
        ->name('admin.communities.members.export');
    
    // ====================
    // Discord Integration
    // ====================
    Route::get('/communities/{community}/discord', [CommunityController::class, 'discordSettings'])
        ->name('admin.communities.discord');
    
    Route::post('/communities/{community}/discord', [CommunityController::class, 'updateDiscordSettings'])
        ->name('admin.communities.discord.update');
    
    Route::delete('/communities/{community}/discord', [CommunityController::class, 'unlinkDiscord'])
        ->name('admin.communities.discord.unlink');
});
```

## Controller Methods to Implement

### Bulk Operations
```php
public function bulkRemoveMembers(Community $community, Request $request): RedirectResponse
{
    $validated = $request->validate([
        'user_ids' => ['required', 'array'],
        'user_ids.*' => ['exists:users,id'],
    ]);

    $community->members()->detach($validated['user_ids']);

    return Redirect::back()->with('success', count($validated['user_ids']) . ' members removed successfully.');
}

public function bulkUpdateRole(Community $community, Request $request): RedirectResponse
{
    $validated = $request->validate([
        'user_ids' => ['required', 'array'],
        'user_ids.*' => ['exists:users,id'],
        'role' => ['required', 'in:admin,moderator,member'],
    ]);

    foreach ($validated['user_ids'] as $userId) {
        $community->members()->updateExistingPivot($userId, [
            'role' => $validated['role'],
        ]);
    }

    return Redirect::back()->with('success', count($validated['user_ids']) . ' member roles updated successfully.');
}
```

### Analytics
```php
public function analytics(Community $community): Response
{
    $stats = [
        'total_members' => $community->members()->count(),
        'active_members' => $community->members()->wherePivot('status', 'active')->count(),
        'banned_members' => $community->members()->wherePivot('status', 'banned')->count(),
        'admins_count' => $community->members()->wherePivot('role', 'admin')->count(),
        'moderators_count' => $community->members()->wherePivot('role', 'moderator')->count(),
        'total_content' => $community->content()->count(),
        'avg_engagement' => $community->engagementScores()->avg('engagement_score'),
    ];

    $memberGrowth = $community->members()
        ->selectRaw('DATE(community_members.created_at) as date, COUNT(*) as count')
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->limit(30)
        ->get();

    return Inertia::render('Admin/Communities/Analytics', [
        'community' => $community,
        'stats' => $stats,
        'memberGrowth' => $memberGrowth,
    ]);
}
```

### Content Moderation
```php
public function content(Community $community, Request $request): Response
{
    $content = $community->content()
        ->with(['user:id,full_name,username'])
        ->when($request->status, fn($q) => $q->where('status', $request->status))
        ->when($request->type, fn($q) => $q->where('type', $request->type))
        ->latest()
        ->paginate(20);

    return Inertia::render('Admin/Communities/Content', [
        'community' => $community,
        'content' => $content,
        'filters' => [
            'status' => $request->status,
            'type' => $request->type,
        ],
    ]);
}
```

### Export Functions
```php
use Illuminate\Support\Facades\Response;

public function exportMembers(Community $community)
{
    $members = $community->members()
        ->select('users.id', 'users.full_name', 'users.username', 'users.email', 'users.status')
        ->withPivot('role', 'status', 'created_at', 'last_activity_at')
        ->get();

    $csv = "ID,Name,Username,Email,Role,Status,Joined At,Last Activity\n";
    
    foreach ($members as $member) {
        $csv .= sprintf(
            "%s,%s,%s,%s,%s,%s,%s,%s\n",
            $member->id,
            $member->full_name,
            $member->username,
            $member->email,
            $member->pivot->role,
            $member->pivot->status,
            $member->pivot->created_at,
            $member->pivot->last_activity_at ?? 'N/A'
        );
    }

    return Response::make($csv, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $community->slug . '-members.csv"',
    ]);
}
```

## API Routes (Optional)

For AJAX/API calls from the frontend:

```php
Route::prefix('api/admin')->middleware(['auth:admin'])->group(function () {
    // Quick stats
    Route::get('/communities/{community}/quick-stats', [CommunityController::class, 'quickStats']);
    
    // Member search
    Route::get('/communities/{community}/members/search', [CommunityController::class, 'searchMembers']);
    
    // Activity feed
    Route::get('/communities/{community}/activity', [CommunityController::class, 'activityFeed']);
});
```

## Frontend Integration

### Inertia.js Props Pattern
```javascript
// In your Vue/React components
const props = defineProps({
    community: Object,
    members: Object, // Paginated
    filters: Object,
    stats: Object,
})
```

### Example Vue Component Usage
```vue
<script setup>
import { router } from '@inertiajs/vue3'

const updateRole = (userId, newRole) => {
    router.patch(route('admin.communities.members.update-role', {
        community: props.community.id,
        user: userId
    }), {
        role: newRole
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success
        }
    })
}
</script>
```

## Middleware Recommendations

```php
// In app/Http/Kernel.php or bootstrap/app.php (Laravel 11)
'admin' => \App\Http\Middleware\AdminAuthenticate::class,
'admin.permissions' => \App\Http\Middleware\CheckAdminPermissions::class,

// Usage in routes
Route::middleware(['admin', 'admin.permissions:manage-communities'])->group(function () {
    // Community routes
});
```

## Notes

- All routes assume the admin authentication guard is properly configured
- Consider adding rate limiting for bulk operations
- Implement activity logging for all admin actions
- Add confirmation dialogs for destructive operations (ban, delete)
- Consider implementing role-based permissions within the admin panel
