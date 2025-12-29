# Wishlist Gift Reservation Feature

## Overview
This feature allows list creators to enable purchase tracking on their lists, so visitors can mark items as "purchased" to prevent duplicate gifts. It integrates with Bol.com's affiliate tracking system and provides both automatic and manual claiming mechanisms.

## Features Implemented

### 1. Database Schema
- **`lists.is_crossable`**: Boolean flag (default: 1) to enable/disable purchase marking per list
- **`list_products.claimed_at`**: Timestamp when item was marked as purchased
- **`list_products.claimed_by_subid`**: Anonymous tracking ID for the purchase

### 2. List Creation/Editing
- Checkbox option: "Sta toe dat items als gekocht gemarkeerd worden"
- Default: checked (enabled)
- Appears in both create and edit list forms
- Located in: `app/Views/dashboard/create_list.php` and `app/Views/dashboard/edit_list.php`

### 3. Public List View Enhancements
- Visual indicators for claimed items:
  - Reduced opacity (60%)
  - Diagonal stripe pattern overlay
  - Green "Gekocht" badge
  - Strikethrough on product title
- "Ik Kocht Dit" button for manual claiming (only visible if list is crossable)
- "Al Gekocht" disabled button for already-claimed items
- Located in: `app/Views/lists/view.php`

### 4. Affiliate Link Tracking
- SubId encoding format: `L{listId}P{listProductId}`
- Example: `L5P123` = List ID 5, List Product ID 123
- Automatically generated when clicking product links
- Passed to Bol.com for order tracking
- Located in: `app/Controllers/Tracker.php`

### 5. Manual Claiming Endpoint
- Route: `POST /list/claim`
- Validates list is crossable and item not already claimed
- Generates anonymous subId for tracking
- GDPR-compliant (no personal data stored)
- Located in: `app/Controllers/Lists.php::claimProduct()`

### 6. Automatic Claiming via Order Reports
- CLI Command: `php spark affiliate:process-orders`
- Polls Bol.com order reports (last 30 days)
- Decodes subId to identify list_product_id
- Auto-marks items as claimed when orders are confirmed
- Logs all claims for analytics
- Located in: `app/Commands/ProcessBolOrders.php`

## Installation

### Step 1: Database Migration
Run migrations:
```bash
php spark migrate
```

If migrations fail, manually execute:
```bash
mysql -u username -p database_name < database_updates.sql
```

### Step 2: Verify Installation
Check that new columns exist:
```sql
SHOW COLUMNS FROM lists LIKE 'is_crossable';
SHOW COLUMNS FROM list_products LIKE 'claimed_at';
SHOW COLUMNS FROM list_products LIKE 'claimed_by_subid';
```

### Step 3: Set Up Cron Job (Optional)
For automatic order processing, add to crontab:
```bash
# Run every hour
0 * * * * cd /path/to/project && php spark affiliate:process-orders >> /var/log/affiliate-orders.log 2>&1
```

## Usage

### For List Creators
1. Create or edit a list
2. Check/uncheck "Sta toe dat items als gekocht gemarkeerd worden"
3. Save the list
4. Share the list URL with friends/family

### For List Visitors
1. Browse a public list
2. Click "Product Bekijken" to view item (tracks via subId)
3. OR click "Ik Kocht Dit" to manually mark as purchased
4. Claimed items show with green badge and strikethrough

### For Administrators
1. Monitor claims via logs: `writable/logs/log-*.log`
2. Run order processing: `php spark affiliate:process-orders`
3. Check analytics in sales table for commission tracking

## Technical Details

### SubId Format
- **Manual claims**: `manual_{listId}_{listProductId}_{timestamp}`
- **Affiliate links**: `L{listId}P{listProductId}`
- Decoding: `Tracker::decodeSubId($subId)`

### Security & Privacy
- No personal information stored (GDPR-compliant)
- Anonymous tracking via subId only
- List owners cannot see who purchased items
- Visitors can claim without authentication

### API Integration
- Uses Bol.com Affiliate Reporting API v2
- Endpoint: `/order-report`
- Requires valid API credentials in `.env`
- Processes orders from last 30 days

## Models Updated
- `ListModel`: Added `is_crossable` to allowed fields
- `ListProductModel`: Added claim/unclaim methods and subId lookup

## Controllers Updated
- `Dashboard`: Handles `is_crossable` in create/edit
- `Lists`: Added `claimProduct()` endpoint
- `Tracker`: Added subId encoding/decoding

## Views Updated
- `dashboard/create_list.php`: Added checkbox
- `dashboard/edit_list.php`: Added checkbox
- `lists/view.php`: Added claimed state styling and buttons

## Testing

### Manual Testing
1. Create a list with crossable enabled
2. Add products to the list
3. Visit public list view
4. Click "Ik Kocht Dit" on an item
5. Verify item shows as claimed
6. Verify button changes to "Al Gekocht"

### Affiliate Link Testing
1. Click "Product Bekijken" on a list item
2. Check URL contains `&lp={list_product_id}`
3. Verify subId is generated in tracking

### Order Processing Testing
```bash
php spark affiliate:process-orders
```
Check output for processed orders and claimed items.

## Troubleshooting

### Items Not Auto-Claiming
- Verify Bol.com API credentials are correct
- Check order reports are being fetched
- Ensure subId format matches: `L{listId}P{listProductId}`
- Check logs for errors

### Manual Claim Not Working
- Verify list has `is_crossable = 1`
- Check item is not already claimed
- Inspect browser console for JavaScript errors
- Verify route `/list/claim` is accessible

### Database Issues
- Run `database_updates.sql` manually
- Check column types match migration
- Verify foreign keys are intact

## Future Enhancements
- Email notifications when items are claimed
- Unclaim functionality for list owners
- Claim history/analytics dashboard
- Multiple quantity support (claim 1 of 3)
- Expiration dates for claims
