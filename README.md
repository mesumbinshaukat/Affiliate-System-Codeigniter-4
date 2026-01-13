# Lijstje.nl Clone - Affiliate List Platform

A complete affiliate marketing platform built with CodeIgniter 4, allowing users to create and share curated product lists with integrated Bol.com affiliate tracking and commission management.

## Features

### Public Features
- Browse curated product lists
- Category-based filtering with age restrictions
- Search functionality
- Responsive design with Bootstrap 5
- Social sharing (Facebook, WhatsApp, Copy Link)
- SEO-friendly URLs
- Drawing/Lottery system (Loten Trekken)

### User Features
- User registration and authentication with optional gender field
- User profile management with age calculation
- Create and manage product lists
- **Co-owner collaboration** - Invite others to co-manage lists
- **Section organization** - Organize products into custom sections
- **Group Gift crowdfunding** - Enable contributions for expensive items
- Search products via Bol.com API with advanced filters
- Personalized product suggestions based on age and gender
- Professional product filters (sort, category, price range)
- Drag-and-drop product ordering
- List status management (Draft, Published, Private)
- Personal analytics dashboard with sales and commission tracking
- Click tracking for affiliate links with subId attribution
- Sales and commission history
- Drawing/Lottery event creation and management

### Admin Features
- Complete user management
- List moderation and featured lists
- Category management with age restrictions
- Comprehensive analytics with sales overview
- Affiliate source management
- Site-wide settings
- Click tracking and statistics
- Sales and commission tracking by user
- Drawing/Lottery management
- Commission report viewing

### Affiliate System
- Bol.com Marketing Catalog API v1 integration
- Bol.com Affiliate Reporting API v2 integration
- Automatic affiliate link generation with subId tracking
- Click tracking with anonymized IPs (GDPR compliant)
- Product database with caching
- Redirect tracking system
- Commission attribution via subId (user_id_list_id format)
- Sales data storage and reporting
- Scheduled commission report fetching
- Commission status tracking (pending, approved, rejected)

### Special Features
- **Automated Event Reminder System**
  - Set event dates (birthdays, anniversaries, etc.) for lists
  - Automated email reminders to collaborators
  - Customizable reminder intervals (e.g., 30, 14, 7 days before)
  - Smart scheduling with cron job support
  - Beautiful HTML email templates
  - Prevents duplicate reminders
  - Tracks sent/pending/failed reminders
- **Group Gift / Crowdfunding System**
  - Enable group contributions for expensive items
  - Set funding goals for products
  - Track contributions with progress bars
  - Anonymous contribution option
  - Contributor list with messages
  - Real-time funding progress
- **Co-Owner / Collaboration System**
  - Invite co-owners to edit lists
  - Accept/reject collaboration invitations
  - Share list management permissions
  - Section management for co-owners
  - Full CRUD permissions for collaborators
- Drawing/Lottery System (Loten Trekken)
  - Create drawing events
  - Invite participants
  - Random lot drawing
  - Participant status tracking
- Gender-based personalization
- Age-based product recommendations
- Advanced product filtering
- Commission management system

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

**Alternative: Run migrations via browser (for shared hosting without CLI access):**

1. Visit `http://your-domain/run_migration_bol_sales.php` to set up Bol.com sales tracking
2. Visit `http://your-domain/run_migration_gender.php` to add gender field and personalization
3. Delete these files after running for security

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
   - Fill in your details (gender is optional)
   - Login with your credentials

2. **Update Your Profile**
   - Go to `/user/profile`
   - Update personal information
   - Set your gender for personalized suggestions
   - Your age is automatically calculated from date of birth

3. **Create a List**
   - Navigate to Dashboard → Create List
   - Enter title, description, and category
   - Save as draft or publish immediately

4. **Add Products with Personalization**
   - Edit your list
   - Go to Products tab
   - View personalized suggestions based on your age and gender
   - Use advanced filters: sort by price/popularity, filter by category, set price range
   - Search for specific products from Bol.com
   - Click "Add" to include them in your list

5. **Organize with Sections** (Optional)
   - Go to Products tab
   - Click "Nieuwe Sectie" to create sections
   - Examples: "Sieraden", "Tech", "Lifetime Wensen"
   - Assign products to sections when adding or after

6. **Enable Group Gifts** (Optional)
   - For expensive items, toggle "Groepscadeau"
   - Set a target amount (funding goal)
   - Friends can contribute money towards the item
   - Track progress with real-time updates

7. **Set Event Date & Reminders** (Optional)
   - Set an event date (e.g., your birthday: February 19)
   - Enable "Stuur automatische e-mail herinneringen"
   - Customize reminder intervals (default: 30, 14, 7 days before)
   - Collaborators automatically receive email reminders
   - Example: Birthday on Feb 19 → Reminders on Jan 20, Feb 5, Feb 12

8. **Invite Co-Owners** (Optional)
   - Go to "Samenwerken" tab
   - Enter email to invite someone
   - They can help manage the list once accepted

9. **Publish and Share**
   - Set list status to "Published"
   - Share the public URL with others
   - Track clicks and commissions in your analytics

10. **View Sales & Commissions**
   - Go to Dashboard → Analytics
   - View your sales history
   - Track commission status (pending, approved, rejected)
   - See total earned commissions

11. **Create Drawing Events (Loten Trekken)**
   - Go to Drawings section
   - Create new drawing event
   - Invite participants
   - Draw lots to randomly assign participants
   - Track participant responses

### For Admins

1. **Access Admin Panel**
   - Login with admin credentials
   - Navigate to `/admin`

2. **Manage Users**
   - View all registered users
   - Edit user roles and status
   - Block or delete users
   - View user profile information including gender

3. **Manage Lists**
   - View all lists
   - Feature lists on homepage
   - Delete inappropriate content
   - View list analytics

4. **Manage Categories**
   - Create new categories
   - Edit existing categories
   - Set Font Awesome icons
   - Configure age restrictions (min_age, max_age)

5. **View Analytics**
   - Track total clicks and traffic
   - See top products and lists
   - Monitor daily statistics
   - View sales and commission overview
   - Track commissions by user
   - View commission status breakdown

6. **Manage Sales & Commissions**
   - View all sales records with user attribution
   - Track commission status (pending, approved, rejected)
   - See total commissions earned
   - Monitor sales by user
   - View product information for each sale

7. **Manage Drawing Events**
   - View all drawing events
   - View participant details
   - Delete drawing events
   - Monitor drawing statistics

8. **Configure Affiliate Sources**
   - Enable/disable affiliate sources
   - Manage API credentials
   - Monitor API health

## API Integration

### Bol.com API Setup

1. Register for Bol.com Partner Program
2. Get API credentials from partner dashboard
3. Add credentials to `.env` file:
   ```
   BOL_CLIENT_ID = your_client_id
   BOL_CLIENT_SECRET = your_client_secret
   BOL_AFFILIATE_ID = your_affiliate_id
   ```
4. The system will automatically:
   - Authenticate via OAuth 2.0
   - Search products with advanced filters
   - Generate affiliate links with subId tracking
   - Track clicks and commissions
   - Fetch commission reports

### Bol.com API Features

**Marketing Catalog API v1:**
- Product search with filters (sort, category, price range)
- Personalized suggestions based on age and gender
- Popular products listing
- Product details and ratings

**Affiliate Reporting API v2:**
- Commission reports
- Order reports
- Promotion reports
- Scheduled daily fetching via cron job

### Commission Tracking System

The system tracks commissions through:
1. **SubId Attribution**: Each affiliate link includes a unique subId (user_id_list_id)
2. **Click Logging**: Clicks are recorded with the subId
3. **Report Fetching**: Daily scheduled task fetches commission reports from Bol.com
4. **Sales Storage**: Commission data is stored in the sales table
5. **Status Tracking**: Commissions tracked as pending, approved, or rejected

**Scheduled Report Fetching:**
```bash
# Run manually
php spark fetch:bol-reports

# Run with custom date range
php spark fetch:bol-reports --start-date=2024-12-01 --end-date=2024-12-15

# Dry run (preview without saving)
php spark fetch:bol-reports --dry-run
```

**Cron Job Setup (for automated daily fetching):**
```bash
# Linux/Unix - Add to crontab
0 2 * * * cd /path/to/project && php spark fetch:bol-reports

# Windows Task Scheduler
Program: C:\php\php.exe
Arguments: spark fetch:bol-reports
Start in: C:\path\to\project
```

### Adding More Affiliate Sources

To add additional affiliate sources (Amazon, Coolblue, etc.):

1. Create a new library in `app/Libraries/` (e.g., `AmazonAPI.php`)
2. Implement similar methods as `BolComAPI.php`
3. Add source to database via admin panel
4. Update product search to support multiple sources

## Database Schema

### Main Tables

- **users**: User accounts and profiles (includes gender field, date_of_birth)
- **lists**: Product lists created by users
- **products**: Product information from APIs
- **list_products**: Many-to-many relationship
- **categories**: List categories (includes min_age, max_age for age restrictions)
- **clicks**: Affiliate click tracking (includes sub_id for commission attribution)
- **affiliate_sources**: API source configuration
- **settings**: Site-wide settings
- **sales**: Commission and sales data from Bol.com API
- **drawings**: Drawing/Lottery events
- **drawing_participants**: Participants in drawing events
- **list_sections**: Sections for organizing products within lists
- **list_collaborators**: Co-owners/collaborators for lists
- **list_invitations**: Collaboration invitation tracking
- **contributions**: Group gift contributions and crowdfunding

### New Fields Added

**users table:**
- `gender` (ENUM: 'male', 'female', 'other') - Optional, for personalization
- `date_of_birth` - Used for age calculation and personalized suggestions

**categories table:**
- `min_age` - Minimum age for products in this category
- `max_age` - Maximum age for products in this category

**clicks table:**
- `sub_id` - Tracking ID for commission attribution (format: user_id_list_id)

**sales table (new):**
- `sub_id` - Tracking ID from affiliate link
- `order_id` - Bol.com order ID
- `product_id` - Bol.com product ID
- `quantity` - Number of items
- `commission` - Commission amount in EUR
- `revenue_excl_vat` - Order revenue
- `status` - Commission status (pending, approved, rejected)
- `user_id` - List owner user ID
- `list_id` - List ID

**list_products table:**
- `is_group_gift` - Enable group contributions (0 or 1)
- `target_amount` - Funding goal in EUR
- `section_id` - Reference to list_sections table

**contributions table (new):**
- `list_product_id` - Reference to list_products
- `contributor_name` - Name of contributor
- `contributor_email` - Optional email for notifications
- `amount` - Contribution amount in EUR
- `message` - Optional message from contributor
- `is_anonymous` - Hide contributor name (0 or 1)
- `status` - Contribution status (pending, completed, refunded)

**list_collaborators table (new):**
- `list_id` - Reference to lists
- `user_id` - Collaborator user ID
- `role` - Collaborator role (owner, editor)
- `invited_by` - User who sent invitation
- `invited_at` - Invitation timestamp
- `accepted_at` - Acceptance timestamp

**list_invitations table (new):**
- `list_id` - Reference to lists
- `inviter_id` - User sending invitation
- `invitee_email` - Email of invited user
- `invitee_id` - User ID once accepted
- `token` - Unique invitation token
- `status` - Invitation status (pending, accepted, rejected, expired)
- `expires_at` - Expiration date (default 7 days)

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

## Automated Reminder System Setup

### Prerequisites
- Email configuration in `.env`
- Cron job access on your server

### Configure Email Settings

In your `.env` file, set up email:
```
email.fromEmail = noreply@lijst.je
email.fromName = Lijst.je
email.SMTPHost = smtp.gmail.com
email.SMTPUser = your-email@gmail.com
email.SMTPPass = your-password
email.SMTPPort = 587
email.SMTPCrypto = tls
```

### Set Up Cron Job

Add this to your crontab to send reminders daily at 9:00 AM:

```bash
# Open crontab
crontab -e

# Add this line (adjust path to your project)
0 9 * * * cd /path/to/project && php spark reminders:send
```

**Alternative: Manual Testing**
```bash
# Test reminder system manually
php spark reminders:send
```

### How It Works

1. **User creates a list** with event date (e.g., Birthday: Feb 19, 2025)
2. **User enables reminders** with intervals (30, 14, 7 days before)
3. **System generates reminder records** for all collaborators
4. **Cron job runs daily** and checks for pending reminders
5. **Emails are sent** X days before the event
6. **Status tracked** (pending → sent/failed)

### Example Flow

```
List: "John's Birthday"
Event Date: February 19, 2025
Collaborators: alice@email.com, bob@email.com
Reminder Intervals: 30,14,7

Reminders Sent:
- January 20, 2025 (30 days before) → Alice & Bob
- February 5, 2025 (14 days before) → Alice & Bob  
- February 12, 2025 (7 days before) → Alice & Bob
```

## Production Deployment

1. Set `CI_ENVIRONMENT = production` in `.env`
2. Disable debug mode
3. Enable CSRF protection in `app/Config/Filters.php`
4. Set proper file permissions (755 for directories, 644 for files)
5. Configure SSL certificate
6. Set up proper backup system
7. **Configure cron jobs:**
   - Daily reminder emails: `0 9 * * * php spark reminders:send`
   - Commission sync: As per existing schedule
8. Use production database credentials
9. Enable caching for better performance
10. Configure production email SMTP settings

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

## Advanced Features Documentation

### Gender & Personalization System

**User Profile Management:**
- Optional gender field (male, female, other)
- Automatic age calculation from date of birth
- Profile editing at `/user/profile`

**Personalized Product Suggestions:**
- Suggestions based on user age and gender
- Dutch search terms: "cadeaus voor {age} jarige {gender}"
- Fallback to popular products if no personalization data
- Displayed in list editor above category suggestions

**Advanced Product Filters:**
- Sort options: Relevance, Price (Low-High, High-Low), Popularity, Rating
- Category filtering with product counts
- Price range filtering (min-max)
- Dynamic filter UI populated from API response

### Sales & Commission System

**Commission Attribution:**
- SubId format: `user_id_list_id` (e.g., `10_45`)
- Automatically appended to all affiliate links
- Tracked through entire customer journey
- Stored in clicks table for reference

**Sales Data Storage:**
- Order ID from Bol.com
- Commission amount in EUR
- Product information
- Commission status tracking
- User and list attribution

**Commission Status Values:**
- `pending` - Reported by Bol.com, awaiting approval
- `approved` - Commission confirmed and ready for payout
- `rejected` - Commission rejected (returns, fraud, etc.)

**Scheduled Report Fetching:**
- Daily automated fetching via cron job
- Manual fetching with custom date ranges
- Dry-run mode for testing
- Duplicate prevention (orderExists check)
- Comprehensive error logging

### Drawing/Lottery System (Loten Trekken)

**Event Management:**
- Create drawing events with title and description
- Set event date
- Track event status (pending, drawn, completed)

**Participant Management:**
- Invite users to participate
- Track participant status (pending, accepted, declined)
- Assign wish lists to participants
- Random lot drawing

**Features:**
- Email notifications for invitations
- Participant response tracking
- Random assignment of participants
- Wish list viewing for assigned person

### Dashboard Analytics

**User Dashboard:**
- Total lists, clicks, and sales count
- Commission statistics (approved, pending, rejected)
- Total earned commissions
- Sales history table
- Click history

**Admin Dashboard:**
- Global sales statistics
- Top users by commission
- Complete sales history with user attribution
- Commission status breakdown
- Sales by user ranking

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check database credentials in `.env`
- Ensure database exists
- Try browser-based migration runners if CLI unavailable

### 404 Errors
- Check `.htaccess` is present in `public/`
- Verify `mod_rewrite` is enabled in Apache
- Ensure `app.baseURL` is correct in `.env`

### Bol.com API Not Working
- Verify API credentials are correct in `.env`
- Check if credentials are properly set
- API may not work without valid credentials (will show friendly error)
- Verify OAuth token generation is working

### Permission Denied
- Ensure `writable/` directory has write permissions
- Check file ownership on Linux/Mac
- Verify `public/` folder is accessible

### Commission Reports Not Fetching
- Verify cron job is configured correctly
- Check application logs for API errors
- Ensure Bol.com API credentials are valid
- Verify date format is YYYY-MM-DD
- Check if orders have been placed with affiliate links

### Personalized Suggestions Not Showing
- Verify user has date_of_birth set
- Check gender field is populated (optional but recommended)
- Verify Bol.com API credentials are valid
- Check application logs for API errors

### Gender Field Not Appearing
- Run migration: `php spark migrate`
- Or use browser migration runner: `/run_migration_gender.php`
- Clear browser cache if field still not visible

## Changelog

### Version 2.0.0 (Major Update)
- Gender field and personalization system
- Personalized product suggestions based on age/gender
- Advanced product filters (sort, category, price)
- Bol.com Affiliate Reporting API v2 integration
- Commission tracking and sales management
- Scheduled report fetching with cron jobs
- Sales dashboard for users and admins
- Drawing/Lottery system (Loten Trekken)
- User profile management
- Age-based product recommendations
- Commission status tracking

### Version 1.0.0 (Initial Release)
- Complete user authentication system
- List creation and management
- Bol.com Marketing Catalog API v1 integration
- Admin dashboard
- Click tracking with subId support
- Analytics
- Category management with age restrictions
- Responsive design
- Drawing event creation and management
