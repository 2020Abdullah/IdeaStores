-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 09, 2025 at 03:30 PM
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
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `accountable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accountable_id` bigint UNSIGNED DEFAULT NULL,
  `type` enum('warehouse','supplier','customer','partner','expense') COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_capital_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_profit_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `current_balance` decimal(10,0) NOT NULL DEFAULT '0',
  `is_main` tinyint NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `name`, `accountable_type`, `accountable_id`, `type`, `total_capital_balance`, `total_profit_balance`, `current_balance`, `is_main`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'حساب المورد: محمد', 'App\\Models\\Supplier', 1, 'supplier', '0.00', '0.00', '1000', 0, NULL, '2025-08-09 10:53:40', '2025-08-09 11:36:11'),
(2, 'حساب خزنة التوريدات', 'App\\Models\\Warehouse', 1, 'warehouse', '1500.00', '0.00', '1500', 0, NULL, '2025-08-09 11:03:32', '2025-08-09 12:45:55'),
(3, 'حساب خزنة اللحامات', 'App\\Models\\Warehouse', 2, 'warehouse', '0.00', '0.00', '0', 0, NULL, '2025-08-09 11:03:45', '2025-08-09 11:03:45');

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('main','toridat','la7amat') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_main` tinyint NOT NULL DEFAULT '0',
  `statue` tinyint NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `name`, `type`, `is_main`, `statue`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'خزنة التوريدات', 'toridat', 0, 1, NULL, '2025-08-09 11:03:32', '2025-08-09 11:03:32'),
(2, 'خزنة اللحامات', 'la7amat', 0, 1, NULL, '2025-08-09 11:03:45', '2025-08-09 11:03:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accounts_accountable_type_accountable_id_index` (`accountable_type`,`accountable_id`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
