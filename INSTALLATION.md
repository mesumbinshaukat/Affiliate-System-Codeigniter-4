# Quick Installation Guide

## Step-by-Step Installation

### 1. Install Composer Dependencies

Open terminal/command prompt in the project directory and run:

```bash
composer install
```

This will install CodeIgniter 4 and all required dependencies.

### 2. Configure Database

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `lijstje_db`
3. The `.env` file is already configured for XAMPP defaults:
   - Host: localhost
   - Database: lijstje_db
   - Username: root
   - Password: (empty)
   - Port: 3306

If your setup is different, edit the `.env` file accordingly.

### 3. Run Database Migrations

In the terminal, run:

```bash
php spark migrate
```

This creates all necessary tables:
- users
- categories
- products
- lists
- list_products
- clicks
- affiliate_sources
- settings

### 4. Seed Initial Data

Run the seeder to create admin user and default categories:

```bash
php spark db:seed InitialSeeder
```

This creates:
- Admin user (admin@Maakjelijstje.nl / Admin@123)
- 8 default categories
- Bol.com affiliate source
- Default settings

### 5. Generate Encryption Key

```bash
php spark key:generate
```

This generates a secure encryption key and updates your `.env` file.

### 6. Start the Server

#### Option A: PHP Built-in Server (Recommended for Development)

```bash
php spark serve
```

Access the site at: http://localhost:8080

#### Option B: XAMPP Apache

1. Move/copy the project to `C:\xampp\htdocs\`
2. Access via: http://localhost/Affiliate-System-Codeigniter-4/public/

Or configure a virtual host to point to the `public` folder.

### 7. Login

Navigate to http://localhost:8080/login

**Default Admin Credentials:**
- Email: admin@Maakjelijstje.nl
- Password: Admin@123

**IMPORTANT:** Change these credentials immediately after first login!

## Bol.com API Configuration (Optional)

To enable product search from Bol.com:

1. Register at https://partnerprogramma.bol.com/
2. Get your API credentials
3. Edit `.env` and add:
   ```
   BOL_CLIENT_ID = your_client_id_here
   BOL_CLIENT_SECRET = your_client_secret_here
   BOL_AFFILIATE_ID = your_affiliate_id_here
   ```

**Note:** The system works without API credentials, but product search will show a friendly error message.

## Verify Installation

After installation, verify everything works:

1. ✅ Homepage loads (http://localhost:8080)
2. ✅ Can login with admin credentials
3. ✅ Admin dashboard accessible (/admin)
4. ✅ Can create a new list
5. ✅ Categories are visible
6. ✅ No database errors

## Common Issues

### "Database connection failed"
- Ensure MySQL is running in XAMPP
- Check database name is `lijstje_db`
- Verify credentials in `.env`

### "404 Not Found" on all pages
- Make sure you're accessing via `public/` folder
- Or use `php spark serve` command

### "Class not found" errors
- Run `composer install` again
- Clear cache: `php spark cache:clear`

### "Permission denied" on writable folder
- On Windows: Right-click `writable` folder → Properties → Security → Give full control
- On Linux/Mac: `chmod -R 777 writable/`

## Next Steps

1. Login to admin panel
2. Change default admin password
3. Create some test lists
4. Explore the features
5. Configure Bol.com API (optional)
6. Customize the design if needed

## Need Help?

Check the main README.md for detailed documentation and troubleshooting.
