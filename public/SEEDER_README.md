# Admin User Seeder

This file allows you to create an admin user for the Maakjelijstje.nl application via a web interface.

## How to Use

### 1. Access the Seeder
Simply visit the seeder in your browser:
```
http://lijst.wmcdev.nl/seed_admin.php
```

### 2. Run the Seeder
The seeder will automatically start and perform the following steps:

The seeder will:
1. ✓ Test database connection
2. ✓ Check if users table exists
3. ✓ Check if admin already exists
4. ✓ Create admin user (if not exists)
5. ✓ Display database statistics

### 3. Admin Credentials
After successful creation, use these credentials to login:
- **Email:** `admin@Maakjelijstje.nl`
- **Password:** `admin123`

### 4. Change Password
⚠️ **IMPORTANT:** Change the admin password immediately after first login!

1. Login to the admin panel
2. Go to Admin Dashboard
3. Edit your user profile
4. Change the password to something secure

### 5. Delete the Seeder
After creating the admin user, **DELETE THIS FILE IMMEDIATELY** for security reasons:
```
/public/seed_admin.php
```

## Security Notes

- Delete the seeder file after use for security reasons
- Change the default password immediately after login
- Do not share the default credentials
- The seeder can be run multiple times (it won't create duplicate admins)

## Troubleshooting

### "Database connection failed"
- Check your `.env` file for correct database credentials
- Ensure the database exists in phpMyAdmin
- Verify the database user has proper permissions

### "Users table not found"
- Run migrations first: `/public/run_migrations.php`
- Ensure all migrations completed successfully

### "Admin user already exists"
- The admin user has already been created
- You can edit the existing admin user in the admin panel
- If you need to reset the password, use the admin panel's user edit feature

## Files

- `seed_admin.php` - The seeder script (DELETE AFTER USE)
- `SEEDER_README.md` - This file (optional, can be deleted)

## Quick Start

1. Visit: `http://lijst.wmcdev.nl/seed_admin.php`
2. Wait for the seeder to complete
3. Login with `admin@Maakjelijstje.nl` / `admin123`
4. Change the password immediately
5. Delete the seeder file

## Related Files

- `/public/run_migrations.php` - Database migration runner
- `/app/Models/UserModel.php` - User model with password hashing
- `/app/Config/Database.php` - Database configuration
