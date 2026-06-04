-- ============================================================
-- HustleKonnect — Complete MySQL Database Schema
-- Version: 1.0.0 | MySQL 8.0+ | utf8mb4_unicode_ci
-- Generated: 2026-06-04
-- ============================================================
-- Import: mysql -u user -p hustlekonnect < database/hustlekonnect.sql
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- ============================================================
-- USERS
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('client','owner','broker','admin','superadmin') NOT NULL DEFAULT 'client',
  `kyc_status` enum('pending','submitted','approved','rejected') NOT NULL DEFAULT 'pending',
  `country` char(2) DEFAULT 'KE',
  `preferred_currency` varchar(10) DEFAULT 'KES',
  `trust_score` tinyint unsigned DEFAULT 50,
  `referral_code` varchar(20) DEFAULT NULL,
  `referred_by` bigint unsigned DEFAULT NULL,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_referral_code_unique` (`referral_code`),
  KEY `users_role_index` (`role`),
  KEY `users_kyc_status_index` (`kyc_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PERSONAL ACCESS TOKENS (Sanctum)
-- ============================================================
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `tokenable_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- KYC DOCUMENTS
-- ============================================================
CREATE TABLE IF NOT EXISTS `kyc_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `document_type` varchar(60) NOT NULL,
  `document_number` varchar(60) DEFAULT NULL,
  `front_url` varchar(500) DEFAULT NULL,
  `back_url` varchar(500) DEFAULT NULL,
  `selfie_url` varchar(500) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text,
  `smile_job_id` varchar(100) DEFAULT NULL,
  `smile_result` json DEFAULT NULL,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kyc_user_id_index` (`user_id`),
  CONSTRAINT `kyc_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- YARDS
-- ============================================================
CREATE TABLE IF NOT EXISTS `yards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `description` text,
  `address` varchar(500) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` char(2) DEFAULT 'KE',
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `rating_avg` decimal(3,2) DEFAULT NULL,
  `total_reviews` int NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `yards_user_id_fk` (`user_id`),
  CONSTRAINT `yards_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ASSETS
-- ============================================================
CREATE TABLE IF NOT EXISTS `assets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `yard_id` bigint unsigned NOT NULL,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `title` varchar(191) NOT NULL,
  `description` text,
  `category` varchar(60) NOT NULL,
  `make` varchar(60) DEFAULT NULL,
  `model` varchar(60) DEFAULT NULL,
  `year` smallint DEFAULT NULL,
  `color` varchar(40) DEFAULT NULL,
  `plate_number` varchar(30) DEFAULT NULL,
  `vin` varchar(50) DEFAULT NULL,
  `daily_rate_kes` decimal(12,2) NOT NULL,
  `weekly_rate_kes` decimal(12,2) DEFAULT NULL,
  `monthly_rate_kes` decimal(12,2) DEFAULT NULL,
  `deposit_kes` decimal(12,2) NOT NULL DEFAULT 0,
  `pricing_currency` varchar(10) DEFAULT 'KES',
  `location_city` varchar(100) DEFAULT NULL,
  `location_country` char(2) DEFAULT 'KE',
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `seats` tinyint DEFAULT NULL,
  `fuel_type` varchar(30) DEFAULT NULL,
  `transmission` varchar(30) DEFAULT NULL,
  `mileage_km` int DEFAULT NULL,
  `features` json DEFAULT NULL,
  `is_for_sale` tinyint(1) NOT NULL DEFAULT 0,
  `sale_price_kes` decimal(14,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `views_count` int NOT NULL DEFAULT 0,
  `bookings_count` int NOT NULL DEFAULT 0,
  `rating_avg` decimal(3,2) DEFAULT NULL,
  `photo_hashes` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assets_yard_id_fk` (`yard_id`),
  KEY `assets_category_idx` (`category`),
  KEY `assets_active_available_idx` (`is_active`,`is_available`),
  CONSTRAINT `assets_yard_id_fk` FOREIGN KEY (`yard_id`) REFERENCES `yards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ASSET MEDIA
-- ============================================================
CREATE TABLE IF NOT EXISTS `asset_media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint unsigned NOT NULL,
  `url` varchar(500) NOT NULL,
  `type` enum('image','video') NOT NULL DEFAULT 'image',
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` tinyint NOT NULL DEFAULT 0,
  `file_hash` varchar(64) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_media_asset_fk` (`asset_id`),
  CONSTRAINT `asset_media_asset_fk` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- AVAILABILITY SLOTS
-- ============================================================
CREATE TABLE IF NOT EXISTS `availability_slots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `price_override_kes` decimal(12,2) DEFAULT NULL,
  `blocked_reason` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `avail_asset_date_unique` (`asset_id`,`date`),
  CONSTRAINT `avail_asset_fk` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BOOKINGS
-- ============================================================
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` char(36) NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `asset_id` bigint unsigned NOT NULL,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `broker_id` bigint unsigned DEFAULT NULL,
  `driver_id` bigint unsigned DEFAULT NULL,
  `corporate_account_id` bigint unsigned DEFAULT NULL,
  `status` enum('pending_payment','payment_processing','confirmed','owner_notified','client_prepared','active','completed','closed','cancelled','disputed') NOT NULL DEFAULT 'pending_payment',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `total_days` tinyint unsigned NOT NULL DEFAULT 1,
  `daily_rate_kes` decimal(12,2) NOT NULL,
  `subtotal_kes` decimal(12,2) NOT NULL,
  `insurance_fee_kes` decimal(12,2) NOT NULL DEFAULT 0,
  `driver_fee_kes` decimal(12,2) NOT NULL DEFAULT 0,
  `platform_fee_kes` decimal(12,2) NOT NULL DEFAULT 0,
  `discount_kes` decimal(12,2) NOT NULL DEFAULT 0,
  `total_amount_kes` decimal(12,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'KES',
  `pickup_location` varchar(500) DEFAULT NULL,
  `dropoff_location` varchar(500) DEFAULT NULL,
  `insurance_type` enum('cdw','tpl','none') NOT NULL DEFAULT 'none',
  `with_driver` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text,
  `cancellation_reason` text,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `activated_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bookings_user_fk` (`user_id`),
  KEY `bookings_asset_fk` (`asset_id`),
  KEY `bookings_status_idx` (`status`),
  KEY `bookings_dates_idx` (`start_date`,`end_date`),
  CONSTRAINT `bookings_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `bookings_asset_fk` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PAYMENTS
-- ============================================================
CREATE TABLE IF NOT EXISTS `payments` (
  `id` char(36) NOT NULL,
  `booking_id` char(36) NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `gateway` enum('mpesa','mtn_momo','flutterwave','stripe','paystack','wallet','bank_transfer') NOT NULL,
  `gateway_reference` varchar(191) DEFAULT NULL,
  `idempotency_key` varchar(191) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'KES',
  `status` enum('pending','processing','completed','failed','refunded','cancelled') NOT NULL DEFAULT 'pending',
  `gateway_response` json DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_idempotency_unique` (`idempotency_key`),
  KEY `payments_booking_fk` (`booking_id`),
  KEY `payments_user_fk` (`user_id`),
  CONSTRAINT `payments_booking_fk` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  CONSTRAINT `payments_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ESCROW ACCOUNTS
-- ============================================================
CREATE TABLE IF NOT EXISTS `escrow_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` char(36) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `platform_fee` decimal(12,2) NOT NULL DEFAULT 0,
  `owner_amount` decimal(12,2) NOT NULL,
  `broker_commission` decimal(12,2) NOT NULL DEFAULT 0,
  `status` enum('pending','held','released','refunded','disputed') NOT NULL DEFAULT 'pending',
  `held_at` timestamp NULL DEFAULT NULL,
  `released_at` timestamp NULL DEFAULT NULL,
  `release_trigger` enum('auto','mutual_confirmation','admin_override','dispute_resolution') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `escrow_booking_unique` (`booking_id`),
  CONSTRAINT `escrow_booking_fk` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- WALLETS
-- ============================================================
CREATE TABLE IF NOT EXISTS `wallets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `balance` decimal(14,2) NOT NULL DEFAULT 0,
  `pending_balance` decimal(14,2) NOT NULL DEFAULT 0,
  `currency` varchar(10) NOT NULL DEFAULT 'KES',
  `is_frozen` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wallets_user_unique` (`user_id`),
  CONSTRAINT `wallets_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- WALLET TRANSACTIONS
-- ============================================================
CREATE TABLE IF NOT EXISTS `wallet_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `wallet_id` bigint unsigned NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `balance_after` decimal(14,2) NOT NULL,
  `reference` varchar(191) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wallet_tx_wallet_fk` (`wallet_id`),
  CONSTRAINT `wallet_tx_wallet_fk` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- REVIEWS
-- ============================================================
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` char(36) NOT NULL,
  `reviewer_id` bigint unsigned NOT NULL,
  `reviewed_id` bigint unsigned NOT NULL,
  `rating` tinyint unsigned NOT NULL,
  `comment` text,
  `aspects` json DEFAULT NULL,
  `is_flagged` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reviews_booking_reviewer_unique` (`booking_id`,`reviewer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PLATFORM NOTIFICATIONS
-- ============================================================
CREATE TABLE IF NOT EXISTS `platform_notifications` (
  `id` char(36) NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `type` varchar(60) DEFAULT NULL,
  `data` json DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notif_user_read_idx` (`user_id`,`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DISPUTES
-- ============================================================
CREATE TABLE IF NOT EXISTS `disputes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` char(36) NOT NULL,
  `raised_by` bigint unsigned NOT NULL,
  `against` bigint unsigned NOT NULL,
  `type` varchar(60) NOT NULL,
  `description` text NOT NULL,
  `evidence_urls` json DEFAULT NULL,
  `status` enum('open','under_review','resolved','closed') NOT NULL DEFAULT 'open',
  `resolution` text,
  `resolved_by` bigint unsigned DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `disputes_booking_fk` (`booking_id`),
  CONSTRAINT `disputes_booking_fk` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DRIVERS
-- ============================================================
CREATE TABLE IF NOT EXISTS `drivers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `license_number` varchar(60) NOT NULL,
  `license_expiry` date DEFAULT NULL,
  `license_class` varchar(20) DEFAULT NULL,
  `experience_years` tinyint DEFAULT NULL,
  `daily_rate_kes` decimal(10,2) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `rating_avg` decimal(3,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `drivers_user_unique` (`user_id`),
  CONSTRAINT `drivers_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MAINTENANCE RECORDS
-- ============================================================
CREATE TABLE IF NOT EXISTS `maintenance_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint unsigned NOT NULL,
  `type` varchar(60) NOT NULL,
  `description` text,
  `cost_kes` decimal(10,2) DEFAULT NULL,
  `mileage_at_service` int DEFAULT NULL,
  `serviced_by` varchar(191) DEFAULT NULL,
  `service_date` date NOT NULL,
  `next_service_date` date DEFAULT NULL,
  `next_service_mileage` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `maint_asset_fk` (`asset_id`),
  CONSTRAINT `maint_asset_fk` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SALE OFFERS
-- ============================================================
CREATE TABLE IF NOT EXISTS `sale_offers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint unsigned NOT NULL,
  `buyer_id` bigint unsigned NOT NULL,
  `offer_price_kes` decimal(14,2) NOT NULL,
  `message` text,
  `status` enum('pending','accepted','rejected','payment_pending','payment_completed','ownership_transferred','expired') NOT NULL DEFAULT 'pending',
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_offers_asset_fk` (`asset_id`),
  KEY `sale_offers_buyer_fk` (`buyer_id`),
  CONSTRAINT `sale_offers_asset_fk` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`),
  CONSTRAINT `sale_offers_buyer_fk` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CORPORATE ACCOUNTS
-- ============================================================
CREATE TABLE IF NOT EXISTS `corporate_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` bigint unsigned NOT NULL,
  `company_name` varchar(191) NOT NULL,
  `registration_number` varchar(100) DEFAULT NULL,
  `credit_limit_kes` decimal(14,2) NOT NULL DEFAULT 0,
  `current_balance_kes` decimal(14,2) NOT NULL DEFAULT 0,
  `payment_terms_days` tinyint NOT NULL DEFAULT 30,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `corporate_owner_fk` (`owner_id`),
  CONSTRAINT `corporate_owner_fk` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TELEMATICS
-- ============================================================
CREATE TABLE IF NOT EXISTS `telematics_devices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint unsigned NOT NULL,
  `device_id` varchar(60) NOT NULL,
  `device_type` varchar(40) NOT NULL DEFAULT 'gps_tracker',
  `sim_iccid` varchar(30) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_ping_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `telematics_asset_unique` (`asset_id`),
  UNIQUE KEY `telematics_device_id_unique` (`device_id`),
  CONSTRAINT `telematics_asset_fk` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `telematics_pings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `device_id` bigint unsigned NOT NULL,
  `asset_id` bigint unsigned NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `speed_kmh` decimal(6,2) NOT NULL DEFAULT 0,
  `heading` smallint DEFAULT NULL,
  `ignition_on` tinyint(1) NOT NULL DEFAULT 0,
  `battery_pct` tinyint DEFAULT NULL,
  `odometer_km` int DEFAULT NULL,
  `recorded_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pings_device_idx` (`device_id`),
  KEY `pings_recorded_idx` (`recorded_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `geofences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `yard_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `fence_type` enum('circle','polygon') NOT NULL DEFAULT 'circle',
  `center_lat` decimal(10,7) DEFAULT NULL,
  `center_lng` decimal(10,7) DEFAULT NULL,
  `radius_meters` int DEFAULT NULL,
  `polygon_coordinates` json DEFAULT NULL,
  `alert_on_enter` tinyint(1) NOT NULL DEFAULT 1,
  `alert_on_exit` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `telematics_trips` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `device_id` bigint unsigned NOT NULL,
  `asset_id` bigint unsigned NOT NULL,
  `booking_id` char(36) DEFAULT NULL,
  `started_at` timestamp NOT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `distance_km` decimal(10,2) NOT NULL DEFAULT 0,
  `duration_minutes` int NOT NULL DEFAULT 0,
  `max_speed_kmh` decimal(6,2) DEFAULT NULL,
  `driving_score` tinyint DEFAULT NULL,
  `status` enum('active','completed') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- FINANCING
-- ============================================================
CREATE TABLE IF NOT EXISTS `financing_partners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `partner_type` enum('bank','sacco','microfinance','leasing') NOT NULL,
  `min_loan_kes` decimal(14,2) NOT NULL,
  `max_loan_kes` decimal(14,2) NOT NULL,
  `interest_rate_annual` decimal(5,2) NOT NULL,
  `max_tenure_months` tinyint NOT NULL,
  `processing_fee_pct` decimal(4,2) DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `financing_applications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `asset_id` bigint unsigned DEFAULT NULL,
  `financing_partner_id` bigint unsigned NOT NULL,
  `loan_amount_kes` decimal(14,2) NOT NULL,
  `tenure_months` tinyint NOT NULL,
  `monthly_payment_kes` decimal(12,2) NOT NULL,
  `purpose` text,
  `monthly_income_kes` decimal(12,2) DEFAULT NULL,
  `status` enum('pending','under_review','approved','rejected','disbursed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `financing_app_user_fk` (`user_id`),
  CONSTRAINT `financing_app_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lease_to_own_agreements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `asset_id` bigint unsigned NOT NULL,
  `total_asset_value_kes` decimal(14,2) NOT NULL,
  `monthly_payment_kes` decimal(12,2) NOT NULL,
  `tenure_months` tinyint NOT NULL,
  `amount_paid_kes` decimal(14,2) NOT NULL DEFAULT 0,
  `next_payment_date` date NOT NULL,
  `status` enum('active','completed','defaulted','cancelled') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `lto_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- GOVERNMENT PROCUREMENT
-- ============================================================
CREATE TABLE IF NOT EXISTS `government_entities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `entity_type` varchar(60) DEFAULT NULL,
  `country` char(2) NOT NULL DEFAULT 'KE',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `procurement_tenders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `government_entity_id` bigint unsigned DEFAULT NULL,
  `tender_number` varchar(60) NOT NULL,
  `title` varchar(500) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(60) NOT NULL,
  `budget_min_kes` decimal(14,2) NOT NULL,
  `budget_max_kes` decimal(14,2) NOT NULL,
  `submission_deadline` timestamp NOT NULL,
  `evaluation_criteria` json DEFAULT NULL,
  `status` enum('draft','published','closed','awarded','cancelled') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tender_number_unique` (`tender_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tender_bids` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tender_id` bigint unsigned NOT NULL,
  `bidder_id` bigint unsigned NOT NULL,
  `bid_amount_kes` decimal(14,2) NOT NULL,
  `technical_proposal` text NOT NULL,
  `delivery_timeline_days` int DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `status` enum('submitted','under_review','shortlisted','awarded','rejected') NOT NULL DEFAULT 'submitted',
  `submitted_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bid_tender_bidder_unique` (`tender_id`,`bidder_id`),
  CONSTRAINT `bid_tender_fk` FOREIGN KEY (`tender_id`) REFERENCES `procurement_tenders` (`id`),
  CONSTRAINT `bid_bidder_fk` FOREIGN KEY (`bidder_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ERP INTEGRATIONS
-- ============================================================
CREATE TABLE IF NOT EXISTS `erp_integrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `platform` enum('quickbooks','xero','sage','wave') NOT NULL,
  `access_token_enc` text,
  `refresh_token_enc` text,
  `token_expires_at` timestamp NULL DEFAULT NULL,
  `realm_id` varchar(191) DEFAULT NULL,
  `status` enum('connected','disconnected','error','pending') NOT NULL DEFAULT 'pending',
  `last_sync_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `erp_user_platform_unique` (`user_id`,`platform`),
  CONSTRAINT `erp_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TENANTS
-- ============================================================
CREATE TABLE IF NOT EXISTS `tenants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `subdomain` varchar(60) NOT NULL,
  `custom_domain` varchar(191) DEFAULT NULL,
  `primary_color` varchar(10) DEFAULT '#E8922A',
  `owner_id` bigint unsigned NOT NULL,
  `plan` enum('starter','growth','enterprise') NOT NULL DEFAULT 'starter',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `settings` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_subdomain_unique` (`subdomain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MARKET CONFIGS
-- ============================================================
CREATE TABLE IF NOT EXISTS `market_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `country_code` char(2) NOT NULL,
  `country_name` varchar(100) NOT NULL,
  `currency_code` varchar(10) NOT NULL,
  `currency_symbol` varchar(10) NOT NULL,
  `phone_prefix` varchar(10) NOT NULL,
  `phone_regex` varchar(200) DEFAULT NULL,
  `primary_gateway` varchar(60) DEFAULT NULL,
  `vat_rate` decimal(5,2) NOT NULL DEFAULT 0,
  `platform_fee_pct` decimal(5,2) NOT NULL DEFAULT 5,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `config` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `market_country_unique` (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DATA PRODUCTS
-- ============================================================
CREATE TABLE IF NOT EXISTS `data_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` text,
  `product_type` enum('pricing_index','demand_analytics','market_report','raw_api') NOT NULL,
  `price_kes_monthly` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PAYSTACK TRANSACTIONS
-- ============================================================
CREATE TABLE IF NOT EXISTS `paystack_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` char(36) DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `reference` varchar(191) NOT NULL,
  `amount_kobo` bigint NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'NGN',
  `status` enum('pending','success','failed','abandoned') NOT NULL DEFAULT 'pending',
  `gateway_response` json DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paystack_ref_unique` (`reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- QUEUE TABLES
-- ============================================================
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_idx` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED DATA
-- ============================================================

INSERT INTO `market_configs` (`country_code`,`country_name`,`currency_code`,`currency_symbol`,`phone_prefix`,`phone_regex`,`primary_gateway`,`vat_rate`,`platform_fee_pct`,`is_active`,`created_at`,`updated_at`) VALUES
('KE','Kenya','KES','KSh','+254','/^(\\+254|0)[17][0-9]{8}$/','mpesa',16.00,5.00,1,NOW(),NOW()),
('UG','Uganda','UGX','USh','+256','/^(\\+256|0)[37][0-9]{8}$/','mtn_momo',18.00,5.00,1,NOW(),NOW()),
('TZ','Tanzania','TZS','TSh','+255','/^(\\+255|0)[67][0-9]{8}$/','airtel_money',18.00,5.00,1,NOW(),NOW()),
('NG','Nigeria','NGN','₦','+234','/^(\\+234|0)[789][0-9]{9}$/','paystack',7.50,5.00,1,NOW(),NOW()),
('GH','Ghana','GHS','GH₵','+233','/^(\\+233|0)[235][0-9]{8}$/','paystack',15.00,5.00,1,NOW(),NOW()),
('ZA','South Africa','ZAR','R','+27','/^(\\+27|0)[678][0-9]{8}$/','stripe',15.00,5.00,1,NOW(),NOW());

INSERT INTO `financing_partners` (`name`,`partner_type`,`min_loan_kes`,`max_loan_kes`,`interest_rate_annual`,`max_tenure_months`,`processing_fee_pct`,`is_active`,`created_at`,`updated_at`) VALUES
('KCB Bank Kenya','bank',100000,10000000,13.00,60,1.00,1,NOW(),NOW()),
('Equity Bank Kenya','bank',50000,5000000,14.50,48,1.50,1,NOW(),NOW()),
('NCBA Bank Kenya','bank',200000,15000000,12.50,60,1.00,1,NOW(),NOW()),
('Stanbic Bank Kenya','bank',500000,20000000,11.50,72,0.75,1,NOW(),NOW()),
('Mwalimu SACCO','sacco',20000,2000000,10.00,36,0.50,1,NOW(),NOW());

INSERT INTO `data_products` (`name`,`description`,`product_type`,`price_kes_monthly`,`is_active`,`created_at`,`updated_at`) VALUES
('Kenya Vehicle Pricing Index','Monthly rental price benchmarks by vehicle category','pricing_index',15000.00,1,NOW(),NOW()),
('East Africa Demand Analytics','Weekly demand forecasting data for vehicle rentals','demand_analytics',25000.00,1,NOW(),NOW()),
('Pan-Africa Fleet Market Report','Quarterly comprehensive fleet market report','market_report',50000.00,1,NOW(),NOW()),
('Raw Fleet Data API','Real-time anonymised fleet utilisation API feed','raw_api',75000.00,1,NOW(),NOW());

-- Default admin user (password: "password" — CHANGE IMMEDIATELY)
INSERT INTO `users` (`name`,`email`,`phone`,`password`,`role`,`kyc_status`,`country`,`is_active`,`email_verified_at`,`created_at`,`updated_at`) VALUES
('HustleKonnect Admin','admin@hustlekonnect.com','+254700000000','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','superadmin','approved','KE',1,NOW(),NOW(),NOW());

SET FOREIGN_KEY_CHECKS = 1;
-- End of hustlekonnect.sql
