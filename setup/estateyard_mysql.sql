-- EstateYard MySQL Dump
-- Generated: 2026-06-02 09:17:57
-- 34 tables, MySQL 8.0+, InnoDB utf8mb4

SET NAMES utf8mb4;
SET foreign_key_checks = 0;

CREATE DATABASE IF NOT EXISTS \`estateyard\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE \`estateyard\`;

DROP TABLE IF EXISTS \`migrations\`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`users\`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'tenant',
  `phone` varchar(20) DEFAULT NULL,
  `county` varchar(100) DEFAULT NULL,
  `bio` text,
  `avatar` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `kyc_status` varchar(20) DEFAULT 'none',
  `license_number` varchar(100) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `api_token` varchar(80) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_api_token_unique` (`api_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`properties\`;
CREATE TABLE IF NOT EXISTS `properties` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` enum('house','apartment','land','commercial','airbnb','hotel','villa','office') NOT NULL,
  `listing_type` enum('sale','rent','airbnb','hotel','auction') NOT NULL,
  `status` enum('active','pending','sold','rented','draft','suspended') NOT NULL DEFAULT 'active',
  `price` decimal(15,2) NOT NULL,
  `price_period` enum('night','month','year') DEFAULT NULL,
  `bedrooms` tinyint unsigned DEFAULT NULL,
  `bathrooms` tinyint unsigned DEFAULT NULL,
  `area_sqft` decimal(10,2) DEFAULT NULL,
  `floors` tinyint unsigned DEFAULT NULL,
  `year_built` year DEFAULT NULL,
  `county` varchar(255) NOT NULL,
  `constituency` varchar(255) DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `amenities` json DEFAULT NULL,
  `images` json DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `view_count` int unsigned NOT NULL DEFAULT 0,
  `save_count` int unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `properties_slug_unique` (`slug`),
  KEY `properties_user_id_foreign` (`user_id`),
  CONSTRAINT `properties_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`property_documents\`;
CREATE TABLE IF NOT EXISTS `property_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `property_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `document_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `property_documents_property_id_foreign` (`property_id`),
  CONSTRAINT `property_documents_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`property_saves\`;
CREATE TABLE IF NOT EXISTS `property_saves` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `property_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `property_saves_user_id_property_id_unique` (`user_id`,`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`leases\`;
CREATE TABLE IF NOT EXISTS `leases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `property_id` bigint unsigned NOT NULL,
  `landlord_id` bigint unsigned NOT NULL,
  `tenant_id` bigint unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `monthly_rent` decimal(15,2) NOT NULL,
  `deposit` decimal(15,2) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leases_property_id_foreign` (`property_id`),
  CONSTRAINT `leases_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`rent_payments\`;
CREATE TABLE IF NOT EXISTS `rent_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lease_id` bigint unsigned NOT NULL,
  `landlord_id` bigint unsigned NOT NULL,
  `tenant_id` bigint unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `due_date` date NOT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `mpesa_code` varchar(50) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rent_payments_lease_id_foreign` (`lease_id`),
  CONSTRAINT `rent_payments_lease_id_foreign` FOREIGN KEY (`lease_id`) REFERENCES `leases` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`escrow_transactions\`;
CREATE TABLE IF NOT EXISTS `escrow_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `property_id` bigint unsigned NOT NULL,
  `buyer_id` bigint unsigned NOT NULL,
  `seller_id` bigint unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `status` enum('pending','funded','released','disputed','refunded') NOT NULL DEFAULT 'pending',
  `notes` text,
  `funded_at` timestamp NULL DEFAULT NULL,
  `released_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `escrow_property_id_foreign` (`property_id`),
  CONSTRAINT `escrow_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`auctions\`;
CREATE TABLE IF NOT EXISTS `auctions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `property_id` bigint unsigned NOT NULL,
  `auctioneer_id` bigint unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `starting_bid` decimal(15,2) NOT NULL,
  `reserve_price` decimal(15,2) DEFAULT NULL,
  `current_bid` decimal(15,2) DEFAULT NULL,
  `winner_id` bigint unsigned DEFAULT NULL,
  `status` enum('scheduled','live','ended','cancelled') NOT NULL DEFAULT 'scheduled',
  `starts_at` datetime NOT NULL,
  `ends_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auctions_property_id_foreign` (`property_id`),
  CONSTRAINT `auctions_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`bids\`;
CREATE TABLE IF NOT EXISTS `bids` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `auction_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `is_winning` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bids_auction_id_foreign` (`auction_id`),
  CONSTRAINT `bids_auction_id_foreign` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`referrals\`;
CREATE TABLE IF NOT EXISTS `referrals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `referrer_id` bigint unsigned NOT NULL,
  `referred_id` bigint unsigned DEFAULT NULL,
  `code` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `commission` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `referrals_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`referral_conversions\`;
CREATE TABLE IF NOT EXISTS `referral_conversions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `referral_id` bigint unsigned NOT NULL,
  `property_id` bigint unsigned DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`verifications\`;
CREATE TABLE IF NOT EXISTS `verifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `notes` text,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `verifications_user_id_foreign` (`user_id`),
  CONSTRAINT `verifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`verification_documents\`;
CREATE TABLE IF NOT EXISTS `verification_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `verification_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`inspections\`;
CREATE TABLE IF NOT EXISTS `inspections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `property_id` bigint unsigned NOT NULL,
  `inspector_id` bigint unsigned NOT NULL,
  `requested_by` bigint unsigned NOT NULL,
  `scheduled_at` datetime NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'scheduled',
  `report` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`maintenance_requests\`;
CREATE TABLE IF NOT EXISTS `maintenance_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `property_id` bigint unsigned NOT NULL,
  `lease_id` bigint unsigned DEFAULT NULL,
  `tenant_id` bigint unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`messages\`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint unsigned NOT NULL,
  `receiver_id` bigint unsigned NOT NULL,
  `property_id` bigint unsigned DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`notifications_log\`;
CREATE TABLE IF NOT EXISTS `notifications_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text,
  `data` json DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_log_user_id_foreign` (`user_id`),
  CONSTRAINT `notifications_log_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`developer_projects\`;
CREATE TABLE IF NOT EXISTS `developer_projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `developer_id` bigint unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `location` varchar(255) NOT NULL,
  `county` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'planning',
  `total_units` int unsigned DEFAULT NULL,
  `sold_units` int unsigned NOT NULL DEFAULT 0,
  `completion_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`valuations\`;
CREATE TABLE IF NOT EXISTS `valuations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `property_id` bigint unsigned NOT NULL,
  `valuer_id` bigint unsigned NOT NULL,
  `requested_by` bigint unsigned NOT NULL,
  `estimated_value` decimal(15,2) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `report` text,
  `valuation_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`surveys\`;
CREATE TABLE IF NOT EXISTS `surveys` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `property_id` bigint unsigned NOT NULL,
  `surveyor_id` bigint unsigned NOT NULL,
  `requested_by` bigint unsigned NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `report` text,
  `survey_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`hotel_rooms\`;
CREATE TABLE IF NOT EXISTS `hotel_rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `property_id` bigint unsigned NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `max_guests` tinyint unsigned NOT NULL DEFAULT 2,
  `amenities` json DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`bookings\`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `property_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `hotel_room_id` bigint unsigned DEFAULT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `guests` tinyint unsigned NOT NULL DEFAULT 1,
  `total_price` decimal(15,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `special_requests` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`property_availability\`;
CREATE TABLE IF NOT EXISTS `property_availability` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `property_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `price_override` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`property_pricing_rules\`;
CREATE TABLE IF NOT EXISTS `property_pricing_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `property_id` bigint unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `price_multiplier` decimal(5,2) NOT NULL DEFAULT 1.00,
  `min_nights` tinyint unsigned NOT NULL DEFAULT 1,
  `day_of_week` tinyint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`booking_reviews\`;
CREATE TABLE IF NOT EXISTS `booking_reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint unsigned NOT NULL,
  `reviewer_id` bigint unsigned NOT NULL,
  `rating` tinyint unsigned NOT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`sessions\`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`cache\`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`cache_locks\`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`password_reset_tokens\`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`jobs\`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`job_batches\`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`failed_jobs\`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`personal_access_tokens\`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for `migrations` (30 rows)
INSERT INTO \`migrations\` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_01_01_000010_add_role_fields_to_users_table', 1),
(5, '2024_01_01_000020_create_properties_table', 1),
(6, '2024_01_01_000021_create_property_documents_table', 1),
(7, '2024_01_01_000030_create_leases_table', 1),
(8, '2024_01_01_000031_create_rent_payments_table', 1),
(9, '2024_01_01_000040_create_escrow_transactions_table', 1),
(10, '2024_01_01_000050_create_auctions_table', 1),
(11, '2024_01_01_000051_create_bids_table', 1),
(12, '2024_01_01_000060_create_referrals_table', 1),
(13, '2024_01_01_000061_create_referral_conversions_table', 1),
(14, '2024_01_01_000070_create_verifications_table', 1),
(15, '2024_01_01_000071_create_verification_documents_table', 1),
(16, '2024_01_01_000080_create_inspections_table', 1),
(17, '2024_01_01_000081_create_maintenance_requests_table', 1),
(18, '2024_01_01_000090_create_messages_table', 1),
(19, '2024_01_01_000091_create_notifications_log_table', 1),
(20, '2024_01_01_000100_create_developer_projects_table', 1),
(21, '2024_01_01_000110_create_valuations_table', 1),
(22, '2024_01_01_000120_create_surveys_table', 1),
(23, '2024_01_01_000130_create_property_saves_table', 1),
(24, '2024_01_01_000140_add_api_token_to_users_table', 1),
(25, '2024_02_01_000009_create_hotel_rooms_table', 1),
(26, '2024_02_01_000010_create_bookings_table', 1),
(27, '2024_02_01_000011_create_property_availability_table', 1),
(28, '2024_02_01_000013_create_property_pricing_rules_table', 1),
(29, '2024_02_01_000014_create_booking_reviews_table', 1),
(30, '2026_05_31_063846_create_personal_access_tokens_table', 1);

-- Data for `users` (66 rows)
INSERT INTO \`users\` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`, `phone`, `avatar`, `is_verified`, `verification_tier`, `referral_code`, `referred_by`, `bio`, `is_active`, `api_token`) VALUES
(1, 'James Kamau', 'admin1@estateyard.co.ke', NULL, '$2y$12$mrjU9A04lYMv68bkgfi8W..AkMf2ndmCoq3TlWi5e8eQRwgkPqJr6', NULL, '2026-06-02 09:15:41', '2026-06-02 09:15:41', 'admin', '+254733604924', NULL, 1, 'elite', 'AQDBUJ', NULL, 'Experienced admin professional based in Nairobi, Kenya.', 1, NULL),
(2, 'Grace Wanjiku', 'admin2@estateyard.co.ke', NULL, '$2y$12$oktBE8CjvWsKmoVruuDCN.lpVgfONNrIiU///v.EN0RpOb7FuyJV6', NULL, '2026-06-02 09:15:41', '2026-06-02 09:15:41', 'admin', '+254767794189', NULL, 1, 'professional', 'HJQASY', NULL, 'Experienced admin professional based in Nairobi, Kenya.', 1, NULL),
(3, 'Peter Otieno', 'admin3@estateyard.co.ke', NULL, '$2y$12$4Rke.2P4.cJ7CAvS1bv7ZO2.So/wqVXMjn/h/EBenddtJBS4mB5sK', NULL, '2026-06-02 09:15:41', '2026-06-02 09:15:41', 'admin', '+254739188345', NULL, 1, 'professional', 'OP2DKR', NULL, 'Experienced admin professional based in Nairobi, Kenya.', 1, NULL),
(4, 'Mary Njeri', 'admin4@estateyard.co.ke', NULL, '$2y$12$ei1TIkIFXK5KeBobm9t.E.j5TUNJeApquQ.feX.fbCBviHZgDLXF.', NULL, '2026-06-02 09:15:42', '2026-06-02 09:15:42', 'admin', '+254769639297', NULL, 0, 'none', '7EWP9X', NULL, 'Experienced admin professional based in Nairobi, Kenya.', 1, NULL),
(5, 'John Mwangi', 'admin5@estateyard.co.ke', NULL, '$2y$12$I2DToXOnj3iizaREqYh.0eirZ8yVNg5hpoduQJqnSi2D3xn3ZeWXy', NULL, '2026-06-02 09:15:42', '2026-06-02 09:15:42', 'admin', '+254725075486', NULL, 0, 'none', 'DR6WIG', NULL, 'Experienced admin professional based in Nairobi, Kenya.', 1, NULL),
(6, 'Alice Achieng', 'landlord1@estateyard.co.ke', NULL, '$2y$12$yvi/BY1SG.3jhpBbKEcYG.996u8Pja7G9mDhvIj9gIHBZoshVpIee', NULL, '2026-06-02 09:15:42', '2026-06-02 09:15:42', 'landlord', '+254764875865', NULL, 1, 'elite', '5MBAHH', NULL, 'Experienced landlord professional based in Nairobi, Kenya.', 1, NULL),
(7, 'Samuel Kipchoge', 'landlord2@estateyard.co.ke', NULL, '$2y$12$p3VUUEW/K33bqq42bpwLMO7jQhMi/tHc5eTtZvcDx/DHsxeZgSiFG', NULL, '2026-06-02 09:15:42', '2026-06-02 09:15:42', 'landlord', '+254749852531', NULL, 1, 'professional', '8YPEPC', NULL, 'Experienced landlord professional based in Nairobi, Kenya.', 1, NULL),
(8, 'Ruth Wangari', 'landlord3@estateyard.co.ke', NULL, '$2y$12$ZjWqZwYos30DNvVD2l34ZezaScJyr.p2HqpbDPHQD18QiJRiE8ofy', NULL, '2026-06-02 09:15:43', '2026-06-02 09:15:43', 'landlord', '+254720697142', NULL, 1, 'professional', 'NSMHXW', NULL, 'Experienced landlord professional based in Nairobi, Kenya.', 1, NULL),
(9, 'David Odhiambo', 'landlord4@estateyard.co.ke', NULL, '$2y$12$Jg9/gPoOlwpRKrLGKP9rauoAaeWL84okyd2vF0WAsz0M9mCkeVMZm', NULL, '2026-06-02 09:15:43', '2026-06-02 09:15:43', 'landlord', '+254750954354', NULL, 0, 'none', 'W5WHWR', NULL, 'Experienced landlord professional based in Nairobi, Kenya.', 1, NULL),
(10, 'Faith Chege', 'landlord5@estateyard.co.ke', NULL, '$2y$12$Shemi8oyvpEl7T1W2ReGTubaGHVChJYv8pCGkqQ91Hg7zbQfedXau', NULL, '2026-06-02 09:15:43', '2026-06-02 09:15:43', 'landlord', '+254735971305', NULL, 0, 'none', '304HDL', NULL, 'Experienced landlord professional based in Nairobi, Kenya.', 1, NULL),
(11, 'Michael Njoroge', 'broker_licensed1@estateyard.co.ke', NULL, '$2y$12$nctoGIholgSxgfbzjnEUae7p9YT.4cPIVmXnYLbeeoeEEhkAcj.kW', NULL, '2026-06-02 09:15:43', '2026-06-02 09:15:43', 'broker_licensed', '+254740281766', NULL, 1, 'elite', 'RZ6548', NULL, 'Experienced broker_licensed professional based in Nairobi, Kenya.', 1, NULL),
(12, 'Esther Mutua', 'broker_licensed2@estateyard.co.ke', NULL, '$2y$12$dKUgj6tsG6/DbHdAYuRSwu3.SN3ctQR1MsHws1wbeHGpbtmJ0SLKy', NULL, '2026-06-02 09:15:44', '2026-06-02 09:15:44', 'broker_licensed', '+254797667174', NULL, 1, 'professional', 'ILSHWE', NULL, 'Experienced broker_licensed professional based in Nairobi, Kenya.', 1, NULL),
(13, 'Daniel Kimani', 'broker_licensed3@estateyard.co.ke', NULL, '$2y$12$CT5T2jQrezMVpd8SDokHfeRGkdZvjzcRujvSQLngx5Y.JYSLitnEy', NULL, '2026-06-02 09:15:44', '2026-06-02 09:15:44', 'broker_licensed', '+254748604486', NULL, 1, 'professional', 'EYT1TG', NULL, 'Experienced broker_licensed professional based in Nairobi, Kenya.', 1, NULL),
(14, 'Priscilla Waweru', 'broker_licensed4@estateyard.co.ke', NULL, '$2y$12$LQB10vrb3PzonoILMp7u9.sNVM7kKB5LwhZFErqEiEYAVJ3.HXvYy', NULL, '2026-06-02 09:15:44', '2026-06-02 09:15:44', 'broker_licensed', '+254734736083', NULL, 0, 'none', '5NJWZG', NULL, 'Experienced broker_licensed professional based in Nairobi, Kenya.', 1, NULL),
(15, 'Joseph Mugo', 'broker_licensed5@estateyard.co.ke', NULL, '$2y$12$MWVMGFwIYmoSpnSG3vBMFuM9umjI9s7fwOC8/TZNvaMElBPGG/5ju', NULL, '2026-06-02 09:15:44', '2026-06-02 09:15:44', 'broker_licensed', '+254713237697', NULL, 0, 'none', 'L5DHSM', NULL, 'Experienced broker_licensed professional based in Nairobi, Kenya.', 1, NULL),
(16, 'Lydia Karanja', 'broker_unlicensed1@estateyard.co.ke', NULL, '$2y$12$OYsclUxGcFFrzTF2xJDBT.kkU5yjmCo3L4Y39cVMgXjQ85YmO.Ny6', NULL, '2026-06-02 09:15:45', '2026-06-02 09:15:45', 'broker_unlicensed', '+254788015925', NULL, 1, 'elite', '7IZAXU', NULL, 'Experienced broker_unlicensed professional based in Nairobi, Kenya.', 1, NULL),
(17, 'Stephen Gitau', 'broker_unlicensed2@estateyard.co.ke', NULL, '$2y$12$uZV6fYXJuvK6SFhHRX5iT.vy9R4FJnnwl7qzcT5JtmVzpuA1UvXVy', NULL, '2026-06-02 09:15:45', '2026-06-02 09:15:45', 'broker_unlicensed', '+254778306850', NULL, 1, 'professional', 'A5NEKK', NULL, 'Experienced broker_unlicensed professional based in Nairobi, Kenya.', 1, NULL),
(18, 'Beatrice Mbugua', 'broker_unlicensed3@estateyard.co.ke', NULL, '$2y$12$7lpKDpZ3BBiUoYNnorJjPe5aGIaNaSFP3Qz4Y7APDYN73o7sKTsba', NULL, '2026-06-02 09:15:45', '2026-06-02 09:15:45', 'broker_unlicensed', '+254710383735', NULL, 1, 'professional', '7KX4YC', NULL, 'Experienced broker_unlicensed professional based in Nairobi, Kenya.', 1, NULL),
(19, 'Charles Njuguna', 'broker_unlicensed4@estateyard.co.ke', NULL, '$2y$12$uB7Zjb2hAn/6spTSASqcaepiU0hCBHEjg63puJnz1spVk2.lVH8aS', NULL, '2026-06-02 09:15:45', '2026-06-02 09:15:45', 'broker_unlicensed', '+254719250802', NULL, 0, 'none', 'PYPL6K', NULL, 'Experienced broker_unlicensed professional based in Nairobi, Kenya.', 1, NULL),
(20, 'Mercy Maina', 'broker_unlicensed5@estateyard.co.ke', NULL, '$2y$12$BIsiI7fNW313maIqWxaSLO4NecJY5FjbNhPbEUtjICnoUES2t2OSC', NULL, '2026-06-02 09:15:46', '2026-06-02 09:15:46', 'broker_unlicensed', '+254761702452', NULL, 0, 'none', 'VF0KNW', NULL, 'Experienced broker_unlicensed professional based in Nairobi, Kenya.', 1, NULL),
(21, 'Andrew Wakiumu', 'tenant1@estateyard.co.ke', NULL, '$2y$12$Lu0q75OcY3P2M.Cvz7fKzeF4NFEUSxpdKM9iAP41vrNmrTuCs9LEe', NULL, '2026-06-02 09:15:46', '2026-06-02 09:15:46', 'tenant', '+254776908311', NULL, 1, 'elite', 'CLEGCY', NULL, 'Experienced tenant professional based in Nairobi, Kenya.', 1, NULL),
(22, 'Jane Wairimu', 'tenant2@estateyard.co.ke', NULL, '$2y$12$uOw8gUOJlBh3ObWE3GEpouGrF6S78SBt2jwASgq.SZnLLtmwJOsru', NULL, '2026-06-02 09:15:46', '2026-06-02 09:15:46', 'tenant', '+254755933079', NULL, 1, 'professional', 'LUKLIO', NULL, 'Experienced tenant professional based in Nairobi, Kenya.', 1, NULL),
(23, 'Patrick Ndungu', 'tenant3@estateyard.co.ke', NULL, '$2y$12$FbUiRUtn.QTypC6BBjNomOx6Ik74FxPzdWUYBVlqFkMnyMNNIXG6.', NULL, '2026-06-02 09:15:46', '2026-06-02 09:15:46', 'tenant', '+254799218844', NULL, 1, 'professional', 'SRPN5I', NULL, 'Experienced tenant professional based in Nairobi, Kenya.', 1, NULL),
(24, 'Susan Mureithi', 'tenant4@estateyard.co.ke', NULL, '$2y$12$/C9B6u/cnO3Mzcubtjkpzu9eoQEWZvkTPdNpAPfXZ40We0kVC1zY.', NULL, '2026-06-02 09:15:47', '2026-06-02 09:15:47', 'tenant', '+254713520446', NULL, 0, 'none', 'WBEDPA', NULL, 'Experienced tenant professional based in Nairobi, Kenya.', 1, NULL),
(25, 'George Kibet', 'tenant5@estateyard.co.ke', NULL, '$2y$12$e2T6i67qFGTSsryHaF7JQOuURcazfjQqEMN/P2E9/XtV.jitCB34e', NULL, '2026-06-02 09:15:47', '2026-06-02 09:15:47', 'tenant', '+254765322030', NULL, 0, 'none', 'B6M7OR', NULL, 'Experienced tenant professional based in Nairobi, Kenya.', 1, NULL),
(26, 'Agnes Chebet', 'developer1@estateyard.co.ke', NULL, '$2y$12$HobPDh/psKGXSsMk23hRvuqNS44rC.ydGSzPq1gpBNVfOqSTFPKce', NULL, '2026-06-02 09:15:47', '2026-06-02 09:15:47', 'developer', '+254728748773', NULL, 1, 'elite', 'HU4X3B', NULL, 'Experienced developer professional based in Nairobi, Kenya.', 1, NULL),
(27, 'Francis Gacheru', 'developer2@estateyard.co.ke', NULL, '$2y$12$0F2BeDmyb8y8GD7inOxSZ.fztuIn5kTJrC/1ObfplfKG5edGwFSp.', NULL, '2026-06-02 09:15:47', '2026-06-02 09:15:47', 'developer', '+254753849800', NULL, 1, 'professional', 'QLHLN8', NULL, 'Experienced developer professional based in Nairobi, Kenya.', 1, NULL),
(28, 'Hannah Muthoni', 'developer3@estateyard.co.ke', NULL, '$2y$12$t4gOoZPvVyjUh7zt8sXrAe6AcOSzQjhcd6.deU/KzF1mlsdCHoXqy', NULL, '2026-06-02 09:15:47', '2026-06-02 09:15:47', 'developer', '+254781835031', NULL, 1, 'professional', 'JVEFLL', NULL, 'Experienced developer professional based in Nairobi, Kenya.', 1, NULL),
(29, 'Robert Ngugi', 'developer4@estateyard.co.ke', NULL, '$2y$12$dDxoTB8eASGhPuNGOI1QhuLsu4l6i0sWxtjp97A5HeXeOzjq.49B.', NULL, '2026-06-02 09:15:48', '2026-06-02 09:15:48', 'developer', '+254759417631', NULL, 0, 'none', 'DEHCB3', NULL, 'Experienced developer professional based in Nairobi, Kenya.', 1, NULL),
(30, 'Catherine Waruguru', 'developer5@estateyard.co.ke', NULL, '$2y$12$91e0nZKHAazo2Oi2qiAMYO387CTKYgHqjy66IsnY7qCKXRs7bEGoO', NULL, '2026-06-02 09:15:48', '2026-06-02 09:15:48', 'developer', '+254727968470', NULL, 0, 'none', 'MGKEH0', NULL, 'Experienced developer professional based in Nairobi, Kenya.', 1, NULL),
(31, 'Eric Muriuki', 'valuer1@estateyard.co.ke', NULL, '$2y$12$4HIwsoCwSB0Q29QQYFqtcO0U7p95Mi1zlsp9MPlZ8LZW.Sq7OCPcu', NULL, '2026-06-02 09:15:48', '2026-06-02 09:15:48', 'valuer', '+254769060803', NULL, 1, 'elite', 'X70WXA', NULL, 'Experienced valuer professional based in Nairobi, Kenya.', 1, NULL),
(32, 'Eunice Chepkoech', 'valuer2@estateyard.co.ke', NULL, '$2y$12$cc2MkhvwuE8B.5WGZq0fxuXNhTFtbuQtVTnRaTfUSiCmh5GI26zRa', NULL, '2026-06-02 09:15:48', '2026-06-02 09:15:48', 'valuer', '+254770345705', NULL, 1, 'professional', 'GZ1DBS', NULL, 'Experienced valuer professional based in Nairobi, Kenya.', 1, NULL),
(33, 'Vincent Karimi', 'valuer3@estateyard.co.ke', NULL, '$2y$12$fHjTeWySIqh8ZSTIwDmUvOaZJs1qVo8Rx/7b8ACjS9UNvQV7SSD4C', NULL, '2026-06-02 09:15:49', '2026-06-02 09:15:49', 'valuer', '+254776574265', NULL, 1, 'professional', 'KXANPN', NULL, 'Experienced valuer professional based in Nairobi, Kenya.', 1, NULL),
(34, 'Tabitha Waithira', 'valuer4@estateyard.co.ke', NULL, '$2y$12$S8rUqITGPabirRWgLqe.LOQKpdcP9ZbhRzjXHFr3Fg0n7n1M8QN2K', NULL, '2026-06-02 09:15:49', '2026-06-02 09:15:49', 'valuer', '+254792505658', NULL, 0, 'none', 'I5PFHE', NULL, 'Experienced valuer professional based in Nairobi, Kenya.', 1, NULL),
(35, 'Kevin Njeru', 'valuer5@estateyard.co.ke', NULL, '$2y$12$tzo/Li7jnyfhtgR/Pb6JAO2qzuJEf7sUg10rcYAoow2zuFp.oukqK', NULL, '2026-06-02 09:15:49', '2026-06-02 09:15:49', 'valuer', '+254730424210', NULL, 0, 'none', 'MNIJWA', NULL, 'Experienced valuer professional based in Nairobi, Kenya.', 1, NULL),
(36, 'Rose Auma', 'surveyor1@estateyard.co.ke', NULL, '$2y$12$k.cki0L9v1Qy2/a.svUute6SHldiume7y7qZjgzkAXcuLSnqS5CLC', NULL, '2026-06-02 09:15:49', '2026-06-02 09:15:49', 'surveyor', '+254799668363', NULL, 1, 'elite', 'HAPAT2', NULL, 'Experienced surveyor professional based in Nairobi, Kenya.', 1, NULL),
(37, 'Brian Kamande', 'surveyor2@estateyard.co.ke', NULL, '$2y$12$oHJQfQJo57eK/clhzwGVEOEFpcH/WpOrKFF5N5AC2No5MFJx11PHa', NULL, '2026-06-02 09:15:50', '2026-06-02 09:15:50', 'surveyor', '+254722537970', NULL, 1, 'professional', 'XXDLN7', NULL, 'Experienced surveyor professional based in Nairobi, Kenya.', 1, NULL),
(38, 'Irene Waceke', 'surveyor3@estateyard.co.ke', NULL, '$2y$12$nEhfz1oiq.MF3mKCYLmvJuqgKdAF85Bl5HwWZKAJlhC/SRr9M2Xq.', NULL, '2026-06-02 09:15:50', '2026-06-02 09:15:50', 'surveyor', '+254759570862', NULL, 1, 'professional', 'WBNEZV', NULL, 'Experienced surveyor professional based in Nairobi, Kenya.', 1, NULL),
(39, 'Paul Kiragu', 'surveyor4@estateyard.co.ke', NULL, '$2y$12$IOxPXpJARKzxxxz5Dw.k3OZH7irvbrOTtcPlp9lhRpvXjy1StET5G', NULL, '2026-06-02 09:15:50', '2026-06-02 09:15:50', 'surveyor', '+254724488465', NULL, 0, 'none', 'MRGEJI', NULL, 'Experienced surveyor professional based in Nairobi, Kenya.', 1, NULL),
(40, 'Lucy Nyambura', 'surveyor5@estateyard.co.ke', NULL, '$2y$12$8DeX6wmS6c2m0beo3wltpuNxcBUWfEi9zNmr84SY8OhAoq4i/aemu', NULL, '2026-06-02 09:15:50', '2026-06-02 09:15:50', 'surveyor', '+254793032741', NULL, 0, 'none', '43V0S3', NULL, 'Experienced surveyor professional based in Nairobi, Kenya.', 1, NULL),
(41, 'Simon Ochieng', 'auctioneer1@estateyard.co.ke', NULL, '$2y$12$xKuyOfYIx3aOaPiioQ6z2.rOOhrB3EQj96N2AEKA53biatIbjf8dO', NULL, '2026-06-02 09:15:51', '2026-06-02 09:15:51', 'auctioneer', '+254710082338', NULL, 1, 'elite', 'YT9ENS', NULL, 'Experienced auctioneer professional based in Nairobi, Kenya.', 1, NULL),
(42, 'Doris Wekesa', 'auctioneer2@estateyard.co.ke', NULL, '$2y$12$3/w6Luc7iipyV25gTxHUJ.E.LkKWsgjWgm8ppG3XIeKWoNPB8bRbG', NULL, '2026-06-02 09:15:51', '2026-06-02 09:15:51', 'auctioneer', '+254757530056', NULL, 1, 'professional', 'J5TBML', NULL, 'Experienced auctioneer professional based in Nairobi, Kenya.', 1, NULL),
(43, 'Timothy Gichuki', 'auctioneer3@estateyard.co.ke', NULL, '$2y$12$OnOv56LGYLzlcnMafQm9fu3lexZRnsJrh30lmzo8Nc2quhaMdyshG', NULL, '2026-06-02 09:15:51', '2026-06-02 09:15:51', 'auctioneer', '+254722227219', NULL, 1, 'professional', 'SWR0AW', NULL, 'Experienced auctioneer professional based in Nairobi, Kenya.', 1, NULL),
(44, 'Winnie Mwende', 'auctioneer4@estateyard.co.ke', NULL, '$2y$12$J.JYVcmlgwxoVgZb/Yya4Osju9rOnntwAcP2Ndmp1QZwKjp0sjoG.', NULL, '2026-06-02 09:15:51', '2026-06-02 09:15:51', 'auctioneer', '+254787228690', NULL, 0, 'none', 'UCP5HH', NULL, 'Experienced auctioneer professional based in Nairobi, Kenya.', 1, NULL),
(45, 'Moses Kirui', 'auctioneer5@estateyard.co.ke', NULL, '$2y$12$QbhUTGzZjzPskXNWhQYUcOt9FmRZpjH8E.GFOdp2paneYY8K5oSTW', NULL, '2026-06-02 09:15:52', '2026-06-02 09:15:52', 'auctioneer', '+254772118914', NULL, 0, 'none', 'SKZJWD', NULL, 'Experienced auctioneer professional based in Nairobi, Kenya.', 1, NULL),
(46, 'Gladys Adhiambo', 'investor1@estateyard.co.ke', NULL, '$2y$12$CT.m/sqAX14BeeWDR8x9YOkcd5/.YSz98kN7bdahUV/dAKsnIExXS', NULL, '2026-06-02 09:15:52', '2026-06-02 09:15:52', 'investor', '+254736710331', NULL, 1, 'elite', 'BQLFUG', NULL, 'Experienced investor professional based in Nairobi, Kenya.', 1, NULL),
(47, 'Isaac Muigai', 'investor2@estateyard.co.ke', NULL, '$2y$12$nXVqNrvrd.KnhOHOMVArV.dNrvHsgn/CLoqY61BFrdVR.ndqglY16', NULL, '2026-06-02 09:15:52', '2026-06-02 09:15:52', 'investor', '+254768049920', NULL, 1, 'professional', 'MWVRS3', NULL, 'Experienced investor professional based in Nairobi, Kenya.', 1, NULL),
(48, 'Florence Wanjiru', 'investor3@estateyard.co.ke', NULL, '$2y$12$R.KsEopP.yoz/k4heehhA.0dixSuR.3A3zqhjkXS7.3oV3jwcugvC', NULL, '2026-06-02 09:15:52', '2026-06-02 09:15:52', 'investor', '+254743781096', NULL, 1, 'professional', '8P62FN', NULL, 'Experienced investor professional based in Nairobi, Kenya.', 1, NULL),
(49, 'Solomon Maina', 'investor4@estateyard.co.ke', NULL, '$2y$12$oCPKUmIhQEvehQwrO/RX5OCKjdQxDHnKAE84K.nKrN4qQiq5gh1zi', NULL, '2026-06-02 09:15:52', '2026-06-02 09:15:52', 'investor', '+254720604541', NULL, 0, 'none', 'FHVCP2', NULL, 'Experienced investor professional based in Nairobi, Kenya.', 1, NULL),
(50, 'Helen Chepchumba', 'investor5@estateyard.co.ke', NULL, '$2y$12$z8gJNATJZddUZub1OHLoSu/XAWoEJFE1M.rFPms9GoFwD..v4gQBe', NULL, '2026-06-02 09:15:53', '2026-06-02 09:15:53', 'investor', '+254752368198', NULL, 0, 'none', 'Y8S9XL', NULL, 'Experienced investor professional based in Nairobi, Kenya.', 1, NULL);
INSERT INTO \`users\` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`, `phone`, `avatar`, `is_verified`, `verification_tier`, `referral_code`, `referred_by`, `bio`, `is_active`, `api_token`) VALUES
(51, 'Philip Wainaina', 'corporate1@estateyard.co.ke', NULL, '$2y$12$hFiOWJRLZ0QIThcREHLCXeJPPTi.rStwfbxZYI1lmPOlwO5p6WJZy', NULL, '2026-06-02 09:15:53', '2026-06-02 09:15:53', 'corporate', '+254792963381', NULL, 1, 'elite', 'WJ12EN', NULL, 'Experienced corporate professional based in Nairobi, Kenya.', 1, NULL),
(52, 'Judith Kananu', 'corporate2@estateyard.co.ke', NULL, '$2y$12$xg.U1EqYvTUsj5vLSj29r.B34mjTP2qibDZv.1pbiwGEVDxkRQK0C', NULL, '2026-06-02 09:15:53', '2026-06-02 09:15:53', 'corporate', '+254719121333', NULL, 1, 'professional', 'Q1IOJF', NULL, 'Experienced corporate professional based in Nairobi, Kenya.', 1, NULL),
(53, 'Geoffrey Mutisya', 'corporate3@estateyard.co.ke', NULL, '$2y$12$madpcfLNyhol/Matw8acmOvWY0LECsjs2h6OWytYd0xOpKf8rtVS6', NULL, '2026-06-02 09:15:53', '2026-06-02 09:15:53', 'corporate', '+254797431681', NULL, 1, 'professional', '05VPXR', NULL, 'Experienced corporate professional based in Nairobi, Kenya.', 1, NULL),
(54, 'Carolyne Nduta', 'corporate4@estateyard.co.ke', NULL, '$2y$12$0AEj8DDCuWPmURyT.ny96.sjMuebK17XFOyl9opFyrRdUHOja1vuy', NULL, '2026-06-02 09:15:54', '2026-06-02 09:15:54', 'corporate', '+254713460883', NULL, 0, 'none', 'EXRBD7', NULL, 'Experienced corporate professional based in Nairobi, Kenya.', 1, NULL),
(55, 'Mark Musyoka', 'corporate5@estateyard.co.ke', NULL, '$2y$12$O/aph8NP1TplrVwz4NKx/.1Uk6Re0IuktRNRgNGADMLYBbQvIHLi6', NULL, '2026-06-02 09:15:54', '2026-06-02 09:15:54', 'corporate', '+254765534292', NULL, 0, 'none', 'HVYQJI', NULL, 'Experienced corporate professional based in Nairobi, Kenya.', 1, NULL),
(56, 'Purity Nyawira', 'property_manager1@estateyard.co.ke', NULL, '$2y$12$luD1a/hjEZ8T78SCeBCHg.d1/YmxI5f7.JRmzsgwsMuWogOQAlmoq', NULL, '2026-06-02 09:15:54', '2026-06-02 09:15:54', 'property_manager', '+254795669229', NULL, 1, 'elite', 'Q8I9S8', NULL, 'Experienced property_manager professional based in Nairobi, Kenya.', 1, NULL),
(57, 'Anthony Njomo', 'property_manager2@estateyard.co.ke', NULL, '$2y$12$llBWQ6./tK3prV3AXaYwR.DX8UzDKx0Ne8RgKQCJk2K9Quzbyoexy', NULL, '2026-06-02 09:15:54', '2026-06-02 09:15:54', 'property_manager', '+254764107938', NULL, 1, 'professional', 'GUATZJ', NULL, 'Experienced property_manager professional based in Nairobi, Kenya.', 1, NULL),
(58, 'Martha Wambua', 'property_manager3@estateyard.co.ke', NULL, '$2y$12$TQZGad4QIOwcjVuLdZHtIuJ.WNN8xcM34TqRbMELHQ4FiirRetZHG', NULL, '2026-06-02 09:15:55', '2026-06-02 09:15:55', 'property_manager', '+254767664626', NULL, 1, 'professional', 'DRKXB9', NULL, 'Experienced property_manager professional based in Nairobi, Kenya.', 1, NULL),
(59, 'Henry Oloo', 'property_manager4@estateyard.co.ke', NULL, '$2y$12$g8k63XPmi5CBOvCnde83sOhMeVP4s2v7..6NDJkYxAj.AxdY.dAkO', NULL, '2026-06-02 09:15:55', '2026-06-02 09:15:55', 'property_manager', '+254772275945', NULL, 0, 'none', 'VZC94O', NULL, 'Experienced property_manager professional based in Nairobi, Kenya.', 1, NULL),
(60, 'Zipporah Mutheu', 'property_manager5@estateyard.co.ke', NULL, '$2y$12$1IAhu7UwiS1eqCs1bCAEsuNb3hIiOKqlVMZXbG57cs8CW3S2dYhJC', NULL, '2026-06-02 09:15:55', '2026-06-02 09:15:55', 'property_manager', '+254774090932', NULL, 0, 'none', '8SAJSU', NULL, 'Experienced property_manager professional based in Nairobi, Kenya.', 1, NULL),
(61, 'Gabriel Macharia', 'finance1@estateyard.co.ke', NULL, '$2y$12$eIrgaoKDZLELiUDc3Jtp1epa8Nff8zUZvNnSs0SDjWDxSFZ5hya7K', NULL, '2026-06-02 09:15:55', '2026-06-02 09:15:55', 'finance', '+254780089470', NULL, 1, 'elite', '6S2HDP', NULL, 'Experienced finance professional based in Nairobi, Kenya.', 1, NULL),
(62, 'Diana Kerubo', 'finance2@estateyard.co.ke', NULL, '$2y$12$5qBW0mZx34gPF1648cxQa.401dfjJh810bNArF7my552SLmqwggT6', NULL, '2026-06-02 09:15:56', '2026-06-02 09:15:56', 'finance', '+254720784041', NULL, 1, 'professional', 'NS1QJQ', NULL, 'Experienced finance professional based in Nairobi, Kenya.', 1, NULL),
(63, 'Elijah Muchangi', 'finance3@estateyard.co.ke', NULL, '$2y$12$1SXB9/F/2s6FaJs7RvdDUO5A9zNEtAUMWHL5RNo02OA6xXvwp5pYS', NULL, '2026-06-02 09:15:56', '2026-06-02 09:15:56', 'finance', '+254738660255', NULL, 1, 'professional', 'TO7VRZ', NULL, 'Experienced finance professional based in Nairobi, Kenya.', 1, NULL),
(64, 'Vivian Njoki', 'finance4@estateyard.co.ke', NULL, '$2y$12$6F/CtMtojKMBlUMWF958femDbnP8sn18tH3FUuqCwdlfg70i/Gsha', NULL, '2026-06-02 09:15:56', '2026-06-02 09:15:56', 'finance', '+254718583681', NULL, 0, 'none', 'CMSG5B', NULL, 'Experienced finance professional based in Nairobi, Kenya.', 1, NULL),
(65, 'Caleb Mwiti', 'finance5@estateyard.co.ke', NULL, '$2y$12$Rlznw5JaqlQosG8/uuhp9evlpgd9RFuke14A3X2Glin41JTZrOf6y', NULL, '2026-06-02 09:15:56', '2026-06-02 09:15:56', 'finance', '+254724614341', NULL, 0, 'none', 'YSVRGV', NULL, 'Experienced finance professional based in Nairobi, Kenya.', 1, NULL),
(66, 'Super Admin', 'admin@estateyard.co.ke', NULL, '$2y$12$9B3BrrqjewEbnAq7ptKrUeYNE/INEF9x6pWpgx6MVxiGfSbq6HuES', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57', 'admin', NULL, NULL, 1, 'elite', 'BEZ6AG', NULL, NULL, 1, NULL);

-- Data for `properties` (50 rows)
INSERT INTO \`properties\` (`id`, `user_id`, `title`, `slug`, `description`, `type`, `listing_type`, `status`, `price`, `price_period`, `bedrooms`, `bathrooms`, `area_sqft`, `floors`, `year_built`, `county`, `constituency`, `location`, `latitude`, `longitude`, `amenities`, `images`, `video_url`, `is_featured`, `view_count`, `save_count`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 6, '5-Bedroom Villa with Pool in Karen', '5-bedroom-villa-with-pool-in-karen-1', 'Beautiful house located in Karen, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'sale', 'active', 110000000, NULL, 6, 1, 2802, 1, 2005, 'Nairobi', 'Karen', 'Karen, Nairobi', -1.27, 36.865, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=0a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=0b"]', NULL, 1, 193, 21, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(2, 7, 'Modern 3-Bedroom Apartment in Kilimani', 'modern-3-bedroom-apartment-in-kilimani-2', 'Beautiful apartment located in Westlands, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'rent', 'active', 79000, 'year', 5, 4, 2901, 1, 2006, 'Nairobi', 'Westlands', 'Westlands, Nairobi', -1.2682, 36.7971, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=1a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=1b"]', NULL, 1, 359, 44, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(3, 8, '0.5 Acre Land for Sale in Runda', '05-acre-land-for-sale-in-runda-3', 'Beautiful land located in Kilimani, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'land', 'sale', 'active', 25000000, NULL, NULL, NULL, 2562, NULL, 2004, 'Nairobi', 'Kilimani', 'Kilimani, Nairobi', -1.2463, 36.8692, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=2a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=2b"]', NULL, 1, 73, 11, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(4, 9, 'Commercial Office Space in Upper Hill', 'commercial-office-space-in-upper-hill-4', 'Beautiful commercial located in Lavington, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'commercial', 'rent', 'active', 188000, 'year', NULL, NULL, 1041, 2, 2005, 'Kiambu', 'Lavington', 'Lavington, Kiambu', -1.263, 36.8583, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=3a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=3b"]', NULL, 1, 184, 3, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(5, 10, 'Luxury Penthouse in Westlands', 'luxury-penthouse-in-westlands-5', 'Beautiful villa located in Runda, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'villa', 'sale', 'active', 144000000, NULL, 2, 3, 2542, 4, 2003, 'Machakos', 'Runda', 'Runda, Machakos', -1.2633, 36.8351, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=4a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=4b"]', NULL, 1, 394, 13, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(6, 26, '4-Bedroom Townhouse in Lavington', '4-bedroom-townhouse-in-lavington-6', 'Beautiful office located in Muthaiga, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'office', 'sale', 'active', 149000000, NULL, NULL, NULL, 1913, 2, 2016, 'Nairobi', 'Muthaiga', 'Muthaiga, Nairobi', -1.3072, 36.8675, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=5a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=5b"]', NULL, 1, 219, 32, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(7, 27, 'Studio Apartment in Parklands', 'studio-apartment-in-parklands-7', 'Beautiful house located in Upper Hill, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'rent', 'active', 80000, 'month', 5, 3, 1705, 1, 2016, 'Nairobi', 'Upper Hill', 'Upper Hill, Nairobi', -1.2783, 36.8155, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=6a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=6b"]', NULL, 1, 454, 37, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(8, 28, '2-Bedroom Flat in Hurlingham', '2-bedroom-flat-in-hurlingham-8', 'Beautiful apartment located in Parklands, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'sale', 'active', 58000000, NULL, 2, 2, 4498, 1, 2017, 'Nairobi', 'Parklands', 'Parklands, Nairobi', -1.3323, 36.8701, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=7a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=7b"]', NULL, 1, 49, 48, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(9, 29, 'Commercial Building in Upper Hill', 'commercial-building-in-upper-hill-9', 'Beautiful land located in Spring Valley, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'land', 'rent', 'active', 33000, 'month', NULL, NULL, 2948, NULL, 2018, 'Kiambu', 'Spring Valley', 'Spring Valley, Kiambu', -1.3256, 36.8303, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=8a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=8b"]', NULL, 0, 308, 10, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(10, 30, '3-Bedroom Maisonette in South C', '3-bedroom-maisonette-in-south-c-10', 'Beautiful commercial located in Gigiri, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'commercial', 'sale', 'active', 144000000, NULL, NULL, NULL, 3579, 3, 2003, 'Machakos', 'Gigiri', 'Gigiri, Machakos', -1.2577, 36.7948, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=9a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=9b"]', NULL, 0, 36, 21, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(11, 51, 'Prime Land in Muthaiga 1 Acre', 'prime-land-in-muthaiga-1-acre-11', 'Beautiful villa located in Langata, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'villa', 'sale', 'active', 24000000, NULL, 1, 1, 1932, 4, 2018, 'Nairobi', 'Langata', 'Langata, Nairobi', -1.336, 36.8347, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=10a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=10b"]', NULL, 0, 312, 35, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(12, 52, 'Serviced Apartment in Gigiri', 'serviced-apartment-in-gigiri-12', 'Beautiful office located in South C, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'office', 'rent', 'active', 279000, 'year', NULL, NULL, 1709, 4, 2009, 'Nairobi', 'South C', 'South C, Nairobi', -1.2824, 36.8661, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=11a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=11b"]', NULL, 0, 338, 18, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(13, 53, 'Bungalow in Langata', 'bungalow-in-langata-13', 'Beautiful house located in Hurlingham, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'sale', 'active', 32000000, NULL, 6, 1, 587, 2, 2015, 'Nairobi', 'Hurlingham', 'Hurlingham, Nairobi', -1.3262, 36.7918, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=12a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=12b"]', NULL, 0, 90, 37, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(14, 54, 'Modern Villa in Spring Valley', 'modern-villa-in-spring-valley-14', 'Beautiful apartment located in Riverside, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'rent', 'active', 103000, 'year', 1, 3, 1535, 2, 2001, 'Kiambu', 'Riverside', 'Riverside, Kiambu', -1.3347, 36.7771, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=13a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=13b"]', NULL, 0, 120, 42, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(15, 55, 'Office Suite in Riverside Drive', 'office-suite-in-riverside-drive-15', 'Beautiful land located in Milimani, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'land', 'sale', 'active', 29000000, NULL, NULL, NULL, 4244, NULL, 2014, 'Machakos', 'Milimani', 'Milimani, Machakos', -1.3078, 36.8323, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=14a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=14b"]', NULL, 0, 468, 28, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(16, 6, '6-Bedroom Mansion in Muthaiga', '6-bedroom-mansion-in-muthaiga-16', 'Beautiful commercial located in Kitisuru, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'commercial', 'sale', 'active', 147000000, NULL, NULL, NULL, 3904, 2, 2019, 'Nairobi', 'Kitisuru', 'Kitisuru, Nairobi', -1.3301, 36.828, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=15a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=15b"]', NULL, 0, 498, 2, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(17, 7, '1-Bedroom Apartment in Kileleshwa', '1-bedroom-apartment-in-kileleshwa-17', 'Beautiful villa located in Rosslyn, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'villa', 'rent', 'active', 190000, 'month', 5, 4, 3418, 1, 2006, 'Nairobi', 'Rosslyn', 'Rosslyn, Nairobi', -1.3387, 36.8676, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=16a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=16b"]', NULL, 0, 431, 35, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(18, 8, 'Retail Space in Westlands CBD', 'retail-space-in-westlands-cbd-18', 'Beautiful office located in Loresho, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'office', 'sale', 'active', 104000000, NULL, NULL, NULL, 2541, 3, 2017, 'Nairobi', 'Loresho', 'Loresho, Nairobi', -1.3197, 36.8632, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=17a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=17b"]', NULL, 0, 242, 10, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(19, 9, 'Semi-Detached House in Loresho', 'semi-detached-house-in-loresho-19', 'Beautiful house located in Brookside, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'rent', 'active', 107000, 'month', 3, 5, 2246, 1, 2011, 'Kiambu', 'Brookside', 'Brookside, Kiambu', -1.2779, 36.7898, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=18a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=18b"]', NULL, 0, 10, 24, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(20, 10, '4-Bedroom Apartment in Brookside', '4-bedroom-apartment-in-brookside-20', 'Beautiful apartment located in Kileleshwa, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'sale', 'active', 42000000, NULL, 3, 3, 1291, 1, 2014, 'Machakos', 'Kileleshwa', 'Kileleshwa, Machakos', -1.3026, 36.8258, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=19a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=19b"]', NULL, 0, 21, 43, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(21, 26, 'Warehouse Space in Industrial Area', 'warehouse-space-in-industrial-area-21', 'Beautiful land located in Karen, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'land', 'sale', 'active', 46000000, NULL, NULL, NULL, 1199, NULL, 2014, 'Nairobi', 'Karen', 'Karen, Nairobi', -1.2729, 36.8044, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=20a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=20b"]', NULL, 0, 390, 41, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(22, 27, 'Beach Plot in Nyali Mombasa', 'beach-plot-in-nyali-mombasa-22', 'Beautiful commercial located in Westlands, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'commercial', 'rent', 'active', 235000, 'year', NULL, NULL, 1878, 4, 2005, 'Nairobi', 'Westlands', 'Westlands, Nairobi', -1.2761, 36.8617, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=21a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=21b"]', NULL, 0, 343, 33, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(23, 28, '3-Bedroom Townhouse in Kitisuru', '3-bedroom-townhouse-in-kitisuru-23', 'Beautiful villa located in Kilimani, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'villa', 'sale', 'active', 27000000, NULL, 6, 3, 986, 4, 2001, 'Nairobi', 'Kilimani', 'Kilimani, Nairobi', -1.291, 36.8173, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=22a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=22b"]', NULL, 0, 447, 26, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(24, 29, 'Prime Corner Plot in Karen', 'prime-corner-plot-in-karen-24', 'Beautiful office located in Lavington, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'office', 'rent', 'active', 283000, 'year', NULL, NULL, 3003, 2, 2005, 'Kiambu', 'Lavington', 'Lavington, Kiambu', -1.2532, 36.8524, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=23a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=23b"]', NULL, 0, 201, 32, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(25, 30, 'Grade A Office in Upper Hill Towers', 'grade-a-office-in-upper-hill-towers-25', 'Beautiful house located in Runda, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'sale', 'active', 134000000, NULL, 2, 1, 4967, 1, 2018, 'Machakos', 'Runda', 'Runda, Machakos', -1.3164, 36.8447, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=24a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=24b"]', NULL, 0, 391, 5, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(26, 51, '2-Bedroom Apartment Gigiri UN Area', '2-bedroom-apartment-gigiri-un-area-26', 'Beautiful apartment located in Muthaiga, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'sale', 'active', 96000000, NULL, 5, 2, 4466, 4, 2002, 'Nairobi', 'Muthaiga', 'Muthaiga, Nairobi', -1.2773, 36.8588, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=25a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=25b"]', NULL, 0, 111, 27, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(27, 52, 'Detached Villa in Runda Estate', 'detached-villa-in-runda-estate-27', 'Beautiful land located in Upper Hill, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'land', 'rent', 'active', 153000, 'month', NULL, NULL, 2679, NULL, 2005, 'Nairobi', 'Upper Hill', 'Upper Hill, Nairobi', -1.3067, 36.8163, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=26a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=26b"]', NULL, 0, 221, 43, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(28, 53, 'Commercial Plot Thika Road', 'commercial-plot-thika-road-28', 'Beautiful commercial located in Parklands, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'commercial', 'sale', 'active', 125000000, NULL, NULL, NULL, 3298, 1, 2011, 'Nairobi', 'Parklands', 'Parklands, Nairobi', -1.3064, 36.797, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=27a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=27b"]', NULL, 0, 500, 13, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(29, 54, '5-Bedroom Home in Rosslyn', '5-bedroom-home-in-rosslyn-29', 'Beautiful villa located in Spring Valley, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'villa', 'rent', 'active', 124000, 'month', 1, 2, 1350, 1, 2014, 'Kiambu', 'Spring Valley', 'Spring Valley, Kiambu', -1.2783, 36.7794, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=28a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=28b"]', NULL, 0, 230, 30, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(30, 55, 'Furnished 2BR Apartment Kilimani', 'furnished-2br-apartment-kilimani-30', 'Beautiful office located in Gigiri, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'office', 'sale', 'active', 81000000, NULL, NULL, NULL, 1504, 4, 2001, 'Machakos', 'Gigiri', 'Gigiri, Machakos', -1.3265, 36.8046, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=29a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=29b"]', NULL, 0, 115, 16, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(31, 6, '1-Acre Farm in Limuru', '1-acre-farm-in-limuru-31', 'Beautiful house located in Langata, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'sale', 'active', 26000000, NULL, 3, 3, 2692, 1, 2022, 'Nairobi', 'Langata', 'Langata, Nairobi', -1.2549, 36.8436, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=30a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=30b"]', NULL, 0, 190, 16, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(32, 7, 'Modern Duplex in Lavington', 'modern-duplex-in-lavington-32', 'Beautiful apartment located in South C, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'rent', 'active', 195000, 'year', 6, 1, 608, 2, 2006, 'Nairobi', 'South C', 'South C, Nairobi', -1.3411, 36.8693, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=31a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=31b"]', NULL, 0, 489, 7, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(33, 8, 'Hostel Investment in Westlands', 'hostel-investment-in-westlands-33', 'Beautiful land located in Hurlingham, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'land', 'sale', 'active', 7000000, NULL, NULL, NULL, 2416, NULL, 2001, 'Nairobi', 'Hurlingham', 'Hurlingham, Nairobi', -1.3002, 36.7953, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=32a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=32b"]', NULL, 0, 155, 25, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(34, 9, 'Prime Land in Ruiru 2 Acres', 'prime-land-in-ruiru-2-acres-34', 'Beautiful commercial located in Riverside, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'commercial', 'rent', 'active', 91000, 'year', NULL, NULL, 3377, 1, 2006, 'Kiambu', 'Riverside', 'Riverside, Kiambu', -1.3208, 36.8265, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=33a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=33b"]', NULL, 0, 487, 36, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(35, 10, '3-Bedroom House in South B', '3-bedroom-house-in-south-b-35', 'Beautiful villa located in Milimani, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'villa', 'sale', 'active', 17000000, NULL, 1, 2, 1395, 2, 2006, 'Machakos', 'Milimani', 'Milimani, Machakos', -1.3315, 36.7903, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=34a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=34b"]', NULL, 0, 363, 17, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(36, 26, 'Office Block in Upper Hill', 'office-block-in-upper-hill-36', 'Beautiful office located in Kitisuru, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'office', 'sale', 'active', 144000000, NULL, NULL, NULL, 801, 2, 2009, 'Nairobi', 'Kitisuru', 'Kitisuru, Nairobi', -1.3386, 36.7746, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=35a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=35b"]', NULL, 0, 215, 37, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(37, 27, 'Luxury Maisonette in Karen', 'luxury-maisonette-in-karen-37', 'Beautiful house located in Rosslyn, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'rent', 'active', 78000, 'month', 6, 5, 3413, 2, 2004, 'Nairobi', 'Rosslyn', 'Rosslyn, Nairobi', -1.2719, 36.7731, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=36a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=36b"]', NULL, 0, 451, 33, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(38, 28, 'Affordable Apartment in Rongai', 'affordable-apartment-in-rongai-38', 'Beautiful apartment located in Loresho, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'sale', 'active', 58000000, NULL, 5, 3, 563, 2, 2002, 'Nairobi', 'Loresho', 'Loresho, Nairobi', -1.2671, 36.847, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=37a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=37b"]', NULL, 0, 222, 35, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(39, 29, '4-Bedroom Bungalow in Athi River', '4-bedroom-bungalow-in-athi-river-39', 'Beautiful land located in Brookside, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'land', 'rent', 'active', 76000, 'month', NULL, NULL, 4998, NULL, 2012, 'Kiambu', 'Brookside', 'Brookside, Kiambu', -1.3354, 36.8266, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=38a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=38b"]', NULL, 0, 181, 41, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(40, 30, 'Industrial Shed in Mombasa Road', 'industrial-shed-in-mombasa-road-40', 'Beautiful commercial located in Kileleshwa, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'commercial', 'sale', 'active', 141000000, NULL, NULL, NULL, 4881, 3, 2014, 'Machakos', 'Kileleshwa', 'Kileleshwa, Machakos', -1.256, 36.8262, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=39a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=39b"]', NULL, 0, 388, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(41, 51, 'Studio Apartment in Kilimani', 'studio-apartment-in-kilimani-41', 'Beautiful villa located in Karen, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'villa', 'sale', 'pending', 45000000, NULL, 5, 2, 4866, 3, 2002, 'Nairobi', 'Karen', 'Karen, Nairobi', -1.2788, 36.8332, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=40a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=40b"]', NULL, 0, 422, 23, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(42, 52, '5BR Detached House Nyari Estate', '5br-detached-house-nyari-estate-42', 'Beautiful office located in Westlands, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'office', 'rent', 'pending', 228000, 'year', NULL, NULL, 1059, 3, 2008, 'Nairobi', 'Westlands', 'Westlands, Nairobi', -1.3047, 36.8379, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=41a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=41b"]', NULL, 0, 354, 2, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(43, 53, 'Corner Apartment in Westlands', 'corner-apartment-in-westlands-43', 'Beautiful house located in Kilimani, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'sale', 'pending', 109000000, NULL, 2, 4, 1418, 2, 2010, 'Nairobi', 'Kilimani', 'Kilimani, Nairobi', -1.2463, 36.7896, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=42a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=42b"]', NULL, 0, 349, 37, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(44, 54, 'Plot in Ruaka Township', 'plot-in-ruaka-township-44', 'Beautiful apartment located in Lavington, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'rent', 'pending', 168000, 'year', 1, 3, 2646, 4, 2004, 'Kiambu', 'Lavington', 'Lavington, Kiambu', -1.2942, 36.8435, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=43a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=43b"]', NULL, 0, 324, 7, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(45, 55, '3BR Flat in Buruburu', '3br-flat-in-buruburu-45', 'Beautiful land located in Runda, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'land', 'sale', 'pending', 44000000, NULL, NULL, NULL, 2650, NULL, 2008, 'Machakos', 'Runda', 'Runda, Machakos', -1.3331, 36.8373, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=44a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=44b"]', NULL, 0, 300, 47, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(46, 6, 'Commercial Unit in Ngong Road', 'commercial-unit-in-ngong-road-46', 'Beautiful commercial located in Muthaiga, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'commercial', 'sale', 'draft', 92000000, NULL, NULL, NULL, 3057, 3, 2021, 'Nairobi', 'Muthaiga', 'Muthaiga, Nairobi', -1.3376, 36.786, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=45a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=45b"]', NULL, 0, 130, 42, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(47, 7, 'Townhouse Complex in Karen', 'townhouse-complex-in-karen-47', 'Beautiful villa located in Upper Hill, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'villa', 'rent', 'draft', 241000, 'month', 1, 5, 2881, 1, 2001, 'Nairobi', 'Upper Hill', 'Upper Hill, Nairobi', -1.2776, 36.7801, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=46a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=46b"]', NULL, 0, 84, 46, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(48, 8, 'Student Hostel Kenyatta University', 'student-hostel-kenyatta-university-48', 'Beautiful office located in Parklands, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'office', 'sale', 'draft', 93000000, NULL, NULL, NULL, 1434, 3, 2009, 'Nairobi', 'Parklands', 'Parklands, Nairobi', -1.3035, 36.8457, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=47a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=47b"]', NULL, 0, 221, 4, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(49, 9, 'Duplex in Kitengela', 'duplex-in-kitengela-49', 'Beautiful house located in Spring Valley, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'rent', 'draft', 175000, 'month', 4, 4, 639, 4, 2002, 'Kiambu', 'Spring Valley', 'Spring Valley, Kiambu', -1.2628, 36.774, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=48a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=48b"]', NULL, 0, 178, 18, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL),
(50, 10, 'Penthouse Nairobi CBD', 'penthouse-nairobi-cbd-50', 'Beautiful apartment located in Gigiri, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'sale', 'draft', 23000000, NULL, 2, 3, 1658, 4, 2021, 'Machakos', 'Gigiri', 'Gigiri, Machakos', -1.3158, 36.8669, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=49a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=49b"]', NULL, 0, 410, 11, '2026-06-02 09:15:57', '2026-06-02 09:15:57', NULL);

-- Data for `leases` (20 rows)
INSERT INTO \`leases\` (`id`, `property_id`, `landlord_id`, `tenant_id`, `monthly_rent`, `deposit`, `start_date`, `end_date`, `status`, `terms`, `signed_at`, `created_at`, `updated_at`) VALUES
(1, 2, 6, 21, 79000, 158000, '2025-07-02 00:00:00', '2026-07-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-07-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(2, 4, 7, 22, 188000, 376000, '2025-07-02 00:00:00', '2026-07-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-07-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(3, 7, 8, 23, 80000, 160000, '2025-06-02 00:00:00', '2026-06-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-06-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(4, 9, 9, 24, 33000, 66000, '2025-04-02 00:00:00', '2026-04-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-04-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(5, 12, 10, 25, 279000, 558000, '2025-02-02 00:00:00', '2026-02-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-02-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(6, 14, 6, 21, 103000, 206000, '2025-08-02 00:00:00', '2026-08-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-08-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(7, 17, 7, 22, 190000, 380000, '2025-05-02 00:00:00', '2026-05-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-05-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(8, 19, 8, 23, 107000, 214000, '2025-07-02 00:00:00', '2026-07-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-07-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(9, 22, 9, 24, 235000, 470000, '2025-09-02 00:00:00', '2026-09-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-09-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(10, 24, 10, 25, 283000, 566000, '2025-03-02 00:00:00', '2026-03-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-03-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(11, 27, 6, 21, 153000, 306000, '2025-05-02 00:00:00', '2026-05-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-05-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(12, 29, 7, 22, 124000, 248000, '2025-01-02 00:00:00', '2026-01-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-01-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(13, 32, 8, 23, 195000, 390000, '2026-01-02 00:00:00', '2027-01-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2026-01-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(14, 34, 9, 24, 91000, 182000, '2025-01-02 00:00:00', '2026-01-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-01-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(15, 37, 10, 25, 78000, 156000, '2025-01-02 00:00:00', '2026-01-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-01-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(16, 39, 6, 21, 76000, 152000, '2025-04-02 00:00:00', '2026-04-02 00:00:00', 'expired', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-04-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(17, 42, 7, 22, 228000, 456000, '2025-11-02 00:00:00', '2026-11-02 00:00:00', 'expired', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-11-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(18, 44, 8, 23, 168000, 336000, '2025-12-02 00:00:00', '2026-12-02 00:00:00', 'expired', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-12-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(19, 47, 9, 24, 241000, 482000, '2026-03-02 00:00:00', '2027-03-02 00:00:00', 'pending', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(20, 49, 10, 25, 175000, 350000, '2025-12-02 00:00:00', '2026-12-02 00:00:00', 'pending', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57');

-- Data for `rent_payments` (30 rows)
INSERT INTO \`rent_payments\` (`id`, `lease_id`, `tenant_id`, `landlord_id`, `amount`, `month_year`, `payment_method`, `transaction_ref`, `status`, `paid_at`, `due_date`, `created_at`, `updated_at`) VALUES
(1, 1, 21, 6, 79000, '2026-06', 'card', 'QHJBJUPXNG3', 'paid', '2026-05-30 09:15:57', '2026-05-30 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(2, 1, 21, 6, 79000, '2026-05', 'card', 'QHJVPOLKZZU', 'paid', '2026-04-28 09:15:57', '2026-04-28 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(3, 2, 22, 7, 188000, '2026-06', 'bank', 'QHJUZWZW0XA', 'paid', '2026-06-01 09:15:57', '2026-06-01 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(4, 2, 22, 7, 188000, '2026-05', 'mpesa', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(5, 3, 23, 8, 80000, '2026-06', 'card', 'QHJDDQOCWMB', 'paid', '2026-05-28 09:15:57', '2026-05-28 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(6, 3, 23, 8, 80000, '2026-05', 'card', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(7, 4, 24, 9, 33000, '2026-06', 'bank', 'QHJATJ3HGF1', 'paid', '2026-05-29 09:15:57', '2026-05-29 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(8, 4, 24, 9, 33000, '2026-05', 'card', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(9, 5, 25, 10, 279000, '2026-06', 'cash', 'QHJL4FMGUES', 'paid', '2026-06-02 09:15:57', '2026-06-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(10, 5, 25, 10, 279000, '2026-05', 'bank', 'QHJ6MY7QZLB', 'paid', '2026-05-02 09:15:57', '2026-05-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(11, 6, 21, 6, 103000, '2026-06', 'mpesa', 'QHJKD7L7JJR', 'paid', '2026-06-02 09:15:57', '2026-06-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(12, 6, 21, 6, 103000, '2026-05', 'card', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(13, 7, 22, 7, 190000, '2026-06', 'mpesa', 'QHJZTMMYML3', 'paid', '2026-06-01 09:15:57', '2026-06-01 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(14, 7, 22, 7, 190000, '2026-05', 'bank', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(15, 8, 23, 8, 107000, '2026-06', 'cash', 'QHJARURZ7EZ', 'paid', '2026-05-31 09:15:57', '2026-05-31 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(16, 8, 23, 8, 107000, '2026-05', 'cash', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(17, 9, 24, 9, 235000, '2026-06', 'card', 'QHJBSWTPIHR', 'paid', '2026-06-01 09:15:57', '2026-06-01 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(18, 9, 24, 9, 235000, '2026-05', 'mpesa', 'QHJFPIOOOEY', 'paid', '2026-05-02 09:15:57', '2026-05-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(19, 10, 25, 10, 283000, '2026-06', 'card', 'QHJASXPHOIN', 'paid', '2026-05-30 09:15:57', '2026-05-30 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(20, 10, 25, 10, 283000, '2026-05', 'bank', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(21, 11, 21, 6, 153000, '2026-06', 'card', 'QHJGLT59GWG', 'paid', '2026-06-01 09:15:57', '2026-06-01 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(22, 11, 21, 6, 153000, '2026-05', 'card', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(23, 12, 22, 7, 124000, '2026-06', 'mpesa', 'QHJGLSBTAN3', 'paid', '2026-06-02 09:15:57', '2026-06-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(24, 12, 22, 7, 124000, '2026-05', 'cash', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(25, 13, 23, 8, 195000, '2026-06', 'cash', 'QHJ6BBIKHNQ', 'paid', '2026-05-31 09:15:57', '2026-05-31 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(26, 13, 23, 8, 195000, '2026-05', 'bank', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(27, 14, 24, 9, 91000, '2026-06', 'bank', 'QHJYTAYSQD0', 'paid', '2026-06-02 09:15:57', '2026-06-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(28, 14, 24, 9, 91000, '2026-05', 'cash', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(29, 15, 25, 10, 78000, '2026-06', 'cash', 'QHJGX3LAFQ7', 'paid', '2026-05-29 09:15:57', '2026-05-29 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(30, 15, 25, 10, 78000, '2026-05', 'cash', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 09:15:57', '2026-06-02 09:15:57');

-- Data for `escrow_transactions` (20 rows)
INSERT INTO \`escrow_transactions\` (`id`, `property_id`, `buyer_id`, `seller_id`, `amount`, `type`, `status`, `reference`, `notes`, `released_at`, `created_at`, `updated_at`) VALUES
(1, 1, 46, 6, 11000000, 'deposit', 'held', 'ESC-RQKNFWD5', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(2, 3, 47, 7, 2500000, 'deposit', 'held', 'ESC-H1INOCIQ', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(3, 5, 48, 8, 14400000, 'deposit', 'held', 'ESC-L0JAU8YR', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(4, 6, 49, 9, 14900000, 'deposit', 'held', 'ESC-2KRIWCQI', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(5, 8, 50, 10, 5800000, 'deposit', 'held', 'ESC-ZXUYRYJR', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(6, 10, 46, 6, 14400000, 'deposit', 'held', 'ESC-M2Q8QIFT', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(7, 11, 47, 7, 2400000, 'deposit', 'held', 'ESC-D76I4TCT', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(8, 13, 48, 8, 3200000, 'deposit', 'held', 'ESC-OFU5U5MA', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(9, 15, 49, 9, 2900000, 'deposit', 'released', 'ESC-GTDN83TI', 'Deposit held pending title deed transfer and legal clearance.', '2026-05-18 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(10, 16, 50, 10, 14700000, 'deposit', 'released', 'ESC-CJNQKDTD', 'Deposit held pending title deed transfer and legal clearance.', '2026-05-07 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(11, 18, 46, 6, 10400000, 'sale', 'released', 'ESC-WW08PBJJ', 'Deposit held pending title deed transfer and legal clearance.', '2026-05-15 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(12, 20, 47, 7, 4200000, 'sale', 'released', 'ESC-G2JR5MJV', 'Deposit held pending title deed transfer and legal clearance.', '2026-05-19 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(13, 21, 48, 8, 4600000, 'sale', 'released', 'ESC-R5MWKK6D', 'Deposit held pending title deed transfer and legal clearance.', '2026-05-27 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(14, 23, 49, 9, 2700000, 'sale', 'released', 'ESC-X25AUBXQ', 'Deposit held pending title deed transfer and legal clearance.', '2026-05-23 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(15, 25, 50, 10, 13400000, 'sale', 'disputed', 'ESC-DHX8G5CV', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(16, 26, 46, 6, 9600000, 'refund', 'disputed', 'ESC-7IVSQYZA', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(17, 28, 47, 7, 12500000, 'refund', 'disputed', 'ESC-XMAG7HAE', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(18, 30, 48, 8, 8100000, 'refund', 'refunded', 'ESC-NN9LLOGX', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(19, 31, 49, 9, 2600000, 'refund', 'refunded', 'ESC-EYQFFO0Y', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(20, 33, 50, 10, 700000, 'refund', 'refunded', 'ESC-BFYPMR25', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57');

-- Data for `auctions` (10 rows)
INSERT INTO \`auctions\` (`id`, `property_id`, `auctioneer_id`, `title`, `description`, `reserve_price`, `starting_bid`, `current_bid`, `bid_increment`, `starts_at`, `ends_at`, `status`, `winner_id`, `created_at`, `updated_at`) VALUES
(1, 1, 41, 'Prime Karen Mansion Auction', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 88000000, 66000000, 73260000, 50000, '2026-06-02 04:15:57', '2026-06-02 20:15:57', 'live', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(2, 2, 42, 'Westlands Commercial Block Sale', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 63200, 47400, 51192, 50000, '2026-06-02 06:15:57', '2026-06-02 14:15:57', 'live', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(3, 3, 43, 'Distressed Sale Kilimani Apartment', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 20000000, 15000000, 17400000, 50000, '2026-06-02 04:15:57', '2026-06-02 11:15:57', 'live', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(4, 4, 44, 'Bank-Seized Property Runda Villa', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 150400, 112800, NULL, 50000, '2026-06-09 09:15:57', '2026-06-10 09:15:57', 'upcoming', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(5, 5, 45, 'Government Surplus Muthaiga Land', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 115200000, 86400000, NULL, 50000, '2026-06-06 09:15:57', '2026-06-07 09:15:57', 'upcoming', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(6, 6, 41, 'Probate Sale Lavington Estate', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 119200000, 89400000, NULL, 50000, '2026-06-14 09:15:57', '2026-06-15 09:15:57', 'upcoming', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(7, 7, 42, 'Developer Closeout Upper Hill Offices', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 64000, 48000, NULL, 50000, '2026-06-06 09:15:57', '2026-06-07 09:15:57', 'upcoming', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(8, 8, 43, 'Foreclosure Parklands Townhouse', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 46400000, 34800000, 38628000, 50000, '2026-05-29 09:15:57', '2026-05-30 09:15:57', 'ended', 46, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(9, 9, 44, 'Heritage Property Gigiri Residence', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 26400, 19800, 23364, 50000, '2026-05-13 09:15:57', '2026-05-14 09:15:57', 'ended', 46, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(10, 10, 45, 'Agricultural Land Limuru Farm', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 115200000, 86400000, 93312000, 50000, '2026-05-14 09:15:57', '2026-05-15 09:15:57', 'ended', 46, '2026-06-02 09:15:57', '2026-06-02 09:15:57');

-- Data for `bids` (24 rows)
INSERT INTO \`bids\` (`id`, `auction_id`, `bidder_id`, `amount`, `is_winning`, `created_at`, `updated_at`) VALUES
(1, 1, 47, 66100000, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(2, 1, 48, 66150000, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(3, 1, 49, 66300000, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(4, 1, 50, 66350000, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(5, 2, 48, 197400, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(6, 2, 49, 247400, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(7, 2, 50, 397400, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(8, 3, 49, 15100000, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(9, 3, 50, 15250000, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(10, 3, 21, 15400000, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(11, 3, 22, 15550000, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(12, 3, 23, 15700000, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(13, 8, 24, 34850000, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(14, 8, 25, 34900000, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(15, 8, 46, 35000000, 1, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(16, 9, 25, 169800, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(17, 9, 46, 219800, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(18, 9, 47, 269800, 1, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(19, 10, 46, 86450000, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(20, 10, 47, 86550000, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(21, 10, 48, 86600000, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(22, 10, 49, 86700000, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(23, 10, 50, 86850000, 0, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(24, 10, 21, 86950000, 1, '2026-06-02 09:15:57', '2026-06-02 09:15:57');

-- Data for `referrals` (10 rows)
INSERT INTO \`referrals\` (`id`, `referrer_id`, `referred_user_id`, `referral_code`, `click_count`, `conversion_count`, `total_earned`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 11, 21, 'RZ6548', 57, 10, 28000, '2026-12-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(2, 12, 22, 'ILSHWE', 37, 4, 15000, '2026-12-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(3, 13, 23, 'EYT1TG', 52, 9, 31000, '2026-12-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(4, 14, 24, '5NJWZG', 91, 9, 49000, '2026-12-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(5, 15, 25, 'L5DHSM', 7, 3, 47000, '2026-12-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(6, 16, NULL, '7IZAXU', 61, 1, 11000, '2026-12-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(7, 17, NULL, 'A5NEKK', 64, 3, 15000, '2026-12-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(8, 18, NULL, '7KX4YC', 64, 7, 11000, '2026-12-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(9, 19, NULL, 'PYPL6K', 47, 4, 46000, '2026-12-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(10, 20, NULL, 'VF0KNW', 46, 2, 9000, '2026-12-02 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57');

-- Data for `verifications` (10 rows)
INSERT INTO \`verifications\` (`id`, `user_id`, `tier`, `status`, `submitted_at`, `approved_at`, `expires_at`, `notes`, `reviewed_by`, `created_at`, `updated_at`) VALUES
(1, 6, 'basic', 'pending', '2026-05-16 09:15:57', NULL, NULL, NULL, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(2, 7, 'professional', 'approved', '2026-05-15 09:15:57', '2026-05-17 09:15:57', '2027-06-02 09:15:57', NULL, 66, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(3, 8, 'elite', 'approved', '2026-05-18 09:15:57', '2026-05-12 09:15:57', '2027-06-02 09:15:57', NULL, 66, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(4, 9, 'basic', 'rejected', '2026-05-02 09:15:57', NULL, NULL, 'Documents not clear. Please resubmit with clearer copies.', 66, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(5, 10, 'professional', 'expired', '2026-04-17 09:15:57', NULL, NULL, NULL, 66, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(6, 11, 'elite', 'pending', '2026-05-12 09:15:57', NULL, NULL, NULL, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(7, 12, 'basic', 'approved', '2026-04-23 09:15:57', '2026-05-27 09:15:57', '2027-06-02 09:15:57', NULL, 66, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(8, 13, 'professional', 'approved', '2026-04-08 09:15:57', '2026-05-16 09:15:57', '2027-06-02 09:15:57', NULL, 66, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(9, 14, 'elite', 'rejected', '2026-04-14 09:15:57', NULL, NULL, 'Documents not clear. Please resubmit with clearer copies.', 66, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(10, 15, 'basic', 'expired', '2026-05-02 09:15:57', NULL, NULL, NULL, 66, '2026-06-02 09:15:57', '2026-06-02 09:15:57');

-- Data for `verification_documents` (16 rows)
INSERT INTO \`verification_documents\` (`id`, `verification_id`, `document_type`, `file_path`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'national_id', 'verifications/6/national_id.pdf', 'pending', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(2, 2, 'national_id', 'verifications/7/national_id.pdf', 'approved', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(3, 2, 'kra_pin', 'verifications/7/kra_pin.pdf', 'approved', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(4, 3, 'national_id', 'verifications/8/national_id.pdf', 'approved', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(5, 3, 'kra_pin', 'verifications/8/kra_pin.pdf', 'approved', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(6, 4, 'national_id', 'verifications/9/national_id.pdf', 'rejected', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(7, 5, 'national_id', 'verifications/10/national_id.pdf', 'pending', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(8, 5, 'kra_pin', 'verifications/10/kra_pin.pdf', 'pending', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(9, 6, 'national_id', 'verifications/11/national_id.pdf', 'pending', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(10, 6, 'kra_pin', 'verifications/11/kra_pin.pdf', 'pending', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(11, 7, 'national_id', 'verifications/12/national_id.pdf', 'approved', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(12, 8, 'national_id', 'verifications/13/national_id.pdf', 'approved', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(13, 8, 'kra_pin', 'verifications/13/kra_pin.pdf', 'approved', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(14, 9, 'national_id', 'verifications/14/national_id.pdf', 'rejected', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(15, 9, 'kra_pin', 'verifications/14/kra_pin.pdf', 'pending', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(16, 10, 'national_id', 'verifications/15/national_id.pdf', 'pending', '2026-06-02 09:15:57', '2026-06-02 09:15:57');

-- Data for `inspections` (15 rows)
INSERT INTO \`inspections\` (`id`, `property_id`, `inspector_id`, `requester_id`, `scheduled_at`, `status`, `notes`, `report_url`, `created_at`, `updated_at`) VALUES
(1, 1, 36, 21, '2026-05-28 09:15:57', 'pending', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(2, 2, 37, 22, '2026-06-08 09:15:57', 'confirmed', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(3, 3, 38, 23, '2026-06-12 09:15:57', 'completed', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', 'reports/inspection_2.pdf', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(4, 4, 39, 24, '2026-05-30 09:15:57', 'cancelled', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(5, 5, 40, 25, '2026-06-12 09:15:57', 'pending', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(6, 6, 36, 21, '2026-06-02 09:15:57', 'confirmed', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(7, 7, 37, 22, '2026-06-07 09:15:57', 'completed', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', 'reports/inspection_6.pdf', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(8, 8, 38, 23, '2026-06-13 09:15:57', 'cancelled', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(9, 9, 39, 24, '2026-05-24 09:15:57', 'pending', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(10, 10, 40, 25, '2026-06-16 09:15:57', 'confirmed', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(11, 11, 36, 21, '2026-06-13 09:15:57', 'completed', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', 'reports/inspection_10.pdf', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(12, 12, 37, 22, '2026-05-26 09:15:57', 'cancelled', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(13, 13, NULL, 23, '2026-05-30 09:15:57', 'pending', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(14, 14, NULL, 24, '2026-06-20 09:15:57', 'confirmed', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(15, 15, NULL, 25, '2026-06-03 09:15:57', 'completed', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', 'reports/inspection_14.pdf', '2026-06-02 09:15:57', '2026-06-02 09:15:57');

-- Data for `maintenance_requests` (20 rows)
INSERT INTO \`maintenance_requests\` (`id`, `property_id`, `tenant_id`, `title`, `description`, `priority`, `status`, `assigned_to`, `resolved_at`, `created_at`, `updated_at`) VALUES
(1, 2, 21, 'Leaking Roof Urgent Repair', 'Tenant reported issue: Leaking Roof Urgent Repair. Requires prompt attention.', 'low', 'open', 56, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(2, 4, 22, 'Broken Water Heater', 'Tenant reported issue: Broken Water Heater. Requires prompt attention.', 'medium', 'in_progress', 57, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(3, 7, 23, 'Electrical Fault in Kitchen', 'Tenant reported issue: Electrical Fault in Kitchen. Requires prompt attention.', 'high', 'resolved', 58, '2026-05-27 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(4, 9, 24, 'Clogged Drainage System', 'Tenant reported issue: Clogged Drainage System. Requires prompt attention.', 'urgent', 'closed', 59, '2026-06-01 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(5, 12, 25, 'Paint Peeling in Living Room', 'Tenant reported issue: Paint Peeling in Living Room. Requires prompt attention.', 'low', 'open', 60, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(6, 14, 21, 'Broken Window Lock', 'Tenant reported issue: Broken Window Lock. Requires prompt attention.', 'medium', 'in_progress', 56, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(7, 17, 22, 'AC Unit Not Working', 'Tenant reported issue: AC Unit Not Working. Requires prompt attention.', 'high', 'resolved', 57, '2026-05-23 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(8, 19, 23, 'Faulty Gate Motor', 'Tenant reported issue: Faulty Gate Motor. Requires prompt attention.', 'urgent', 'closed', 58, '2026-05-30 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(9, 22, 24, 'Mold in Bathroom', 'Tenant reported issue: Mold in Bathroom. Requires prompt attention.', 'low', 'open', 59, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(10, 24, 25, 'Burst Water Pipe', 'Tenant reported issue: Burst Water Pipe. Requires prompt attention.', 'medium', 'in_progress', 60, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(11, 27, 21, 'Sewage Overflow', 'Tenant reported issue: Sewage Overflow. Requires prompt attention.', 'high', 'resolved', 56, '2026-05-25 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(12, 29, 22, 'Broken Staircase Railing', 'Tenant reported issue: Broken Staircase Railing. Requires prompt attention.', 'urgent', 'closed', 57, '2026-05-28 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(13, 32, 23, 'Power Outage Generator Fault', 'Tenant reported issue: Power Outage Generator Fault. Requires prompt attention.', 'low', 'open', 58, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(14, 34, 24, 'Fallen Boundary Wall', 'Tenant reported issue: Fallen Boundary Wall. Requires prompt attention.', 'medium', 'in_progress', 59, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(15, 37, 25, 'Rodent Infestation', 'Tenant reported issue: Rodent Infestation. Requires prompt attention.', 'high', 'resolved', 60, '2026-05-30 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(16, 39, 21, 'Damaged Floor Tiles', 'Tenant reported issue: Damaged Floor Tiles. Requires prompt attention.', 'urgent', 'closed', NULL, '2026-05-24 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(17, 42, 22, 'Broken Door Hinge', 'Tenant reported issue: Broken Door Hinge. Requires prompt attention.', 'low', 'open', NULL, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(18, 44, 23, 'Water Tank Malfunction', 'Tenant reported issue: Water Tank Malfunction. Requires prompt attention.', 'medium', 'in_progress', NULL, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(19, 47, 24, 'Broken CCTV Camera', 'Tenant reported issue: Broken CCTV Camera. Requires prompt attention.', 'high', 'resolved', NULL, '2026-05-31 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(20, 49, 25, 'Faulty Elevator', 'Tenant reported issue: Faulty Elevator. Requires prompt attention.', 'urgent', 'closed', NULL, '2026-05-26 09:15:57', '2026-06-02 09:15:57', '2026-06-02 09:15:57');

-- Data for `messages` (20 rows)
INSERT INTO \`messages\` (`id`, `sender_id`, `recipient_id`, `subject`, `body`, `is_read`, `read_at`, `property_id`, `created_at`, `updated_at`) VALUES
(1, 21, 6, 'Rent Payment Confirmation', 'Hi, I wanted to confirm my rent payment for this month has been sent via MPESA.', 1, '2026-05-31 23:15:57', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(2, 22, 7, 'Maintenance Request Follow-up', 'I submitted a maintenance request last week regarding a leaking pipe. Any update on this?', 0, NULL, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(3, 23, 8, 'Lease Renewal Inquiry', 'My lease expires next month. Are you open to renewal at the same terms?', 0, NULL, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(4, 24, 9, 'Property Viewing Request', 'I am interested in viewing your property. What time works for you this weekend?', 1, '2026-06-02 08:15:57', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(5, 25, 10, 'Utility Bill Query', 'Could you please clarify the utility bill charges reflected on my last statement?', 0, NULL, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(6, 21, 6, 'Rent Payment Confirmation', 'Hi, I wanted to confirm my rent payment for this month has been sent via MPESA.', 0, NULL, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(7, 22, 7, 'Maintenance Request Follow-up', 'I submitted a maintenance request last week regarding a leaking pipe. Any update on this?', 1, '2026-06-01 18:15:57', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(8, 23, 8, 'Lease Renewal Inquiry', 'My lease expires next month. Are you open to renewal at the same terms?', 0, NULL, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(9, 24, 9, 'Property Viewing Request', 'I am interested in viewing your property. What time works for you this weekend?', 0, NULL, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(10, 25, 10, 'Utility Bill Query', 'Could you please clarify the utility bill charges reflected on my last statement?', 1, '2026-05-31 13:15:57', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(11, 21, 6, 'Rent Payment Confirmation', 'Hi, I wanted to confirm my rent payment for this month has been sent via MPESA.', 0, NULL, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(12, 22, 7, 'Maintenance Request Follow-up', 'I submitted a maintenance request last week regarding a leaking pipe. Any update on this?', 0, NULL, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(13, 23, 8, 'Lease Renewal Inquiry', 'My lease expires next month. Are you open to renewal at the same terms?', 1, '2026-06-01 12:15:57', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(14, 24, 9, 'Property Viewing Request', 'I am interested in viewing your property. What time works for you this weekend?', 0, NULL, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(15, 25, 10, 'Utility Bill Query', 'Could you please clarify the utility bill charges reflected on my last statement?', 0, NULL, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(16, 21, 6, 'Rent Payment Confirmation', 'Hi, I wanted to confirm my rent payment for this month has been sent via MPESA.', 1, '2026-05-31 23:15:57', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(17, 22, 7, 'Maintenance Request Follow-up', 'I submitted a maintenance request last week regarding a leaking pipe. Any update on this?', 0, NULL, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(18, 23, 8, 'Lease Renewal Inquiry', 'My lease expires next month. Are you open to renewal at the same terms?', 0, NULL, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(19, 24, 9, 'Property Viewing Request', 'I am interested in viewing your property. What time works for you this weekend?', 1, '2026-06-01 04:15:57', NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(20, 25, 10, 'Utility Bill Query', 'Could you please clarify the utility bill charges reflected on my last statement?', 0, NULL, NULL, '2026-06-02 09:15:57', '2026-06-02 09:15:57');

-- Data for `notifications_log` (20 rows)
INSERT INTO \`notifications_log\` (`id`, `user_id`, `title`, `body`, `type`, `is_read`, `url`, `created_at`, `updated_at`) VALUES
(1, 1, 'Rent Payment Received', 'Your rent payment of KES 45,000 has been received successfully.', 'payment', 1, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(2, 2, 'Inspection Scheduled', 'Property inspection has been confirmed for tomorrow at 10am.', 'inspection', 0, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(3, 3, 'Document Verified', 'Your National ID has been successfully verified.', 'document', 1, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(4, 4, 'Auction Starting Soon', 'The Karen Mansion auction starts in 2 hours. Place your bid!', 'auction', 0, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(5, 5, 'Account Verified', 'Congratulations! Your professional verification has been approved.', 'system', 1, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(6, 6, 'New Message', 'You have a new message from your landlord regarding the lease.', 'message', 0, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(7, 7, 'Rent Payment Received', 'Your rent payment of KES 45,000 has been received successfully.', 'payment', 1, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(8, 8, 'Inspection Scheduled', 'Property inspection has been confirmed for tomorrow at 10am.', 'inspection', 0, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(9, 9, 'Document Verified', 'Your National ID has been successfully verified.', 'document', 1, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(10, 10, 'Auction Starting Soon', 'The Karen Mansion auction starts in 2 hours. Place your bid!', 'auction', 0, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(11, 11, 'Account Verified', 'Congratulations! Your professional verification has been approved.', 'system', 1, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(12, 12, 'New Message', 'You have a new message from your landlord regarding the lease.', 'message', 0, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(13, 13, 'Rent Payment Received', 'Your rent payment of KES 45,000 has been received successfully.', 'payment', 1, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(14, 14, 'Inspection Scheduled', 'Property inspection has been confirmed for tomorrow at 10am.', 'inspection', 0, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(15, 15, 'Document Verified', 'Your National ID has been successfully verified.', 'document', 1, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(16, 16, 'Auction Starting Soon', 'The Karen Mansion auction starts in 2 hours. Place your bid!', 'auction', 0, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(17, 17, 'Account Verified', 'Congratulations! Your professional verification has been approved.', 'system', 1, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(18, 18, 'New Message', 'You have a new message from your landlord regarding the lease.', 'message', 0, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(19, 19, 'Rent Payment Received', 'Your rent payment of KES 45,000 has been received successfully.', 'payment', 1, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(20, 20, 'Inspection Scheduled', 'Property inspection has been confirmed for tomorrow at 10am.', 'inspection', 0, '/dashboard', '2026-06-02 09:15:57', '2026-06-02 09:15:57');

-- Data for `developer_projects` (10 rows)
INSERT INTO \`developer_projects\` (`id`, `developer_id`, `name`, `description`, `location`, `total_units`, `sold_units`, `reserved_units`, `price_from`, `price_to`, `status`, `completion_date`, `images`, `created_at`, `updated_at`) VALUES
(1, 26, 'Garden City Residences', 'Premium residential development offering modern units at Garden City Residences in prime Nairobi location.', 'Garden City Residences, Nairobi', 178, 8, 6, 8000000, 42000000, 'planning', '2028-12-02 00:00:00', '["projects\\/0\\/cover.jpg"]', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(2, 27, 'Kilimani Heights', 'Premium residential development offering modern units at Kilimani Heights in prime Nairobi location.', 'Kilimani Heights, Nairobi', 166, 31, 16, 6000000, 45000000, 'construction', '2029-03-02 00:00:00', '["projects\\/1\\/cover.jpg"]', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(3, 28, 'Runda Forest Estate', 'Premium residential development offering modern units at Runda Forest Estate in prime Nairobi location.', 'Runda Forest Estate, Nairobi', 125, 38, 20, 13000000, 20000000, 'completed', '2027-07-02 00:00:00', '["projects\\/2\\/cover.jpg"]', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(4, 29, 'Westlands Prime Towers', 'Premium residential development offering modern units at Westlands Prime Towers in prime Nairobi location.', 'Westlands Prime Towers, Nairobi', 86, 18, 17, 12000000, 22000000, 'selling', '2028-12-02 00:00:00', '["projects\\/3\\/cover.jpg"]', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(5, 30, 'Karen Golf Villas', 'Premium residential development offering modern units at Karen Golf Villas in prime Nairobi location.', 'Karen Golf Villas, Nairobi', 81, 39, 1, 12000000, 26000000, 'planning', '2028-05-02 00:00:00', '["projects\\/4\\/cover.jpg"]', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(6, 26, 'Lavington Green Park', 'Premium residential development offering modern units at Lavington Green Park in prime Nairobi location.', 'Lavington Green Park, Nairobi', 175, 16, 2, 14000000, 62000000, 'construction', '2028-04-02 00:00:00', '["projects\\/5\\/cover.jpg"]', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(7, 27, 'Parklands Skyline Apartments', 'Premium residential development offering modern units at Parklands Skyline Apartments in prime Nairobi location.', 'Parklands Skyline Apartments, Nairobi', 39, 48, 8, 10000000, 45000000, 'completed', '2027-10-02 00:00:00', '["projects\\/6\\/cover.jpg"]', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(8, 28, 'Upper Hill Business Hub', 'Premium residential development offering modern units at Upper Hill Business Hub in prime Nairobi location.', 'Upper Hill Business Hub, Nairobi', 42, 7, 0, 12000000, 28000000, 'selling', '2028-06-02 00:00:00', '["projects\\/7\\/cover.jpg"]', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(9, 29, 'Muthaiga Royal Manors', 'Premium residential development offering modern units at Muthaiga Royal Manors in prime Nairobi location.', 'Muthaiga Royal Manors, Nairobi', 164, 23, 10, 15000000, 55000000, 'planning', '2029-06-02 00:00:00', '["projects\\/8\\/cover.jpg"]', '2026-06-02 09:15:57', '2026-06-02 09:15:57'),
(10, 30, 'Riverside Executive Suites', 'Premium residential development offering modern units at Riverside Executive Suites in prime Nairobi location.', 'Riverside Executive Suites, Nairobi', 88, 11, 6, 13000000, 28000000, 'construction', '2029-03-02 00:00:00', '["projects\\/9\\/cover.jpg"]', '2026-06-02 09:15:57', '2026-06-02 09:15:57');

SET foreign_key_checks = 1;
-- End of dump
