-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2025 at 10:00 AM
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
-- Database: `sigklinikkecantikan`
--

-- --------------------------------------------------------

--
-- Table structure for table `kliniks`
--

CREATE TABLE `kliniks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `jam_operasional` text NOT NULL,
  `telepon` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kliniks`
--

INSERT INTO `kliniks` (`id`, `nama`, `alamat`, `foto`, `latitude`, `longitude`, `jam_operasional`, `telepon`, `email`, `instagram`, `facebook`, `twitter`, `website`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Beauty Clinic Jambi', 'Jl. Arif Rahman Hakim No. 10, Simpang IV Sipin, Kota Jambi', '1690019410_beauty-clinic-jambi.jpg', -1.6096639, 103.6131639, 'Senin-Sabtu: 09:00-17:00', '074134567', 'beautyclinic@example.com', 'beautyclinicjambi', 'beautyclinicjambi', 'beautyclinicjbi', 'https://beautyclinicjambi.com', 'approved', '2025-07-22 02:30:10', '2025-07-22 02:30:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kliniks`
--
ALTER TABLE `kliniks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kliniks`
--
ALTER TABLE `kliniks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
