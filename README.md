# Lijstje.nl Clone - Affiliate List Platform

A complete affiliate marketing platform built with CodeIgniter 4, allowing users to create and share curated product lists with integrated Bol.com affiliate tracking.

## Features

### Public Features
- Browse curated product lists
- Category-based filtering
- Search functionality
- Responsive design with Bootstrap 5
- Social sharing (Facebook, WhatsApp, Copy Link)
- SEO-friendly URLs

### User Features
- User registration and authentication
- Create and manage product lists
- Search products via Bol.com API
- Drag-and-drop product ordering
- List status management (Draft, Published, Private)
- Personal analytics dashboard
- Click tracking for affiliate links

### Admin Features
- Complete user management
- List moderation and featured lists
- Category management
- Comprehensive analytics
- Affiliate source management
- Site-wide settings
- Click tracking and statistics

### Affiliate System
- Bol.com API integration
- Automatic affiliate link generation
- Click tracking with anonymized IPs (GDPR compliant)
- Product database with caching
- Redirect tracking system

## Technology Stack

- **Backend**: CodeIgniter 4.4+
- **Database**: MySQL/MariaDB
- **Frontend**: Bootstrap 5, jQuery, Font Awesome
- **API**: Bol.com Affiliate API

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- XAMPP/WAMP/LAMP or similar (for local development)

### Step 1: Install Dependencies

```bash
composer install
```

### Step 2: Configure Environment

1. Copy the `.env` file (already created)
2. Update database credentials in `.env`:
   ```
   database.default.hostname = localhost
   database.default.database = lijstje_db
   database.default.username = root
   database.default.password = 
   database.default.port = 3306
   ```

3. Add your Bol.com API credentials:
   ```
   BOL_CLIENT_ID = your_client_id
   BOL_CLIENT_SECRET = your_client_secret
   BOL_AFFILIATE_ID = your_affiliate_id
   ```

### Step 3: Create Database

Create a new database named `lijstje_db` in phpMyAdmin or via command line:

```sql
CREATE DATABASE lijstje_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

### Step 4: Run Migrations

Run the database migrations to create all tables:

```bash
php spark migrate
```

### Step 5: Seed Initial Data

Run the seeder to populate initial data (admin user, categories, etc.):

```bash
php spark db:seed InitialSeeder
```

### Step 6: Generate Encryption Key

Generate a secure encryption key:

```bash
php spark key:generate
```

This will automatically update your `.env` file.

### Step 7: Set Permissions

Ensure the `writable` directory is writable:

```bash
chmod -R 777 writable/
```

On Windows, right-click the `writable` folder → Properties → Security → Edit → Add write permissions.

### Step 8: Start Development Server

```bash
php spark serve
```

Or configure Apache/Nginx to point to the `public` folder.

## Default Credentials

After running the seeder, you can login with:

- **Email**: admin@lijstje.nl
- **Password**: Admin@123

**Important**: Change these credentials immediately after first login!

## Project Structure

```
├── app/
│   ├── Config/          # Configuration files
│   ├── Controllers/     # Application controllers
│   ├── Database/        # Migrations and seeders
│   ├── Filters/         # Authentication filters
│   ├── Libraries/       # Custom libraries (API, Tracker)
│   ├── Models/          # Database models
│   └── Views/           # View templates
├── public/              # Public web root
│   ├── index.php        # Front controller
│   └── .htaccess        # Apache rewrite rules
├── writable/            # Cache, logs, sessions, uploads
├── .env                 # Environment configuration
├── composer.json        # PHP dependencies
└── spark                # CLI tool
```

## Usage Guide

### For Users

1. **Register an Account**
   - Go to `/register`
   - Fill in your details
   - Login with your credentials

2. **Create a List**
   - Navigate to Dashboard → Create List
   - Enter title, description, and category
   - Save as draft or publish immediately

3. **Add Products**
   - Edit your list
   - Go to Products tab
   - Search for products from Bol.com
   - Click "Add" to include them in your list

4. **Publish and Share**
   - Set list status to "Published"
   - Share the public URL with others
   - Track clicks in your analytics

### For Admins

1. **Access Admin Panel**
   - Login with admin credentials
   - Navigate to `/admin`

2. **Manage Users**
   - View all registered users
   - Edit user roles and status
   - Block or delete users

3. **Manage Lists**
   - View all lists
   - Feature lists on homepage
   - Delete inappropriate content

4. **Manage Categories**
   - Create new categories
   - Edit existing categories
   - Set Font Awesome icons

5. **View Analytics**
   - Track total clicks
   - See top products and lists
   - Monitor daily statistics

## API Integration

### Bol.com API Setup

1. Register for Bol.com Partner Program
2. Get API credentials from partner dashboard
3. Add credentials to `.env` file
4. The system will automatically:
   - Authenticate via OAuth 2.0
   - Search products
   - Generate affiliate links
   - Track clicks

### Adding More Affiliate Sources

To add additional affiliate sources (Amazon, Coolblue, etc.):

1. Create a new library in `app/Libraries/` (e.g., `AmazonAPI.php`)
2. Implement similar methods as `BolComAPI.php`
3. Add source to database via admin panel
4. Update product search to support multiple sources

## Database Schema

### Main Tables

- **users**: User accounts and profiles
- **lists**: Product lists created by users
- **products**: Product information from APIs
- **list_products**: Many-to-many relationship
- **categories**: List categories
- **clicks**: Affiliate click tracking
- **affiliate_sources**: API source configuration
- **settings**: Site-wide settings

## Security Features

- Password hashing with bcrypt
- CSRF protection (can be enabled in filters)
- SQL injection prevention via Query Builder
- XSS protection with output escaping
- IP anonymization for GDPR compliance
- Role-based access control

## Performance Optimization

- Database query optimization with joins
- Session-based authentication
- Prepared statements for all queries
- Efficient pagination
- Asset CDN usage (Bootstrap, jQuery, Font Awesome)

## Customization

### Changing Colors

Edit the CSS variables in `app/Views/layouts/main.php`:

```css
:root {
    --primary-color: #2563eb;
    --secondary-color: #64748b;
    --success-color: #10b981;
    --danger-color: #ef4444;
}
```

### Adding Custom Fields

1. Create migration for new column
2. Update model's `$allowedFields`
3. Add form field in view
4. Update controller to handle new field

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check database credentials in `.env`
- Ensure database exists

### 404 Errors
- Check `.htaccess` is present in `public/`
- Verify `mod_rewrite` is enabled in Apache
- Ensure `app.baseURL` is correct in `.env`

### Bol.com API Not Working
- Verify API credentials are correct
- Check if credentials are properly set in `.env`
- API may not work without valid credentials (will show friendly error)

### Permission Denied
- Ensure `writable/` directory has write permissions
- Check file ownership on Linux/Mac

## Development

### Running Migrations

```bash
# Run all migrations
php spark migrate

# Rollback last migration
php spark migrate:rollback

# Reset database
php spark migrate:refresh
```

### Creating New Migration

```bash
php spark make:migration CreateTableName
```

### Creating New Controller

```bash
php spark make:controller ControllerName
```

### Creating New Model

```bash
php spark make:model ModelName
```

## Production Deployment

1. Set `CI_ENVIRONMENT = production` in `.env`
2. Disable debug mode
3. Enable CSRF protection in `app/Config/Filters.php`
4. Set proper file permissions (755 for directories, 644 for files)
5. Configure SSL certificate
6. Set up proper backup system
7. Configure cron jobs for maintenance tasks
8. Use production database credentials
9. Enable caching for better performance

## License

This project is open-source and available under the MIT License.

## Support

For issues and questions:
- Check the documentation
- Review CodeIgniter 4 documentation
- Check database migrations are run correctly

## Credits

- Built with CodeIgniter 4
- Bootstrap 5 for UI
- Font Awesome for icons
- Bol.com for affiliate products

## Changelog

### Version 1.0.0 (Initial Release)
- Complete user authentication system
- List creation and management
- Bol.com API integration
- Admin dashboard
- Click tracking
- Analytics
- Category management
- Responsive design
