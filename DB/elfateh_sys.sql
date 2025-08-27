-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 27, 2025 at 03:19 PM
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
  `type` enum('warehouse','wallet','supplier','customer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_main` tinyint NOT NULL DEFAULT '0',
  `current_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `name`, `accountable_type`, `accountable_id`, `type`, `is_main`, `current_balance`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'حساب خزنة التوريدات', 'App\\Models\\Warehouse', 2, 'warehouse', 0, '0.00', NULL, '2025-08-16 11:29:26', '2025-08-16 11:29:26'),
(2, 'حساب خزنة اللحامات', 'App\\Models\\Warehouse', 3, 'warehouse', 0, '0.00', NULL, '2025-08-16 11:29:36', '2025-08-16 11:29:36'),
(20, 'حساب مورد: أشرف', 'App\\Models\\Supplier', 6, 'supplier', 0, '0.00', NULL, '2025-08-26 12:54:55', '2025-08-26 12:54:55'),
(21, 'حساب العميل: سالم', 'App\\Models\\Customer', 9, 'customer', 0, '0.00', NULL, '2025-08-26 12:58:53', '2025-08-26 12:58:53');

-- --------------------------------------------------------

--
-- Table structure for table `account_transactions`
--

CREATE TABLE `account_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED DEFAULT NULL,
  `wallet_id` bigint UNSIGNED DEFAULT NULL,
  `direction` enum('in','out') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `profit_amount` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'الربح من العملية إن وجد',
  `transaction_type` enum('payment','expense','purchase','sale','profit_adjust','added','transfer','refund') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `related_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_id` bigint UNSIGNED DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL COMMENT 'تاريخ العملية الحقيقي',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_transactions`
--

INSERT INTO `account_transactions` (`id`, `account_id`, `wallet_id`, `direction`, `amount`, `profit_amount`, `transaction_type`, `related_type`, `related_id`, `description`, `source_code`, `date`, `created_at`, `updated_at`) VALUES
(62, 1, 1, 'out', '-700.00', '0.00', 'expense', 'App\\Models\\Supplier_invoice', 17, 'مصروفات فواتير موردين', 'SU-20251', '2025-08-01', '2025-08-26 12:56:30', '2025-08-27 13:41:11'),
(63, 1, 5, 'in', '2000.00', '0.00', 'payment', 'App\\Models\\Customer', 9, 'دفعة مقدمة', NULL, NULL, '2025-08-26 12:59:21', '2025-08-26 12:59:21');

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
  `is_active` tinyint NOT NULL DEFAULT '1',
  `secret_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `apps`
--

INSERT INTO `apps` (`id`, `logo`, `company_name`, `company_info`, `Tax_number`, `statue`, `user_id`, `is_active`, `secret_key`, `created_at`, `updated_at`) VALUES
(1, 'uploads/setting/1755346235.png', 'شركه الفتح للحام وتوريد السيور الناقلة', NULL, NULL, 1, 1, 1, 'cc73ba33-e494-46ce-9a3d-ce8b37926685', '2025-08-16 11:10:35', '2025-08-27 12:09:36');

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

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `busniess_name`, `busniess_type`, `whatsUp`, `place`, `notes`, `created_at`, `updated_at`) VALUES
(9, 'سالم', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-26 12:58:53', '2025-08-26 12:58:53');

-- --------------------------------------------------------

--
-- Table structure for table `customer_dues`
--

CREATE TABLE `customer_dues` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `customer_invoice_id` bigint UNSIGNED DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `due_date` date DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_invoices`
--

CREATE TABLE `customer_invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `type` enum('cash','credit','opening_balance') COLLATE utf8mb4_unicode_ci NOT NULL,
  `staute` tinyint NOT NULL DEFAULT '0' COMMENT 'حالة الفاتورة',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'إجمالي المدفوع',
  `cost_price` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'التكاليف',
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'إجمالي الفاتورة',
  `total_profit` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'سعر الإجمالي',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `warehouse_id` bigint UNSIGNED DEFAULT NULL,
  `wallet_id` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_invoices`
--

INSERT INTO `customer_invoices` (`id`, `customer_id`, `code`, `date`, `type`, `staute`, `paid_amount`, `cost_price`, `total_amount`, `total_profit`, `notes`, `warehouse_id`, `wallet_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(34, 9, 'CU-20251', '2025-08-02', 'credit', 1, '1200.00', '0.00', '1200.00', '172.70', NULL, NULL, NULL, NULL, '2025-08-26 13:00:28', '2025-08-27 13:35:16'),
(35, 9, 'CU-20252', '2025-08-02', 'opening_balance', 1, '800.00', '0.00', '800.00', '0.00', NULL, NULL, NULL, NULL, '2025-08-26 13:01:58', '2025-08-27 13:35:16');

-- --------------------------------------------------------

--
-- Table structure for table `customer_invoices_items`
--

CREATE TABLE `customer_invoices_items` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_invoice_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `unit_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` int NOT NULL COMMENT 'الكمية',
  `sale_price` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'سعر بيع الوحدة',
  `total_price` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'سعر الإجمالي',
  `profit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_profit` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'سعر الإجمالي',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_invoices_items`
--

INSERT INTO `customer_invoices_items` (`id`, `customer_invoice_id`, `category_id`, `product_id`, `unit_name`, `size_id`, `quantity`, `sale_price`, `total_price`, `profit`, `total_profit`, `deleted_at`, `created_at`, `updated_at`) VALUES
(47, 34, 7, 2, 'قطعة', 2, 10, '120.00', '1200.00', '17.00', '173.00', NULL, '2025-08-27 13:35:16', '2025-08-27 13:35:16');

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
  `source_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exponses`
--

INSERT INTO `exponses` (`id`, `expenseable_type`, `expenseable_id`, `expense_item_id`, `account_id`, `amount`, `note`, `date`, `source_code`, `created_at`, `updated_at`) VALUES
(72, 'App\\Models\\CustomerInvoices', 34, 7, NULL, '43.18', 'توزيع ربحية الفاتورة', '2025-08-02', 'CU-20251', '2025-08-27 13:35:16', '2025-08-27 13:35:16'),
(73, 'App\\Models\\CustomerInvoices', 34, 8, NULL, '43.18', 'توزيع ربحية الفاتورة', '2025-08-02', 'CU-20251', '2025-08-27 13:35:16', '2025-08-27 13:35:16'),
(74, 'App\\Models\\CustomerInvoices', 34, 9, NULL, '43.18', 'توزيع ربحية الفاتورة', '2025-08-02', 'CU-20251', '2025-08-27 13:35:16', '2025-08-27 13:35:16'),
(75, 'App\\Models\\CustomerInvoices', 34, 10, NULL, '43.18', 'توزيع ربحية الفاتورة', '2025-08-02', 'CU-20251', '2025-08-27 13:35:16', '2025-08-27 13:35:16'),
(78, 'App\\Models\\Supplier_invoice', 17, 1, 1, '500.00', 'تكاليف إضافية', '2025-08-01', 'SU-20251', '2025-08-27 13:41:11', '2025-08-27 13:41:11'),
(79, 'App\\Models\\Supplier_invoice', 17, 2, 1, '200.00', 'تكاليف إضافية', '2025-08-01', 'SU-20251', '2025-08-27 13:41:11', '2025-08-27 13:41:11');

-- --------------------------------------------------------

--
-- Table structure for table `exponse_items`
--

CREATE TABLE `exponse_items` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_profit` tinyint NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exponse_items`
--

INSERT INTO `exponse_items` (`id`, `name`, `is_profit`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'نقل', 0, NULL, '2025-08-16 11:22:08', '2025-08-16 11:22:08'),
(2, 'إكرامية', 0, NULL, '2025-08-16 11:26:20', '2025-08-16 11:26:20'),
(3, 'كرتة', 0, NULL, '2025-08-16 11:26:25', '2025-08-16 11:26:25'),
(4, 'كهرباء', 0, NULL, '2025-08-16 11:26:30', '2025-08-16 11:26:30'),
(5, 'مياه', 0, NULL, '2025-08-16 11:26:39', '2025-08-16 11:26:39'),
(6, 'إيجار', 0, NULL, '2025-08-16 11:26:50', '2025-08-16 11:26:50'),
(7, 'المكان', 1, NULL, '2025-08-16 11:27:10', '2025-08-23 09:47:59'),
(8, 'مكن', 1, NULL, '2025-08-16 11:27:17', '2025-08-23 09:48:20'),
(9, 'الشركاء', 1, NULL, '2025-08-20 09:48:05', '2025-08-23 09:48:28'),
(10, 'صاحب المكان', 1, NULL, '2025-08-20 09:48:23', '2025-08-23 09:48:33');

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
(43, 'App\\Models\\Supplier_invoice', 17, 'دين كامل على الفاتورة للمورد أشرف', '22000.00', '0.00', '22000.00', 0, '2025-08-01', '2025-08-27 13:41:11', '2025-08-27 13:41:11');

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
  `rate` int NOT NULL DEFAULT '0' COMMENT 'النسبة لعمل البيع المقترح',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_product_costs`
--

INSERT INTO `invoice_product_costs` (`id`, `stock_id`, `base_cost`, `cost_share`, `suggested_price`, `rate`, `created_at`, `updated_at`) VALUES
(17, 7, '10000.00', '10318.18', NULL, 0, '2025-08-26 12:56:30', '2025-08-27 13:41:11'),
(18, 8, '12000.00', '12381.82', NULL, 0, '2025-08-26 12:56:30', '2025-08-27 13:41:11');

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
(16, '2025_07_17_163919_create_wallets_table', 1),
(17, '2025_07_18_222930_create_wallet_warehouse_table', 1),
(18, '2025_07_19_162415_create_supplier_invoices_table', 1),
(19, '2025_07_19_162424_create_supplier_invoice_items_table', 1),
(22, '2025_07_21_101559_create_stock_adjustments_table', 1),
(23, '2025_07_21_101611_create_stock_adjustment_items_table', 1),
(25, '2025_08_01_003208_create_invoice_product_costs_table', 1),
(26, '2025_08_05_110354_create_exponse_items_table', 1),
(27, '2025_08_05_110659_create_exponses_table', 1),
(28, '2025_08_05_125722_create_external_debts_table', 1),
(33, '2025_07_17_163918_create_account_transactions_table', 3),
(34, '2025_07_25_214147_create_wallet_movements_table', 4),
(35, '2025_08_10_141937_create_payment_transactions_table', 5),
(41, '2025_07_20_163914_create_stocks_table', 6),
(42, '2025_07_20_163916_create_stock_movements_table', 6),
(43, '2025_08_14_131314_create_customer_invoices_table', 6),
(45, '2025_08_19_170305_create_customer_dues_table', 6),
(47, '2025_08_14_131624_create_customer_invoices_items_table', 7);

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
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `related_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_id` bigint UNSIGNED DEFAULT NULL,
  `direction` enum('in','out') COLLATE utf8mb4_unicode_ci NOT NULL,
  `wallet_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_date` date NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_transactions`
--

INSERT INTO `payment_transactions` (`id`, `related_type`, `related_id`, `direction`, `wallet_id`, `amount`, `payment_date`, `description`, `created_at`, `updated_at`) VALUES
(10, 'App\\Models\\Customer', 3, 'in', 2, '5000.00', '2025-08-24', 'دفعة مقدمة', '2025-08-24 09:14:08', '2025-08-24 09:14:08'),
(11, 'App\\Models\\Customer', 3, 'in', 2, '40000.00', '2025-08-25', 'دفعة مقدمة', '2025-08-25 08:07:01', '2025-08-25 08:07:01'),
(12, 'App\\Models\\Supplier', 5, 'in', 5, '5000.00', '2025-08-26', 'دفعة مقدمة', '2025-08-26 09:01:37', '2025-08-26 09:01:37'),
(13, 'App\\Models\\Supplier', 5, 'in', 5, '5000.00', '2025-08-26', 'دفعة مقدمة', '2025-08-26 09:01:37', '2025-08-26 09:01:37'),
(14, 'App\\Models\\Supplier', 5, 'in', 5, '5000.00', '2025-08-26', 'دفعة مقدمة', '2025-08-26 09:11:10', '2025-08-26 09:11:10'),
(15, 'App\\Models\\Customer', 9, 'in', 5, '2000.00', '2025-08-26', 'دفعة مقدمة', '2025-08-26 12:59:21', '2025-08-26 12:59:21');

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
(1, 20, NULL, '2025-08-16 11:10:47', '2025-08-16 11:10:47'),
(2, 25, NULL, '2025-08-16 11:10:51', '2025-08-16 11:10:51'),
(3, 30, NULL, '2025-08-16 11:10:55', '2025-08-16 11:10:55'),
(4, 35, NULL, '2025-08-16 11:12:25', '2025-08-16 11:12:25'),
(5, 40, NULL, '2025-08-16 11:15:59', '2025-08-16 11:15:59'),
(6, 45, NULL, '2025-08-16 11:16:04', '2025-08-16 11:16:04'),
(7, 50, NULL, '2025-08-16 11:16:08', '2025-08-16 11:16:08'),
(8, 55, NULL, '2025-08-16 11:16:12', '2025-08-16 11:16:12'),
(9, 60, NULL, '2025-08-16 11:16:17', '2025-08-16 11:16:17'),
(10, 65, NULL, '2025-08-16 11:16:22', '2025-08-16 11:16:22'),
(11, 70, NULL, '2025-08-16 11:16:25', '2025-08-16 11:16:25'),
(12, 75, NULL, '2025-08-16 11:16:29', '2025-08-16 11:16:29'),
(13, 80, NULL, '2025-08-16 11:16:33', '2025-08-16 11:16:33'),
(14, 90, NULL, '2025-08-16 11:16:38', '2025-08-16 11:16:38'),
(15, 100, NULL, '2025-08-16 11:16:42', '2025-08-16 11:16:42'),
(16, 110, NULL, '2025-08-16 11:16:45', '2025-08-16 11:16:45'),
(17, 120, NULL, '2025-08-16 11:16:49', '2025-08-16 11:16:49'),
(18, 130, NULL, '2025-08-16 11:16:54', '2025-08-16 11:16:54'),
(19, 150, NULL, '2025-08-16 11:16:58', '2025-08-16 11:16:58'),
(20, 155, NULL, '2025-08-16 11:17:01', '2025-08-16 11:17:01');

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
  `size_id` bigint UNSIGNED DEFAULT NULL,
  `initial_quantity` int NOT NULL DEFAULT '0' COMMENT 'الكمية الواردة',
  `remaining_quantity` int NOT NULL DEFAULT '0' COMMENT 'الكمية المتبقية',
  `date` date DEFAULT NULL COMMENT 'تاريخ الإضافة بتاريخ الفاتورة',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`id`, `category_id`, `product_id`, `store_house_id`, `unit_id`, `size_id`, `initial_quantity`, `remaining_quantity`, `date`, `created_at`, `updated_at`) VALUES
(7, 7, 2, 1, 3, 2, 100, 100, '2025-08-01', '2025-08-26 12:56:30', '2025-08-27 13:41:11'),
(8, 7, 3, 1, 3, 3, 100, 100, '2025-08-01', '2025-08-26 12:56:30', '2025-08-27 13:41:11');

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
  `related_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_id` bigint UNSIGNED DEFAULT NULL,
  `stock_id` bigint UNSIGNED NOT NULL,
  `type` enum('in','out') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'in => إضافة / out => خصم',
  `quantity` int NOT NULL COMMENT 'الكمية المحركة',
  `note` text COLLATE utf8mb4_unicode_ci COMMENT 'شراء / بيع',
  `source_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'كود الفاتورة',
  `date` date DEFAULT NULL COMMENT 'تاريخ الإضافة بتاريخ الفاتورة',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `related_type`, `related_id`, `stock_id`, `type`, `quantity`, `note`, `source_code`, `date`, `created_at`, `updated_at`) VALUES
(47, 'App\\Models\\Supplier', 6, 7, 'in', 100, 'شراء', 'SU-20251', '2025-08-01', '2025-08-26 12:56:30', '2025-08-26 12:56:30'),
(48, 'App\\Models\\Supplier', 6, 8, 'in', 100, 'شراء', 'SU-20251', '2025-08-01', '2025-08-26 12:56:30', '2025-08-26 12:56:30'),
(54, 'App\\Models\\Customer', 9, 7, 'out', -10, 'بيع', 'CU-20251', NULL, '2025-08-27 13:35:16', '2025-08-27 13:35:16');

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
(1, 'مخزن رئيسي', '01070380594', 'شارع النقطة - المرج', 1, '2025-08-16 11:27:43', '2025-08-16 11:27:43');

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
(6, 'أشرف', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-26 12:54:55', '2025-08-26 12:54:55');

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
(17, 6, 'SU-20251', '2025-08-01', 'credit', 0, '0.00', '700.00', '22700.00', '22000.00', NULL, NULL, NULL, NULL, '2025-08-26 12:56:30', '2025-08-27 13:41:11');

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
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier_invoice_items`
--

INSERT INTO `supplier_invoice_items` (`id`, `supplier_invoice_id`, `category_id`, `product_id`, `unit_id`, `size_id`, `quantity`, `pricePerMeter`, `length`, `purchase_price`, `total_price`, `deleted_at`, `created_at`, `updated_at`) VALUES
(49, 17, 7, 2, 3, 2, 100, '0.00', '30.00', '100.00', '10000.00', NULL, '2025-08-27 13:41:11', '2025-08-27 13:41:11'),
(50, 17, 7, 3, 3, 3, 100, '0.00', '30.00', '120.00', '12000.00', NULL, '2025-08-27 13:41:11', '2025-08-27 13:41:11');

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
(1, 'متر', 'م', '2025-08-16 11:19:43', '2025-08-16 11:19:43'),
(2, 'سنتيمتر', 'سم', '2025-08-16 11:20:03', '2025-08-16 11:20:03'),
(3, 'قطعة', 'ق', '2025-08-16 11:20:12', '2025-08-16 11:20:12'),
(4, 'كيلو', 'ك', '2025-08-16 11:20:21', '2025-08-16 11:20:21'),
(5, 'ملي', 'مم', '2025-08-16 11:20:36', '2025-08-16 11:20:36');

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
  ADD KEY `account_transactions_wallet_id_foreign` (`wallet_id`),
  ADD KEY `account_transactions_related_type_related_id_index` (`related_type`,`related_id`);

--
-- Indexes for table `apps`
--
ALTER TABLE `apps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `apps_user_id_foreign` (`user_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_dues`
--
ALTER TABLE `customer_dues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_dues_customer_id_foreign` (`customer_id`),
  ADD KEY `customer_dues_customer_invoice_id_foreign` (`customer_invoice_id`);

--
-- Indexes for table `customer_invoices`
--
ALTER TABLE `customer_invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_invoices_customer_id_foreign` (`customer_id`),
  ADD KEY `customer_invoices_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `customer_invoices_wallet_id_foreign` (`wallet_id`);

--
-- Indexes for table `customer_invoices_items`
--
ALTER TABLE `customer_invoices_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_invoices_items_customer_invoice_id_foreign` (`customer_invoice_id`),
  ADD KEY `customer_invoices_items_category_id_foreign` (`category_id`),
  ADD KEY `customer_invoices_items_product_id_foreign` (`product_id`),
  ADD KEY `customer_invoices_items_size_id_foreign` (`size_id`);

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
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_transactions_related_type_related_id_index` (`related_type`,`related_id`),
  ADD KEY `payment_transactions_wallet_id_foreign` (`wallet_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

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
  ADD KEY `stocks_unit_id_foreign` (`unit_id`),
  ADD KEY `stocks_size_id_foreign` (`size_id`);

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
  ADD KEY `stock_movements_related_type_related_id_index` (`related_type`,`related_id`),
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
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `account_transactions`
--
ALTER TABLE `account_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `apps`
--
ALTER TABLE `apps`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `customer_dues`
--
ALTER TABLE `customer_dues`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `customer_invoices`
--
ALTER TABLE `customer_invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `customer_invoices_items`
--
ALTER TABLE `customer_invoices_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `exponses`
--
ALTER TABLE `exponses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `exponse_items`
--
ALTER TABLE `exponse_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `external_debts`
--
ALTER TABLE `external_debts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_product_costs`
--
ALTER TABLE `invoice_product_costs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `store_houses`
--
ALTER TABLE `store_houses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `supplier_invoice_items`
--
ALTER TABLE `supplier_invoice_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- Constraints for table `account_transactions`
--
ALTER TABLE `account_transactions`
  ADD CONSTRAINT `account_transactions_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `account_transactions_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `apps`
--
ALTER TABLE `apps`
  ADD CONSTRAINT `apps_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `customer_dues`
--
ALTER TABLE `customer_dues`
  ADD CONSTRAINT `customer_dues_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_dues_customer_invoice_id_foreign` FOREIGN KEY (`customer_invoice_id`) REFERENCES `customer_invoices` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customer_invoices`
--
ALTER TABLE `customer_invoices`
  ADD CONSTRAINT `customer_invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_invoices_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_invoices_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `customer_invoices_items`
--
ALTER TABLE `customer_invoices_items`
  ADD CONSTRAINT `customer_invoices_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_invoices_items_customer_invoice_id_foreign` FOREIGN KEY (`customer_invoice_id`) REFERENCES `customer_invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_invoices_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_invoices_items_size_id_foreign` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE SET NULL;

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
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `payment_transactions_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stocks`
--
ALTER TABLE `stocks`
  ADD CONSTRAINT `stocks_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stocks_size_id_foreign` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE SET NULL,
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
  ADD CONSTRAINT `stock_movements_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `wallet_warehouse`
--
ALTER TABLE `wallet_warehouse`
  ADD CONSTRAINT `wallet_warehouse_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wallet_warehouse_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
