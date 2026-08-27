-- =============================================================================
-- SQL UPDATE SCRIPT - DONASIBACKEND (26 AGUSTUS 2026)
-- Cara Penggunaan di phpMyAdmin:
-- 1. Buka phpMyAdmin di cPanel
-- 2. Pilih database project Anda di sidebar kiri
-- 3. Klik tab "SQL" di bagian atas
-- 4. Copy & Paste seluruh isi query di bawah ini, lalu klik "Go / Kirim"
-- =============================================================================

-- 1. Update tabel `settings`
ALTER TABLE `settings` 
ADD COLUMN `receipt_template` TINYINT UNSIGNED NOT NULL DEFAULT 1,
ADD COLUMN `receipt_footer` TEXT NULL AFTER `address`;

-- 2. Update tabel `donations`
ALTER TABLE `donations` 
ADD COLUMN `proof_image` VARCHAR(255) NULL AFTER `note`,
ADD COLUMN `bank_account` VARCHAR(255) NULL AFTER `recipient`,
ADD COLUMN `bank_name` VARCHAR(255) NULL AFTER `bank_account`,
ADD COLUMN `account_number` VARCHAR(255) NULL AFTER `bank_name`,
ADD COLUMN `account_name` VARCHAR(255) NULL AFTER `account_number`;

-- 3. Sinkronisasi tabel `migrations` Laravel
-- (Supaya Laravel mencatat bahwa 4 migrasi terbaru ini sudah selesai dieksekusi)
SET @next_batch = (SELECT IFNULL(MAX(batch), 0) + 1 FROM `migrations`);

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2026_08_25_000000_add_receipt_template_to_settings_table', @next_batch),
('2026_08_26_000000_add_proof_image_to_donations_table', @next_batch),
('2026_08_26_130900_add_bank_account_columns_to_donations_table', @next_batch),
('2026_08_26_145500_add_receipt_footer_to_settings_table', @next_batch);
