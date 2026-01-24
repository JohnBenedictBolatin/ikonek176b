-- SQL script to add reason_type column to document_requests table
-- Run this in your MySQL/MariaDB database

-- Check if column exists and add it if it doesn't
ALTER TABLE `document_requests` 
ADD COLUMN `reason_type` VARCHAR(100) NULL 
AFTER `purpose`;

-- Verify the column was added
-- SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() 
-- AND TABLE_NAME = 'document_requests' 
-- AND COLUMN_NAME = 'reason_type';

















