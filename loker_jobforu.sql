-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 06, 2025 at 07:26 PM
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
-- Database: `loker_jobforu`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cv_path` varchar(255) NOT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `job_id`, `user_id`, `cv_path`, `status`, `applied_at`) VALUES
(10, 9, 7, 'uploads/cv/cv_7_9_1750495417.pdf', 'accepted', '2025-06-21 08:43:37'),
(12, 11, 7, 'uploads/cv/cv_7_11_1750496726.pdf', 'accepted', '2025-06-21 09:05:26'),
(13, 11, 2, 'uploads/cv/cv_2_11_1750514247.pdf', 'rejected', '2025-06-21 13:57:27'),
(14, 9, 2, 'uploads/cv/cv_2_9_1750514255.pdf', 'accepted', '2025-06-21 13:57:35'),
(15, 12, 7, 'uploads/cv/cv_7_12_1750516569.pdf', 'accepted', '2025-06-21 14:36:09'),
(16, 12, 8, 'uploads/cv/cv_8_12_1750516910.pdf', 'accepted', '2025-06-21 14:41:50'),
(17, 11, 8, 'uploads/cv/cv_8_11_1750519440.pdf', 'rejected', '2025-06-21 15:24:00'),
(18, 12, 2, 'uploads/cv/cv_2_12_1750526445.pdf', 'rejected', '2025-06-21 17:20:45'),
(19, 9, 8, 'uploads/cv/cv_8_9_1750526586.pdf', 'rejected', '2025-06-21 17:23:06'),
(20, 13, 8, 'uploads/cv/cv_8_13_1750671790.pdf', 'rejected', '2025-06-23 09:43:10'),
(21, 14, 8, 'uploads/cv/cv_8_14_1750813987.pdf', 'accepted', '2025-06-25 01:13:07');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `lokasi` varchar(100) NOT NULL,
  `gaji` varchar(100) DEFAULT NULL,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nama_perusahaan` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `judul`, `deskripsi`, `lokasi`, `gaji`, `posted_at`, `nama_perusahaan`) VALUES
(9, 'Web Developer', 'bisa buat buat bumi', 'Jakarta, Indonesia', '1.000.000', '2025-06-21 07:24:40', 'Google'),
(11, 'Marketing', 'bisa ilmu hitam', 'Pontianak, Kalimantan Barat', '5.000.000', '2025-06-21 09:04:30', 'Tokopedia'),
(12, 'Satpam', 'jaga komplek sama tangkap maling', 'Pontianak, Kalimantan Barat', '8.000.000', '2025-06-21 14:35:01', 'PT Griya'),
(13, 'Product Manager', 'p', 'Pontianak, Kalimantan Barat', '8.000.000', '2025-06-23 09:42:35', 'PT Griya'),
(14, 'Product Manager', 'minimal s1', 'Pontianak, Kalimantan Barat', '8.000.000', '2025-06-25 01:12:18', 'PT Griya');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@jobforu.com', '$2y$10$laxh0TPF/Q4SPRCrjferH.Hmo.80nXGCzXxeEFlidVIp46U4OR1XS', 'admin', '2025-06-10 14:01:08'),
(2, 'User2', 'user2@gmail.com', '$2y$10$2BkVTiuGamt35pnQ4z8EXumbgcgtwgfc30sSv6jV1Bmam.K4rDa6a', 'user', '2025-06-21 13:53:06'),
(7, 'user1', 'user1@gmail.com', '$2y$10$p4ccpfjrepsOX09Vc1xrueY83SJHW07LDCV.ULjPrsol5io.D9GfO', 'user', '2025-06-21 08:40:41'),
(8, 'bobi', 'bobi@gmail.com', '$2y$10$iOwcu/sObdjXqI3Wg9cpO.IPCt5rWlUcAYGhMMVN5TfaPI2CemY3y', 'user', '2025-06-21 14:41:22'),
(9, 'Ledy', 'ledy@gmail.com', '$2y$10$jlYvi0aQnNRHasThsOcYSONqZVPpu3.L/c5JUkWQvboW5qQ8syT0.', 'user', '2025-06-23 09:44:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
