-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 09, 2025 at 03:33 PM
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
-- Table structure for table `account_transactions`
--

CREATE TABLE `account_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `direction` enum('in','out') COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` enum('cash','bank','vodafone_cash','instapay') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `transaction_type` enum('payment','expense','purchase','sale','added','transfer','open_balance','profit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `related_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_id` bigint UNSIGNED DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL COMMENT 'تاريخ الإضافة بتاريخ الفاتورة',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `apps`
--

CREATE TABLE `apps` (
  `id` bigint UNSIGNED NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_info` text COLLATE utf8mb4_unicode_ci,
  `Tax_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'رقم التعريف الضريبي',
  `statue` tinyint NOT NULL DEFAULT '1',
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `apps`
--

INSERT INTO `apps` (`id`, `logo`, `company_name`, `company_info`, `Tax_number`, `statue`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'uploads/setting/1754739335.png', 'شركه الفتح للحام وتوريد السيور الناقلة', NULL, NULL, 1, 1, '2025-08-09 10:35:35', '2025-08-09 10:35:35');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'سيور', NULL, '2025-08-09 10:39:08', '2025-08-09 10:39:08'),
(2, 'شيفرون', 1, '2025-08-09 10:39:20', '2025-08-09 10:39:20'),
(3, 'شيفرون عالي', 2, '2025-08-09 10:41:37', '2025-08-09 10:41:37'),
(4, 'شيفرون واطي', 2, '2025-08-09 10:41:55', '2025-08-09 10:41:55'),
(5, 'بكر', NULL, '2025-08-09 10:48:20', '2025-08-09 10:48:20');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `busniess_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `busniess_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsUp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `place` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exponses`
--

CREATE TABLE `exponses` (
  `id` bigint UNSIGNED NOT NULL,
  `expenseable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expenseable_id` bigint UNSIGNED NOT NULL,
  `expense_item_id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `date` date DEFAULT NULL COMMENT 'تاريخ الإضافة بتاريخ الفاتورة',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exponses`
--

INSERT INTO `exponses` (`id`, `expenseable_type`, `expenseable_id`, `expense_item_id`, `account_id`, `amount`, `note`, `date`, `created_at`, `updated_at`) VALUES
(7, 'App\\Models\\Supplier_invoice', 5, 1, 2, '100.00', 'تكاليف إضافية', '2025-08-09', '2025-08-09 11:36:11', '2025-08-09 13:50:35'),
(8, 'App\\Models\\Supplier_invoice', 5, 2, 2, '200.00', 'تكاليف إضافية', '2025-08-09', '2025-08-09 11:36:11', '2025-08-09 13:50:35');

-- --------------------------------------------------------

--
-- Table structure for table `exponse_items`
--

CREATE TABLE `exponse_items` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `affect_debt` tinyint(1) NOT NULL COMMENT 'هل يؤثر علي المديونة ؟',
  `affect_wallet` tinyint(1) NOT NULL COMMENT 'هل يؤثر علي الخزنة',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exponse_items`
--

INSERT INTO `exponse_items` (`id`, `name`, `affect_debt`, `affect_wallet`, `created_at`, `updated_at`) VALUES
(1, 'نقل', 0, 1, '2025-08-09 10:50:19', '2025-08-09 10:50:19'),
(2, 'إكرامية', 0, 1, '2025-08-09 10:50:29', '2025-08-09 10:50:29'),
(3, 'كهرباء', 0, 1, '2025-08-09 10:50:35', '2025-08-09 10:50:35'),
(4, 'كرتة', 0, 1, '2025-08-09 10:50:40', '2025-08-09 10:50:40'),
(5, 'غفرة', 0, 1, '2025-08-09 10:50:53', '2025-08-09 10:50:53'),
(6, 'المكان', 0, 1, '2025-08-09 10:50:57', '2025-08-09 10:50:57'),
(7, 'مكن', 0, 1, '2025-08-09 10:51:39', '2025-08-09 10:51:39');

-- --------------------------------------------------------

--
-- Table structure for table `external_debts`
--

CREATE TABLE `external_debts` (
  `id` bigint UNSIGNED NOT NULL,
  `debtable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `debtable_id` bigint UNSIGNED NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `paid` decimal(15,2) NOT NULL,
  `remaining` decimal(15,2) NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `date` date DEFAULT NULL COMMENT 'تاريخ الإضافة بتاريخ الفاتورة',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `external_debts`
--

INSERT INTO `external_debts` (`id`, `debtable_type`, `debtable_id`, `description`, `amount`, `paid`, `remaining`, `is_paid`, `date`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\Supplier_invoice', 4, 'رصيد افتتاحي للمورد محمد', '1000.00', '0.00', '1000.00', 0, '2025-08-09', '2025-08-09 11:35:18', '2025-08-09 11:35:18');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_product_costs`
--

CREATE TABLE `invoice_product_costs` (
  `id` bigint UNSIGNED NOT NULL,
  `stock_id` bigint UNSIGNED DEFAULT NULL,
  `base_cost` decimal(10,2) NOT NULL COMMENT 'السعر الأساسي من المورد',
  `cost_share` decimal(10,2) NOT NULL COMMENT 'سعر التكلفة للصنف',
  `suggested_price` decimal(10,2) DEFAULT NULL COMMENT 'سعر البيع المقترح',
  `rate` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_product_costs`
--

INSERT INTO `invoice_product_costs` (`id`, `stock_id`, `base_cost`, `cost_share`, `suggested_price`, `rate`, `created_at`, `updated_at`) VALUES
(1, 1, '100.00', '302000.00', '511.02', 2, '2025-08-09 11:36:11', '2025-08-09 13:50:35');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_07_09_114532_create_apps_table', 1),
(6, '2025_07_15_150145_create_warehouses_table', 1),
(7, '2025_07_16_163054_create_store_houses_table', 1),
(8, '2025_07_16_173651_create_sizes_table', 1),
(9, '2025_07_17_130952_create_units_table', 1),
(10, '2025_07_17_131009_create_categories_table', 1),
(11, '2025_07_17_131329_create_products_table', 1),
(12, '2025_07_17_163903_create_suppliers_table', 1),
(13, '2025_07_17_163904_create_customers_table', 1),
(14, '2025_07_17_163917_create_accounts_table', 1),
(15, '2025_07_17_163918_create_account_transactions_table', 1),
(16, '2025_07_17_163919_create_wallets_table', 1),
(17, '2025_07_19_162415_create_supplier_invoices_table', 1),
(18, '2025_07_19_162424_create_supplier_invoice_items_table', 1),
(19, '2025_07_20_163914_create_stocks_table', 1),
(20, '2025_07_20_163916_create_stock_movements_table', 1),
(21, '2025_07_21_101559_create_stock_adjustments_table', 1),
(22, '2025_07_21_101611_create_stock_adjustment_items_table', 1),
(23, '2025_07_25_214147_create_wallet_movements_table', 1),
(24, '2025_08_01_003208_create_invoice_product_costs_table', 1),
(25, '2025_08_05_110354_create_exponse_items_table', 1),
(26, '2025_08_05_110659_create_exponses_table', 1),
(27, '2025_08_05_125722_create_external_debts_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `width` decimal(8,1) DEFAULT NULL,
  `length` decimal(8,1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `unit_id`, `name`, `width`, `length`, `created_at`, `updated_at`) VALUES
(1, 5, 4, 'بكر 25', NULL, NULL, '2025-08-09 11:10:49', '2025-08-09 11:12:10'),
(2, 3, 2, 'سير G7', NULL, NULL, '2025-08-09 11:12:33', '2025-08-09 11:12:33');

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `id` bigint UNSIGNED NOT NULL,
  `width` int NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`id`, `width`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 20, NULL, '2025-08-09 10:36:51', '2025-08-09 10:36:51'),
(2, 25, NULL, '2025-08-09 10:36:54', '2025-08-09 10:36:54'),
(3, 30, NULL, '2025-08-09 10:36:58', '2025-08-09 10:36:58'),
(4, 35, NULL, '2025-08-09 10:37:02', '2025-08-09 10:37:02'),
(5, 40, NULL, '2025-08-09 10:37:07', '2025-08-09 10:37:07'),
(6, 45, NULL, '2025-08-09 10:37:11', '2025-08-09 10:37:11'),
(7, 50, NULL, '2025-08-09 10:37:15', '2025-08-09 10:37:15'),
(8, 55, NULL, '2025-08-09 10:37:18', '2025-08-09 10:37:18'),
(9, 60, NULL, '2025-08-09 10:37:23', '2025-08-09 10:37:23'),
(10, 65, NULL, '2025-08-09 10:37:26', '2025-08-09 10:37:26'),
(11, 70, NULL, '2025-08-09 10:37:31', '2025-08-09 10:37:31'),
(12, 75, NULL, '2025-08-09 10:37:39', '2025-08-09 10:37:39'),
(13, 80, NULL, '2025-08-09 10:37:43', '2025-08-09 10:37:43'),
(14, 85, NULL, '2025-08-09 10:37:46', '2025-08-09 10:37:46'),
(15, 90, NULL, '2025-08-09 10:37:50', '2025-08-09 10:37:50'),
(16, 95, NULL, '2025-08-09 10:38:00', '2025-08-09 10:38:00'),
(17, 100, NULL, '2025-08-09 10:38:04', '2025-08-09 10:38:04'),
(18, 110, NULL, '2025-08-09 10:38:10', '2025-08-09 10:38:10'),
(19, 120, NULL, '2025-08-09 10:38:15', '2025-08-09 10:38:15'),
(20, 150, NULL, '2025-08-09 10:38:21', '2025-08-09 10:38:21');

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `store_house_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `initial_quantity` int NOT NULL DEFAULT '0' COMMENT 'الكمية الواردة',
  `remaining_quantity` int NOT NULL DEFAULT '0' COMMENT 'الكمية المتبقية',
  `date` date DEFAULT NULL COMMENT 'تاريخ الإضافة بتاريخ الفاتورة',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`id`, `category_id`, `product_id`, `store_house_id`, `unit_id`, `initial_quantity`, `remaining_quantity`, `date`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 1, 4, 90, 90, '2025-08-09', '2025-08-09 11:36:11', '2025-08-09 13:50:35');

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustments`
--

CREATE TABLE `stock_adjustments` (
  `id` bigint UNSIGNED NOT NULL,
  `type` enum('surplus','shortage') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'عجز أم فائض',
  `adjusted_at` timestamp NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint UNSIGNED NOT NULL,
  `date` date DEFAULT NULL COMMENT 'تاريخ الإضافة',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustment_items`
--

CREATE TABLE `stock_adjustment_items` (
  `id` bigint UNSIGNED NOT NULL,
  `stock_adjustment_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `system_qty` int NOT NULL COMMENT 'الكمية الدفترية',
  `actual_qty` int NOT NULL COMMENT 'القيمة الفعلية',
  `difference` int NOT NULL COMMENT 'قيمة الجرد',
  `date` date DEFAULT NULL COMMENT 'تاريخ الإضافة',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `stock_id` bigint UNSIGNED NOT NULL,
  `type` enum('in','out') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'in => إضافة / out => خصم',
  `quantity` int NOT NULL DEFAULT '0' COMMENT 'الكمية المحركة',
  `note` text COLLATE utf8mb4_unicode_ci COMMENT 'شراء / بيع',
  `source_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'كود الفاتورة',
  `date` date DEFAULT NULL COMMENT 'تاريخ الإضافة بتاريخ الفاتورة',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `supplier_id`, `stock_id`, `type`, `quantity`, `note`, `source_code`, `date`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'in', 20, 'شراء (تعديل)', 'SU-20252', '2025-08-09', '2025-08-09 11:36:11', '2025-08-09 12:40:34');

-- --------------------------------------------------------

--
-- Table structure for table `store_houses`
--

CREATE TABLE `store_houses` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statue` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `store_houses`
--

INSERT INTO `store_houses` (`id`, `name`, `phone`, `address`, `statue`, `created_at`, `updated_at`) VALUES
(1, 'مخزن رئيسي', '01070380594', 'شارع النقطة - المرج', 1, '2025-08-09 11:29:00', '2025-08-09 11:29:00');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `busniess_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `busniess_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsUp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `place` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `phone`, `busniess_name`, `busniess_type`, `whatsUp`, `place`, `notes`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'محمد', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-09 10:53:40', '2025-08-09 10:53:40');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_invoices`
--

CREATE TABLE `supplier_invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `invoice_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_date` date NOT NULL,
  `invoice_type` enum('cash','credit','opening_balance') COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_staute` tinyint NOT NULL DEFAULT '0' COMMENT 'حالة الفاتورة',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'إجمالي المدفوع',
  `cost_price` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'التكاليف',
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'إجمالي الفاتورة',
  `total_amount_invoice` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'إجمالي الفاتورة بدون تكاليف',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `warehouse_id` bigint UNSIGNED DEFAULT NULL,
  `wallet_id` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier_invoices`
--

INSERT INTO `supplier_invoices` (`id`, `supplier_id`, `invoice_code`, `invoice_date`, `invoice_type`, `invoice_staute`, `paid_amount`, `cost_price`, `total_amount`, `total_amount_invoice`, `notes`, `warehouse_id`, `wallet_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(4, 1, 'SU-20251', '2025-08-09', 'opening_balance', 0, '0.00', '0.00', '1000.00', '1000.00', NULL, NULL, NULL, NULL, '2025-08-09 11:35:18', '2025-08-09 11:35:18'),
(5, 1, 'SU-20252', '2025-08-09', 'cash', 1, '2000.00', '300.00', '2300.00', '2000.00', NULL, 1, 1, NULL, '2025-08-09 11:36:11', '2025-08-09 13:50:35');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_invoice_items`
--

CREATE TABLE `supplier_invoice_items` (
  `id` bigint UNSIGNED NOT NULL,
  `supplier_invoice_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `size_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1' COMMENT 'الكمية',
  `pricePerMeter` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'سعر المتر',
  `length` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'الطول',
  `purchase_price` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'سعر الشراء',
  `total_price` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'سعر الإجمالي',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier_invoice_items`
--

INSERT INTO `supplier_invoice_items` (`id`, `supplier_invoice_id`, `category_id`, `product_id`, `unit_id`, `size_id`, `quantity`, `pricePerMeter`, `length`, `purchase_price`, `total_price`, `created_at`, `updated_at`) VALUES
(6, 5, 5, 1, 4, 2, 20, '0.00', '25.00', '100.00', '2000.00', '2025-08-09 13:50:35', '2025-08-09 13:50:35');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `symbol`, `created_at`, `updated_at`) VALUES
(1, 'متر', 'م', '2025-08-09 10:35:56', '2025-08-09 10:35:56'),
(2, 'سنتيمتر', 'سم', '2025-08-09 10:36:04', '2025-08-09 10:36:04'),
(3, 'ملي', 'مم', '2025-08-09 10:36:13', '2025-08-09 10:36:13'),
(4, 'قطع', 'ق', '2025-08-09 10:36:24', '2025-08-09 10:36:24'),
(5, 'كيلو', 'ك', '2025-08-09 10:36:34', '2025-08-09 10:36:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_admin` tinyint NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `google_id`, `avatar`, `is_admin`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@example.com', NULL, '$2y$12$ldczOQGQ..UJwKNQjvdc9.D/iIJ819oNyGBsXD6X0zFqqwGn1XD32', NULL, NULL, 1, NULL, '2025-08-09 10:35:02', '2025-08-09 10:35:02');

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` enum('cash','bank','vodafone_cash','instapay') COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_balance` decimal(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `account_id`, `name`, `method`, `details`, `current_balance`, `created_at`, `updated_at`) VALUES
(1, 2, 'محفظة الكاش', 'cash', '0', '1500.00', '2025-08-09 11:04:10', '2025-08-09 12:45:55'),
(2, 2, 'فودافون كاش', 'vodafone_cash', '01070380597', '0.00', '2025-08-09 11:04:34', '2025-08-09 11:25:17'),
(3, 2, 'انستا باى', 'instapay', '01070380597', '0.00', '2025-08-09 11:05:11', '2025-08-09 11:05:11'),
(4, 2, 'حساب CIP', 'bank', '0', '0.00', '2025-08-09 11:05:35', '2025-08-09 11:05:35'),
(5, 3, 'الكاش', 'cash', '0', '0.00', '2025-08-09 11:06:06', '2025-08-09 11:06:06'),
(6, 3, 'فودافون كاش', 'vodafone_cash', '01070380597', '0.00', '2025-08-09 11:06:18', '2025-08-09 11:06:18'),
(7, 3, 'انستا باى', 'instapay', '0', '0.00', '2025-08-09 11:06:30', '2025-08-09 11:06:30'),
(8, 3, 'حساب cip', 'bank', '0', '0.00', '2025-08-09 11:06:45', '2025-08-09 11:06:45');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_movements`
--

CREATE TABLE `wallet_movements` (
  `id` bigint UNSIGNED NOT NULL,
  `wallet_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `direction` enum('in','out') COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallet_movements`
--

INSERT INTO `wallet_movements` (`id`, `wallet_id`, `amount`, `direction`, `note`, `source_code`, `created_at`, `updated_at`) VALUES
(2, 1, '-2300.00', 'out', 'فاتورة شراء', 'SU-20252', '2025-08-09 11:36:11', '2025-08-09 13:50:35'),
(3, 1, '3000.00', 'in', 'إضافة رصيد يدوى', NULL, '2025-08-09 12:45:55', '2025-08-09 12:45:55');

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
-- Indexes for table `account_transactions`
--
ALTER TABLE `account_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_transactions_account_id_foreign` (`account_id`),
  ADD KEY `account_transactions_related_type_related_id_index` (`related_type`,`related_id`);

--
-- Indexes for table `apps`
--
ALTER TABLE `apps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `apps_user_id_foreign` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exponses`
--
ALTER TABLE `exponses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exponses_expenseable_type_expenseable_id_index` (`expenseable_type`,`expenseable_id`),
  ADD KEY `exponses_expense_item_id_foreign` (`expense_item_id`),
  ADD KEY `exponses_account_id_foreign` (`account_id`);

--
-- Indexes for table `exponse_items`
--
ALTER TABLE `exponse_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `external_debts`
--
ALTER TABLE `external_debts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `external_debts_debtable_type_debtable_id_index` (`debtable_type`,`debtable_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `invoice_product_costs`
--
ALTER TABLE `invoice_product_costs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_product_costs_stock_id_foreign` (`stock_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_unit_id_foreign` (`unit_id`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stocks_category_id_foreign` (`category_id`),
  ADD KEY `stocks_product_id_foreign` (`product_id`),
  ADD KEY `stocks_store_house_id_foreign` (`store_house_id`),
  ADD KEY `stocks_unit_id_foreign` (`unit_id`);

--
-- Indexes for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_adjustments_user_id_foreign` (`user_id`);

--
-- Indexes for table `stock_adjustment_items`
--
ALTER TABLE `stock_adjustment_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_adjustment_items_stock_adjustment_id_foreign` (`stock_adjustment_id`),
  ADD KEY `stock_adjustment_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_movements_supplier_id_foreign` (`supplier_id`),
  ADD KEY `stock_movements_stock_id_foreign` (`stock_id`);

--
-- Indexes for table `store_houses`
--
ALTER TABLE `store_houses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_invoices_supplier_id_foreign` (`supplier_id`),
  ADD KEY `supplier_invoices_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `supplier_invoices_wallet_id_foreign` (`wallet_id`);

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
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallets_account_id_foreign` (`account_id`);

--
-- Indexes for table `wallet_movements`
--
ALTER TABLE `wallet_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallet_movements_wallet_id_foreign` (`wallet_id`);

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
-- AUTO_INCREMENT for table `account_transactions`
--
ALTER TABLE `account_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `apps`
--
ALTER TABLE `apps`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exponses`
--
ALTER TABLE `exponses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `exponse_items`
--
ALTER TABLE `exponse_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `external_debts`
--
ALTER TABLE `external_debts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_product_costs`
--
ALTER TABLE `invoice_product_costs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_adjustment_items`
--
ALTER TABLE `stock_adjustment_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `store_houses`
--
ALTER TABLE `store_houses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `supplier_invoice_items`
--
ALTER TABLE `supplier_invoice_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `wallet_movements`
--
ALTER TABLE `wallet_movements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_transactions`
--
ALTER TABLE `account_transactions`
  ADD CONSTRAINT `account_transactions_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `apps`
--
ALTER TABLE `apps`
  ADD CONSTRAINT `apps_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exponses`
--
ALTER TABLE `exponses`
  ADD CONSTRAINT `exponses_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `exponses_expense_item_id_foreign` FOREIGN KEY (`expense_item_id`) REFERENCES `exponse_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoice_product_costs`
--
ALTER TABLE `invoice_product_costs`
  ADD CONSTRAINT `invoice_product_costs_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stocks`
--
ALTER TABLE `stocks`
  ADD CONSTRAINT `stocks_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stocks_store_house_id_foreign` FOREIGN KEY (`store_house_id`) REFERENCES `store_houses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stocks_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD CONSTRAINT `stock_adjustments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_adjustment_items`
--
ALTER TABLE `stock_adjustment_items`
  ADD CONSTRAINT `stock_adjustment_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_adjustment_items_stock_adjustment_id_foreign` FOREIGN KEY (`stock_adjustment_id`) REFERENCES `stock_adjustments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  ADD CONSTRAINT `supplier_invoices_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_invoices_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_invoices_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `supplier_invoice_items`
--
ALTER TABLE `supplier_invoice_items`
  ADD CONSTRAINT `supplier_invoice_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_invoice_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_invoice_items_size_id_foreign` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `supplier_invoice_items_supplier_invoice_id_foreign` FOREIGN KEY (`supplier_invoice_id`) REFERENCES `supplier_invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_invoice_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallet_movements`
--
ALTER TABLE `wallet_movements`
  ADD CONSTRAINT `wallet_movements_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
