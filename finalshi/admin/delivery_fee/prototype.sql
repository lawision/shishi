-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2025 at 07:36 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prototype`
--

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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_name` (`key_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
