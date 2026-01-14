#!/bin/bash

# 🚀 Quick Setup Script for Admin Authentication System
# Run this after implementing the system

echo "🔧 Setting up Admin Authentication System..."
echo ""

# Step 1: Run migrations
echo "📦 Step 1/5: Running migrations..."
php artisan migrate --force
echo "✅ Migrations complete"
echo ""

# Step 2: Seed admins
echo "👥 Step 2/5: Creating initial admins..."
php artisan db:seed --class=AdminSeeder --force
echo "✅ Admins created"
echo ""

# Step 3: Clear cache
echo "🧹 Step 3/5: Clearing cache..."
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo "✅ Cache cleared"
echo ""

# Step 4: Build assets
echo "🎨 Step 4/5: Building frontend assets..."
npm install
npm run build
echo "✅ Assets built"
echo ""

# Step 5: Set permissions
echo "🔐 Step 5/5: Setting permissions..."
chmod -R 775 storage bootstrap/cache
echo "✅ Permissions set"
echo ""

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🎉 Setup Complete!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📝 Test Accounts Created:"
echo "   🔴 Super Admin: superadmin@contentmatch.test / password"
echo "   🔵 Admin:       admin@contentmatch.test / password"
echo "   🟢 Moderator:   moderator@contentmatch.test / password"
echo ""
echo "🌐 Login URL: /admin/login"
echo ""
echo "⚠️  Important Next Steps:"
echo "   1. Configure email in .env (required for OTP)"
echo "   2. Test the login flow"
echo "   3. Change default passwords"
echo ""
echo "📚 Full documentation: ADMIN_AUTH_GUIDE.md"
echo "✨ Implementation summary: IMPLEMENTATION_COMPLETE.md"
echo ""
