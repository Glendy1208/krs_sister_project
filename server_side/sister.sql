-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 18, 2024 at 10:14 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sister`
--

-- --------------------------------------------------------

--
-- Table structure for table `hari`
--

CREATE TABLE `hari` (
  `id_hari` int NOT NULL,
  `nama_hari` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `id_jadwal` int NOT NULL,
  `semester_id` int NOT NULL,
  `matkul_id` int NOT NULL,
  `aktif` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jenis_matkul`
--

CREATE TABLE `jenis_matkul` (
  `id_jenis_matkul` int NOT NULL,
  `tipe_matkul` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id_kelas` int NOT NULL,
  `nama_kelas` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `nim` bigint NOT NULL,
  `nama` varchar(255) NOT NULL,
  `password` varchar(270) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `semester_now` int NOT NULL,
  `ukt_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `matakuliah`
--

CREATE TABLE `matakuliah` (
  `id_matkul` int NOT NULL,
  `waktu_id` int NOT NULL,
  `kelas_id` int NOT NULL,
  `jenis_matkul_id` int NOT NULL,
  `hari_id` int NOT NULL,
  `ruangan_id` int NOT NULL,
  `nama_matkul` varchar(255) NOT NULL,
  `sks` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ruangan`
--

CREATE TABLE `ruangan` (
  `id_ruangan` int NOT NULL,
  `nama_ruangan` char(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `semester_history`
--

CREATE TABLE `semester_history` (
  `id_semester` int NOT NULL,
  `angka_semester` int NOT NULL,
  `paid` tinyint(1) NOT NULL,
  `nim_fk` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ukt`
--

CREATE TABLE `ukt` (
  `id_ukt` int NOT NULL,
  `besaran` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `waktu`
--

CREATE TABLE `waktu` (
  `id_waktu` int NOT NULL,
  `jam` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hari`
--
ALTER TABLE `hari`
  ADD PRIMARY KEY (`id_hari`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `semester_foreign` (`semester_id`),
  ADD KEY `matakul_foreign` (`matkul_id`);

--
-- Indexes for table `jenis_matkul`
--
ALTER TABLE `jenis_matkul`
  ADD PRIMARY KEY (`id_jenis_matkul`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id_kelas`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`nim`),
  ADD KEY `ukt_foreign` (`ukt_id`);

--
-- Indexes for table `matakuliah`
--
ALTER TABLE `matakuliah`
  ADD PRIMARY KEY (`id_matkul`),
  ADD KEY `waktu_foreign` (`waktu_id`),
  ADD KEY `kelas_foreign` (`kelas_id`),
  ADD KEY `jm_foreign` (`jenis_matkul_id`),
  ADD KEY `hari_foreign` (`hari_id`),
  ADD KEY `ruangan_foreign` (`ruangan_id`);

--
-- Indexes for table `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`id_ruangan`);

--
-- Indexes for table `semester_history`
--
ALTER TABLE `semester_history`
  ADD PRIMARY KEY (`id_semester`),
  ADD KEY `nim_foreign` (`nim_fk`);

--
-- Indexes for table `ukt`
--
ALTER TABLE `ukt`
  ADD PRIMARY KEY (`id_ukt`);

--
-- Indexes for table `waktu`
--
ALTER TABLE `waktu`
  ADD PRIMARY KEY (`id_waktu`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hari`
--
ALTER TABLE `hari`
  MODIFY `id_hari` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id_jadwal` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jenis_matkul`
--
ALTER TABLE `jenis_matkul`
  MODIFY `id_jenis_matkul` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id_kelas` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `nim` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `matakuliah`
--
ALTER TABLE `matakuliah`
  MODIFY `id_matkul` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `id_ruangan` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `semester_history`
--
ALTER TABLE `semester_history`
  MODIFY `id_semester` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ukt`
--
ALTER TABLE `ukt`
  MODIFY `id_ukt` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `waktu`
--
ALTER TABLE `waktu`
  MODIFY `id_waktu` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `matakul_foreign` FOREIGN KEY (`matkul_id`) REFERENCES `matakuliah` (`id_matkul`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `semester_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semester_history` (`id_semester`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `ukt_foreign` FOREIGN KEY (`ukt_id`) REFERENCES `ukt` (`id_ukt`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `matakuliah`
--
ALTER TABLE `matakuliah`
  ADD CONSTRAINT `hari_foreign` FOREIGN KEY (`hari_id`) REFERENCES `hari` (`id_hari`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `jm_foreign` FOREIGN KEY (`jenis_matkul_id`) REFERENCES `jenis_matkul` (`id_jenis_matkul`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `kelas_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id_kelas`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `ruangan_foreign` FOREIGN KEY (`ruangan_id`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `waktu_foreign` FOREIGN KEY (`waktu_id`) REFERENCES `waktu` (`id_waktu`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `semester_history`
--
ALTER TABLE `semester_history`
  ADD CONSTRAINT `nim_foreign` FOREIGN KEY (`nim_fk`) REFERENCES `mahasiswa` (`nim`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
