-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table klinik_cantikku.detail_transaksi
CREATE TABLE IF NOT EXISTS `detail_transaksi` (
  `id_detail` int NOT NULL AUTO_INCREMENT,
  `id_transaksi` int NOT NULL,
  `id_layanan` int NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `harga` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_detail`),
  KEY `idx_dt_transaksi` (`id_transaksi`),
  KEY `idx_dt_layanan` (`id_layanan`),
  CONSTRAINT `fk_dt_layanan` FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id_layanan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_dt_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=230 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table klinik_cantikku.detail_transaksi: ~53 rows (approximately)
INSERT INTO `detail_transaksi` (`id_detail`, `id_transaksi`, `id_layanan`, `qty`, `harga`, `subtotal`) VALUES
	(1, 1, 2, 1, 150000.00, 150000.00),
	(2, 2, 1, 1, 75000.00, 75000.00),
	(151, 84, 2, 1, 150000.00, 150000.00),
	(152, 84, 4, 1, 50000.00, 50000.00),
	(153, 85, 1, 1, 75000.00, 75000.00),
	(154, 85, 4, 1, 50000.00, 50000.00),
	(155, 86, 1, 1, 75000.00, 75000.00),
	(156, 86, 5, 1, 60000.00, 60000.00),
	(157, 87, 1, 1, 75000.00, 75000.00),
	(158, 87, 5, 1, 60000.00, 60000.00),
	(159, 88, 5, 1, 60000.00, 60000.00),
	(160, 89, 3, 1, 250000.00, 250000.00),
	(161, 90, 1, 1, 75000.00, 75000.00),
	(162, 90, 2, 1, 150000.00, 150000.00),
	(176, 98, 1, 1, 75000.00, 75000.00),
	(177, 98, 2, 1, 150000.00, 150000.00),
	(178, 99, 1, 1, 75000.00, 75000.00),
	(179, 99, 5, 1, 60000.00, 60000.00),
	(180, 100, 2, 1, 150000.00, 150000.00),
	(181, 100, 3, 1, 250000.00, 250000.00),
	(182, 101, 3, 1, 250000.00, 250000.00),
	(183, 101, 1, 1, 75000.00, 75000.00),
	(184, 102, 1, 1, 75000.00, 75000.00),
	(185, 102, 2, 1, 150000.00, 150000.00),
	(186, 103, 4, 1, 50000.00, 50000.00),
	(187, 103, 1, 1, 75000.00, 75000.00),
	(188, 104, 1, 1, 75000.00, 75000.00),
	(189, 104, 5, 1, 60000.00, 60000.00),
	(190, 105, 1, 1, 75000.00, 75000.00),
	(191, 105, 2, 1, 150000.00, 150000.00),
	(192, 106, 1, 1, 75000.00, 75000.00),
	(193, 106, 4, 1, 50000.00, 50000.00),
	(194, 107, 5, 1, 60000.00, 60000.00),
	(195, 107, 4, 1, 50000.00, 50000.00),
	(196, 108, 1, 1, 75000.00, 75000.00),
	(197, 109, 2, 1, 150000.00, 150000.00),
	(198, 109, 5, 1, 60000.00, 60000.00),
	(199, 110, 2, 1, 150000.00, 150000.00),
	(200, 111, 1, 1, 75000.00, 75000.00),
	(201, 111, 4, 1, 50000.00, 50000.00),
	(202, 113, 2, 1, 150000.00, 150000.00),
	(203, 114, 1, 1, 75000.00, 75000.00),
	(204, 115, 3, 1, 250000.00, 250000.00),
	(205, 116, 4, 1, 50000.00, 50000.00),
	(206, 117, 4, 1, 50000.00, 50000.00),
	(207, 118, 4, 1, 50000.00, 50000.00),
	(208, 118, 5, 1, 60000.00, 60000.00),
	(209, 119, 4, 1, 50000.00, 50000.00),
	(210, 119, 5, 1, 60000.00, 60000.00),
	(211, 120, 5, 1, 60000.00, 60000.00),
	(212, 121, 4, 1, 50000.00, 50000.00),
	(213, 122, 1, 1, 75000.00, 75000.00),
	(214, 123, 1, 1, 75000.00, 75000.00),
	(215, 123, 5, 1, 60000.00, 60000.00),
	(216, 123, 2, 1, 150000.00, 150000.00),
	(217, 124, 2, 1, 150000.00, 150000.00),
	(218, 125, 2, 1, 150000.00, 150000.00),
	(219, 125, 5, 1, 60000.00, 60000.00),
	(220, 125, 6, 1, 85000.00, 85000.00),
	(221, 125, 3, 1, 250000.00, 250000.00),
	(222, 126, 3, 1, 250000.00, 250000.00),
	(223, 127, 2, 1, 150000.00, 150000.00),
	(224, 128, 3, 1, 250000.00, 250000.00),
	(225, 129, 2, 1, 150000.00, 150000.00),
	(226, 130, 2, 1, 150000.00, 150000.00),
	(227, 131, 1, 1, 75000.00, 75000.00),
	(228, 132, 1, 1, 75000.00, 75000.00),
	(229, 133, 1, 1, 75000.00, 75000.00);

-- Dumping structure for table klinik_cantikku.layanan
CREATE TABLE IF NOT EXISTS `layanan` (
  `id_layanan` int NOT NULL AUTO_INCREMENT,
  `nama_layanan` varchar(150) NOT NULL,
  `harga` decimal(12,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_layanan`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table klinik_cantikku.layanan: ~5 rows (approximately)
INSERT INTO `layanan` (`id_layanan`, `nama_layanan`, `harga`) VALUES
	(1, 'Facial Basic', 75000.00),
	(2, 'Facial Premium', 150000.00),
	(3, 'Perawatan Wajah Deluxe', 250000.00),
	(4, 'Manicure', 50000.00),
	(5, 'Pedicure', 60000.00),
	(6, 'Pijat Bayi', 85000.00);

-- Dumping structure for table klinik_cantikku.pasien
CREATE TABLE IF NOT EXISTS `pasien` (
  `id_pasien` int NOT NULL AUTO_INCREMENT,
  `no_register` int DEFAULT NULL,
  `nama` varchar(150) NOT NULL,
  `no_hp` varchar(30) DEFAULT NULL,
  `alamat` text,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pasien`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table klinik_cantikku.pasien: ~11 rows (approximately)
INSERT INTO `pasien` (`id_pasien`, `no_register`, `nama`, `no_hp`, `alamat`, `tanggal_lahir`, `jenis_kelamin`, `created_at`) VALUES
	(1, 23424, 'Budi Santoso', '081234567890', 'Jl mawar', '1990-07-10', 'L', '2025-11-12 21:09:02'),
	(31, 56753, 'freya', '012345', 'Jl stecu', '2002-02-02', 'P', '2025-11-12 22:50:50'),
	(32, 89674, 'yuna', '4520450', 'jl juju', '2001-01-01', 'P', '2025-11-12 22:51:19'),
	(33, 45242, 'dugong', '345351325', 'dgsrdgsrhs', '2005-04-04', 'P', '2025-11-12 22:52:42'),
	(34, 43562354, 'chiba', '34234214124', 'dfgdsgsdgbsd', '2006-06-06', 'L', '2025-11-12 22:53:17'),
	(35, 91523, 'entum', '4353453535', 'rtthdrfhdrnbd', '2006-06-06', 'L', '2025-11-12 22:58:51'),
	(36, 646824511, 'cendol', '34242425235', 'drfrfghrsgsrg', '2003-03-03', 'P', '2025-11-12 22:59:18'),
	(37, 24234, 'klerer', '4353535', 'jhl aman', '2002-02-02', 'P', '2025-11-12 23:09:18'),
	(38, 23432154, 'Diah', '12540320', 'dwfwfwefgeagr', '2003-03-03', 'P', '2025-11-14 01:33:53'),
	(46, 3452, 'dewi', '0222243', 'jl melati', '2013-09-05', 'P', '2025-11-17 00:24:39'),
	(47, 4532535, 'capi', '435254535', 'sdfdsfasf', '2013-06-25', 'L', '2025-11-17 00:30:38');

-- Dumping structure for table klinik_cantikku.pengaturan_klinik
CREATE TABLE IF NOT EXISTS `pengaturan_klinik` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_klinik` varchar(191) DEFAULT 'Klinik Cantikku',
  `alamat` text,
  `no_hp` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `instagram` varchar(100) DEFAULT NULL,
  `facebook` varchar(100) DEFAULT NULL,
  `tiktok` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `print_mode` enum('usb','bluetooth') DEFAULT 'usb',
  `printer_name` varchar(100) DEFAULT NULL,
  `template_struk` enum('A','B') NOT NULL DEFAULT 'A',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table klinik_cantikku.pengaturan_klinik: ~1 rows (approximately)
INSERT INTO `pengaturan_klinik` (`id`, `nama_klinik`, `alamat`, `no_hp`, `email`, `instagram`, `facebook`, `tiktok`, `logo`, `print_mode`, `printer_name`, `template_struk`) VALUES
	(1, 'Ayusasa Mom & Baby Care', 'Jl. Contoh No.1, Jakarta', '081234567890', 'admin@ayusasa.test', 'Ayusasa', 'Ayusasa', '@ayusasa', 'logo.png', 'usb', '', 'A');

-- Dumping structure for table klinik_cantikku.print_config
CREATE TABLE IF NOT EXISTS `print_config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `print_mode` enum('usb','bluetooth') NOT NULL DEFAULT 'usb',
  `template` enum('A','B') NOT NULL DEFAULT 'A',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table klinik_cantikku.print_config: ~0 rows (approximately)
INSERT INTO `print_config` (`id`, `print_mode`, `template`, `updated_at`) VALUES
	(1, 'usb', 'A', '2025-11-18 17:58:07');

-- Dumping structure for table klinik_cantikku.transaksi
CREATE TABLE IF NOT EXISTS `transaksi` (
  `id_transaksi` int NOT NULL AUTO_INCREMENT,
  `id_user` int DEFAULT NULL,
  `id_pasien` int DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT '0.00',
  `diskon` decimal(12,2) DEFAULT '0.00',
  `total` decimal(12,2) DEFAULT '0.00',
  `tanggal` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_transaksi`),
  KEY `idx_id_user` (`id_user`),
  KEY `idx_id_pasien` (`id_pasien`),
  CONSTRAINT `fk_transaksi_pasien` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table klinik_cantikku.transaksi: ~35 rows (approximately)
INSERT INTO `transaksi` (`id_transaksi`, `id_user`, `id_pasien`, `subtotal`, `diskon`, `total`, `tanggal`) VALUES
	(1, 2, 1, 150000.00, 0.00, 150000.00, '2025-11-10 23:28:00'),
	(2, 2, 1, 75000.00, 0.00, 75000.00, '2025-10-10 23:28:00'),
	(4, 2, 1, 60000.00, 0.00, 60000.00, '2025-08-10 23:28:00'),
	(6, 2, 1, 50000.00, 0.00, 50000.00, '2025-06-10 23:28:00'),
	(84, 3, 1, 200000.00, 0.00, 200000.00, '2025-11-12 21:10:18'),
	(85, 2, 1, 125000.00, 0.00, 125000.00, '2025-11-12 21:19:04'),
	(86, 2, 1, 135000.00, 50000.00, 85000.00, '2025-11-12 21:28:46'),
	(87, 2, 1, 135000.00, 0.00, 135000.00, '2025-11-12 21:36:44'),
	(88, 2, 1, 60000.00, 0.00, 60000.00, '2025-11-12 21:37:16'),
	(89, 2, 1, 250000.00, 0.00, 250000.00, '2025-11-12 21:39:08'),
	(90, 2, 1, 225000.00, 0.00, 225000.00, '2025-11-12 21:46:20'),
	(98, 2, 32, 225000.00, 0.00, 225000.00, '2025-11-12 22:51:48'),
	(99, 2, 32, 135000.00, 0.00, 135000.00, '2025-11-12 22:52:18'),
	(100, 2, 34, 400000.00, 0.00, 400000.00, '2025-11-12 22:53:38'),
	(101, 2, 33, 325000.00, 0.00, 325000.00, '2025-11-12 22:53:54'),
	(102, 2, 33, 225000.00, 0.00, 225000.00, '2025-11-12 22:58:15'),
	(103, 2, 32, 125000.00, 0.00, 125000.00, '2025-11-12 22:58:31'),
	(104, 2, 31, 135000.00, 0.00, 135000.00, '2025-11-13 00:02:29'),
	(105, 2, 1, 225000.00, 0.00, 225000.00, '2025-11-14 00:57:25'),
	(106, 2, 38, 125000.00, 50000.00, 75000.00, '2025-11-14 01:54:31'),
	(107, 2, 32, 110000.00, 35000.00, 75000.00, '2025-11-14 02:07:45'),
	(108, 2, 1, 75000.00, 0.00, 75000.00, '2025-11-15 22:09:03'),
	(109, 3, 1, 210000.00, 0.00, 210000.00, '2025-11-15 23:20:03'),
	(110, 3, 1, 150000.00, 0.00, 150000.00, '2025-11-15 23:47:40'),
	(111, 3, 1, 125000.00, 25000.00, 100000.00, '2025-11-18 23:00:16'),
	(112, 1, 1, 210000.00, 0.00, 210000.00, '2025-11-19 23:24:49'),
	(113, 1, 1, 150000.00, 0.00, 150000.00, '2025-11-19 23:40:12'),
	(114, 1, 38, 75000.00, 0.00, 75000.00, '2025-11-19 23:41:41'),
	(115, 1, 31, 250000.00, 0.00, 250000.00, '2025-11-19 23:53:23'),
	(116, 1, 46, 50000.00, 0.00, 50000.00, '2025-11-20 00:03:43'),
	(117, 1, 46, 50000.00, 0.00, 50000.00, '2025-11-20 00:04:36'),
	(118, 1, 33, 110000.00, 0.00, 110000.00, '2025-11-20 00:10:36'),
	(119, 1, 33, 110000.00, 0.00, 110000.00, '2025-11-20 00:11:16'),
	(120, 1, 37, 60000.00, 0.00, 60000.00, '2025-11-20 00:14:45'),
	(121, 1, 37, 50000.00, 0.00, 50000.00, '2025-11-20 00:29:56'),
	(122, 1, 36, 75000.00, 0.00, 75000.00, '2025-11-20 00:32:16'),
	(123, 1, 31, 285000.00, 50000.00, 235000.00, '2025-11-20 00:33:58'),
	(124, 1, 36, 150000.00, 0.00, 150000.00, '2025-11-20 00:43:08'),
	(125, 1, 36, 545000.00, 50000.00, 495000.00, '2025-11-20 19:53:26'),
	(126, 1, 36, 250000.00, 0.00, 250000.00, '2025-11-20 19:54:09'),
	(127, 1, 46, 150000.00, 0.00, 150000.00, '2025-11-21 00:26:30'),
	(128, 1, 46, 250000.00, 75000.00, 175000.00, '2025-11-22 11:28:32'),
	(129, 1, 46, 150000.00, 75000.00, 75000.00, '2025-11-22 11:52:38'),
	(130, 1, 38, 150000.00, 0.00, 150000.00, '2025-11-22 13:21:57'),
	(131, 1, 36, 75000.00, 0.00, 75000.00, '2025-11-22 13:25:23'),
	(132, 1, 33, 75000.00, 0.00, 75000.00, '2025-11-22 23:23:23'),
	(133, 1, 1, 75000.00, 0.00, 75000.00, '2025-12-09 00:18:25');

-- Dumping structure for table klinik_cantikku.users
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','terapis') NOT NULL DEFAULT 'terapis',
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table klinik_cantikku.users: ~3 rows (approximately)
INSERT INTO `users` (`id_user`, `nama`, `username`, `password`, `role`, `status`) VALUES
	(1, 'Ayusasa', 'admin', '$2y$10$vysmhnj/R4h/oygKBW6Dh.v6VbtR9qjKPOobzmhY.NIa3rh6VRQCK', 'admin', 'aktif'),
	(2, 'Terapis Ayu', 'terapis', '$2y$10$yPyY4QvaKk6DRUB3z0bxCuvxY168KG4mjMPuRFD30kbZuKI/cHI0u', 'terapis', 'aktif'),
	(3, 'Terapis Sasa', 'terapis2', '$2y$10$5jyqiWGBC4zSJDrQz/F9Be93z4WQESP00xz64tKe5GbEBc/CgXh12', 'terapis', 'aktif');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
