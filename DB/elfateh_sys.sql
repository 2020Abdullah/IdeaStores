-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 27, 2025 at 04:12 PM
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
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `name`, `details`, `is_default`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'نقدى', 'درج الخزنة', 1, NULL, '2025-08-16 11:57:30', '2025-08-16 12:05:50'),
(2, 'فودافون كاش 177', '01013798177', 0, NULL, '2025-08-16 11:58:13', '2025-08-16 11:58:13'),
(3, 'فودافون كاش 97', '01070380597', 0, NULL, '2025-08-16 11:58:32', '2025-08-16 12:04:37'),
(4, 'حساب CIP', '0', 0, NULL, '2025-08-16 12:06:37', '2025-08-16 12:06:37'),
(5, 'انستا باى', '01070380597', 0, NULL, '2025-08-16 12:07:11', '2025-08-16 12:07:11');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_warehouse`
--

CREATE TABLE `wallet_warehouse` (
  `id` bigint UNSIGNED NOT NULL,
  `wallet_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallet_warehouse`
--

INSERT INTO `wallet_warehouse` (`id`, `wallet_id`, `warehouse_id`, `created_at`, `updated_at`) VALUES
(4, 5, 2, NULL, NULL),
(5, 4, 2, NULL, NULL),
(7, 4, 3, NULL, NULL),
(8, 5, 3, NULL, NULL),
(9, 2, 2, NULL, NULL),
(10, 3, 2, NULL, NULL),
(11, 2, 3, NULL, NULL),
(12, 3, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('main','toridat','la7amat') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint NOT NULL DEFAULT '0',
  `statue` tinyint NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `name`, `type`, `is_default`, `statue`, `deleted_at`, `created_at`, `updated_at`) VALUES
(2, 'خزنة التوريدات', 'toridat', 1, 1, NULL, '2025-08-16 11:29:26', '2025-08-16 11:29:26'),
(3, 'خزنة اللحامات', 'la7amat', 0, 1, NULL, '2025-08-16 11:29:36', '2025-08-16 11:29:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallet_warehouse`
--
ALTER TABLE `wallet_warehouse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallet_warehouse_wallet_id_foreign` (`wallet_id`),
  ADD KEY `wallet_warehouse_warehouse_id_foreign` (`warehouse_id`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `wallet_warehouse`
--
ALTER TABLE `wallet_warehouse`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `wallet_warehouse`
--
ALTER TABLE `wallet_warehouse`
  ADD CONSTRAINT `wallet_warehouse_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wallet_warehouse_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
