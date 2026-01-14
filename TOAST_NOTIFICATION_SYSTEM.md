# Toast Notification System

A comprehensive toast notification system has been implemented to provide visual feedback for backend operations.

## Components

### 1. **Toast.vue** (`resources/js/Components/Toast.vue`)
Individual toast notification component with:
- Success (green), Error (red), Warning (yellow), and Info (blue) styles
- Auto-dismiss after configurable duration (default: 5 seconds)
- Manual close button
- Smooth enter/exit animations

### 2. **ToastContainer.vue** (`resources/js/Components/ToastContainer.vue`)
Container that displays all active toasts stacked at the bottom-right corner of the screen.

### 3. **useToast.js** (`resources/js/Composables/useToast.js`)
Composable for managing toasts with methods:
- `success(message, duration)` - Show success toast
- `error(message, duration)` - Show error toast
- `warning(message, duration)` - Show warning toast
- `info(message, duration)` - Show info toast
- `remove(id)` - Remove a specific toast

## Usage

### In Vue Components

```javascript
import { useToast } from '@/Composables/useToast';

const { success, error, warning, info } = useToast();

// Show success message
success('User updated successfully!');

// Show error message
error('Failed to update user.');

// Show warning with custom duration
warning('This action cannot be undone', 7000);

// Show info message
info('Processing your request...');
```

### With Inertia Router

The system automatically captures flash messages from the backend:

```javascript
router.post(route('admin.users.suspend', user.id), data, {
  onSuccess: (page) => {
    // Flash message from backend is auto-displayed
    // Or manually show a toast
    success('User suspended successfully');
  },
  onError: (errors) => {
    error(errors.message || 'Operation failed');
  }
});
```

### Backend Flash Messages

The `AuthenticatedLayout.vue` automatically listens for flash messages:

#### Laravel Controller Example:
```php
// Success message
return Redirect::back()->with('success', 'User updated successfully.');

// Error message
return Redirect::back()->with('error', 'Failed to update user.');
```

These messages are automatically displayed as toasts when the page loads.

## Features

- ✅ **Automatic Flash Message Detection** - Captures Laravel flash messages
- ✅ **Multiple Toast Types** - Success, error, warning, and info
- ✅ **Auto-dismiss** - Configurable duration (default 5 seconds)
- ✅ **Manual Close** - Users can close toasts manually
- ✅ **Stacked Display** - Multiple toasts stack vertically
- ✅ **Smooth Animations** - Slide-in and fade-out effects
- ✅ **Responsive** - Works on all screen sizes
- ✅ **Accessible** - Proper ARIA roles and keyboard support

## Integration

The toast system is globally integrated in `AuthenticatedLayout.vue` and available throughout the admin panel. All existing backend flash messages are automatically captured and displayed as toasts.

## Customization

### Duration
Default duration is 5000ms (5 seconds). Customize per toast:
```javascript
success('Message', 10000); // 10 seconds
```

### Styling
Toast colors and styles are defined in `Toast.vue` and can be customized using Tailwind classes.

## Examples in Use

The following pages have been updated with toast notifications:
- User management (suspend, ban, reactivate, remove)
- Community management
- All admin CRUD operations

All flash messages from the backend are automatically converted to toast notifications.
