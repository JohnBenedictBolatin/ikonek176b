-- Run this SQL command in your database to create the post_report_reasons table
-- First, check the actual type of report_id in post_reports table, then use the matching type below

-- Option 1: If report_id is bigint unsigned (most likely)
CREATE TABLE IF NOT EXISTS `post_report_reasons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fk_report_id` bigint unsigned NOT NULL,
  `reason` enum('spam','harassment','hate','violence','inappropriate','false','other') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_report_reasons_fk_report_id_foreign` (`fk_report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Then add the foreign key constraint separately
ALTER TABLE `post_report_reasons` 
ADD CONSTRAINT `post_report_reasons_fk_report_id_foreign` 
FOREIGN KEY (`fk_report_id`) REFERENCES `post_reports` (`report_id`) ON DELETE CASCADE;

-- Option 2: If report_id is int (if the above doesn't work, try this)
-- DROP TABLE IF EXISTS `post_report_reasons`;
-- CREATE TABLE `post_report_reasons` (
--   `id` bigint unsigned NOT NULL AUTO_INCREMENT,
--   `fk_report_id` int unsigned NOT NULL,
--   `reason` enum('spam','harassment','hate','violence','inappropriate','false','other') NOT NULL,
--   `created_at` timestamp NULL DEFAULT NULL,
--   `updated_at` timestamp NULL DEFAULT NULL,
--   PRIMARY KEY (`id`),
--   KEY `post_report_reasons_fk_report_id_foreign` (`fk_report_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- 
-- ALTER TABLE `post_report_reasons` 
-- ADD CONSTRAINT `post_report_reasons_fk_report_id_foreign` 
-- FOREIGN KEY (`fk_report_id`) REFERENCES `post_reports` (`report_id`) ON DELETE CASCADE;








