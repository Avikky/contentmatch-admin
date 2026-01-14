# 🔐 Admin Authentication System - Setup & Usage Guide

## Overview

This is a secure, two-factor admin authentication system built with Laravel, Inertia.js, and Vue 3. It features:

- **Email + Password + OTP** authentication
- **Role-based access control** (Super Admin, Admin, Moderator)
- **Admin-only system** (no public registration)
- **Super Admin manages all admins**
- Beautiful, responsive UI

---

## 📦 Installation Steps

### 1️⃣ Run Migrations

```bash
php artisan migrate
```

This creates:
- `admins` table
- `admin_otps` table

### 2️⃣ Seed Initial Admin

```bash
php artisan db:seed --class=AdminSeeder
```

This creates three test admins:
- **Super Admin**: superadmin@contentmatch.test / password
- **Admin**: admin@contentmatch.test / password
- **Moderator**: moderator@contentmatch.test / password

### 3️⃣ Configure Mail Settings

Update your `.env` file with mail credentials:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=admin@contentmatch.test
MAIL_FROM_NAME="ContentMatch Admin"
```

For development, use [Mailtrap](https://mailtrap.io/) or [MailHog](https://github.com/mailhog/MailHog).

### 4️⃣ Clear Cache

```bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 5️⃣ Build Frontend Assets

```bash
npm install
npm run build
```

For development:
```bash
npm run dev
```

---

## 🔑 Login Flow

### Step 1: Email & Password
1. Admin visits `/admin/login`
2. Enters email and password
3. System validates credentials
4. If valid, generates 6-digit OTP
5. Sends OTP to admin's email
6. Redirects to OTP verification page

### Step 2: OTP Verification
1. Admin enters 6-digit OTP
2. System validates OTP (must be used within 5 minutes)
3. If valid, logs admin in
4. Redirects to dashboard

**Security Features:**
- Rate limiting on login attempts
- Rate limiting on OTP attempts
- OTP expires after 5 minutes
- OTP is one-time use only
- Sessions regenerated on login

---

## 👥 Admin Management

### Super Admin Capabilities
- ✅ Create new admins
- ✅ Update admin details
- ✅ Change admin roles
- ✅ Activate/deactivate admins
- ✅ Delete admins

### Creating a New Admin (Super Admin Only)

1. Navigate to **Admin Management**
2. Click **"Create Admin"**
3. Fill in:
   - Full Name
   - Email
   - Role (Admin, Super Admin, or Moderator)
4. Click **"Create Admin"**

**What happens:**
- System generates a secure random password
- Credentials are emailed to the new admin
- New admin must login with email + password
- Then complete OTP verification

### Role Definitions

| Role | Permissions |
|------|------------|
| **Super Admin** | Full access - Can manage all admins and access all features |
| **Admin** | Standard access - Can manage content and users |
| **Moderator** | Limited access - Can review and moderate content |

---

## 🛠️ Technical Details

### Database Tables

#### `admins`
```
- id
- full_name
- email (unique)
- password (hashed)
- role (admin, superadmin, moderator)
- profile_photo (nullable)
- is_active (boolean)
- last_login_at (timestamp)
- created_by (foreign key to admins)
- timestamps
```

#### `admin_otps`
```
- id
- admin_id (foreign key)
- otp_hash (hashed OTP)
- expires_at (timestamp)
- used_at (nullable timestamp)
- timestamps
```

### Models

**Admin Model** (`app/Models/Admin.php`)
- Extends `Authenticatable`
- Methods: `isSuperAdmin()`, `isAdmin()`, `isModerator()`
- Auto-generates avatar from initials if no photo

**AdminOtp Model** (`app/Models/AdminOtp.php`)
- Methods: `generateOtp()`, `createForAdmin()`, `verify()`
- Automatically invalidates old OTPs

### Controllers

1. **AdminAuthController** - Handles login and OTP verification
2. **AdminManagementController** - Handles admin CRUD operations

### Middleware

1. **EnsureAdminIsAuthenticated** (`admin.auth`) - Checks if admin is logged in
2. **EnsureAdminIsSuperAdmin** (`admin.super`) - Checks if admin is super admin
3. **EnsureIsAdmin** (`admin.role`) - Checks if admin has admin role

### Routes

```php
// Guest routes
GET  /admin/login          - Login page
POST /admin/login          - Process login

// OTP routes
GET  /admin/otp/verify     - OTP verification page
POST /admin/otp/verify     - Verify OTP
POST /admin/otp/resend     - Resend OTP

// Authenticated routes
GET  /admin/dashboard      - Dashboard
POST /admin/logout         - Logout

// Admin management (Super Admin only)
GET    /admin/admins           - List admins
GET    /admin/admins/create    - Create admin form
POST   /admin/admins           - Store admin
GET    /admin/admins/{id}/edit - Edit admin form
PUT    /admin/admins/{id}      - Update admin
DELETE /admin/admins/{id}      - Delete admin
POST   /admin/admins/{id}/toggle-status - Toggle active status
```

### Vue Components

1. **Auth/AdminLogin.vue** - Login page (Step 1)
2. **Auth/OtpVerify.vue** - OTP verification page (Step 2)
3. **Admin/Dashboard.vue** - Admin dashboard
4. **Admin/AdminList.vue** - List of all admins
5. **Admin/CreateAdmin.vue** - Create admin form
6. **Admin/EditAdmin.vue** - Edit admin form

### Email Templates

1. **emails/admin-otp.blade.php** - OTP verification email
2. **emails/admin-account-created.blade.php** - New admin welcome email

---

## 🔒 Security Features

1. **Password Hashing** - All passwords hashed with bcrypt
2. **OTP Hashing** - OTPs stored as hashes, not plain text
3. **Session Regeneration** - Session regenerated on login
4. **Rate Limiting** - Prevents brute force attacks
5. **One-Time OTPs** - Each OTP can only be used once
6. **OTP Expiry** - OTPs expire after 5 minutes
7. **Active Status Check** - Inactive admins cannot login

---

## 🧪 Testing

### Test Login (Without Email)

For development/testing, you can temporarily disable OTP:

1. Comment out the OTP generation in `AdminAuthController@login`
2. Directly login the admin
3. **⚠️ Remove this before production!**

### Test Credentials

```
Super Admin:
Email: superadmin@contentmatch.test
Password: password

Admin:
Email: admin@contentmatch.test
Password: password

Moderator:
Email: moderator@contentmatch.test
Password: password
```

---

## 📝 Common Tasks

### Change Password (Manual)

```bash
php artisan tinker
```

```php
$admin = App\Models\Admin::where('email', 'admin@contentmatch.test')->first();
$admin->password = bcrypt('newpassword');
$admin->save();
```

### Activate/Deactivate Admin (Manual)

```bash
php artisan tinker
```

```php
$admin = App\Models\Admin::find(1);
$admin->is_active = false; // or true
$admin->save();
```

### Clear Old OTPs

```bash
php artisan tinker
```

```php
App\Models\AdminOtp::where('expires_at', '<', now())->delete();
```

---

## 🐛 Troubleshooting

### Issue: "Session expired" error on OTP page
**Solution:** Clear browser cookies and try again

### Issue: OTP emails not sending
**Solution:** 
1. Check `.env` mail configuration
2. Check Laravel logs: `storage/logs/laravel.log`
3. Test with Mailtrap or MailHog

### Issue: "Too many attempts" error
**Solution:** Wait for rate limit to expire (60 seconds) or clear cache:
```bash
php artisan cache:clear
```

### Issue: Admin table already exists
**Solution:** You may need to drop the old admins table or modify the migration to alter the existing table instead of creating a new one.

---

## 🚀 Deployment Checklist

- [ ] Change default admin passwords
- [ ] Configure production mail settings
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Enable HTTPS
- [ ] Set up SSL certificates
- [ ] Configure proper CORS settings
- [ ] Set up database backups
- [ ] Configure rate limiting appropriately
- [ ] Set up monitoring/logging
- [ ] Review and test all security features

---

## 📚 Additional Resources

- [Laravel Authentication](https://laravel.com/docs/authentication)
- [Inertia.js Documentation](https://inertiajs.com/)
- [Vue 3 Documentation](https://vuejs.org/)
- [Laravel Mail](https://laravel.com/docs/mail)

---

## 🤝 Support

For issues or questions:
1. Check the troubleshooting section
2. Review Laravel logs
3. Check browser console for errors
4. Contact system administrator

---

**Built with ❤️ using Laravel, Inertia.js, and Vue 3**
