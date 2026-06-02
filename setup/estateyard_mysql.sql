-- EstateYard MySQL Database
-- Generated: 2026-06-02 07:24:22
-- Tables: 34

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE IF NOT EXISTS \`estateyard\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE \`estateyard\`;

-- ----------------------------
-- Table: migrations
-- ----------------------------
DROP TABLE IF EXISTS \`migrations\`;
CREATE TABLE `migrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- ----------------------------
-- Table: users
-- ----------------------------
DROP TABLE IF EXISTS \`users\`;
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` DATETIME NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100) NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'tenant',
  `phone` VARCHAR(50) NULL,
  `avatar` VARCHAR(255) NULL,
  `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `verification_tier` VARCHAR(50) NOT NULL DEFAULT 'none',
  `referral_code` VARCHAR(255) NULL,
  `referred_by` INT UNSIGNED NULL,
  `bio` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `api_token` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  INDEX `users_referred_by_index` (`referred_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO \`users\` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`, `phone`, `avatar`, `is_verified`, `verification_tier`, `referral_code`, `referred_by`, `bio`, `is_active`, `api_token`) VALUES
(1, 'James Kamau', 'admin1@estateyard.co.ke', NULL, '$2y$12$Ii.gQcTDkwTyn07OWLSJJuRrShd9iYHR00HUU7ZSckhZYzS5uzQce', NULL, '2026-06-02 06:33:43', '2026-06-02 06:33:43', 'admin', '+254798948166', NULL, 1, 'elite', 'HHGHTS', NULL, 'Experienced admin professional based in Nairobi, Kenya.', 1, NULL),
(2, 'Grace Wanjiku', 'admin2@estateyard.co.ke', NULL, '$2y$12$R.44GkRwSZpbWmOpctTfb.fLh.jF3cf2.BY8zkbsV/loIdIpZyC7q', NULL, '2026-06-02 06:33:44', '2026-06-02 06:33:44', 'admin', '+254789702262', NULL, 1, 'professional', 'M1PEB2', NULL, 'Experienced admin professional based in Nairobi, Kenya.', 1, NULL),
(3, 'Peter Otieno', 'admin3@estateyard.co.ke', NULL, '$2y$12$vqPIVfMl.gk1NqeqPb1GkOF.9f0sjIHCblz.ProZ/ivAYWHrGJ6f6', NULL, '2026-06-02 06:33:44', '2026-06-02 06:33:44', 'admin', '+254729648504', NULL, 1, 'professional', 'WNSPBM', NULL, 'Experienced admin professional based in Nairobi, Kenya.', 1, NULL),
(4, 'Mary Njeri', 'admin4@estateyard.co.ke', NULL, '$2y$12$9a6JepfYbiX.WzTJJDx/puP2U9WKZnGv.EGSbx2wFsw4.XRe1uVf2', NULL, '2026-06-02 06:33:44', '2026-06-02 06:33:44', 'admin', '+254755900642', NULL, 0, 'none', 'D4KGNJ', NULL, 'Experienced admin professional based in Nairobi, Kenya.', 1, NULL),
(5, 'John Mwangi', 'admin5@estateyard.co.ke', NULL, '$2y$12$QXtNGhvJnw0HIa/TDbzbgu1mSJUzSSvNDmATEfHJkEEXEKiKt/aXK', NULL, '2026-06-02 06:33:44', '2026-06-02 06:33:44', 'admin', '+254786984930', NULL, 0, 'none', '5W8IHJ', NULL, 'Experienced admin professional based in Nairobi, Kenya.', 1, NULL),
(6, 'Alice Achieng', 'landlord1@estateyard.co.ke', NULL, '$2y$12$f0DF2oPvHeOrvMkg0b7BQ.nW9hZY1kIvCQqxZjijYtnNYxSwYCM1O', NULL, '2026-06-02 06:33:45', '2026-06-02 06:33:45', 'landlord', '+254746209757', NULL, 1, 'elite', 'QAAORP', NULL, 'Experienced landlord professional based in Nairobi, Kenya.', 1, NULL),
(7, 'Samuel Kipchoge', 'landlord2@estateyard.co.ke', NULL, '$2y$12$tuLv7G.Xxh4yOzgshTm3r.8JedPPydwXa9TGKk0Q7NTG0LrjgEDXC', NULL, '2026-06-02 06:33:45', '2026-06-02 06:33:45', 'landlord', '+254718673264', NULL, 1, 'professional', 'FU48NC', NULL, 'Experienced landlord professional based in Nairobi, Kenya.', 1, NULL),
(8, 'Ruth Wangari', 'landlord3@estateyard.co.ke', NULL, '$2y$12$aOzZulH.n8IUDwZ6me/zvuJVtQR.MSTCR4WDQBOoQH/SPt1obnGki', NULL, '2026-06-02 06:33:45', '2026-06-02 06:33:45', 'landlord', '+254788518796', NULL, 1, 'professional', 'KDPEO9', NULL, 'Experienced landlord professional based in Nairobi, Kenya.', 1, NULL),
(9, 'David Odhiambo', 'landlord4@estateyard.co.ke', NULL, '$2y$12$W.b0.L/hNYPa7YhexelbZeZ44LyytauUPHskmriGLeundlHPzpJHW', NULL, '2026-06-02 06:33:45', '2026-06-02 06:33:45', 'landlord', '+254782981329', NULL, 0, 'none', 'HE3CEK', NULL, 'Experienced landlord professional based in Nairobi, Kenya.', 1, NULL),
(10, 'Faith Chege', 'landlord5@estateyard.co.ke', NULL, '$2y$12$hUDm/qI7ARekXGDk1xX8Ye3EKzI.SyzNxX5klihNhZFv66zFI7SOG', NULL, '2026-06-02 06:33:46', '2026-06-02 06:33:46', 'landlord', '+254733295989', NULL, 0, 'none', 'FPVHAF', NULL, 'Experienced landlord professional based in Nairobi, Kenya.', 1, NULL),
(11, 'Michael Njoroge', 'broker_licensed1@estateyard.co.ke', NULL, '$2y$12$Rt9N1fJ9OIGhBViGjoT/deUZEoKZ9y5AfFhmknw1hUk5TRHBldrri', NULL, '2026-06-02 06:33:46', '2026-06-02 06:33:46', 'broker_licensed', '+254719536123', NULL, 1, 'elite', 'WAOW9V', NULL, 'Experienced broker_licensed professional based in Nairobi, Kenya.', 1, NULL),
(12, 'Esther Mutua', 'broker_licensed2@estateyard.co.ke', NULL, '$2y$12$hVf0YD9HOsVkUPETJaXGj.Pc.Gjl79SOsK915nh1WfmzSNLfXlv0i', NULL, '2026-06-02 06:33:46', '2026-06-02 06:33:46', 'broker_licensed', '+254718927178', NULL, 1, 'professional', 'RFBDQE', NULL, 'Experienced broker_licensed professional based in Nairobi, Kenya.', 1, NULL),
(13, 'Daniel Kimani', 'broker_licensed3@estateyard.co.ke', NULL, '$2y$12$rvQ2dthdxRhBFagMMbS3y.gZb2jsZwiCoPRgycQRBJOFA4NsK4l/G', NULL, '2026-06-02 06:33:46', '2026-06-02 06:33:46', 'broker_licensed', '+254729126877', NULL, 1, 'professional', 'GEMRLN', NULL, 'Experienced broker_licensed professional based in Nairobi, Kenya.', 1, NULL),
(14, 'Priscilla Waweru', 'broker_licensed4@estateyard.co.ke', NULL, '$2y$12$BdQVJyZlzQRHRR6mXsWdVeOnbbOuN8R1ZNWKdQlEI1pr7AgIslwFO', NULL, '2026-06-02 06:33:47', '2026-06-02 06:33:47', 'broker_licensed', '+254791554892', NULL, 0, 'none', 'PYATXE', NULL, 'Experienced broker_licensed professional based in Nairobi, Kenya.', 1, NULL),
(15, 'Joseph Mugo', 'broker_licensed5@estateyard.co.ke', NULL, '$2y$12$EEz1XvZZLdUzDXkXY8gQ/.or.jDmp4EQlU/Au4nwtWrzSu2ckS/j.', NULL, '2026-06-02 06:33:47', '2026-06-02 06:33:47', 'broker_licensed', '+254726924108', NULL, 0, 'none', 'CJQBYI', NULL, 'Experienced broker_licensed professional based in Nairobi, Kenya.', 1, NULL),
(16, 'Lydia Karanja', 'broker_unlicensed1@estateyard.co.ke', NULL, '$2y$12$3mm.DiX7DF75RV.mKDtJLeaoc8MjoZshzIa/OPCZ4s8/squ2HYsKq', NULL, '2026-06-02 06:33:47', '2026-06-02 06:33:47', 'broker_unlicensed', '+254720076445', NULL, 1, 'elite', 'CAMXKZ', NULL, 'Experienced broker_unlicensed professional based in Nairobi, Kenya.', 1, NULL),
(17, 'Stephen Gitau', 'broker_unlicensed2@estateyard.co.ke', NULL, '$2y$12$4e72LjELZFAiOmhdgGbNauYKrUNd1PTExjbdSV1L18s12N/frDLSe', NULL, '2026-06-02 06:33:47', '2026-06-02 06:33:47', 'broker_unlicensed', '+254721336755', NULL, 1, 'professional', 'WYTMFG', NULL, 'Experienced broker_unlicensed professional based in Nairobi, Kenya.', 1, NULL),
(18, 'Beatrice Mbugua', 'broker_unlicensed3@estateyard.co.ke', NULL, '$2y$12$F8zoTgDTfiUoALuh4VKvv.zk3KsiaHj2VlsnG0Ak7ShWIoDZZnmny', NULL, '2026-06-02 06:33:47', '2026-06-02 06:33:47', 'broker_unlicensed', '+254740424465', NULL, 1, 'professional', 'I205YO', NULL, 'Experienced broker_unlicensed professional based in Nairobi, Kenya.', 1, NULL),
(19, 'Charles Njuguna', 'broker_unlicensed4@estateyard.co.ke', NULL, '$2y$12$hEozq0/3wgg4uiSC2zm03.RA/B0kVa//nAdCU2XvMDF9S3hvNh1gq', NULL, '2026-06-02 06:33:48', '2026-06-02 06:33:48', 'broker_unlicensed', '+254732881485', NULL, 0, 'none', 'GKILQ0', NULL, 'Experienced broker_unlicensed professional based in Nairobi, Kenya.', 1, NULL),
(20, 'Mercy Maina', 'broker_unlicensed5@estateyard.co.ke', NULL, '$2y$12$owj9io8YPBD80gChT8eey.oNRcXrtgCCLEph/ovId.okEm9.ZPbaW', NULL, '2026-06-02 06:33:48', '2026-06-02 06:33:48', 'broker_unlicensed', '+254758434766', NULL, 0, 'none', '6WI55G', NULL, 'Experienced broker_unlicensed professional based in Nairobi, Kenya.', 1, NULL),
(21, 'Andrew Wakiumu', 'tenant1@estateyard.co.ke', NULL, '$2y$12$XWRjYwOa3apeRW48bLkHtuBJes915GdoQM7MJ22l0uUWEbQUDCH0K', NULL, '2026-06-02 06:33:48', '2026-06-02 06:33:48', 'tenant', '+254779090735', NULL, 1, 'elite', 'BKEXBN', NULL, 'Experienced tenant professional based in Nairobi, Kenya.', 1, NULL),
(22, 'Jane Wairimu', 'tenant2@estateyard.co.ke', NULL, '$2y$12$b2CzLFMJJjtsJkd5FmbPSup.t2bknWJdlSRljwTL0nssHKN95GYCm', NULL, '2026-06-02 06:33:48', '2026-06-02 06:33:48', 'tenant', '+254784218646', NULL, 1, 'professional', 'XCWGR9', NULL, 'Experienced tenant professional based in Nairobi, Kenya.', 1, NULL),
(23, 'Patrick Ndungu', 'tenant3@estateyard.co.ke', NULL, '$2y$12$0fTt.ughH0x9P8rKq13ri.A.pKj.p4iGHlLmnbCGzPhC7oFz5goC2', NULL, '2026-06-02 06:33:49', '2026-06-02 06:33:49', 'tenant', '+254775752234', NULL, 1, 'professional', 'DOXSRC', NULL, 'Experienced tenant professional based in Nairobi, Kenya.', 1, NULL),
(24, 'Susan Mureithi', 'tenant4@estateyard.co.ke', NULL, '$2y$12$s3bmDKvNpB75CNibO9Pxku0FnHVQMrHN4qsmbavfn0E6r45k8TIqy', NULL, '2026-06-02 06:33:49', '2026-06-02 06:33:49', 'tenant', '+254727894119', NULL, 0, 'none', 'NXZXQP', NULL, 'Experienced tenant professional based in Nairobi, Kenya.', 1, NULL),
(25, 'George Kibet', 'tenant5@estateyard.co.ke', NULL, '$2y$12$uYFgjuPefwX8vtrb1UssTOj2y.vV0tlv2Llm3py379tHIaCQm2rrG', NULL, '2026-06-02 06:33:49', '2026-06-02 06:33:49', 'tenant', '+254768789783', NULL, 0, 'none', 'CZT4PI', NULL, 'Experienced tenant professional based in Nairobi, Kenya.', 1, NULL),
(26, 'Agnes Chebet', 'developer1@estateyard.co.ke', NULL, '$2y$12$YN3vmaHz8eDiRZ.2AbtObOyGgt.LvnPrtYZptwwengeBKUn/b1AmK', NULL, '2026-06-02 06:33:49', '2026-06-02 06:33:49', 'developer', '+254716950864', NULL, 1, 'elite', 'PUQEDC', NULL, 'Experienced developer professional based in Nairobi, Kenya.', 1, NULL),
(27, 'Francis Gacheru', 'developer2@estateyard.co.ke', NULL, '$2y$12$Z7fZUYiIYFDJB7S8bYw7Ses36pKqqj9LdXtfDm8hBMGHtxFoQGgJS', NULL, '2026-06-02 06:33:50', '2026-06-02 06:33:50', 'developer', '+254777228373', NULL, 1, 'professional', 'MPNOA6', NULL, 'Experienced developer professional based in Nairobi, Kenya.', 1, NULL),
(28, 'Hannah Muthoni', 'developer3@estateyard.co.ke', NULL, '$2y$12$AaAEHg.nZ8S.v2omoCeUQ..Hi0l/apP2EjSLXM7ggV5I6fU8zfHu.', NULL, '2026-06-02 06:33:50', '2026-06-02 06:33:50', 'developer', '+254782940044', NULL, 1, 'professional', 'NCHIPW', NULL, 'Experienced developer professional based in Nairobi, Kenya.', 1, NULL),
(29, 'Robert Ngugi', 'developer4@estateyard.co.ke', NULL, '$2y$12$C5Eoa0qthIlalRa4zIWQTe/2evzP0yWfc6JutXm59U3gAOk/sb7ZW', NULL, '2026-06-02 06:33:50', '2026-06-02 06:33:50', 'developer', '+254742306153', NULL, 0, 'none', 'YZUSV7', NULL, 'Experienced developer professional based in Nairobi, Kenya.', 1, NULL),
(30, 'Catherine Waruguru', 'developer5@estateyard.co.ke', NULL, '$2y$12$z3a4E05L/T01xZCNx3zlouUhWs5oXrsGvFDcfm2wm5LkBn.geE9Vm', NULL, '2026-06-02 06:33:50', '2026-06-02 06:33:50', 'developer', '+254746821190', NULL, 0, 'none', '3XUZW0', NULL, 'Experienced developer professional based in Nairobi, Kenya.', 1, NULL),
(31, 'Eric Muriuki', 'valuer1@estateyard.co.ke', NULL, '$2y$12$1XFVilQmNglwM8/YPGv4AuFZ3rsi/Wl247X95hz/4fGVRvf64Bjk6', NULL, '2026-06-02 06:33:51', '2026-06-02 06:33:51', 'valuer', '+254787750039', NULL, 1, 'elite', 'DQN2F2', NULL, 'Experienced valuer professional based in Nairobi, Kenya.', 1, NULL),
(32, 'Eunice Chepkoech', 'valuer2@estateyard.co.ke', NULL, '$2y$12$7Y5GVsMrxH0bmcSXlKFRWec90yLOFHytTtWgGQFSax/pu7e9kosJ2', NULL, '2026-06-02 06:33:51', '2026-06-02 06:33:51', 'valuer', '+254738164942', NULL, 1, 'professional', 'OHFVLI', NULL, 'Experienced valuer professional based in Nairobi, Kenya.', 1, NULL),
(33, 'Vincent Karimi', 'valuer3@estateyard.co.ke', NULL, '$2y$12$Rs0htsCHgxM.3oiqYJcJeOaZ8S15J/Q8RmsATx9olYoXjhxiRV7Gi', NULL, '2026-06-02 06:33:51', '2026-06-02 06:33:51', 'valuer', '+254786424377', NULL, 1, 'professional', '5HBAB4', NULL, 'Experienced valuer professional based in Nairobi, Kenya.', 1, NULL),
(34, 'Tabitha Waithira', 'valuer4@estateyard.co.ke', NULL, '$2y$12$wxtkEHgi/vLakItZQSWMD.5Pklq0CAIUEHO9T7raylv0t9cOoA7WO', NULL, '2026-06-02 06:33:51', '2026-06-02 06:33:51', 'valuer', '+254775267209', NULL, 0, 'none', 'RU7YTW', NULL, 'Experienced valuer professional based in Nairobi, Kenya.', 1, NULL),
(35, 'Kevin Njeru', 'valuer5@estateyard.co.ke', NULL, '$2y$12$cU6mJXr8r476ZIVA5CAnkeKm3DQo6KSQVxz2kQwXSotj07HbkVyIS', NULL, '2026-06-02 06:33:52', '2026-06-02 06:33:52', 'valuer', '+254749135463', NULL, 0, 'none', 'HW3HTF', NULL, 'Experienced valuer professional based in Nairobi, Kenya.', 1, NULL),
(36, 'Rose Auma', 'surveyor1@estateyard.co.ke', NULL, '$2y$12$WgFchG34AMsFCRgBViQD4u3kJGXZZtoVuszNQM9UmNJctpQiq/hcO', NULL, '2026-06-02 06:33:52', '2026-06-02 06:33:52', 'surveyor', '+254788521040', NULL, 1, 'elite', 'RO1JA3', NULL, 'Experienced surveyor professional based in Nairobi, Kenya.', 1, NULL),
(37, 'Brian Kamande', 'surveyor2@estateyard.co.ke', NULL, '$2y$12$oBlNtnuJchuQYxHvfqwy9uvqPdgzc.OOlECubqlMsVx7RJiVTf1cC', NULL, '2026-06-02 06:33:52', '2026-06-02 06:33:52', 'surveyor', '+254759948923', NULL, 1, 'professional', 'P2IOHQ', NULL, 'Experienced surveyor professional based in Nairobi, Kenya.', 1, NULL),
(38, 'Irene Waceke', 'surveyor3@estateyard.co.ke', NULL, '$2y$12$IZf9hpQkXpmB7LbAdb68ku0MIKCDqD91k1ZH2aii11g3pCItgetui', NULL, '2026-06-02 06:33:52', '2026-06-02 06:33:52', 'surveyor', '+254784490993', NULL, 1, 'professional', 'TGSDAJ', NULL, 'Experienced surveyor professional based in Nairobi, Kenya.', 1, NULL),
(39, 'Paul Kiragu', 'surveyor4@estateyard.co.ke', NULL, '$2y$12$LhA3nXDR0hyuLDgP8Fvgee9CD0egD3RNxfJYpgYJbYwKarMoEhDSK', NULL, '2026-06-02 06:33:52', '2026-06-02 06:33:52', 'surveyor', '+254775988666', NULL, 0, 'none', 'JJTBTM', NULL, 'Experienced surveyor professional based in Nairobi, Kenya.', 1, NULL),
(40, 'Lucy Nyambura', 'surveyor5@estateyard.co.ke', NULL, '$2y$12$GFaqp9n2O/GRzxfaTxj2oeOoc7vZtCjM/FgC7jtKxLRrsnYBmkj4a', NULL, '2026-06-02 06:33:53', '2026-06-02 06:33:53', 'surveyor', '+254748037336', NULL, 0, 'none', 'KYTK8Y', NULL, 'Experienced surveyor professional based in Nairobi, Kenya.', 1, NULL),
(41, 'Simon Ochieng', 'auctioneer1@estateyard.co.ke', NULL, '$2y$12$.k9E9yv54TlbcHJhI/oooeywlLnYfSE6tD2ZLmOnZwFnKbXFOOOXu', NULL, '2026-06-02 06:33:53', '2026-06-02 06:33:53', 'auctioneer', '+254787621045', NULL, 1, 'elite', 'ULGJWL', NULL, 'Experienced auctioneer professional based in Nairobi, Kenya.', 1, NULL),
(42, 'Doris Wekesa', 'auctioneer2@estateyard.co.ke', NULL, '$2y$12$XTVYu7BNoHkR62RDFoEwBu0ifoWvZUtFBdi1tJo0fG6J568nSaYI.', NULL, '2026-06-02 06:33:53', '2026-06-02 06:33:53', 'auctioneer', '+254751624318', NULL, 1, 'professional', 'PZTZKY', NULL, 'Experienced auctioneer professional based in Nairobi, Kenya.', 1, NULL),
(43, 'Timothy Gichuki', 'auctioneer3@estateyard.co.ke', NULL, '$2y$12$.MrQDJo8LJ3iwGyr0bziUebr.TaD0Ai7BQxnSck9cm/QqdwziT1J.', NULL, '2026-06-02 06:33:53', '2026-06-02 06:33:53', 'auctioneer', '+254736396656', NULL, 1, 'professional', 'QYDQ5C', NULL, 'Experienced auctioneer professional based in Nairobi, Kenya.', 1, NULL),
(44, 'Winnie Mwende', 'auctioneer4@estateyard.co.ke', NULL, '$2y$12$c9xXTjBkDb7YlXJLnnYuwOI4ka3hHRifxlqKBQe6ApaJnj7JomVY2', NULL, '2026-06-02 06:33:54', '2026-06-02 06:33:54', 'auctioneer', '+254776564935', NULL, 0, 'none', 'J74SPW', NULL, 'Experienced auctioneer professional based in Nairobi, Kenya.', 1, NULL),
(45, 'Moses Kirui', 'auctioneer5@estateyard.co.ke', NULL, '$2y$12$VQTBG2V.1XNBx9YuJ9IEQOm7JBNnx7kJGkjZb8GnL1AG.5D9A3cXe', NULL, '2026-06-02 06:33:54', '2026-06-02 06:33:54', 'auctioneer', '+254718425872', NULL, 0, 'none', 'LRINQH', NULL, 'Experienced auctioneer professional based in Nairobi, Kenya.', 1, NULL),
(46, 'Gladys Adhiambo', 'investor1@estateyard.co.ke', NULL, '$2y$12$gOa66otn3Ge3OX.LWEH/1OMAwTYL.c3RiDrlYv.6EpdaRdnVaxoWS', NULL, '2026-06-02 06:33:54', '2026-06-02 06:33:54', 'investor', '+254727678143', NULL, 1, 'elite', 'Q6FS52', NULL, 'Experienced investor professional based in Nairobi, Kenya.', 1, NULL),
(47, 'Isaac Muigai', 'investor2@estateyard.co.ke', NULL, '$2y$12$uNCLy4W05vjhWPWgH/E.6uTtOkdtaic8xwCEYDBtInkjspf/E9Lta', NULL, '2026-06-02 06:33:54', '2026-06-02 06:33:54', 'investor', '+254747615149', NULL, 1, 'professional', 'TKWPFV', NULL, 'Experienced investor professional based in Nairobi, Kenya.', 1, NULL),
(48, 'Florence Wanjiru', 'investor3@estateyard.co.ke', NULL, '$2y$12$fdR4B5yhKc.RF6bhB6U7Z.V6jDxo9NQZ4WLoigsNobHewhPeEWOb.', NULL, '2026-06-02 06:33:55', '2026-06-02 06:33:55', 'investor', '+254749890156', NULL, 1, 'professional', 'ARHSTR', NULL, 'Experienced investor professional based in Nairobi, Kenya.', 1, NULL),
(49, 'Solomon Maina', 'investor4@estateyard.co.ke', NULL, '$2y$12$KD8p.PzghZPE5JrOZOnieuB1DaLbUywTxpIA.yXXIyG6S/v8imbTK', NULL, '2026-06-02 06:33:55', '2026-06-02 06:33:55', 'investor', '+254711388198', NULL, 0, 'none', 'QJ4KNG', NULL, 'Experienced investor professional based in Nairobi, Kenya.', 1, NULL),
(50, 'Helen Chepchumba', 'investor5@estateyard.co.ke', NULL, '$2y$12$MuCn8ADfPrT/ShU187iZP.PJ2M0uF2aQgLCz2E4XLSfPIksopxHGK', NULL, '2026-06-02 06:33:55', '2026-06-02 06:33:55', 'investor', '+254791544670', NULL, 0, 'none', 'G6TFMW', NULL, 'Experienced investor professional based in Nairobi, Kenya.', 1, NULL),
(51, 'Philip Wainaina', 'corporate1@estateyard.co.ke', NULL, '$2y$12$hkyuyggKihdRirTZngRS6O9TFt0aNUXdCNJujvlfv4681sSEhyzBq', NULL, '2026-06-02 06:33:55', '2026-06-02 06:33:55', 'corporate', '+254795202727', NULL, 1, 'elite', 'YVVBR6', NULL, 'Experienced corporate professional based in Nairobi, Kenya.', 1, NULL),
(52, 'Judith Kananu', 'corporate2@estateyard.co.ke', NULL, '$2y$12$BiXRAOKrnchG.DhOQqXQUekTNUJCq8lOvEcAk/rRHpohxKZOPqT7e', NULL, '2026-06-02 06:33:56', '2026-06-02 06:33:56', 'corporate', '+254751580895', NULL, 1, 'professional', 'DEO8IJ', NULL, 'Experienced corporate professional based in Nairobi, Kenya.', 1, NULL),
(53, 'Geoffrey Mutisya', 'corporate3@estateyard.co.ke', NULL, '$2y$12$bBEX0mb.keqXw8QFkwbrLuA4yNy/2sNlkghToNDRoYBjcyQtWx8GW', NULL, '2026-06-02 06:33:56', '2026-06-02 06:33:56', 'corporate', '+254711060537', NULL, 1, 'professional', '6YUH7N', NULL, 'Experienced corporate professional based in Nairobi, Kenya.', 1, NULL),
(54, 'Carolyne Nduta', 'corporate4@estateyard.co.ke', NULL, '$2y$12$sQp2f90cyoE6bwiF4TE8guOIyhuNsINz3G4GLJGTM4IhIAs0LP1ju', NULL, '2026-06-02 06:33:56', '2026-06-02 06:33:56', 'corporate', '+254783706740', NULL, 0, 'none', 'KB6EEB', NULL, 'Experienced corporate professional based in Nairobi, Kenya.', 1, NULL),
(55, 'Mark Musyoka', 'corporate5@estateyard.co.ke', NULL, '$2y$12$tT9tZDexRGxGc3SeWbBXbuuBSbuSeMue8dS8TK4yGprsv1JUQkVkC', NULL, '2026-06-02 06:33:56', '2026-06-02 06:33:56', 'corporate', '+254744304946', NULL, 0, 'none', 'WQHL9M', NULL, 'Experienced corporate professional based in Nairobi, Kenya.', 1, NULL),
(56, 'Purity Nyawira', 'property_manager1@estateyard.co.ke', NULL, '$2y$12$ChAAxiSusY2FGRly5GYdBuS.W.XU60.3n7.zH86FlIeMHHIVwYHK6', NULL, '2026-06-02 06:33:56', '2026-06-02 06:33:56', 'property_manager', '+254712011220', NULL, 1, 'elite', 'YYGQFI', NULL, 'Experienced property_manager professional based in Nairobi, Kenya.', 1, NULL),
(57, 'Anthony Njomo', 'property_manager2@estateyard.co.ke', NULL, '$2y$12$y2C4QOXYRQSBfj7djN/GHeBFBYWxY4T.5GR1XHYRva9V9jmaFxyXK', NULL, '2026-06-02 06:33:57', '2026-06-02 06:33:57', 'property_manager', '+254731097436', NULL, 1, 'professional', 'LVWZOJ', NULL, 'Experienced property_manager professional based in Nairobi, Kenya.', 1, NULL),
(58, 'Martha Wambua', 'property_manager3@estateyard.co.ke', NULL, '$2y$12$n3ympou2xi8Z/JGBYrS5uOuVhWJtp8ZgyFcQU9YQDhsq7EEIGJxQC', NULL, '2026-06-02 06:33:57', '2026-06-02 06:33:57', 'property_manager', '+254722861347', NULL, 1, 'professional', 'KVCMF0', NULL, 'Experienced property_manager professional based in Nairobi, Kenya.', 1, NULL),
(59, 'Henry Oloo', 'property_manager4@estateyard.co.ke', NULL, '$2y$12$0r2vTiGoPy8rxzxDuzlqQ.8RqjED/LDrmCE7Plw4N/IDMqQi2KZcO', NULL, '2026-06-02 06:33:57', '2026-06-02 06:33:57', 'property_manager', '+254744907171', NULL, 0, 'none', 'UVWOE9', NULL, 'Experienced property_manager professional based in Nairobi, Kenya.', 1, NULL),
(60, 'Zipporah Mutheu', 'property_manager5@estateyard.co.ke', NULL, '$2y$12$tQWKpGzEkpFC9id7hsYPDeb1fdCV0MBRWFhOHBmop3oJMG1jjirqO', NULL, '2026-06-02 06:33:57', '2026-06-02 06:33:57', 'property_manager', '+254768908837', NULL, 0, 'none', 'ZHYFV6', NULL, 'Experienced property_manager professional based in Nairobi, Kenya.', 1, NULL),
(61, 'Gabriel Macharia', 'finance1@estateyard.co.ke', NULL, '$2y$12$1qqRS5lSwHDAhZJ79etr0u9s.pCJYmlir1q/ernM1UcM859QPcRDK', NULL, '2026-06-02 06:33:58', '2026-06-02 06:33:58', 'finance', '+254780568437', NULL, 1, 'elite', 'DNXED0', NULL, 'Experienced finance professional based in Nairobi, Kenya.', 1, NULL),
(62, 'Diana Kerubo', 'finance2@estateyard.co.ke', NULL, '$2y$12$YceUQTfuS5ko4jYXALVQGe3Q7vode8bA4oWvAHPulB/9oo7Einhr2', NULL, '2026-06-02 06:33:58', '2026-06-02 06:33:58', 'finance', '+254774391800', NULL, 1, 'professional', 'DRTPRI', NULL, 'Experienced finance professional based in Nairobi, Kenya.', 1, NULL),
(63, 'Elijah Muchangi', 'finance3@estateyard.co.ke', NULL, '$2y$12$fuM4sCpUKxfCASriCHrVVOt.lyMyRVHR.uT0K3pC8ZrH9da1wQbiG', NULL, '2026-06-02 06:33:58', '2026-06-02 06:33:58', 'finance', '+254745811223', NULL, 1, 'professional', 'VMZEPD', NULL, 'Experienced finance professional based in Nairobi, Kenya.', 1, NULL),
(64, 'Vivian Njoki', 'finance4@estateyard.co.ke', NULL, '$2y$12$V2MjWxxPYtBDxRTp3IQaOOM0GnY/nrFIk8HjoMAOrpftWVIbFtHQm', NULL, '2026-06-02 06:33:58', '2026-06-02 06:33:58', 'finance', '+254766663117', NULL, 0, 'none', 'ZRB8CY', NULL, 'Experienced finance professional based in Nairobi, Kenya.', 1, NULL),
(65, 'Caleb Mwiti', 'finance5@estateyard.co.ke', NULL, '$2y$12$h7qW1hMhmxl9zitH1AH87.hPspULslEyMrjRyejp4pSncxty7y5M.', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59', 'finance', '+254724903906', NULL, 0, 'none', 'VQO5AW', NULL, 'Experienced finance professional based in Nairobi, Kenya.', 1, NULL),
(66, 'Super Admin', 'admin@estateyard.co.ke', NULL, '$2y$12$Gi2WhVTll4YarKmTKfyZOuNUyiwYLsPwIo8ONSGoYI9CuzNWqPfZm', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59', 'admin', NULL, NULL, 1, 'elite', 'YVXZCA', NULL, NULL, 1, NULL);

-- ----------------------------
-- Table: password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS \`password_reset_tokens\`;
CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------
-- Table: sessions
-- ----------------------------
DROP TABLE IF EXISTS \`sessions\`;
CREATE TABLE `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` INT UNSIGNED NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `sessions_user_id_index` (`user_id`),
  INDEX `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------
-- Table: cache
-- ----------------------------
DROP TABLE IF EXISTS \`cache\`;
CREATE TABLE `cache` (
  `key` VARCHAR(255) NOT NULL,
  `value` MEDIUMTEXT NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------
-- Table: cache_locks
-- ----------------------------
DROP TABLE IF EXISTS \`cache_locks\`;
CREATE TABLE `cache_locks` (
  `key` VARCHAR(255) NOT NULL,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------
-- Table: jobs
-- ----------------------------
DROP TABLE IF EXISTS \`jobs\`;
CREATE TABLE `jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` VARCHAR(255) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL,
  `reserved_at` INT UNSIGNED NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------
-- Table: job_batches
-- ----------------------------
DROP TABLE IF EXISTS \`job_batches\`;
CREATE TABLE `job_batches` (
  `id` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `total_jobs` INT NOT NULL,
  `pending_jobs` INT NOT NULL,
  `failed_jobs` INT NOT NULL,
  `failed_job_ids` LONGTEXT NOT NULL,
  `options` MEDIUMTEXT NULL,
  `cancelled_at` INT NULL,
  `created_at` INT NOT NULL,
  `finished_at` INT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------
-- Table: failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS \`failed_jobs\`;
CREATE TABLE `failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(255) NOT NULL,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `exception` LONGTEXT NOT NULL,
  `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------
-- Table: personal_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS \`personal_access_tokens\`;
CREATE TABLE `personal_access_tokens` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` VARCHAR(255) NOT NULL,
  `tokenable_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `token` VARCHAR(64) NOT NULL,
  `abilities` TEXT NULL,
  `last_used_at` TIMESTAMP NULL,
  `expires_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  INDEX `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------
-- Table: properties
-- ----------------------------
DROP TABLE IF EXISTS \`properties\`;
CREATE TABLE `properties` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `listing_type` VARCHAR(50) NOT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'active',
  `price` DECIMAL(15,2) NOT NULL,
  `price_period` VARCHAR(20) NULL,
  `bedrooms` INT NULL,
  `bathrooms` INT NULL,
  `area_sqft` DECIMAL(15,2) NULL,
  `floors` INT NULL,
  `year_built` INT NULL,
  `county` VARCHAR(255) NOT NULL,
  `constituency` VARCHAR(255) NULL,
  `location` VARCHAR(255) NOT NULL,
  `latitude` DECIMAL(10,7) NULL,
  `longitude` DECIMAL(10,7) NULL,
  `amenities` TEXT NULL,
  `images` TEXT NULL,
  `video_url` VARCHAR(255) NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `view_count` INT NOT NULL DEFAULT 0,
  `save_count` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `deleted_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `properties_slug_unique` (`slug`),
  INDEX `properties_user_id_index` (`user_id`),
  INDEX `properties_type_index` (`type`),
  INDEX `properties_listing_type_index` (`listing_type`),
  INDEX `properties_status_index` (`status`),
  INDEX `properties_county_index` (`county`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO \`properties\` (`id`, `user_id`, `title`, `slug`, `description`, `type`, `listing_type`, `status`, `price`, `price_period`, `bedrooms`, `bathrooms`, `area_sqft`, `floors`, `year_built`, `county`, `constituency`, `location`, `latitude`, `longitude`, `amenities`, `images`, `video_url`, `is_featured`, `view_count`, `save_count`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 6, '5-Bedroom Villa with Pool in Karen', '5-bedroom-villa-with-pool-in-karen-1', 'Beautiful house located in Karen, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'sale', 'active', 30000000, NULL, 2, 2, 4409, 2, 2014, 'Nairobi', 'Karen', 'Karen, Nairobi', -1.2533, 36.8438, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=0a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=0b"]', NULL, 1, 431, 35, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(2, 7, 'Modern 3-Bedroom Apartment in Kilimani', 'modern-3-bedroom-apartment-in-kilimani-2', 'Beautiful apartment located in Westlands, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'rent', 'active', 187000, 'year', 4, 2, 3736, 4, 2000, 'Nairobi', 'Westlands', 'Westlands, Nairobi', -1.2885, 36.8299, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=1a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=1b"]', NULL, 1, 268, 28, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(3, 8, '0.5 Acre Land for Sale in Runda', '05-acre-land-for-sale-in-runda-3', 'Beautiful land located in Kilimani, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'land', 'sale', 'active', 41000000, NULL, NULL, NULL, 1359, NULL, 2007, 'Nairobi', 'Kilimani', 'Kilimani, Nairobi', -1.3404, 36.7942, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=2a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=2b"]', NULL, 1, 68, 22, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(4, 9, 'Commercial Office Space in Upper Hill', 'commercial-office-space-in-upper-hill-4', 'Beautiful commercial located in Lavington, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'commercial', 'rent', 'active', 238000, 'year', NULL, NULL, 2943, 1, 2011, 'Kiambu', 'Lavington', 'Lavington, Kiambu', -1.287, 36.774, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=3a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=3b"]', NULL, 1, 366, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(5, 10, 'Luxury Penthouse in Westlands', 'luxury-penthouse-in-westlands-5', 'Beautiful villa located in Runda, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'villa', 'sale', 'active', 112000000, NULL, 2, 3, 1962, 2, 2004, 'Machakos', 'Runda', 'Runda, Machakos', -1.2514, 36.8284, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=4a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=4b"]', NULL, 1, 35, 5, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(6, 26, '4-Bedroom Townhouse in Lavington', '4-bedroom-townhouse-in-lavington-6', 'Beautiful office located in Muthaiga, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'office', 'sale', 'active', 36000000, NULL, NULL, NULL, 1610, 1, 2007, 'Nairobi', 'Muthaiga', 'Muthaiga, Nairobi', -1.2865, 36.8401, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=5a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=5b"]', NULL, 1, 392, 6, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(7, 27, 'Studio Apartment in Parklands', 'studio-apartment-in-parklands-7', 'Beautiful house located in Upper Hill, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'rent', 'active', 56000, 'month', 1, 4, 1896, 4, 2017, 'Nairobi', 'Upper Hill', 'Upper Hill, Nairobi', -1.282, 36.7895, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=6a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=6b"]', NULL, 1, 50, 34, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(8, 28, '2-Bedroom Flat in Hurlingham', '2-bedroom-flat-in-hurlingham-8', 'Beautiful apartment located in Parklands, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'sale', 'active', 90000000, NULL, 3, 1, 3622, 3, 2022, 'Nairobi', 'Parklands', 'Parklands, Nairobi', -1.3151, 36.8322, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=7a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=7b"]', NULL, 1, 169, 13, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(9, 29, 'Commercial Building in Upper Hill', 'commercial-building-in-upper-hill-9', 'Beautiful land located in Spring Valley, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'land', 'rent', 'active', 115000, 'month', NULL, NULL, 711, NULL, 2014, 'Kiambu', 'Spring Valley', 'Spring Valley, Kiambu', -1.3221, 36.7854, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=8a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=8b"]', NULL, 0, 68, 37, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(10, 30, '3-Bedroom Maisonette in South C', '3-bedroom-maisonette-in-south-c-10', 'Beautiful commercial located in Gigiri, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'commercial', 'sale', 'active', 117000000, NULL, NULL, NULL, 967, 2, 2003, 'Machakos', 'Gigiri', 'Gigiri, Machakos', -1.2421, 36.7806, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=9a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=9b"]', NULL, 0, 125, 38, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(11, 51, 'Prime Land in Muthaiga 1 Acre', 'prime-land-in-muthaiga-1-acre-11', 'Beautiful villa located in Langata, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'villa', 'sale', 'active', 67000000, NULL, 6, 3, 922, 2, 2012, 'Nairobi', 'Langata', 'Langata, Nairobi', -1.2867, 36.8118, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=10a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=10b"]', NULL, 0, 381, 49, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(12, 52, 'Serviced Apartment in Gigiri', 'serviced-apartment-in-gigiri-12', 'Beautiful office located in South C, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'office', 'rent', 'active', 272000, 'year', NULL, NULL, 3664, 2, 2017, 'Nairobi', 'South C', 'South C, Nairobi', -1.3105, 36.7797, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=11a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=11b"]', NULL, 0, 241, 40, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(13, 53, 'Bungalow in Langata', 'bungalow-in-langata-13', 'Beautiful house located in Hurlingham, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'sale', 'active', 148000000, NULL, 1, 1, 1542, 3, 2015, 'Nairobi', 'Hurlingham', 'Hurlingham, Nairobi', -1.2989, 36.8713, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=12a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=12b"]', NULL, 0, 496, 7, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(14, 54, 'Modern Villa in Spring Valley', 'modern-villa-in-spring-valley-14', 'Beautiful apartment located in Riverside, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'rent', 'active', 56000, 'year', 6, 1, 2904, 4, 2019, 'Kiambu', 'Riverside', 'Riverside, Kiambu', -1.2526, 36.8296, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=13a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=13b"]', NULL, 0, 415, 17, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(15, 55, 'Office Suite in Riverside Drive', 'office-suite-in-riverside-drive-15', 'Beautiful land located in Milimani, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'land', 'sale', 'active', 36000000, NULL, NULL, NULL, 4661, NULL, 2018, 'Machakos', 'Milimani', 'Milimani, Machakos', -1.2511, 36.7884, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=14a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=14b"]', NULL, 0, 465, 1, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(16, 6, '6-Bedroom Mansion in Muthaiga', '6-bedroom-mansion-in-muthaiga-16', 'Beautiful commercial located in Kitisuru, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'commercial', 'sale', 'active', 121000000, NULL, NULL, NULL, 837, 3, 2020, 'Nairobi', 'Kitisuru', 'Kitisuru, Nairobi', -1.3329, 36.7836, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=15a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=15b"]', NULL, 0, 156, 40, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(17, 7, '1-Bedroom Apartment in Kileleshwa', '1-bedroom-apartment-in-kileleshwa-17', 'Beautiful villa located in Rosslyn, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'villa', 'rent', 'active', 177000, 'month', 5, 4, 4705, 1, 2005, 'Nairobi', 'Rosslyn', 'Rosslyn, Nairobi', -1.2945, 36.8188, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=16a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=16b"]', NULL, 0, 12, 43, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(18, 8, 'Retail Space in Westlands CBD', 'retail-space-in-westlands-cbd-18', 'Beautiful office located in Loresho, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'office', 'sale', 'active', 118000000, NULL, NULL, NULL, 2403, 3, 2021, 'Nairobi', 'Loresho', 'Loresho, Nairobi', -1.2721, 36.7864, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=17a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=17b"]', NULL, 0, 155, 24, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(19, 9, 'Semi-Detached House in Loresho', 'semi-detached-house-in-loresho-19', 'Beautiful house located in Brookside, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'rent', 'active', 221000, 'month', 4, 5, 863, 2, 2022, 'Kiambu', 'Brookside', 'Brookside, Kiambu', -1.3251, 36.7859, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=18a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=18b"]', NULL, 0, 37, 10, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(20, 10, '4-Bedroom Apartment in Brookside', '4-bedroom-apartment-in-brookside-20', 'Beautiful apartment located in Kileleshwa, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'sale', 'active', 40000000, NULL, 3, 2, 4085, 3, 2000, 'Machakos', 'Kileleshwa', 'Kileleshwa, Machakos', -1.3154, 36.8621, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=19a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=19b"]', NULL, 0, 12, 25, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(21, 26, 'Warehouse Space in Industrial Area', 'warehouse-space-in-industrial-area-21', 'Beautiful land located in Karen, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'land', 'sale', 'active', 3000000, NULL, NULL, NULL, 537, NULL, 2016, 'Nairobi', 'Karen', 'Karen, Nairobi', -1.2528, 36.8267, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=20a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=20b"]', NULL, 0, 323, 33, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(22, 27, 'Beach Plot in Nyali Mombasa', 'beach-plot-in-nyali-mombasa-22', 'Beautiful commercial located in Westlands, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'commercial', 'rent', 'active', 296000, 'year', NULL, NULL, 3357, 1, 2011, 'Nairobi', 'Westlands', 'Westlands, Nairobi', -1.2801, 36.8486, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=21a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=21b"]', NULL, 0, 152, 20, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(23, 28, '3-Bedroom Townhouse in Kitisuru', '3-bedroom-townhouse-in-kitisuru-23', 'Beautiful villa located in Kilimani, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'villa', 'sale', 'active', 24000000, NULL, 2, 1, 1494, 3, 2013, 'Nairobi', 'Kilimani', 'Kilimani, Nairobi', -1.2689, 36.8528, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=22a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=22b"]', NULL, 0, 386, 5, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(24, 29, 'Prime Corner Plot in Karen', 'prime-corner-plot-in-karen-24', 'Beautiful office located in Lavington, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'office', 'rent', 'active', 160000, 'year', NULL, NULL, 3214, 1, 2020, 'Kiambu', 'Lavington', 'Lavington, Kiambu', -1.2966, 36.7901, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=23a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=23b"]', NULL, 0, 272, 35, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(25, 30, 'Grade A Office in Upper Hill Towers', 'grade-a-office-in-upper-hill-towers-25', 'Beautiful house located in Runda, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'sale', 'active', 86000000, NULL, 6, 2, 4391, 4, 2022, 'Machakos', 'Runda', 'Runda, Machakos', -1.2974, 36.7897, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=24a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=24b"]', NULL, 0, 15, 35, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(26, 51, '2-Bedroom Apartment Gigiri UN Area', '2-bedroom-apartment-gigiri-un-area-26', 'Beautiful apartment located in Muthaiga, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'sale', 'active', 43000000, NULL, 4, 3, 3464, 2, 2015, 'Nairobi', 'Muthaiga', 'Muthaiga, Nairobi', -1.2615, 36.8606, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=25a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=25b"]', NULL, 0, 115, 19, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(27, 52, 'Detached Villa in Runda Estate', 'detached-villa-in-runda-estate-27', 'Beautiful land located in Upper Hill, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'land', 'rent', 'active', 188000, 'month', NULL, NULL, 2939, NULL, 2020, 'Nairobi', 'Upper Hill', 'Upper Hill, Nairobi', -1.323, 36.8567, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=26a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=26b"]', NULL, 0, 482, 12, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(28, 53, 'Commercial Plot Thika Road', 'commercial-plot-thika-road-28', 'Beautiful commercial located in Parklands, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'commercial', 'sale', 'active', 117000000, NULL, NULL, NULL, 733, 3, 2002, 'Nairobi', 'Parklands', 'Parklands, Nairobi', -1.3334, 36.7916, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=27a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=27b"]', NULL, 0, 222, 4, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(29, 54, '5-Bedroom Home in Rosslyn', '5-bedroom-home-in-rosslyn-29', 'Beautiful villa located in Spring Valley, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'villa', 'rent', 'active', 192000, 'month', 6, 1, 1976, 3, 2005, 'Kiambu', 'Spring Valley', 'Spring Valley, Kiambu', -1.2762, 36.8063, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=28a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=28b"]', NULL, 0, 153, 30, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(30, 55, 'Furnished 2BR Apartment Kilimani', 'furnished-2br-apartment-kilimani-30', 'Beautiful office located in Gigiri, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'office', 'sale', 'active', 56000000, NULL, NULL, NULL, 2783, 4, 2004, 'Machakos', 'Gigiri', 'Gigiri, Machakos', -1.2826, 36.8164, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=29a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=29b"]', NULL, 0, 476, 40, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(31, 6, '1-Acre Farm in Limuru', '1-acre-farm-in-limuru-31', 'Beautiful house located in Langata, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'sale', 'active', 109000000, NULL, 4, 5, 1139, 2, 2014, 'Nairobi', 'Langata', 'Langata, Nairobi', -1.3071, 36.7961, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=30a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=30b"]', NULL, 0, 128, 40, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(32, 7, 'Modern Duplex in Lavington', 'modern-duplex-in-lavington-32', 'Beautiful apartment located in South C, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'rent', 'active', 187000, 'year', 2, 3, 3679, 1, 2002, 'Nairobi', 'South C', 'South C, Nairobi', -1.3273, 36.8528, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=31a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=31b"]', NULL, 0, 273, 50, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(33, 8, 'Hostel Investment in Westlands', 'hostel-investment-in-westlands-33', 'Beautiful land located in Hurlingham, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'land', 'sale', 'active', 50000000, NULL, NULL, NULL, 1545, NULL, 2008, 'Nairobi', 'Hurlingham', 'Hurlingham, Nairobi', -1.3155, 36.798, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=32a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=32b"]', NULL, 0, 168, 33, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(34, 9, 'Prime Land in Ruiru 2 Acres', 'prime-land-in-ruiru-2-acres-34', 'Beautiful commercial located in Riverside, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'commercial', 'rent', 'active', 226000, 'year', NULL, NULL, 1928, 3, 2000, 'Kiambu', 'Riverside', 'Riverside, Kiambu', -1.316, 36.8109, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=33a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=33b"]', NULL, 0, 497, 23, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(35, 10, '3-Bedroom House in South B', '3-bedroom-house-in-south-b-35', 'Beautiful villa located in Milimani, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'villa', 'sale', 'active', 62000000, NULL, 6, 4, 2642, 2, 2023, 'Machakos', 'Milimani', 'Milimani, Machakos', -1.285, 36.8215, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=34a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=34b"]', NULL, 0, 214, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(36, 26, 'Office Block in Upper Hill', 'office-block-in-upper-hill-36', 'Beautiful office located in Kitisuru, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'office', 'sale', 'active', 68000000, NULL, NULL, NULL, 3406, 3, 2001, 'Nairobi', 'Kitisuru', 'Kitisuru, Nairobi', -1.3054, 36.7939, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=35a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=35b"]', NULL, 0, 465, 24, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(37, 27, 'Luxury Maisonette in Karen', 'luxury-maisonette-in-karen-37', 'Beautiful house located in Rosslyn, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'rent', 'active', 135000, 'month', 6, 1, 1103, 3, 2017, 'Nairobi', 'Rosslyn', 'Rosslyn, Nairobi', -1.2993, 36.7821, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=36a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=36b"]', NULL, 0, 230, 16, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(38, 28, 'Affordable Apartment in Rongai', 'affordable-apartment-in-rongai-38', 'Beautiful apartment located in Loresho, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'sale', 'active', 103000000, NULL, 5, 3, 2859, 4, 2020, 'Nairobi', 'Loresho', 'Loresho, Nairobi', -1.2495, 36.7843, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=37a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=37b"]', NULL, 0, 121, 41, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(39, 29, '4-Bedroom Bungalow in Athi River', '4-bedroom-bungalow-in-athi-river-39', 'Beautiful land located in Brookside, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'land', 'rent', 'active', 96000, 'month', NULL, NULL, 4721, NULL, 2006, 'Kiambu', 'Brookside', 'Brookside, Kiambu', -1.2972, 36.8581, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=38a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=38b"]', NULL, 0, 416, 46, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(40, 30, 'Industrial Shed in Mombasa Road', 'industrial-shed-in-mombasa-road-40', 'Beautiful commercial located in Kileleshwa, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'commercial', 'sale', 'active', 34000000, NULL, NULL, NULL, 1965, 1, 2020, 'Machakos', 'Kileleshwa', 'Kileleshwa, Machakos', -1.3299, 36.8053, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=39a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=39b"]', NULL, 0, 259, 43, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(41, 51, 'Studio Apartment in Kilimani', 'studio-apartment-in-kilimani-41', 'Beautiful villa located in Karen, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'villa', 'sale', 'pending', 140000000, NULL, 1, 3, 830, 1, 2011, 'Nairobi', 'Karen', 'Karen, Nairobi', -1.3026, 36.8146, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=40a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=40b"]', NULL, 0, 417, 20, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(42, 52, '5BR Detached House Nyari Estate', '5br-detached-house-nyari-estate-42', 'Beautiful office located in Westlands, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'office', 'rent', 'pending', 68000, 'year', NULL, NULL, 1935, 1, 2014, 'Nairobi', 'Westlands', 'Westlands, Nairobi', -1.2796, 36.8656, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=41a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=41b"]', NULL, 0, 305, 37, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(43, 53, 'Corner Apartment in Westlands', 'corner-apartment-in-westlands-43', 'Beautiful house located in Kilimani, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'sale', 'pending', 49000000, NULL, 5, 3, 1164, 3, 2007, 'Nairobi', 'Kilimani', 'Kilimani, Nairobi', -1.2508, 36.8524, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=42a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=42b"]', NULL, 0, 260, 21, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(44, 54, 'Plot in Ruaka Township', 'plot-in-ruaka-township-44', 'Beautiful apartment located in Lavington, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'rent', 'pending', 52000, 'year', 4, 4, 600, 1, 2014, 'Kiambu', 'Lavington', 'Lavington, Kiambu', -1.2976, 36.8706, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=43a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=43b"]', NULL, 0, 421, 37, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(45, 55, '3BR Flat in Buruburu', '3br-flat-in-buruburu-45', 'Beautiful land located in Runda, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'land', 'sale', 'pending', 18000000, NULL, NULL, NULL, 709, NULL, 2006, 'Machakos', 'Runda', 'Runda, Machakos', -1.3326, 36.867, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=44a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=44b"]', NULL, 0, 189, 17, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(46, 6, 'Commercial Unit in Ngong Road', 'commercial-unit-in-ngong-road-46', 'Beautiful commercial located in Muthaiga, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'commercial', 'sale', 'draft', 117000000, NULL, NULL, NULL, 3097, 3, 2001, 'Nairobi', 'Muthaiga', 'Muthaiga, Nairobi', -1.2796, 36.8387, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=45a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=45b"]', NULL, 0, 73, 45, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(47, 7, 'Townhouse Complex in Karen', 'townhouse-complex-in-karen-47', 'Beautiful villa located in Upper Hill, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'villa', 'rent', 'draft', 230000, 'month', 5, 5, 2243, 3, 2018, 'Nairobi', 'Upper Hill', 'Upper Hill, Nairobi', -1.253, 36.8461, '["Solar Power","Fibre Internet","Air Conditioning","Elevator"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=46a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=46b"]', NULL, 0, 371, 44, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(48, 8, 'Student Hostel Kenyatta University', 'student-hostel-kenyatta-university-48', 'Beautiful office located in Parklands, Nairobi. This property offers excellent value and modern amenities in a prime Nairobi location.', 'office', 'sale', 'draft', 144000000, NULL, NULL, NULL, 4020, 4, 2014, 'Nairobi', 'Parklands', 'Parklands, Nairobi', -1.2505, 36.837, '["Gated Community","Playground","Basketball Court","Tennis Court"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=47a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=47b"]', NULL, 0, 249, 21, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(49, 9, 'Duplex in Kitengela', 'duplex-in-kitengela-49', 'Beautiful house located in Spring Valley, Kiambu. This property offers excellent value and modern amenities in a prime Nairobi location.', 'house', 'rent', 'draft', 124000, 'month', 5, 1, 1832, 3, 2012, 'Kiambu', 'Spring Valley', 'Spring Valley, Kiambu', -1.291, 36.8575, '["Swimming Pool","Gym","Security","Parking","Balcony"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=48a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=48b"]', NULL, 0, 74, 48, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL),
(50, 10, 'Penthouse Nairobi CBD', 'penthouse-nairobi-cbd-50', 'Beautiful apartment located in Gigiri, Machakos. This property offers excellent value and modern amenities in a prime Nairobi location.', 'apartment', 'sale', 'draft', 150000000, NULL, 6, 3, 2885, 4, 2002, 'Machakos', 'Gigiri', 'Gigiri, Machakos', -1.3166, 36.8681, '["Borehole","Generator","CCTV","Garden","Servant Quarter"]', '["https:\\/\\/source.unsplash.com\\/800x600\\/?house,kenya&sig=49a","https:\\/\\/source.unsplash.com\\/800x600\\/?interior,modern&sig=49b"]', NULL, 0, 436, 7, '2026-06-02 06:33:59', '2026-06-02 06:33:59', NULL);

-- ----------------------------
-- Table: property_documents
-- ----------------------------
DROP TABLE IF EXISTS \`property_documents\`;
CREATE TABLE `property_documents` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `property_documents_property_id_index` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------
-- Table: property_saves
-- ----------------------------
DROP TABLE IF EXISTS \`property_saves\`;
CREATE TABLE `property_saves` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `property_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `property_saves_user_property_unique` (`user_id`, `property_id`),
  INDEX `property_saves_property_id_index` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------
-- Table: property_availability
-- ----------------------------
DROP TABLE IF EXISTS \`property_availability\`;
CREATE TABLE `property_availability` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` INT UNSIGNED NOT NULL,
  `blocked_date` DATE NOT NULL,
  `reason` VARCHAR(50) NOT NULL DEFAULT 'booked',
  `booking_id` INT UNSIGNED NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `property_availability_property_id_index` (`property_id`),
  INDEX `property_availability_booking_id_index` (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------
-- Table: property_pricing_rules
-- ----------------------------
DROP TABLE IF EXISTS \`property_pricing_rules\`;
CREATE TABLE `property_pricing_rules` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` INT UNSIGNED NOT NULL,
  `room_id` INT UNSIGNED NULL,
  `day_of_week` INT NULL,
  `date_from` DATE NULL,
  `date_to` DATE NULL,
  `price_per_night` DECIMAL(15,2) NOT NULL,
  `minimum_nights` INT NOT NULL DEFAULT 1,
  `priority` INT NOT NULL DEFAULT 0,
  `label` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `property_pricing_rules_property_id_index` (`property_id`),
  INDEX `property_pricing_rules_room_id_index` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------
-- Table: hotel_rooms
-- ----------------------------
DROP TABLE IF EXISTS \`hotel_rooms\`;
CREATE TABLE `hotel_rooms` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` INT UNSIGNED NOT NULL,
  `room_number` VARCHAR(50) NOT NULL,
  `room_type` VARCHAR(50) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `floor` INT NULL,
  `capacity` INT NOT NULL DEFAULT 2,
  `price_per_night` DECIMAL(15,2) NOT NULL,
  `amenities` TEXT NULL,
  `images` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `hotel_rooms_property_id_index` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------
-- Table: leases
-- ----------------------------
DROP TABLE IF EXISTS \`leases\`;
CREATE TABLE `leases` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` INT UNSIGNED NOT NULL,
  `landlord_id` INT UNSIGNED NOT NULL,
  `tenant_id` INT UNSIGNED NOT NULL,
  `monthly_rent` DECIMAL(15,2) NOT NULL,
  `deposit` DECIMAL(15,2) NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `terms` TEXT NULL,
  `signed_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `leases_property_id_index` (`property_id`),
  INDEX `leases_landlord_id_index` (`landlord_id`),
  INDEX `leases_tenant_id_index` (`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO \`leases\` (`id`, `property_id`, `landlord_id`, `tenant_id`, `monthly_rent`, `deposit`, `start_date`, `end_date`, `status`, `terms`, `signed_at`, `created_at`, `updated_at`) VALUES
(1, 2, 6, 21, 187000, 374000, '2025-05-02 00:00:00', '2026-05-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-05-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(2, 4, 7, 22, 238000, 476000, '2025-11-02 00:00:00', '2026-11-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-11-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(3, 7, 8, 23, 56000, 112000, '2025-04-02 00:00:00', '2026-04-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-04-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(4, 9, 9, 24, 115000, 230000, '2025-10-02 00:00:00', '2026-10-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-10-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(5, 12, 10, 25, 272000, 544000, '2025-08-02 00:00:00', '2026-08-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-08-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(6, 14, 6, 21, 56000, 112000, '2025-10-02 00:00:00', '2026-10-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-10-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(7, 17, 7, 22, 177000, 354000, '2025-08-02 00:00:00', '2026-08-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-08-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(8, 19, 8, 23, 221000, 442000, '2025-01-02 00:00:00', '2026-01-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-01-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(9, 22, 9, 24, 296000, 592000, '2025-01-02 00:00:00', '2026-01-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-01-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(10, 24, 10, 25, 160000, 320000, '2026-02-02 00:00:00', '2027-02-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2026-02-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(11, 27, 6, 21, 188000, 376000, '2025-06-02 00:00:00', '2026-06-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-06-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(12, 29, 7, 22, 192000, 384000, '2025-04-02 00:00:00', '2026-04-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-04-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(13, 32, 8, 23, 187000, 374000, '2025-12-02 00:00:00', '2026-12-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-12-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(14, 34, 9, 24, 226000, 452000, '2026-05-02 00:00:00', '2027-05-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2026-05-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(15, 37, 10, 25, 135000, 270000, '2025-06-02 00:00:00', '2026-06-02 00:00:00', 'active', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-06-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(16, 39, 6, 21, 96000, 192000, '2025-07-02 00:00:00', '2026-07-02 00:00:00', 'expired', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-07-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(17, 42, 7, 22, 68000, 136000, '2025-04-02 00:00:00', '2026-04-02 00:00:00', 'expired', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-04-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(18, 44, 8, 23, 52000, 104000, '2025-05-02 00:00:00', '2026-05-02 00:00:00', 'expired', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', '2025-05-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(19, 47, 9, 24, 230000, 460000, '2025-10-02 00:00:00', '2026-10-02 00:00:00', 'pending', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(20, 49, 10, 25, 124000, 248000, '2025-02-02 00:00:00', '2026-02-02 00:00:00', 'pending', 'Standard residential lease terms apply. Tenant responsible for utility bills. No subletting without landlord consent.', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59');

-- ----------------------------
-- Table: rent_payments
-- ----------------------------
DROP TABLE IF EXISTS \`rent_payments\`;
CREATE TABLE `rent_payments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `lease_id` INT UNSIGNED NOT NULL,
  `tenant_id` INT UNSIGNED NOT NULL,
  `landlord_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `month_year` VARCHAR(20) NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `transaction_ref` VARCHAR(255) NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `paid_at` DATETIME NULL,
  `due_date` DATE NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `rent_payments_lease_id_index` (`lease_id`),
  INDEX `rent_payments_tenant_id_index` (`tenant_id`),
  INDEX `rent_payments_landlord_id_index` (`landlord_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO \`rent_payments\` (`id`, `lease_id`, `tenant_id`, `landlord_id`, `amount`, `month_year`, `payment_method`, `transaction_ref`, `status`, `paid_at`, `due_date`, `created_at`, `updated_at`) VALUES
(1, 1, 21, 6, 187000, '2026-06', 'card', 'QHJZVCAIZ4A', 'paid', '2026-05-29 06:33:59', '2026-05-29 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(2, 1, 21, 6, 187000, '2026-05', 'cash', 'QHJKSJWHYQA', 'paid', '2026-04-28 06:33:59', '2026-04-28 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(3, 2, 22, 7, 238000, '2026-06', 'mpesa', 'QHJMMW9F1FH', 'paid', '2026-05-31 06:33:59', '2026-05-31 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(4, 2, 22, 7, 238000, '2026-05', 'card', 'QHJGKETDTTW', 'paid', '2026-05-01 06:33:59', '2026-05-01 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(5, 3, 23, 8, 56000, '2026-06', 'mpesa', 'QHJSPTT2NYF', 'paid', '2026-05-29 06:33:59', '2026-05-29 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(6, 3, 23, 8, 56000, '2026-05', 'cash', 'QHJSODMYVH3', 'paid', '2026-04-27 06:33:59', '2026-04-27 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(7, 4, 24, 9, 115000, '2026-06', 'card', 'QHJRIEQKVOB', 'paid', '2026-05-29 06:33:59', '2026-05-29 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(8, 4, 24, 9, 115000, '2026-05', 'cash', 'QHJP9SLQMXC', 'paid', '2026-04-30 06:33:59', '2026-04-30 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(9, 5, 25, 10, 272000, '2026-06', 'mpesa', 'QHJW5T5MOPE', 'paid', '2026-06-01 06:33:59', '2026-06-01 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(10, 5, 25, 10, 272000, '2026-05', 'cash', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(11, 6, 21, 6, 56000, '2026-06', 'cash', 'QHJEVVKXC0R', 'paid', '2026-05-30 06:33:59', '2026-05-30 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(12, 6, 21, 6, 56000, '2026-05', 'bank', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(13, 7, 22, 7, 177000, '2026-06', 'bank', 'QHJV5GBVLOT', 'paid', '2026-05-28 06:33:59', '2026-05-28 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(14, 7, 22, 7, 177000, '2026-05', 'cash', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(15, 8, 23, 8, 221000, '2026-06', 'mpesa', 'QHJZ5S9SELX', 'paid', '2026-05-28 06:33:59', '2026-05-28 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(16, 8, 23, 8, 221000, '2026-05', 'card', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(17, 9, 24, 9, 296000, '2026-06', 'cash', 'QHJ5I88FWCA', 'paid', '2026-06-02 06:33:59', '2026-06-02 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(18, 9, 24, 9, 296000, '2026-05', 'cash', 'QHJRUN5KJ2U', 'paid', '2026-04-30 06:33:59', '2026-04-30 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(19, 10, 25, 10, 160000, '2026-06', 'bank', 'QHJCAW6X8WZ', 'paid', '2026-06-02 06:33:59', '2026-06-02 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(20, 10, 25, 10, 160000, '2026-05', 'bank', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(21, 11, 21, 6, 188000, '2026-06', 'mpesa', 'QHJCHA0GMQD', 'paid', '2026-05-29 06:33:59', '2026-05-29 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(22, 11, 21, 6, 188000, '2026-05', 'card', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(23, 12, 22, 7, 192000, '2026-06', 'mpesa', 'QHJSBBLWYNU', 'paid', '2026-05-29 06:33:59', '2026-05-29 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(24, 12, 22, 7, 192000, '2026-05', 'mpesa', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(25, 13, 23, 8, 187000, '2026-06', 'cash', 'QHJJTZESOTN', 'paid', '2026-06-02 06:33:59', '2026-06-02 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(26, 13, 23, 8, 187000, '2026-05', 'card', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(27, 14, 24, 9, 226000, '2026-06', 'cash', 'QHJOH6QEVQQ', 'paid', '2026-05-28 06:33:59', '2026-05-28 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(28, 14, 24, 9, 226000, '2026-05', 'mpesa', 'QHJ7PNQNPC8', 'paid', '2026-04-30 06:33:59', '2026-04-30 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(29, 15, 25, 10, 135000, '2026-06', 'card', 'QHJUQSPV4UN', 'paid', '2026-05-29 06:33:59', '2026-05-29 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(30, 15, 25, 10, 135000, '2026-05', 'bank', NULL, 'overdue', NULL, '2026-05-02 00:00:00', '2026-06-02 06:33:59', '2026-06-02 06:33:59');

-- ----------------------------
-- Table: bookings
-- ----------------------------
DROP TABLE IF EXISTS \`bookings\`;
CREATE TABLE `bookings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` INT UNSIGNED NOT NULL,
  `room_id` INT UNSIGNED NULL,
  `guest_id` INT UNSIGNED NOT NULL,
  `host_id` INT UNSIGNED NOT NULL,
  `type` VARCHAR(50) NOT NULL DEFAULT 'airbnb',
  `check_in` DATE NOT NULL,
  `check_out` DATE NOT NULL,
  `guests_count` INT NOT NULL DEFAULT 1,
  `nights` INT NULL,
  `total_price` DECIMAL(15,2) NOT NULL,
  `base_price_per_night` DECIMAL(15,2) NOT NULL,
  `cleaning_fee` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `service_fee` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `payment_method` VARCHAR(50) NULL,
  `payment_ref` VARCHAR(255) NULL,
  `paid_at` DATETIME NULL,
  `special_requests` TEXT NULL,
  `cancellation_reason` TEXT NULL,
  `cancelled_at` DATETIME NULL,
  `host_notified_at` DATETIME NULL,
  `auto_confirmed` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `bookings_property_id_index` (`property_id`),
  INDEX `bookings_room_id_index` (`room_id`),
  INDEX `bookings_guest_id_index` (`guest_id`),
  INDEX `bookings_host_id_index` (`host_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------
-- Table: booking_reviews
-- ----------------------------
DROP TABLE IF EXISTS \`booking_reviews\`;
CREATE TABLE `booking_reviews` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_id` INT UNSIGNED NOT NULL,
  `reviewer_id` INT UNSIGNED NOT NULL,
  `property_id` INT UNSIGNED NOT NULL,
  `rating` INT NOT NULL,
  `cleanliness` INT NULL,
  `communication` INT NULL,
  `location` INT NULL,
  `value` INT NULL,
  `comment` TEXT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `booking_reviews_booking_id_index` (`booking_id`),
  INDEX `booking_reviews_reviewer_id_index` (`reviewer_id`),
  INDEX `booking_reviews_property_id_index` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------
-- Table: escrow_transactions
-- ----------------------------
DROP TABLE IF EXISTS \`escrow_transactions\`;
CREATE TABLE `escrow_transactions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` INT UNSIGNED NOT NULL,
  `buyer_id` INT UNSIGNED NOT NULL,
  `seller_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'held',
  `reference` VARCHAR(255) NOT NULL,
  `notes` TEXT NULL,
  `released_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `escrow_transactions_reference_unique` (`reference`),
  INDEX `escrow_transactions_property_id_index` (`property_id`),
  INDEX `escrow_transactions_buyer_id_index` (`buyer_id`),
  INDEX `escrow_transactions_seller_id_index` (`seller_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO \`escrow_transactions\` (`id`, `property_id`, `buyer_id`, `seller_id`, `amount`, `type`, `status`, `reference`, `notes`, `released_at`, `created_at`, `updated_at`) VALUES
(1, 1, 46, 6, 3000000, 'deposit', 'held', 'ESC-D9KHOJJZ', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(2, 3, 47, 7, 4100000, 'deposit', 'held', 'ESC-B8JZMC88', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(3, 5, 48, 8, 11200000, 'deposit', 'held', 'ESC-JBML33XM', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(4, 6, 49, 9, 3600000, 'deposit', 'held', 'ESC-CVZXSUKL', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(5, 8, 50, 10, 9000000, 'deposit', 'held', 'ESC-OYIROBOA', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(6, 10, 46, 6, 11700000, 'deposit', 'held', 'ESC-RVBT1KDA', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(7, 11, 47, 7, 6700000, 'deposit', 'held', 'ESC-JYTKDVVV', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(8, 13, 48, 8, 14800000, 'deposit', 'held', 'ESC-Y94PVW40', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(9, 15, 49, 9, 3600000, 'deposit', 'released', 'ESC-FTBGN8KE', 'Deposit held pending title deed transfer and legal clearance.', '2026-05-13 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(10, 16, 50, 10, 12100000, 'deposit', 'released', 'ESC-VQXSY7WF', 'Deposit held pending title deed transfer and legal clearance.', '2026-05-12 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(11, 18, 46, 6, 11800000, 'sale', 'released', 'ESC-BPZAMGAI', 'Deposit held pending title deed transfer and legal clearance.', '2026-06-01 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(12, 20, 47, 7, 4000000, 'sale', 'released', 'ESC-NFTZ1JPQ', 'Deposit held pending title deed transfer and legal clearance.', '2026-06-01 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(13, 21, 48, 8, 300000, 'sale', 'released', 'ESC-0SXV2JWV', 'Deposit held pending title deed transfer and legal clearance.', '2026-05-16 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(14, 23, 49, 9, 2400000, 'sale', 'released', 'ESC-0HCOZ474', 'Deposit held pending title deed transfer and legal clearance.', '2026-05-22 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(15, 25, 50, 10, 8600000, 'sale', 'disputed', 'ESC-UHVLWE7H', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(16, 26, 46, 6, 4300000, 'refund', 'disputed', 'ESC-WICFSFV5', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(17, 28, 47, 7, 11700000, 'refund', 'disputed', 'ESC-JBDEPBLV', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(18, 30, 48, 8, 5600000, 'refund', 'refunded', 'ESC-A2BYJ2FQ', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(19, 31, 49, 9, 10900000, 'refund', 'refunded', 'ESC-QMP31JC5', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(20, 33, 50, 10, 5000000, 'refund', 'refunded', 'ESC-KTQE8JF1', 'Deposit held pending title deed transfer and legal clearance.', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59');

-- ----------------------------
-- Table: auctions
-- ----------------------------
DROP TABLE IF EXISTS \`auctions\`;
CREATE TABLE `auctions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` INT UNSIGNED NOT NULL,
  `auctioneer_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `reserve_price` DECIMAL(15,2) NOT NULL,
  `starting_bid` DECIMAL(15,2) NOT NULL,
  `current_bid` DECIMAL(15,2) NULL,
  `bid_increment` DECIMAL(15,2) NOT NULL DEFAULT 10000,
  `starts_at` DATETIME NOT NULL,
  `ends_at` DATETIME NOT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'upcoming',
  `winner_id` INT UNSIGNED NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `auctions_property_id_index` (`property_id`),
  INDEX `auctions_auctioneer_id_index` (`auctioneer_id`),
  INDEX `auctions_winner_id_index` (`winner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO \`auctions\` (`id`, `property_id`, `auctioneer_id`, `title`, `description`, `reserve_price`, `starting_bid`, `current_bid`, `bid_increment`, `starts_at`, `ends_at`, `status`, `winner_id`, `created_at`, `updated_at`) VALUES
(1, 1, 41, 'Prime Karen Mansion Auction', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 24000000, 18000000, 20700000, 50000, '2026-06-02 03:33:59', '2026-06-02 17:33:59', 'live', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(2, 2, 42, 'Westlands Commercial Block Sale', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 149600, 112200, 143616, 50000, '2026-06-02 03:33:59', '2026-06-02 10:33:59', 'live', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(3, 3, 43, 'Distressed Sale Kilimani Apartment', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 32800000, 24600000, 26568000, 50000, '2026-06-02 01:33:59', '2026-06-02 15:33:59', 'live', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(4, 4, 44, 'Bank-Seized Property Runda Villa', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 190400, 142800, NULL, 50000, '2026-06-05 06:33:59', '2026-06-06 06:33:59', 'upcoming', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(5, 5, 45, 'Government Surplus Muthaiga Land', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 89600000, 67200000, NULL, 50000, '2026-06-10 06:33:59', '2026-06-11 06:33:59', 'upcoming', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(6, 6, 41, 'Probate Sale Lavington Estate', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 28800000, 21600000, NULL, 50000, '2026-06-15 06:33:59', '2026-06-16 06:33:59', 'upcoming', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(7, 7, 42, 'Developer Closeout Upper Hill Offices', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 44800, 33600, NULL, 50000, '2026-06-15 06:33:59', '2026-06-16 06:33:59', 'upcoming', NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(8, 8, 43, 'Foreclosure Parklands Townhouse', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 72000000, 54000000, 66960000, 50000, '2026-05-11 06:33:59', '2026-05-12 06:33:59', 'ended', 46, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(9, 9, 44, 'Heritage Property Gigiri Residence', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 92000, 69000, 85560, 50000, '2026-05-30 06:33:59', '2026-05-31 06:33:59', 'ended', 46, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(10, 10, 45, 'Agricultural Land Limuru Farm', 'Court-ordered sale of prime property. All bids subject to reserve price. Viewing by appointment only.', 93600000, 70200000, 83538000, 50000, '2026-05-15 06:33:59', '2026-05-16 06:33:59', 'ended', 46, '2026-06-02 06:33:59', '2026-06-02 06:33:59');

-- ----------------------------
-- Table: bids
-- ----------------------------
DROP TABLE IF EXISTS \`bids\`;
CREATE TABLE `bids` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `auction_id` INT UNSIGNED NOT NULL,
  `bidder_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `is_winning` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `bids_auction_id_index` (`auction_id`),
  INDEX `bids_bidder_id_index` (`bidder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO \`bids\` (`id`, `auction_id`, `bidder_id`, `amount`, `is_winning`, `created_at`, `updated_at`) VALUES
(1, 1, 47, 18150000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(2, 1, 48, 18300000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(3, 1, 49, 18450000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(4, 2, 48, 262200, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(5, 2, 49, 362200, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(6, 2, 50, 512200, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(7, 3, 49, 24700000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(8, 3, 50, 24800000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(9, 3, 21, 24950000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(10, 3, 22, 25050000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(11, 3, 23, 25200000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(12, 8, 24, 54150000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(13, 8, 25, 54200000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(14, 8, 46, 54250000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(15, 8, 47, 54300000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(16, 8, 48, 54350000, 1, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(17, 9, 25, 219000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(18, 9, 46, 369000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(19, 9, 47, 419000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(20, 9, 48, 519000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(21, 9, 49, 619000, 1, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(22, 10, 46, 70350000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(23, 10, 47, 70500000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(24, 10, 48, 70550000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(25, 10, 49, 70600000, 0, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(26, 10, 50, 70650000, 1, '2026-06-02 06:33:59', '2026-06-02 06:33:59');

-- ----------------------------
-- Table: referrals
-- ----------------------------
DROP TABLE IF EXISTS \`referrals\`;
CREATE TABLE `referrals` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `referrer_id` INT UNSIGNED NOT NULL,
  `referred_user_id` INT UNSIGNED NULL,
  `referral_code` VARCHAR(255) NOT NULL,
  `click_count` INT NOT NULL DEFAULT 0,
  `conversion_count` INT NOT NULL DEFAULT 0,
  `total_earned` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `expires_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `referrals_referral_code_unique` (`referral_code`),
  INDEX `referrals_referrer_id_index` (`referrer_id`),
  INDEX `referrals_referred_user_id_index` (`referred_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO \`referrals\` (`id`, `referrer_id`, `referred_user_id`, `referral_code`, `click_count`, `conversion_count`, `total_earned`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 11, 21, 'WAOW9V', 46, 9, 7000, '2026-12-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(2, 12, 22, 'RFBDQE', 42, 9, 27000, '2026-12-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(3, 13, 23, 'GEMRLN', 57, 5, 25000, '2026-12-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(4, 14, 24, 'PYATXE', 37, 7, 47000, '2026-12-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(5, 15, 25, 'CJQBYI', 34, 10, 45000, '2026-12-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(6, 16, NULL, 'CAMXKZ', 70, 5, 7000, '2026-12-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(7, 17, NULL, 'WYTMFG', 14, 9, 46000, '2026-12-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(8, 18, NULL, 'I205YO', 42, 1, 22000, '2026-12-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(9, 19, NULL, 'GKILQ0', 29, 4, 19000, '2026-12-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(10, 20, NULL, '6WI55G', 62, 8, 50000, '2026-12-02 06:33:59', '2026-06-02 06:33:59', '2026-06-02 06:33:59');

-- ----------------------------
-- Table: referral_conversions
-- ----------------------------
DROP TABLE IF EXISTS \`referral_conversions\`;
CREATE TABLE `referral_conversions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `referral_id` INT UNSIGNED NOT NULL,
  `referred_user_id` INT UNSIGNED NOT NULL,
  `property_id` INT UNSIGNED NULL,
  `conversion_type` VARCHAR(50) NOT NULL,
  `commission` DECIMAL(15,2) NOT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `referral_conversions_referral_id_index` (`referral_id`),
  INDEX `referral_conversions_referred_user_id_index` (`referred_user_id`),
  INDEX `referral_conversions_property_id_index` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------
-- Table: verifications
-- ----------------------------
DROP TABLE IF EXISTS \`verifications\`;
CREATE TABLE `verifications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `tier` VARCHAR(50) NOT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `submitted_at` DATETIME NULL,
  `approved_at` DATETIME NULL,
  `expires_at` DATETIME NULL,
  `notes` TEXT NULL,
  `reviewed_by` INT UNSIGNED NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `verifications_user_id_index` (`user_id`),
  INDEX `verifications_reviewed_by_index` (`reviewed_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO \`verifications\` (`id`, `user_id`, `tier`, `status`, `submitted_at`, `approved_at`, `expires_at`, `notes`, `reviewed_by`, `created_at`, `updated_at`) VALUES
(1, 6, 'basic', 'pending', '2026-04-06 06:33:59', NULL, NULL, NULL, NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(2, 7, 'professional', 'approved', '2026-05-16 06:33:59', '2026-05-03 06:33:59', '2027-06-02 06:33:59', NULL, 66, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(3, 8, 'elite', 'approved', '2026-05-05 06:33:59', '2026-05-16 06:33:59', '2027-06-02 06:33:59', NULL, 66, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(4, 9, 'basic', 'rejected', '2026-05-21 06:33:59', NULL, NULL, 'Documents not clear. Please resubmit with clearer copies.', 66, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(5, 10, 'professional', 'expired', '2026-04-09 06:33:59', NULL, NULL, NULL, 66, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(6, 11, 'elite', 'pending', '2026-04-29 06:33:59', NULL, NULL, NULL, NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(7, 12, 'basic', 'approved', '2026-04-03 06:33:59', '2026-05-03 06:33:59', '2027-06-02 06:33:59', NULL, 66, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(8, 13, 'professional', 'approved', '2026-05-09 06:33:59', '2026-05-25 06:33:59', '2027-06-02 06:33:59', NULL, 66, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(9, 14, 'elite', 'rejected', '2026-05-24 06:33:59', NULL, NULL, 'Documents not clear. Please resubmit with clearer copies.', 66, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(10, 15, 'basic', 'expired', '2026-05-09 06:33:59', NULL, NULL, NULL, 66, '2026-06-02 06:33:59', '2026-06-02 06:33:59');

-- ----------------------------
-- Table: verification_documents
-- ----------------------------
DROP TABLE IF EXISTS \`verification_documents\`;
CREATE TABLE `verification_documents` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `verification_id` INT UNSIGNED NOT NULL,
  `document_type` VARCHAR(50) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `verification_documents_verification_id_index` (`verification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO \`verification_documents\` (`id`, `verification_id`, `document_type`, `file_path`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'national_id', 'verifications/6/national_id.pdf', 'pending', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(2, 2, 'national_id', 'verifications/7/national_id.pdf', 'approved', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(3, 2, 'kra_pin', 'verifications/7/kra_pin.pdf', 'approved', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(4, 3, 'national_id', 'verifications/8/national_id.pdf', 'approved', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(5, 3, 'kra_pin', 'verifications/8/kra_pin.pdf', 'approved', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(6, 4, 'national_id', 'verifications/9/national_id.pdf', 'rejected', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(7, 5, 'national_id', 'verifications/10/national_id.pdf', 'pending', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(8, 5, 'kra_pin', 'verifications/10/kra_pin.pdf', 'pending', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(9, 6, 'national_id', 'verifications/11/national_id.pdf', 'pending', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(10, 6, 'kra_pin', 'verifications/11/kra_pin.pdf', 'pending', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(11, 7, 'national_id', 'verifications/12/national_id.pdf', 'approved', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(12, 8, 'national_id', 'verifications/13/national_id.pdf', 'approved', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(13, 8, 'kra_pin', 'verifications/13/kra_pin.pdf', 'approved', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(14, 9, 'national_id', 'verifications/14/national_id.pdf', 'rejected', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(15, 9, 'kra_pin', 'verifications/14/kra_pin.pdf', 'pending', '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(16, 10, 'national_id', 'verifications/15/national_id.pdf', 'pending', '2026-06-02 06:33:59', '2026-06-02 06:33:59');

-- ----------------------------
-- Table: inspections
-- ----------------------------
DROP TABLE IF EXISTS \`inspections\`;
CREATE TABLE `inspections` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` INT UNSIGNED NOT NULL,
  `inspector_id` INT UNSIGNED NULL,
  `requester_id` INT UNSIGNED NOT NULL,
  `scheduled_at` DATETIME NOT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `notes` TEXT NULL,
  `report_url` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `inspections_property_id_index` (`property_id`),
  INDEX `inspections_inspector_id_index` (`inspector_id`),
  INDEX `inspections_requester_id_index` (`requester_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO \`inspections\` (`id`, `property_id`, `inspector_id`, `requester_id`, `scheduled_at`, `status`, `notes`, `report_url`, `created_at`, `updated_at`) VALUES
(1, 1, 36, 21, '2026-05-23 06:34:00', 'pending', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(2, 2, 37, 22, '2026-06-10 06:34:00', 'confirmed', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(3, 3, 38, 23, '2026-05-24 06:34:00', 'completed', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', 'reports/inspection_2.pdf', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(4, 4, 39, 24, '2026-06-20 06:34:00', 'cancelled', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(5, 5, 40, 25, '2026-06-12 06:34:00', 'pending', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(6, 6, 36, 21, '2026-06-11 06:34:00', 'confirmed', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(7, 7, 37, 22, '2026-05-24 06:34:00', 'completed', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', 'reports/inspection_6.pdf', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(8, 8, 38, 23, '2026-06-11 06:34:00', 'cancelled', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(9, 9, 39, 24, '2026-06-01 06:34:00', 'pending', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(10, 10, 40, 25, '2026-06-12 06:34:00', 'confirmed', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(11, 11, 36, 21, '2026-05-27 06:34:00', 'completed', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', 'reports/inspection_10.pdf', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(12, 12, 37, 22, '2026-06-19 06:34:00', 'cancelled', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(13, 13, NULL, 23, '2026-05-29 06:34:00', 'pending', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(14, 14, NULL, 24, '2026-06-06 06:34:00', 'confirmed', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(15, 15, NULL, 25, '2026-06-18 06:34:00', 'completed', 'Property inspection requested by prospective tenant. Access arranged with caretaker.', 'reports/inspection_14.pdf', '2026-06-02 06:34:00', '2026-06-02 06:34:00');

-- ----------------------------
-- Table: maintenance_requests
-- ----------------------------
DROP TABLE IF EXISTS \`maintenance_requests\`;
CREATE TABLE `maintenance_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` INT UNSIGNED NOT NULL,
  `tenant_id` INT UNSIGNED NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `priority` VARCHAR(50) NOT NULL DEFAULT 'medium',
  `status` VARCHAR(50) NOT NULL DEFAULT 'open',
  `assigned_to` INT UNSIGNED NULL,
  `resolved_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `maintenance_requests_property_id_index` (`property_id`),
  INDEX `maintenance_requests_tenant_id_index` (`tenant_id`),
  INDEX `maintenance_requests_assigned_to_index` (`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO \`maintenance_requests\` (`id`, `property_id`, `tenant_id`, `title`, `description`, `priority`, `status`, `assigned_to`, `resolved_at`, `created_at`, `updated_at`) VALUES
(1, 2, 21, 'Leaking Roof Urgent Repair', 'Tenant reported issue: Leaking Roof Urgent Repair. Requires prompt attention.', 'low', 'open', 56, NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(2, 4, 22, 'Broken Water Heater', 'Tenant reported issue: Broken Water Heater. Requires prompt attention.', 'medium', 'in_progress', 57, NULL, '2026-06-02 06:33:59', '2026-06-02 06:33:59'),
(3, 7, 23, 'Electrical Fault in Kitchen', 'Tenant reported issue: Electrical Fault in Kitchen. Requires prompt attention.', 'high', 'resolved', 58, '2026-05-26 06:34:00', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(4, 9, 24, 'Clogged Drainage System', 'Tenant reported issue: Clogged Drainage System. Requires prompt attention.', 'urgent', 'closed', 59, '2026-05-26 06:34:00', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(5, 12, 25, 'Paint Peeling in Living Room', 'Tenant reported issue: Paint Peeling in Living Room. Requires prompt attention.', 'low', 'open', 60, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(6, 14, 21, 'Broken Window Lock', 'Tenant reported issue: Broken Window Lock. Requires prompt attention.', 'medium', 'in_progress', 56, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(7, 17, 22, 'AC Unit Not Working', 'Tenant reported issue: AC Unit Not Working. Requires prompt attention.', 'high', 'resolved', 57, '2026-05-28 06:34:00', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(8, 19, 23, 'Faulty Gate Motor', 'Tenant reported issue: Faulty Gate Motor. Requires prompt attention.', 'urgent', 'closed', 58, '2026-05-23 06:34:00', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(9, 22, 24, 'Mold in Bathroom', 'Tenant reported issue: Mold in Bathroom. Requires prompt attention.', 'low', 'open', 59, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(10, 24, 25, 'Burst Water Pipe', 'Tenant reported issue: Burst Water Pipe. Requires prompt attention.', 'medium', 'in_progress', 60, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(11, 27, 21, 'Sewage Overflow', 'Tenant reported issue: Sewage Overflow. Requires prompt attention.', 'high', 'resolved', 56, '2026-05-26 06:34:00', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(12, 29, 22, 'Broken Staircase Railing', 'Tenant reported issue: Broken Staircase Railing. Requires prompt attention.', 'urgent', 'closed', 57, '2026-05-26 06:34:00', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(13, 32, 23, 'Power Outage Generator Fault', 'Tenant reported issue: Power Outage Generator Fault. Requires prompt attention.', 'low', 'open', 58, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(14, 34, 24, 'Fallen Boundary Wall', 'Tenant reported issue: Fallen Boundary Wall. Requires prompt attention.', 'medium', 'in_progress', 59, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(15, 37, 25, 'Rodent Infestation', 'Tenant reported issue: Rodent Infestation. Requires prompt attention.', 'high', 'resolved', 60, '2026-05-23 06:34:00', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(16, 39, 21, 'Damaged Floor Tiles', 'Tenant reported issue: Damaged Floor Tiles. Requires prompt attention.', 'urgent', 'closed', NULL, '2026-06-01 06:34:00', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(17, 42, 22, 'Broken Door Hinge', 'Tenant reported issue: Broken Door Hinge. Requires prompt attention.', 'low', 'open', NULL, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(18, 44, 23, 'Water Tank Malfunction', 'Tenant reported issue: Water Tank Malfunction. Requires prompt attention.', 'medium', 'in_progress', NULL, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(19, 47, 24, 'Broken CCTV Camera', 'Tenant reported issue: Broken CCTV Camera. Requires prompt attention.', 'high', 'resolved', NULL, '2026-05-29 06:34:00', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(20, 49, 25, 'Faulty Elevator', 'Tenant reported issue: Faulty Elevator. Requires prompt attention.', 'urgent', 'closed', NULL, '2026-06-01 06:34:00', '2026-06-02 06:34:00', '2026-06-02 06:34:00');

-- ----------------------------
-- Table: messages
-- ----------------------------
DROP TABLE IF EXISTS \`messages\`;
CREATE TABLE `messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id` INT UNSIGNED NOT NULL,
  `recipient_id` INT UNSIGNED NOT NULL,
  `subject` VARCHAR(255) NULL,
  `body` TEXT NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `read_at` DATETIME NULL,
  `property_id` INT UNSIGNED NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `messages_sender_id_index` (`sender_id`),
  INDEX `messages_recipient_id_index` (`recipient_id`),
  INDEX `messages_property_id_index` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO \`messages\` (`id`, `sender_id`, `recipient_id`, `subject`, `body`, `is_read`, `read_at`, `property_id`, `created_at`, `updated_at`) VALUES
(1, 21, 6, 'Rent Payment Confirmation', 'Hi, I wanted to confirm my rent payment for this month has been sent via MPESA.', 1, '2026-06-02 00:34:00', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(2, 22, 7, 'Maintenance Request Follow-up', 'I submitted a maintenance request last week regarding a leaking pipe. Any update on this?', 0, NULL, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(3, 23, 8, 'Lease Renewal Inquiry', 'My lease expires next month. Are you open to renewal at the same terms?', 0, NULL, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(4, 24, 9, 'Property Viewing Request', 'I am interested in viewing your property. What time works for you this weekend?', 1, '2026-05-31 09:34:00', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(5, 25, 10, 'Utility Bill Query', 'Could you please clarify the utility bill charges reflected on my last statement?', 0, NULL, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(6, 21, 6, 'Rent Payment Confirmation', 'Hi, I wanted to confirm my rent payment for this month has been sent via MPESA.', 0, NULL, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(7, 22, 7, 'Maintenance Request Follow-up', 'I submitted a maintenance request last week regarding a leaking pipe. Any update on this?', 1, '2026-05-31 18:34:00', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(8, 23, 8, 'Lease Renewal Inquiry', 'My lease expires next month. Are you open to renewal at the same terms?', 0, NULL, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(9, 24, 9, 'Property Viewing Request', 'I am interested in viewing your property. What time works for you this weekend?', 0, NULL, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(10, 25, 10, 'Utility Bill Query', 'Could you please clarify the utility bill charges reflected on my last statement?', 1, '2026-06-01 07:34:00', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(11, 21, 6, 'Rent Payment Confirmation', 'Hi, I wanted to confirm my rent payment for this month has been sent via MPESA.', 0, NULL, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(12, 22, 7, 'Maintenance Request Follow-up', 'I submitted a maintenance request last week regarding a leaking pipe. Any update on this?', 0, NULL, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(13, 23, 8, 'Lease Renewal Inquiry', 'My lease expires next month. Are you open to renewal at the same terms?', 1, '2026-06-01 18:34:00', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(14, 24, 9, 'Property Viewing Request', 'I am interested in viewing your property. What time works for you this weekend?', 0, NULL, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(15, 25, 10, 'Utility Bill Query', 'Could you please clarify the utility bill charges reflected on my last statement?', 0, NULL, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(16, 21, 6, 'Rent Payment Confirmation', 'Hi, I wanted to confirm my rent payment for this month has been sent via MPESA.', 1, '2026-06-01 20:34:00', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(17, 22, 7, 'Maintenance Request Follow-up', 'I submitted a maintenance request last week regarding a leaking pipe. Any update on this?', 0, NULL, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(18, 23, 8, 'Lease Renewal Inquiry', 'My lease expires next month. Are you open to renewal at the same terms?', 0, NULL, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(19, 24, 9, 'Property Viewing Request', 'I am interested in viewing your property. What time works for you this weekend?', 1, '2026-06-01 12:34:00', NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(20, 25, 10, 'Utility Bill Query', 'Could you please clarify the utility bill charges reflected on my last statement?', 0, NULL, NULL, '2026-06-02 06:34:00', '2026-06-02 06:34:00');

-- ----------------------------
-- Table: notifications_log
-- ----------------------------
DROP TABLE IF EXISTS \`notifications_log\`;
CREATE TABLE `notifications_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `body` TEXT NOT NULL,
  `type` VARCHAR(50) NOT NULL DEFAULT 'system',
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `url` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `notifications_log_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO \`notifications_log\` (`id`, `user_id`, `title`, `body`, `type`, `is_read`, `url`, `created_at`, `updated_at`) VALUES
(1, 1, 'Rent Payment Received', 'Your rent payment of KES 45,000 has been received successfully.', 'payment', 1, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(2, 2, 'Inspection Scheduled', 'Property inspection has been confirmed for tomorrow at 10am.', 'inspection', 0, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(3, 3, 'Document Verified', 'Your National ID has been successfully verified.', 'document', 1, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(4, 4, 'Auction Starting Soon', 'The Karen Mansion auction starts in 2 hours. Place your bid!', 'auction', 0, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(5, 5, 'Account Verified', 'Congratulations! Your professional verification has been approved.', 'system', 1, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(6, 6, 'New Message', 'You have a new message from your landlord regarding the lease.', 'message', 0, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(7, 7, 'Rent Payment Received', 'Your rent payment of KES 45,000 has been received successfully.', 'payment', 1, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(8, 8, 'Inspection Scheduled', 'Property inspection has been confirmed for tomorrow at 10am.', 'inspection', 0, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(9, 9, 'Document Verified', 'Your National ID has been successfully verified.', 'document', 1, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(10, 10, 'Auction Starting Soon', 'The Karen Mansion auction starts in 2 hours. Place your bid!', 'auction', 0, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(11, 11, 'Account Verified', 'Congratulations! Your professional verification has been approved.', 'system', 1, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(12, 12, 'New Message', 'You have a new message from your landlord regarding the lease.', 'message', 0, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(13, 13, 'Rent Payment Received', 'Your rent payment of KES 45,000 has been received successfully.', 'payment', 1, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(14, 14, 'Inspection Scheduled', 'Property inspection has been confirmed for tomorrow at 10am.', 'inspection', 0, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(15, 15, 'Document Verified', 'Your National ID has been successfully verified.', 'document', 1, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(16, 16, 'Auction Starting Soon', 'The Karen Mansion auction starts in 2 hours. Place your bid!', 'auction', 0, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(17, 17, 'Account Verified', 'Congratulations! Your professional verification has been approved.', 'system', 1, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(18, 18, 'New Message', 'You have a new message from your landlord regarding the lease.', 'message', 0, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(19, 19, 'Rent Payment Received', 'Your rent payment of KES 45,000 has been received successfully.', 'payment', 1, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(20, 20, 'Inspection Scheduled', 'Property inspection has been confirmed for tomorrow at 10am.', 'inspection', 0, '/dashboard', '2026-06-02 06:34:00', '2026-06-02 06:34:00');

-- ----------------------------
-- Table: developer_projects
-- ----------------------------
DROP TABLE IF EXISTS \`developer_projects\`;
CREATE TABLE `developer_projects` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `developer_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `location` VARCHAR(255) NOT NULL,
  `total_units` INT NOT NULL,
  `sold_units` INT NOT NULL DEFAULT 0,
  `reserved_units` INT NOT NULL DEFAULT 0,
  `price_from` DECIMAL(15,2) NOT NULL,
  `price_to` DECIMAL(15,2) NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'planning',
  `completion_date` DATE NULL,
  `images` TEXT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `developer_projects_developer_id_index` (`developer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO \`developer_projects\` (`id`, `developer_id`, `name`, `description`, `location`, `total_units`, `sold_units`, `reserved_units`, `price_from`, `price_to`, `status`, `completion_date`, `images`, `created_at`, `updated_at`) VALUES
(1, 26, 'Garden City Residences', 'Premium residential development offering modern units at Garden City Residences in prime Nairobi location.', 'Garden City Residences, Nairobi', 180, 25, 19, 12000000, 61000000, 'planning', '2029-06-02 00:00:00', '["projects\\/0\\/cover.jpg"]', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(2, 27, 'Kilimani Heights', 'Premium residential development offering modern units at Kilimani Heights in prime Nairobi location.', 'Kilimani Heights, Nairobi', 45, 1, 9, 10000000, 38000000, 'construction', '2028-10-02 00:00:00', '["projects\\/1\\/cover.jpg"]', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(3, 28, 'Runda Forest Estate', 'Premium residential development offering modern units at Runda Forest Estate in prime Nairobi location.', 'Runda Forest Estate, Nairobi', 153, 49, 19, 10000000, 55000000, 'completed', '2027-04-02 00:00:00', '["projects\\/2\\/cover.jpg"]', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(4, 29, 'Westlands Prime Towers', 'Premium residential development offering modern units at Westlands Prime Towers in prime Nairobi location.', 'Westlands Prime Towers, Nairobi', 96, 27, 2, 7000000, 38000000, 'selling', '2029-04-02 00:00:00', '["projects\\/3\\/cover.jpg"]', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(5, 30, 'Karen Golf Villas', 'Premium residential development offering modern units at Karen Golf Villas in prime Nairobi location.', 'Karen Golf Villas, Nairobi', 38, 25, 6, 10000000, 76000000, 'planning', '2027-01-02 00:00:00', '["projects\\/4\\/cover.jpg"]', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(6, 26, 'Lavington Green Park', 'Premium residential development offering modern units at Lavington Green Park in prime Nairobi location.', 'Lavington Green Park, Nairobi', 129, 5, 11, 12000000, 57000000, 'construction', '2029-02-02 00:00:00', '["projects\\/5\\/cover.jpg"]', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(7, 27, 'Parklands Skyline Apartments', 'Premium residential development offering modern units at Parklands Skyline Apartments in prime Nairobi location.', 'Parklands Skyline Apartments, Nairobi', 163, 37, 19, 5000000, 61000000, 'completed', '2028-09-02 00:00:00', '["projects\\/6\\/cover.jpg"]', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(8, 28, 'Upper Hill Business Hub', 'Premium residential development offering modern units at Upper Hill Business Hub in prime Nairobi location.', 'Upper Hill Business Hub, Nairobi', 21, 29, 8, 11000000, 71000000, 'selling', '2027-03-02 00:00:00', '["projects\\/7\\/cover.jpg"]', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(9, 29, 'Muthaiga Royal Manors', 'Premium residential development offering modern units at Muthaiga Royal Manors in prime Nairobi location.', 'Muthaiga Royal Manors, Nairobi', 194, 39, 10, 15000000, 59000000, 'planning', '2028-09-02 00:00:00', '["projects\\/8\\/cover.jpg"]', '2026-06-02 06:34:00', '2026-06-02 06:34:00'),
(10, 30, 'Riverside Executive Suites', 'Premium residential development offering modern units at Riverside Executive Suites in prime Nairobi location.', 'Riverside Executive Suites, Nairobi', 142, 32, 20, 12000000, 44000000, 'construction', '2027-10-02 00:00:00', '["projects\\/9\\/cover.jpg"]', '2026-06-02 06:34:00', '2026-06-02 06:34:00');

-- ----------------------------
-- Table: valuations
-- ----------------------------
DROP TABLE IF EXISTS \`valuations\`;
CREATE TABLE `valuations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` INT UNSIGNED NOT NULL,
  `valuer_id` INT UNSIGNED NOT NULL,
  `client_id` INT UNSIGNED NOT NULL,
  `purpose` VARCHAR(50) NOT NULL,
  `market_value` DECIMAL(15,2) NULL,
  `forced_sale_value` DECIMAL(15,2) NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `report_url` VARCHAR(255) NULL,
  `fee` DECIMAL(15,2) NOT NULL,
  `scheduled_date` DATE NULL,
  `completed_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `valuations_property_id_index` (`property_id`),
  INDEX `valuations_valuer_id_index` (`valuer_id`),
  INDEX `valuations_client_id_index` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ----------------------------
-- Table: surveys
-- ----------------------------
DROP TABLE IF EXISTS \`surveys\`;
CREATE TABLE `surveys` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` INT UNSIGNED NOT NULL,
  `surveyor_id` INT UNSIGNED NOT NULL,
  `client_id` INT UNSIGNED NOT NULL,
  `survey_type` VARCHAR(50) NOT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `report_url` VARCHAR(255) NULL,
  `fee` DECIMAL(15,2) NOT NULL,
  `scheduled_date` DATE NULL,
  `completed_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `surveys_property_id_index` (`property_id`),
  INDEX `surveys_surveyor_id_index` (`surveyor_id`),
  INDEX `surveys_client_id_index` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


SET FOREIGN_KEY_CHECKS=1;
