-- Create table without foreign key first, then add it
-- This avoids the constraint error

-- Step 1: Create the table
CREATE TABLE IF NOT EXISTS `post_report_reasons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fk_report_id` bigint unsigned NOT NULL,
  `reason` enum('spam','harassment','hate','violence','inappropriate','false','other') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_report_id` (`fk_report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 2: Add the foreign key constraint
ALTER TABLE `post_report_reasons` 
ADD CONSTRAINT `post_report_reasons_fk_report_id_foreign` 
FOREIGN KEY (`fk_report_id`) REFERENCES `post_reports` (`report_id`) ON DELETE CASCADE;








