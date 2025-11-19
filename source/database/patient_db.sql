-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 18, 2025 at 11:35 AM
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
-- Database: `patient_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `citizen_id` varchar(50) DEFAULT NULL,
  `insurance_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `user_id`, `full_name`, `date_of_birth`, `gender`, `email`, `phone`, `address`, `citizen_id`, `insurance_number`) VALUES
(6, 6, 'Tran Van A', '1990-01-01', 'male', 'bna@example.com', '0912345678', 'VIET KIEU', '123456789', 'BHXH001'),
(7, 7, 'Peter Parker', '1987-04-10', 'other', 'peter.parker@example.com', '0967890123', '25 Lê Duẩn, Huế', '012345678907', 'BHXH007'),
(8, 8, 'Sara KIEm', '1993-03-18', 'female', 'sara.phan@example.com', '0978901234', '100 Phan Xích Long, TP.HCM', '012345678908', 'BHXH008'),
(9, 9, 'Tom Hardy', '2001-12-01', 'male', 'tom.hardy@example.com', '0989012345', '33 Hoàng Diệu, Đà Nẵng', '012345678909', 'BHXH009'),
(10, 10, 'Emma Watson', '1985-06-25', 'female', 'emma.watson@example.com', '0990123456', '77 Trường Chinh, Hà Nội', '012345678910', 'BHXH010'),
(11, 1, 'Le Thi My Hang', '1988-06-17', 'female', 'bna@example.com', '0912345678', 'Tan phong Quan 7 TpHCM', NULL, ''),
(16, 1, 'Tom Hardy', '2001-12-01', 'female', 'trleehan09@gmail.com', '0989012345', '33 Hoàng Diệu, Đà Nẵng', '012345678909', ''),
(19, 1, 'Tran Van A', '1990-01-01', 'male', 'bna@example.com', '0912345678', 'Ha Noi', '123456789', 'BHXH001'),
(20, 1, 'Mỹ Diệu', '1999-01-07', 'female', 'diua.tran@example.com', '0956789012', '90 Quang Trung, Cần Thơ', '012345678906', 'BHXH006');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
