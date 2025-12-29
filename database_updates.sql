-- Wishlist Gift Reservation Feature Database Updates
-- Run these SQL statements manually if migrations fail

-- Add is_crossable column to lists table
ALTER TABLE `lists` 
ADD COLUMN `is_crossable` TINYINT(1) NOT NULL DEFAULT 1 
AFTER `is_featured`;

-- Add claimed tracking columns to list_products table
ALTER TABLE `list_products` 
ADD COLUMN `claimed_at` DATETIME NULL 
AFTER `custom_note`;

ALTER TABLE `list_products` 
ADD COLUMN `claimed_by_subid` VARCHAR(100) NULL 
AFTER `claimed_at`;

-- Add index for claimed_by_subid for faster lookups
ALTER TABLE `list_products` 
ADD INDEX `idx_claimed_by_subid` (`claimed_by_subid`);

-- Verify changes
SHOW COLUMNS FROM `lists` LIKE 'is_crossable';
SHOW COLUMNS FROM `list_products` LIKE 'claimed_at';
SHOW COLUMNS FROM `list_products` LIKE 'claimed_by_subid';
