-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2025 at 08:19 AM
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
-- Database: `finale_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(9, 5, 1, 1, '2025-11-23 09:27:44'),
(16, 8, 6, 1, '2025-11-29 08:21:24'),
(17, 8, 7, 1, '2025-11-29 08:32:31'),
(19, 1, 6, 1, '2025-12-01 06:46:53');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Full Face', 'full-face'),
(2, 'Modular', 'modular'),
(3, 'Open Face', 'open-face'),
(4, 'Half Helmet', 'half-helmet'),
(5, 'Off-Road', 'off-road'),
(6, 'Dual Sport', 'dual-sport');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `distance_from_cebu_km` decimal(8,2) DEFAULT NULL,
  `category` varchar(20) NOT NULL DEFAULT 'Visayas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`, `province`, `distance_from_cebu_km`, `category`) VALUES
(3, 'GALLON', 'Negros', 23.00, 'Visayas'),
(4, 'Manila', 'Metro Manila', 570.00, 'Luzon'),
(5, 'Quezon City', 'Metro Manila', 572.00, 'Luzon'),
(6, 'Caloocan', 'Metro Manila', 574.00, 'Luzon'),
(7, 'Makati', 'Metro Manila', 573.00, 'Luzon'),
(8, 'Pasig', 'Metro Manila', 573.00, 'Luzon'),
(9, 'Taguig', 'Metro Manila', 573.00, 'Luzon'),
(10, 'Pasay', 'Metro Manila', 571.00, 'Luzon'),
(11, 'Mandaluyong', 'Metro Manila', 573.00, 'Luzon'),
(12, 'Las Piñas', 'Metro Manila', 575.00, 'Luzon'),
(13, 'Muntinlupa', 'Metro Manila', 578.00, 'Luzon'),
(14, 'Malabon', 'Metro Manila', 575.00, 'Luzon'),
(15, 'Navotas', 'Metro Manila', 574.00, 'Luzon'),
(16, 'Valenzuela', 'Metro Manila', 575.00, 'Luzon'),
(17, 'Marikina', 'Metro Manila', 573.00, 'Luzon'),
(18, 'San Juan', 'Metro Manila', 573.00, 'Luzon'),
(19, 'Parañaque', 'Metro Manila', 573.00, 'Luzon'),
(20, 'Cavite City', 'Cavite', 545.00, 'Luzon'),
(21, 'Bacoor', 'Cavite', 546.00, 'Luzon'),
(22, 'Imus', 'Cavite', 546.00, 'Luzon'),
(23, 'Dasmariñas', 'Cavite', 547.00, 'Luzon'),
(24, 'Santa Rosa', 'Laguna', 533.00, 'Luzon'),
(25, 'Lipa', 'Laguna', 532.00, 'Luzon'),
(26, 'Tanauan', 'Laguna', 533.00, 'Luzon'),
(27, 'Antipolo', 'Rizal', 570.00, 'Luzon'),
(28, 'Angeles City', 'Pampanga', 564.00, 'Luzon'),
(29, 'San Fernando', 'La Union', 649.00, 'Luzon'),
(30, 'Baguio', 'Benguet', 684.00, 'Luzon'),
(31, 'Naga City', 'Camarines Sur', 480.00, 'Luzon'),
(32, 'Legazpi City', 'Albay', 616.00, 'Luzon'),
(33, 'Lucena', 'Quezon', 520.00, 'Luzon'),
(34, 'Batangas City', 'Batangas', 535.00, 'Luzon'),
(35, 'Malolos', 'Bulacan', 567.00, 'Luzon'),
(36, 'Tarlac City', 'Tarlac', 609.00, 'Luzon'),
(37, 'Cabanatuan', 'Nueva Ecija', 597.00, 'Luzon'),
(38, 'Dagupan', 'Pangasinan', 635.00, 'Luzon'),
(39, 'Olongapo', 'Zambales', 604.00, 'Luzon'),
(40, 'Cebu City', 'Cebu', 5.00, 'Visayas'),
(41, 'Mandaue', 'Cebu', 7.00, 'Visayas'),
(42, 'Lapu-Lapu', 'Cebu', 14.00, 'Visayas'),
(43, 'Talisay', 'Cebu', 10.00, 'Visayas'),
(44, 'Danao', 'Cebu', 30.00, 'Visayas'),
(45, 'Carcar', 'Cebu', 38.00, 'Visayas'),
(46, 'Toledo', 'Cebu', 50.00, 'Visayas'),
(47, 'Dumaguete', 'Negros Oriental', 120.00, 'Visayas'),
(48, 'Bacolod', 'Negros Occidental', 210.00, 'Visayas'),
(49, 'Iloilo City', 'Iloilo', 195.00, 'Visayas'),
(50, 'Roxas City', 'Capiz', 285.00, 'Visayas'),
(51, 'Tagbilaran', 'Bohol', 75.00, 'Visayas'),
(52, 'Ormoc', 'Leyte', 140.00, 'Visayas'),
(53, 'Tacloban', 'Leyte', 185.00, 'Visayas'),
(54, 'Baybay', 'Leyte', 165.00, 'Visayas'),
(55, 'Bogo', 'Cebu', 60.00, 'Visayas'),
(56, 'Maasin', 'Southern Leyte', 180.00, 'Visayas'),
(57, 'Davao City', 'Davao del Sur', 550.00, 'Mindanao'),
(58, 'Cagayan de Oro', 'Misamis Oriental', 640.00, 'Mindanao'),
(59, 'Zamboanga City', 'Zamboanga del Sur', 780.00, 'Mindanao'),
(60, 'General Santos', 'South Cotabato', 700.00, 'Mindanao'),
(61, 'Butuan', 'Agusan del Norte', 640.00, 'Mindanao'),
(62, 'Iligan', 'Lanao del Norte', 620.00, 'Mindanao'),
(63, 'Dipolog', 'Zamboanga del Norte', 780.00, 'Mindanao'),
(64, 'Tagum', 'Davao del Norte', 560.00, 'Mindanao'),
(65, 'Pagadian', 'Zamboanga del Sur', 730.00, 'Mindanao'),
(66, 'Koronadal', 'South Cotabato', 670.00, 'Mindanao'),
(67, 'Cotabato City', 'Cotabato', 650.00, 'Mindanao'),
(68, 'Surigao City', 'Surigao del Sur', 780.00, 'Mindanao'),
(69, 'Tandag', 'Surigao del Sur', 710.00, 'Mindanao');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `size` varchar(10) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) NOT NULL,
  `status` enum('available','sold') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `sold_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `brand`, `size`, `color`, `price`, `quantity`, `image`, `status`, `created_at`, `description`, `category_id`, `sold_count`) VALUES
(1, 'Vortex Pro', 'Shoei', 'Medium', 'Matte Black', 7500.00, 3, 'uploads/1764210573_fe93cb8aa1dd.png', 'sold', '2025-11-17 17:48:05', '', 1, 0),
(2, 'RoadMaster', 'HJC', 'L', 'Red', 4200.00, 1, 'img/hjc_roadmaster.jpg', 'sold', '2025-11-17 17:48:05', '', 2, 0),
(3, 'SpeedRider', 'AGV', 'XL', 'Gloss White', 9800.00, 4, 'img/agv_speedrider.jpg', 'sold', '2025-11-17 17:48:05', '', 3, 0),
(4, 'Urban Cruiser', 'LS2', 'M', 'Blue', 3500.00, 7, 'uploads/1764210596_87a1ed7e3b52.png', 'available', '2025-11-17 17:48:05', '', 1, 0),
(5, 'Vortex Proo', 'Shoeie', 'XL', '0', 13339.00, 15, '0', 'available', '2025-11-29 08:05:08', 'sdfsadfd', 4, 0),
(6, 'Vortex Proa', 'Shoei', 'XL', '0', 144.00, 11, '0', 'available', '2025-11-29 08:07:50', 'sadgfaskflkshyjd', 3, 0),
(7, 'Vortex Proaa', 'HJCa', 'XL', '0', 12321.00, 0, '0', 'available', '2025-11-29 08:32:01', '', 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `delivery_fee` decimal(10,2) DEFAULT 0.00,
  `city_id` int(11) DEFAULT NULL,
  `municipality` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_method` varchar(20) DEFAULT 'gcash'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `user_id`, `total_amount`, `delivery_fee`, `city_id`, `municipality`, `province`, `phone`, `address`, `order_date`, `status`, `payment_method`) VALUES
(1, 1, 7600.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-21 15:22:58', 'pending', 'gcash'),
(2, 1, 9900.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-21 15:25:09', 'pending', 'gcash'),
(3, 5, 8500.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-23 08:41:01', 'cancelled', 'gcash'),
(4, 5, 7600.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-23 09:19:01', 'pending', 'gcash'),
(5, 5, 7600.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-23 09:25:33', 'pending', 'gcash'),
(6, 5, 7600.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-23 09:25:48', 'cancelled', 'gcash'),
(7, 6, 7600.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-27 10:32:37', 'delivered', 'gcash'),
(8, 8, 7600.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-29 04:46:43', 'delivered', 'gcash'),
(9, 8, 7600.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-29 05:01:06', 'delivered', 'bank'),
(10, 8, 3600.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-29 06:32:46', 'pending', 'gcash'),
(11, 8, 3600.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-29 06:52:38', 'pending', 'gcash'),
(12, 8, 9900.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-29 06:53:06', 'pending', 'bank'),
(13, 1, 12977.00, 656.00, 32, 'Legazpi City', 'Albay', '132312311', 'werqwrwqwer', '2025-12-01 06:45:33', 'pending', 'bank');

-- --------------------------------------------------------

--
-- Table structure for table `sales_products`
--

CREATE TABLE `sales_products` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_products`
--

INSERT INTO `sales_products` (`id`, `sale_id`, `product_id`, `quantity`, `price_at_purchase`) VALUES
(1, 1, 1, 1, 7500.00),
(2, 2, 3, 1, 9800.00),
(3, 3, 2, 2, 4200.00),
(4, 4, 1, 1, 7500.00),
(5, 5, 1, 1, 7500.00),
(6, 6, 1, 1, 7500.00),
(7, 7, 1, 1, 7500.00),
(8, 8, 1, 1, 7500.00),
(9, 9, 1, 1, 7500.00),
(10, 10, 4, 1, 3500.00),
(11, 11, 4, 1, 3500.00),
(12, 12, 3, 1, 9800.00),
(13, 13, 7, 1, 12321.00);

-- --------------------------------------------------------

--
-- Table structure for table `sale_promo`
--

CREATE TABLE `sale_promo` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale_promo`
--

INSERT INTO `sale_promo` (`id`, `title`, `description`) VALUES
(1, '🔥 Mid-Year Sale — Up to 40% OFF! 🔥wawawawawaw', 'Top-brand thrifted shoes at massive discounts. Limited stocks only!');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `key_name` varchar(50) DEFAULT NULL,
  `value` decimal(8,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key_name`, `value`) VALUES
(1, 'price_per_km', 1.00);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `city_id` int(11) DEFAULT 40,
  `email_address` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `first_name`, `last_name`, `address`, `contact_number`, `city_id`, `email_address`, `password`, `created_at`, `is_admin`) VALUES
(1, 'louis', 'baulita', 'phili,cebu,lacion', '09121212121', 40, 'baulita@gmail.com', '$2y$10$frrc.2w.VnccOsrEJxorU.VVB0QJqfNSe.aIle23m.WD3lIA4Rzha', '2025-11-21 02:35:47', 0),
(5, 'clark kent', 'judilla', '', '', 40, 'admin@gmail.com', '$2y$10$vBOkVPF60PokiJu86Fn0XOziC4/PVBoCL/vUEXid7GlU4/7NIRPrq', '2025-11-23 08:32:28', 1),
(6, 'clark kent', 'judilla', 'cabancalan', '09392389220', 40, 'clark@hhh.com', '$2y$10$j4Jo9k67JRchlB9NIFJoZ.HUcksXJM.By7zFdb5r.F1Gxpr/tZraC', '2025-11-27 10:32:03', 0),
(7, 'clark kent', 'judilla', 'cabancalan', '09392389220', 40, 'clarky@hhh.com', '$2y$10$3X/V.mB8DNsOHeuUUsnyvO2AoHKt5PYTdwlopWKfKdJ4YnHOGt89a', '2025-11-29 00:32:46', 0),
(8, 'alssadlfs', 'asdf', 'asdfsa', '123123123', 40, 'baba@gmail.com', '$2y$10$13GOkyE.6z.WZ3hlNriLruezX9664r/ovJVzhIt1mz1nhuDjVtG3C', '2025-11-29 04:46:18', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_products`
--
ALTER TABLE `sales_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `sale_promo`
--
ALTER TABLE `sale_promo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_name` (`key_name`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email_address` (`email_address`),
  ADD KEY `fk_user_city` (`city_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `sales_products`
--
ALTER TABLE `sales_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `sale_promo`
--
ALTER TABLE `sale_promo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sales_products`
--
ALTER TABLE `sales_products`
  ADD CONSTRAINT `sales_products_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_city` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
