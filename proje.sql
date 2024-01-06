-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2024 at 05:36 PM
-- Server version: 8.2.0
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `proje`
--

-- --------------------------------------------------------

--
-- Table structure for table `dersler`
--

CREATE TABLE `dersler` (
  `ders_id` int NOT NULL,
  `ders_adi` varchar(255) NOT NULL,
  `ders_sinif` int NOT NULL,
  `saat` varchar(45) NOT NULL,
  `gun` varchar(45) DEFAULT NULL,
  `renk` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `dersler`
--

INSERT INTO `dersler` (`ders_id`, `ders_adi`, `ders_sinif`, `saat`, `gun`, `renk`) VALUES
(101, 'Web Tasarımı', 1030, '13:00', 'Pazartesi', '2'),
(102, 'Elektrik Elektronik Devreler', 1040, '14:00', 'Perşembe', '0'),
(103, 'Ayrık Matematik', 1050, '10:00', 'Cuma', '1'),
(104, 'Bilgisayar Mimari ve Organizasyonu', 1030, '14:00', 'Cuma', '0'),
(105, 'Bilişim Sistemleri Analizi ve Tasarımı', 1030, '12:00', 'Salı', '1'),
(106, 'E-Ticaret ve E-İşletme Uygulamaları', 1040, '10:00', 'Perşembe', '1'),
(107, 'Yazılım Geliştirme Laboratuvarı-I', 1050, '15:00', 'Pazartesi', '0'),
(108, 'Yönetim ve Organizasyon', 1060, '10:00', 'Salı', '2'),
(109, 'Mobil Uygulama Geliştirme', 1030, '14:00', 'Salı', '3'),
(110, 'Nesne Yönelimli Programlama', 1040, '12:00', 'Çarşamba', '2'),
(111, 'Diferansiyel Denklemler', 1050, '12:00', 'Cuma', '2'),
(112, 'İstatistik ve Olasılık', 1060, '15:00', 'Cuma', '0'),
(113, 'Veri Tabanı', 1030, '11:00', 'Salı', '4');

-- --------------------------------------------------------

--
-- Table structure for table `hocalar`
--

CREATE TABLE `hocalar` (
  `hoca_id` int NOT NULL,
  `hoca_adi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hocalar`
--

INSERT INTO `hocalar` (`hoca_id`, `hoca_adi`) VALUES
(501, 'Önder Yakut'),
(502, 'Zeynep Sarı'),
(503, 'Süleyman Eken'),
(504, 'Yavuz Selim Fatihoğlu'),
(505, 'Ceylan Gazi Uçkun');

-- --------------------------------------------------------

--
-- Table structure for table `hoca_ders`
--

CREATE TABLE `hoca_ders` (
  `hoca_id` int NOT NULL,
  `ders_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hoca_ders`
--

INSERT INTO `hoca_ders` (`hoca_id`, `ders_id`) VALUES
(502, 101),
(502, 102),
(502, 103),
(501, 104),
(501, 105),
(503, 106),
(503, 107),
(503, 108),
(504, 109),
(504, 110),
(505, 111),
(505, 112),
(505, 113);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dersler`
--
ALTER TABLE `dersler`
  ADD PRIMARY KEY (`ders_id`);

--
-- Indexes for table `hocalar`
--
ALTER TABLE `hocalar`
  ADD PRIMARY KEY (`hoca_id`);

--
-- Indexes for table `hoca_ders`
--
ALTER TABLE `hoca_ders`
  ADD KEY `hoca_ders_ibfk_2` (`ders_id`),
  ADD KEY `hoca_ders_ibfk_1` (`hoca_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hocalar`
--
ALTER TABLE `hocalar`
  MODIFY `hoca_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=512;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hoca_ders`
--
ALTER TABLE `hoca_ders`
  ADD CONSTRAINT `hoca_ders_ibfk_1` FOREIGN KEY (`hoca_id`) REFERENCES `hocalar` (`hoca_id`),
  ADD CONSTRAINT `hoca_ders_ibfk_2` FOREIGN KEY (`ders_id`) REFERENCES `dersler` (`ders_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
