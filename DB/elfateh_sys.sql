-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 02, 2025 at 03:25 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elfateh_sys`
--

-- --------------------------------------------------------

--
-- Table structure for table `invoice_supplier_costs`
--

CREATE TABLE `invoice_supplier_costs` (
  `id` bigint UNSIGNED NOT NULL,
  `supplier_invoice_id` bigint UNSIGNED NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `invoice_supplier_costs`
--

TRUNCATE TABLE `invoice_supplier_costs`;
--
-- Dumping data for table `invoice_supplier_costs`
--

INSERT INTO `invoice_supplier_costs` (`id`, `supplier_invoice_id`, `description`, `amount`, `created_at`, `updated_at`) VALUES
(166, 59, 'شحن', '500.00', '2025-08-02 14:19:29', '2025-08-02 14:19:29'),
(167, 59, 'إكرامية', '200.00', '2025-08-02 14:19:29', '2025-08-02 14:19:29');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_invoices`
--

CREATE TABLE `supplier_invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `invoice_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_date` date NOT NULL,
  `invoice_type` enum('cash','credit','opening_balance') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_staute` tinyint NOT NULL DEFAULT '0' COMMENT 'حالة الفاتورة',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'إجمالي المدفوع',
  `cost_price` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'التكاليف',
  `total_amount_invoice` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'إجمالي الفاتورة بدون تكاليف',
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'إجمالي الفاتورة',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `warehouse_id` bigint DEFAULT NULL,
  `wallet_id` bigint DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `supplier_invoices`
--

TRUNCATE TABLE `supplier_invoices`;
--
-- Dumping data for table `supplier_invoices`
--

INSERT INTO `supplier_invoices` (`id`, `supplier_id`, `invoice_code`, `invoice_date`, `invoice_type`, `invoice_staute`, `paid_amount`, `cost_price`, `total_amount_invoice`, `total_amount`, `notes`, `warehouse_id`, `wallet_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(49, 17, 'SU-20251', '2025-08-01', 'opening_balance', 1, '1000.00', '0.00', '2000.00', '2000.00', NULL, NULL, NULL, NULL, '2025-08-02 10:58:06', '2025-08-02 11:17:57'),
(59, 17, 'SU-20252', '2025-08-02', 'credit', 0, '0.00', '700.00', '4000.00', '4700.00', NULL, NULL, NULL, NULL, '2025-08-02 12:03:16', '2025-08-02 14:19:29');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_invoice_items`
--

CREATE TABLE `supplier_invoice_items` (
  `id` bigint UNSIGNED NOT NULL,
  `supplier_invoice_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `size_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1' COMMENT 'الكمية',
  `pricePerMeter` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'سعر المتر',
  `length` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT 'الطول',
  `purchase_price` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'سعر الشراء',
  `total_price` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'سعر الإجمالي',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `supplier_invoice_items`
--

TRUNCATE TABLE `supplier_invoice_items`;
--
-- Dumping data for table `supplier_invoice_items`
--

INSERT INTO `supplier_invoice_items` (`id`, `supplier_invoice_id`, `category_id`, `product_id`, `unit_id`, `size_id`, `quantity`, `pricePerMeter`, `length`, `purchase_price`, `total_price`, `created_at`, `updated_at`) VALUES
(70, 59, 23, 27, 4, 3, 10, '0.00', '0.00', '100.00', '1000.00', '2025-08-02 14:19:29', '2025-08-02 14:19:29'),
(71, 59, 23, 28, 4, 4, 30, '0.00', '0.00', '100.00', '3000.00', '2025-08-02 14:19:29', '2025-08-02 14:19:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `invoice_supplier_costs`
--
ALTER TABLE `invoice_supplier_costs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_supplier_costs_supplier_invoice_id_foreign` (`supplier_invoice_id`);

--
-- Indexes for table `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_invoices_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `supplier_invoice_items`
--
ALTER TABLE `supplier_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_invoice_items_supplier_invoice_id_foreign` (`supplier_invoice_id`),
  ADD KEY `supplier_invoice_items_category_id_foreign` (`category_id`),
  ADD KEY `supplier_invoice_items_product_id_foreign` (`product_id`),
  ADD KEY `supplier_invoice_items_unit_id_foreign` (`unit_id`),
  ADD KEY `supplier_invoice_items_size_id_foreign` (`size_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `invoice_supplier_costs`
--
ALTER TABLE `invoice_supplier_costs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT for table `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `supplier_invoice_items`
--
ALTER TABLE `supplier_invoice_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoice_supplier_costs`
--
ALTER TABLE `invoice_supplier_costs`
  ADD CONSTRAINT `invoice_supplier_costs_supplier_invoice_id_foreign` FOREIGN KEY (`supplier_invoice_id`) REFERENCES `supplier_invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  ADD CONSTRAINT `supplier_invoices_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `supplier_invoice_items`
--
ALTER TABLE `supplier_invoice_items`
  ADD CONSTRAINT `supplier_invoice_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `supplier_invoice_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `supplier_invoice_items_size_id_foreign` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `supplier_invoice_items_supplier_invoice_id_foreign` FOREIGN KEY (`supplier_invoice_id`) REFERENCES `supplier_invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_invoice_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
