# Global Toast Notification System

## Overview
A fully responsive global toast notification system that automatically captures and displays backend responses for both success and error states.

## Features

✅ **Automatic Backend Response Handling**
- Automatically displays flash messages from Laravel backend
- Captures validation errors
- Handles server exceptions
- Shows success messages

✅ **Responsive Design**
- Mobile-first approach
- Full-width on mobile devices
- Right-aligned on larger screens (sm and above)
- Responsive text and icon sizes
- Proper word wrapping for long messages

✅ **Multiple Toast Types**
- Success (green)
- Error (red)
- Warning (yellow)
- Info (blue)

✅ **User Experience**
- Auto-dismiss after 5 seconds (configurable)
- Manual close button
- Smooth animations
- Stacking support for multiple toasts
- Non-blocking (pointer-events-none on container)

## How It Works

### 1. Global Event Listeners (app.js)
The system listens to Inertia.js router events:
- `success`: Captures flash success messages
- `error`: Captures validation errors
- `exception`: Handles server exceptions
- `finish`: Catches any remaining flash errors

### 2. Backend Integration (Laravel)
From your controllers, return flash messages:

```php
// Success message
return redirect()->back()->with('success', 'User updated successfully!');

// Error message
return redirect()->back()->with('error', 'Failed to update user.');

// Validation errors (automatically handled)
$request->validate([
    'email' => 'required|email',
]);
```

### 3. Manual Usage in Vue Components
You can also trigger toasts manually from your Vue components:

```vue
<script setup>
import { useToast } from '@/Composables/useToast';

const { success, error, warning, info } = useToast();

const handleAction = () => {
    // Show success toast
    success('Action completed successfully!');
    
    // Show error toast
    error('Something went wrong!');
    
    // Show warning toast
    warning('Please review your changes.');
    
    // Show info toast
    info('New features available.');
    
    // Custom duration (in milliseconds)
    success('Quick message!', 3000);
};
</script>
```

## Component Structure

### Toast.vue
Individual toast notification component with:
- Responsive sizing
- Type-based styling
- Auto-dismiss functionality
- Close button
- Smooth animations

### ToastContainer.vue
Container component that:
- Positions toasts correctly
- Manages multiple toasts
- Handles responsive positioning

### useToast.js
Composable that provides:
- Centralized state management
- Helper methods (success, error, warning, info)
- Toast lifecycle management

## Responsive Behavior

### Mobile (< 640px)
- Full width with 1rem margin on all sides
- Smaller text (text-xs)
- Smaller icons (h-4 w-4)
- Tighter padding (px-3 py-2.5)

### Tablet & Desktop (≥ 640px)
- Max width of 28rem (max-w-md)
- Right-aligned
- Regular text size (text-sm)
- Regular icons (h-5 w-5)
- Standard padding (px-4 py-3)

## Customization

### Change Duration
```javascript
// Default is 5000ms (5 seconds)
success('Message', 3000); // 3 seconds
error('Error', 0); // No auto-dismiss
```

### Styling
Toast colors are defined in the Toast.vue component:
- Success: `bg-green-50 text-green-800 border-green-200`
- Error: `bg-red-50 text-red-800 border-red-200`
- Warning: `bg-yellow-50 text-yellow-800 border-yellow-200`
- Info: `bg-blue-50 text-blue-800 border-blue-200`

## Integration Status

✅ Already integrated in:
- AuthenticatedLayout.vue (includes ToastContainer)
- app.js (global error handling)
- All Inertia requests automatically captured

## Examples

### Laravel Controller Examples

```php
// Create action
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
    ]);
    
    User::create($validated);
    
    return redirect()
        ->route('admin.users.index')
        ->with('success', 'User created successfully!');
}

// Update action
public function update(Request $request, User $user)
{
    try {
        $user->update($request->validated());
        
        return back()->with('success', 'User updated successfully!');
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to update user: ' . $e->getMessage());
    }
}

// Delete action
public function destroy(User $user)
{
    $user->delete();
    
    return redirect()
        ->route('admin.users.index')
        ->with('success', 'User deleted successfully!');
}
```

### Vue Component Examples

```vue
<template>
  <button @click="handleDelete">Delete User</button>
</template>

<script setup>
import { useToast } from '@/Composables/useToast';
import { router } from '@inertiajs/vue3';

const { success, error } = useToast();

const handleDelete = () => {
    if (confirm('Are you sure?')) {
        router.delete(route('admin.users.destroy', userId), {
            preserveScroll: true,
            onSuccess: () => {
                // Toast will be shown automatically from backend flash message
            },
            onError: () => {
                // Toast will be shown automatically from error handler
            }
        });
    }
};
</script>
```

## Testing

To test the toast system:

1. **Backend Flash Messages**: Make any CRUD operation and check for success/error toasts
2. **Validation Errors**: Submit a form with invalid data
3. **Manual Toasts**: Use the useToast composable in any component
4. **Responsiveness**: Test on mobile, tablet, and desktop viewports

## Troubleshooting

### Toasts not appearing
1. Check that ToastContainer is included in your layout
2. Verify flash messages are being returned from backend
3. Check browser console for errors

### Toasts appearing off-screen
- The responsive classes should handle this automatically
- Verify Tailwind CSS is compiled correctly

### Multiple toasts stacking incorrectly
- The container uses `flex-col gap-2` for proper spacing
- Each toast should appear below the previous one

## Future Enhancements

Potential improvements:
- Action buttons within toasts
- Progress bar for auto-dismiss
- Toast grouping/categorization
- Sound notifications
- Custom positions (top, bottom, left, right)
- Animation variants
