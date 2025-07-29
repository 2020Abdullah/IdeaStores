-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 29, 2025 at 03:26 PM
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
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `id` bigint UNSIGNED NOT NULL,
  `width` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`id`, `width`, `deleted_at`, `created_at`, `updated_at`) VALUES
(3, '25', NULL, '2025-07-29 08:52:53', '2025-07-29 08:52:53'),
(4, '30', NULL, '2025-07-29 08:53:05', '2025-07-29 08:53:05'),
(5, '35', NULL, '2025-07-29 08:53:08', '2025-07-29 08:53:08'),
(6, '45', NULL, '2025-07-29 08:53:15', '2025-07-29 08:53:15'),
(7, '50', NULL, '2025-07-29 08:53:19', '2025-07-29 08:53:19'),
(8, '65', NULL, '2025-07-29 08:53:25', '2025-07-29 08:53:25'),
(9, '80', NULL, '2025-07-29 08:53:30', '2025-07-29 08:53:30'),
(10, '90', NULL, '2025-07-29 08:53:37', '2025-07-29 08:53:37'),
(11, '100', NULL, '2025-07-29 08:53:44', '2025-07-29 08:53:44'),
(12, '110', NULL, '2025-07-29 08:53:50', '2025-07-29 08:53:50'),
(13, '120', NULL, '2025-07-29 08:53:53', '2025-07-29 08:53:53'),
(14, '60', NULL, '2025-07-29 10:39:23', '2025-07-29 10:39:23'),
(15, '150', NULL, '2025-07-29 11:21:32', '2025-07-29 11:21:32'),
(16, '145', NULL, '2025-07-29 11:21:41', '2025-07-29 11:21:41'),
(17, '155', NULL, '2025-07-29 11:38:10', '2025-07-29 11:38:10'),
(18, '125', NULL, '2025-07-29 11:38:15', '2025-07-29 11:38:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
