# Quick Start Guide - Get Running in 5 Minutes

## Prerequisites Check
- ✅ XAMPP installed and running
- ✅ MySQL/MariaDB running
- ✅ PHP 7.4+ available
- ✅ Composer installed

## Installation (5 Steps)

### Step 1: Open Terminal
Navigate to project directory:
```bash
cd f:\Projects\Affiliate-System-Codeigniter-4
```

### Step 2: Run Installation Script
**Option A - Automated (Windows):**
```bash
install.bat
```

**Option B - Manual:**
```bash
composer install
php spark migrate
php spark db:seed InitialSeeder
php spark key:generate
```

### Step 3: Start Server
```bash
php spark serve
```

### Step 4: Open Browser
Visit: **http://localhost:8080**

### Step 5: Login
- Email: `admin@lijstje.nl`
- Password: `Admin@123`

## That's It! 🎉

You now have a fully functional affiliate platform running.

## What You Can Do Now

### As Admin
1. Go to `/admin` - Access admin dashboard
2. Manage users, lists, categories
3. View analytics
4. Configure settings

### As User
1. Create a new list
2. Add products (requires Bol.com API)
3. Publish and share
4. Track clicks

## Adding Bol.com API (Optional)

Edit `.env` file and add:
```
BOL_CLIENT_ID = your_client_id
BOL_CLIENT_SECRET = your_client_secret
BOL_AFFILIATE_ID = your_affiliate_id
```

Restart server after editing.

## Common URLs

- Homepage: `http://localhost:8080`
- Login: `http://localhost:8080/login`
- Register: `http://localhost:8080/register`
- Dashboard: `http://localhost:8080/dashboard`
- Admin: `http://localhost:8080/admin`

## Troubleshooting

**Database error?**
- Create database `lijstje_db` in phpMyAdmin
- Check MySQL is running

**404 errors?**
- Use `php spark serve` command
- Or access via `public/` folder

**Can't login?**
- Run seeder: `php spark db:seed InitialSeeder`
- Use exact credentials above

## Need Help?

Check these files:
- `README.md` - Full documentation
- `INSTALLATION.md` - Detailed installation
- `FEATURES.md` - Feature list
- `PROJECT_SUMMARY.md` - Project overview

## Next Steps

1. ✅ Change admin password
2. ✅ Create test lists
3. ✅ Add categories if needed
4. ✅ Configure Bol.com API
5. ✅ Customize design
6. ✅ Deploy to production

---

**You're all set!** The platform is ready to use. 🚀
