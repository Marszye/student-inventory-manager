-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 24, 2025 at 08:11 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zye_lends`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_list`
--

CREATE TABLE `admin_list` (
  `id` int(11) NOT NULL,
  `nama_admin` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_list`
--

INSERT INTO `admin_list` (`id`, `nama_admin`) VALUES
(2, 'raka'),
(3, 'syamil'),
(1, 'Ustadz Amir');

-- --------------------------------------------------------

--
-- Table structure for table `barang_list`
--

CREATE TABLE `barang_list` (
  `id` int(11) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `harga_per_jam` int(11) NOT NULL DEFAULT 0,
  `harga_per_hari` int(11) NOT NULL DEFAULT 0,
  `stok` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barang_list`
--

INSERT INTO `barang_list` (`id`, `nama_barang`, `harga_per_jam`, `harga_per_hari`, `stok`) VALUES
(1, 'Kunci Pintu', 0, 0, 3);

-- --------------------------------------------------------

--
-- Table structure for table `peminjam`
--

CREATE TABLE `peminjam` (
  `nama` varchar(255) NOT NULL,
  `total_peminjaman` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `peminjam`
--

INSERT INTO `peminjam` (`nama`, `total_peminjaman`) VALUES
('azka zuhairi', 4),
('Muhammad Syamil Alfarizi', 4);

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jenis_barang` varchar(255) NOT NULL,
  `tipe_durasi` enum('jam','hari') NOT NULL,
  `waktu_mulai` datetime NOT NULL,
  `durasi` int(11) NOT NULL,
  `status` enum('Dipinjam','Dikembalikan') NOT NULL DEFAULT 'Dipinjam',
  `admin` varchar(255) NOT NULL,
  `waktu_peminjaman` datetime DEFAULT current_timestamp(),
  `waktu_pengembalian` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `nama`, `jenis_barang`, `tipe_durasi`, `waktu_mulai`, `durasi`, `status`, `admin`, `waktu_peminjaman`, `waktu_pengembalian`) VALUES
(1, 'Muhammad Syamil Alfarizi', 'Kunci Pintu', 'jam', '2025-04-26 14:28:00', 1, 'Dikembalikan', 'Ustadz Amir', '2025-04-26 14:33:09', '2025-05-21 16:56:36'),
(2, 'Muhammad Syamil Alfarizi', 'Kunci Pintu', 'hari', '2025-04-26 00:00:00', 1, 'Dikembalikan', 'Ustadz Amir', '2025-04-26 16:06:32', '2025-05-10 02:45:26'),
(3, 'azka zuhairi', 'Kipas', 'hari', '2025-05-09 00:00:00', 1, 'Dikembalikan', 'raka', '2025-05-09 22:09:26', '2025-05-09 17:09:32'),
(4, 'azka zuhairi', 'Buku Tulis', 'jam', '2025-05-09 22:09:00', 1, 'Dikembalikan', 'syamil', '2025-05-09 22:09:48', '2025-05-09 17:09:51'),
(5, 'azka zuhairi', 'Charger', 'hari', '2025-05-09 00:00:00', 1, 'Dikembalikan', 'raka', '2025-05-09 22:10:06', '2025-05-09 17:10:09'),
(6, 'Muhammad Syamil Alfarizi', 'Buku Tulis', 'hari', '2025-05-10 00:00:00', 1, 'Dikembalikan', 'syamil', '2025-05-10 07:54:08', '2025-05-21 16:53:29'),
(7, 'azka zuhairi', 'Charger', 'hari', '2025-05-21 00:00:00', 1, 'Dikembalikan', 'Ustadz Amir', '2025-05-21 22:00:33', '2025-05-24 05:54:34'),
(8, 'Muhammad Syamil Alfarizi', 'Kunci Pintu', 'hari', '2025-05-21 00:00:00', 1, 'Dikembalikan', 'raka', '2025-05-21 22:01:29', '2025-05-23 16:06:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_list`
--
ALTER TABLE `admin_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_admin` (`nama_admin`);

--
-- Indexes for table `barang_list`
--
ALTER TABLE `barang_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_barang` (`nama_barang`);

--
-- Indexes for table `peminjam`
--
ALTER TABLE `peminjam`
  ADD PRIMARY KEY (`nama`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nama` (`nama`),
  ADD KEY `idx_jenis_barang` (`jenis_barang`),
  ADD KEY `idx_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_list`
--
ALTER TABLE `admin_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `barang_list`
--
ALTER TABLE `barang_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
