-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2026 at 03:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quan_ly_thu_vien`
--

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `biography` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`id`, `name`, `slug`, `date_of_birth`, `nationality`, `biography`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Nguyễn Nhật Ánh', 'nguyen-nhat-anh-a9fac100c056b9ebe368b25e619c32ba', NULL, 'Việt Nam', NULL, 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(2, 'Tô Hoài', 'to-hoai-9e95125194bd0658d8d960f48deab378', NULL, 'Việt Nam', NULL, 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(3, 'Martin Fowler', 'martin-fowler-3093c6b89d264f2a278fff73d35588c9', NULL, 'Việt Nam', NULL, 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(4, 'Robert C. Martin', 'robert-c-martin-8ef76a52abb81581da4fd6e4e2e6a53f', NULL, 'Việt Nam', NULL, 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(5, 'Stephen Hawking', 'stephen-hawking-4e43bbc2dc5ff177fe9efe480d84f8bd', NULL, 'Việt Nam', NULL, 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54');

-- --------------------------------------------------------

--
-- Table structure for table `author_book`
--

CREATE TABLE `author_book` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `author_id` bigint(20) UNSIGNED NOT NULL,
  `book_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `author_book`
--

INSERT INTO `author_book` (`id`, `author_id`, `book_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(2, 2, 2, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(3, 3, 3, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(4, 4, 4, '2026-04-16 07:12:54', '2026-04-16 07:12:54');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `isbn` varchar(255) DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `publisher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `published_year` smallint(5) UNSIGNED DEFAULT NULL,
  `shelf_location` varchar(255) DEFAULT NULL,
  `total_copies` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `available_copies` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `price` decimal(12,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `code`, `title`, `slug`, `isbn`, `category_id`, `publisher_id`, `published_year`, `shelf_location`, `total_copies`, `available_copies`, `price`, `description`, `cover_image`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'BK001', 'Cho Tôi Xin Một Vé Đi Tuổi Thơ', 'cho-toi-xin-mot-ve-di-tuoi-tho-bk001', '9786041000011', 1, 1, 2008, 'A1', 20, 20, NULL, NULL, NULL, 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(2, 'BK002', 'Dế Mèn Phiêu Lưu Ký', 'de-men-phieu-luu-ky-bk002', '9786041000028', 5, 2, 1941, 'A2', 15, 15, NULL, NULL, NULL, 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(3, 'BK003', 'Refactoring', 'refactoring-bk003', '9780134757599', 3, 3, 2018, 'B1', 12, 12, NULL, NULL, NULL, 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(4, 'BK004', 'Clean Code', 'clean-code-bk004', '9780132350884', 3, 3, 2008, 'B2', 10, 10, NULL, NULL, NULL, 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54');

-- --------------------------------------------------------

--
-- Table structure for table `borrowings`
--

CREATE TABLE `borrowings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `reader_id` bigint(20) UNSIGNED NOT NULL,
  `processed_by` bigint(20) UNSIGNED NOT NULL,
  `borrow_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('borrowed','returned','overdue','cancelled') NOT NULL DEFAULT 'borrowed',
  `fine_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `borrowing_items`
--

CREATE TABLE `borrowing_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `borrowing_id` bigint(20) UNSIGNED NOT NULL,
  `book_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `returned_quantity` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `fine_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('borrowed','partially_returned','returned','overdue') NOT NULL DEFAULT 'borrowed',
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `borrowing_requests`
--

CREATE TABLE `borrowing_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `reader_id` bigint(20) UNSIGNED NOT NULL,
  `requested_by` bigint(20) UNSIGNED NOT NULL,
  `processed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_borrowing_id` bigint(20) UNSIGNED DEFAULT NULL,
  `request_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `note` text DEFAULT NULL,
  `processed_note` text DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `borrowing_request_items`
--

CREATE TABLE `borrowing_request_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `borrowing_request_id` bigint(20) UNSIGNED NOT NULL,
  `book_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Văn học', 'van-hoc', 'Danh mục Văn học', 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(2, 'Khoa học', 'khoa-hoc', 'Danh mục Khoa học', 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(3, 'Công nghệ thông tin', 'cong-nghe-thong-tin', 'Danh mục Công nghệ thông tin', 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(4, 'Kinh doanh', 'kinh-doanh', 'Danh mục Kinh doanh', 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(5, 'Thiếu nhi', 'thieu-nhi', 'Danh mục Thiếu nhi', 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fine_payments`
--

CREATE TABLE `fine_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `borrowing_item_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `paid_at` date NOT NULL,
  `paid_by` bigint(20) UNSIGNED DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_03_064717_create_categories_table', 1),
(5, '2026_04_03_064718_create_authors_table', 1),
(6, '2026_04_03_064718_create_publishers_table', 1),
(7, '2026_04_03_064719_create_books_table', 1),
(8, '2026_04_03_064719_create_readers_table', 1),
(9, '2026_04_03_064720_create_borrowings_table', 1),
(10, '2026_04_03_064721_create_borrowing_items_table', 1),
(11, '2026_04_03_064721_create_fine_payments_table', 1),
(12, '2026_04_03_064722_create_author_book_table', 1),
(13, '2026_04_15_230000_add_auth_columns_to_readers_table', 1),
(14, '2026_04_16_000000_link_readers_to_users_and_remove_reader_auth_columns', 1),
(15, '2026_04_16_000001_expand_user_role_for_customers', 1),
(16, '2026_04_16_100000_create_borrowing_requests_tables', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publishers`
--

CREATE TABLE `publishers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `publishers`
--

INSERT INTO `publishers` (`id`, `name`, `slug`, `phone`, `email`, `website`, `address`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'NXB Trẻ', 'nxb-tre', '02812345678', 'nxb-tre@example.com', NULL, 'Việt Nam', 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(2, 'NXB Kim Đồng', 'nxb-kim-dong', '02812345678', 'nxb-kim-dong@example.com', NULL, 'Việt Nam', 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(3, 'O\'Reilly Media', 'oreilly-media', '02812345678', 'oreilly-media@example.com', NULL, 'Việt Nam', 1, '2026-04-16 07:12:54', '2026-04-16 07:12:54');

-- --------------------------------------------------------

--
-- Table structure for table `readers`
--

CREATE TABLE `readers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `card_number` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `membership_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` enum('active','inactive','blocked') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `readers`
--

INSERT INTO `readers` (`id`, `user_id`, `card_number`, `full_name`, `email`, `phone`, `gender`, `date_of_birth`, `address`, `membership_date`, `expiry_date`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, NULL, 'DG0001', 'Nguyễn Văn A', 'nguyenvana@example.com', '0911111111', NULL, NULL, NULL, '2026-02-16', '2027-04-16', 'active', NULL, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(2, NULL, 'DG0002', 'Trần Thị B', 'tranthib@example.com', '0922222222', NULL, NULL, NULL, '2026-02-16', '2027-04-16', 'active', NULL, '2026-04-16 07:12:54', '2026-04-16 07:12:54');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('gvh4Bk2zP4CAZhiHYdxXCS5675b7c5aFJdFnArh0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWEpKODh2WHZ6SUd0c1VnR1E4c1BLY2tPMHdWSlpqcjFIWW1WcXVpSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYW5nX25oYXAiO3M6NToicm91dGUiO3M6OToiZGFuZ19uaGFwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1776349418),
('QKADYGOrPsISqpROmTFCNVB93BtotSEVqPxpbmXe', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVXFoandLZ2tEZ1d1alhUbDhudGNmcUhTT1dZME1qRW9tS1VIY2N4ZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC90YWNfZ2lhIjtzOjU6InJvdXRlIjtzOjEzOiJ0YWNfZ2lhLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1776651249);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `role` enum('admin','librarian','customer') NOT NULL DEFAULT 'librarian',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `role`, `is_active`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Quản trị hệ thống', 'admin@gmail.com', '0900000001', 'TP.HCM', 'admin', 1, NULL, '$2y$12$VeKSC2R8LJhzrI0DmXw6YuUDINTlS2IArw4lQ13TN7sOESVT/WrR.', NULL, '2026-04-16 07:12:54', '2026-04-16 07:12:54'),
(2, 'Thủ thư', 'thuthu@gmail.com', '0900000002', 'TP.HCM', 'librarian', 1, NULL, '$2y$12$vx3cewPMXNRXjhjeKIQBiOvYAY3.jZWSAgfZXC3v2dTN4KnCSRpUe', NULL, '2026-04-16 07:12:54', '2026-04-16 07:12:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `authors_slug_unique` (`slug`),
  ADD UNIQUE KEY `authors_name_date_of_birth_unique` (`name`,`date_of_birth`);

--
-- Indexes for table `author_book`
--
ALTER TABLE `author_book`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `author_book_author_id_book_id_unique` (`author_id`,`book_id`),
  ADD KEY `author_book_book_id_foreign` (`book_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `books_code_unique` (`code`),
  ADD UNIQUE KEY `books_slug_unique` (`slug`),
  ADD UNIQUE KEY `books_isbn_unique` (`isbn`),
  ADD KEY `books_category_id_foreign` (`category_id`),
  ADD KEY `books_publisher_id_foreign` (`publisher_id`),
  ADD KEY `books_title_index` (`title`);

--
-- Indexes for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `borrowings_code_unique` (`code`),
  ADD KEY `borrowings_reader_id_foreign` (`reader_id`),
  ADD KEY `borrowings_processed_by_foreign` (`processed_by`),
  ADD KEY `borrowings_status_due_date_index` (`status`,`due_date`);

--
-- Indexes for table `borrowing_items`
--
ALTER TABLE `borrowing_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `borrowing_items_borrowing_id_book_id_unique` (`borrowing_id`,`book_id`),
  ADD KEY `borrowing_items_book_id_foreign` (`book_id`),
  ADD KEY `borrowing_items_status_due_date_index` (`status`,`due_date`);

--
-- Indexes for table `borrowing_requests`
--
ALTER TABLE `borrowing_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `borrowing_requests_code_unique` (`code`),
  ADD KEY `borrowing_requests_reader_id_foreign` (`reader_id`),
  ADD KEY `borrowing_requests_requested_by_foreign` (`requested_by`),
  ADD KEY `borrowing_requests_processed_by_foreign` (`processed_by`),
  ADD KEY `borrowing_requests_approved_borrowing_id_foreign` (`approved_borrowing_id`),
  ADD KEY `borrowing_requests_status_request_date_index` (`status`,`request_date`);

--
-- Indexes for table `borrowing_request_items`
--
ALTER TABLE `borrowing_request_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `borrowing_request_items_borrowing_request_id_book_id_unique` (`borrowing_request_id`,`book_id`),
  ADD KEY `borrowing_request_items_book_id_foreign` (`book_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fine_payments`
--
ALTER TABLE `fine_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fine_payments_borrowing_item_id_foreign` (`borrowing_item_id`),
  ADD KEY `fine_payments_paid_by_foreign` (`paid_by`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `publishers`
--
ALTER TABLE `publishers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `publishers_name_unique` (`name`),
  ADD UNIQUE KEY `publishers_slug_unique` (`slug`);

--
-- Indexes for table `readers`
--
ALTER TABLE `readers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `readers_card_number_unique` (`card_number`),
  ADD UNIQUE KEY `readers_email_unique` (`email`),
  ADD UNIQUE KEY `readers_user_id_unique` (`user_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `author_book`
--
ALTER TABLE `author_book`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `borrowings`
--
ALTER TABLE `borrowings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `borrowing_items`
--
ALTER TABLE `borrowing_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `borrowing_requests`
--
ALTER TABLE `borrowing_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `borrowing_request_items`
--
ALTER TABLE `borrowing_request_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fine_payments`
--
ALTER TABLE `fine_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `publishers`
--
ALTER TABLE `publishers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `readers`
--
ALTER TABLE `readers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `author_book`
--
ALTER TABLE `author_book`
  ADD CONSTRAINT `author_book_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `author_book_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `books_publisher_id_foreign` FOREIGN KEY (`publisher_id`) REFERENCES `publishers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD CONSTRAINT `borrowings_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `borrowings_reader_id_foreign` FOREIGN KEY (`reader_id`) REFERENCES `readers` (`id`);

--
-- Constraints for table `borrowing_items`
--
ALTER TABLE `borrowing_items`
  ADD CONSTRAINT `borrowing_items_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  ADD CONSTRAINT `borrowing_items_borrowing_id_foreign` FOREIGN KEY (`borrowing_id`) REFERENCES `borrowings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `borrowing_requests`
--
ALTER TABLE `borrowing_requests`
  ADD CONSTRAINT `borrowing_requests_approved_borrowing_id_foreign` FOREIGN KEY (`approved_borrowing_id`) REFERENCES `borrowings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `borrowing_requests_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `borrowing_requests_reader_id_foreign` FOREIGN KEY (`reader_id`) REFERENCES `readers` (`id`),
  ADD CONSTRAINT `borrowing_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `borrowing_request_items`
--
ALTER TABLE `borrowing_request_items`
  ADD CONSTRAINT `borrowing_request_items_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  ADD CONSTRAINT `borrowing_request_items_borrowing_request_id_foreign` FOREIGN KEY (`borrowing_request_id`) REFERENCES `borrowing_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fine_payments`
--
ALTER TABLE `fine_payments`
  ADD CONSTRAINT `fine_payments_borrowing_item_id_foreign` FOREIGN KEY (`borrowing_item_id`) REFERENCES `borrowing_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fine_payments_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `readers`
--
ALTER TABLE `readers`
  ADD CONSTRAINT `readers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
