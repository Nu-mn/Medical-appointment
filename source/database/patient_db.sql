-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 19, 2025 at 04:38 PM
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
-- User 1
(1, 1, 'Nguyen Van A', '1990-01-01', 'male', 'a.nguyen@example.com', '0911111111', 'Ha Noi', '123456789001', 'BHXH001'),
(2, 1, 'Tran Thi B', '1995-05-12', 'female', 'b.tran@example.com', '0922222222', 'Hai Phong', '123456789002', 'BHXH002'),

-- User 2
(3, 2, 'Le Van C', '1988-03-10', 'male', 'c.le@example.com', '0933333333', 'Da Nang', '123456789003', 'BHXH003'),
(4, 2, 'Pham Thi D', '1992-09-20', 'female', 'd.pham@example.com', '0944444444', 'Hoi An', '123456789004', 'BHXH004'),

-- User 3
(5, 3, 'Hoang Van E', '2000-12-05', 'male', 'e.hoang@example.com', '0955555555', 'TP.HCM', '123456789005', 'BHXH005'),
(6, 3, 'Vu Thi F', '1998-07-14', 'female', 'f.vu@example.com', '0966666666', 'Can Tho', '123456789006', 'BHXH006');

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
