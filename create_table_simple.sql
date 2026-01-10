-- Simple version without foreign key constraint first, then add it
-- Run these commands one by one:

-- Step 1: Create the table without foreign key
CREATE TABLE IF NOT EXISTS `post_report_reasons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fk_report_id` bigint unsigned NOT NULL,
  `reason` enum('spam','harassment','hate','violence','inappropriate','false','other') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_report_id` (`fk_report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 2: Check what type report_id actually is
-- DESCRIBE post_reports;

-- Step 3: Add foreign key (adjust the type if needed based on step 2)
-- If report_id is bigint unsigned, use this:
ALTER TABLE `post_report_reasons` 
ADD CONSTRAINT `post_report_reasons_fk_report_id_foreign` 
FOREIGN KEY (`fk_report_id`) REFERENCES `post_reports` (`report_id`) ON DELETE CASCADE;

-- If report_id is int unsigned, first drop the table and recreate with int:
-- DROP TABLE IF EXISTS `post_report_reasons`;
-- CREATE TABLE `post_report_reasons` (
--   `id` bigint unsigned NOT NULL AUTO_INCREMENT,
--   `fk_report_id` int unsigned NOT NULL,
--   `reason` enum('spam','harassment','hate','violence','inappropriate','false','other') NOT NULL,
--   `created_at` timestamp NULL DEFAULT NULL,
--   `updated_at` timestamp NULL DEFAULT NULL,
--   PRIMARY KEY (`id`),
--   KEY `fk_report_id` (`fk_report_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- 
-- ALTER TABLE `post_report_reasons` 
-- ADD CONSTRAINT `post_report_reasons_fk_report_id_foreign` 
-- FOREIGN KEY (`fk_report_id`) REFERENCES `post_reports` (`report_id`) ON DELETE CASCADE;






