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
-- Database: `doctor_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `doctor_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gender` enum('Nam','Nữ','Khác') NOT NULL,
  `specialization_id` int(11) NOT NULL,
  `experience_years` int(11) DEFAULT 0,
  `description` text DEFAULT NULL,
  `status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `doctor_name`, `email`, `gender`, `specialization_id`, `experience_years`, `description`, `status`) VALUES
(1, 'BS Nguyễn Văn A', 'vana@hospital.com', 'Nam', 1, 10, 'Chuyên gia tim mạch cấp cao', 1),
(2, 'BS Trần Thị B', 'thib@hospital.com', 'Nữ', 1, 7, 'Điều trị suy tim và tăng huyết áp', 1),
(3, 'BS Lê Quốc C', 'quo cc@hospital.com', 'Nam', 1, 5, 'Bác sĩ tim mạch trẻ, chuyên mạch vành', 1),
(4, 'BS Phạm Thị D', 'thid@hospital.com', 'Nữ', 2, 12, 'Bác sĩ sản khoa nhiều kinh nghiệm', 1),
(5, 'BS Nguyễn Hồng E', 'honge@hospital.com', 'Nữ', 2, 8, 'Khám thai và sinh thường', 1),
(6, 'BS Võ Thanh F', 'thanhf@hospital.com', 'Nam', 2, 6, 'Chuyên phẫu thuật sản', 1),
(7, 'BS Đinh Văn G', 'vang@hospital.com', 'Nam', 3, 9, 'Điều trị viêm tai và viêm xoang', 1),
(8, 'BS Lê Mỹ H', 'myh@hospital.com', 'Nữ', 3, 4, 'Bác sĩ TMH tổng quát', 1),
(9, 'BS Hoàng Phúc I', 'phuci@hospital.com', 'Nam', 3, 11, 'Phẫu thuật nội soi TMH', 1),
(10, 'BS Trịnh Kim J', 'kimj@hospital.com', 'Nữ', 4, 10, 'Điều trị da liễu thẩm mỹ', 1),
(11, 'BS Ngô Thái K', 'thaik@hospital.com', 'Nam', 4, 7, 'Điều trị mụn và viêm da', 1),
(12, 'BS Phạm Mỹ L', 'myl@hospital.com', 'Nữ', 4, 5, 'Chăm sóc da chuyên sâu', 1),
(13, 'BS Nguyễn Minh M', 'minhm@hospital.com', 'Nam', 5, 15, 'Nội khoa tổng quát', 1),
(14, 'BS Võ Thanh N', 'thanhn@hospital.com', 'Nam', 5, 6, 'Điều trị bệnh mạn tính', 1),
(15, 'BS Đặng Mỹ O', 'myo@hospital.com', 'Nữ', 5, 9, 'Khám nội cơ bản', 1),
(16, 'BS Phan Hữu P', 'huup@hospital.com', 'Nam', 6, 14, 'Phẫu thuật ngoại khoa', 1),
(17, 'BS Đỗ Minh Q', 'minhq@hospital.com', 'Nam', 6, 8, 'Ngoại tiêu hóa', 1),
(18, 'BS Trương Mỹ R', 'myr@hospital.com', 'Nữ', 6, 6, 'Ngoại tổng quát', 1);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedule`
--

CREATE TABLE `doctor_schedule` (
  `schedule_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `session` enum('morning','afternoon','evening') NOT NULL,
  `available_slots` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `doctor_schedule`
--

INSERT INTO `doctor_schedule` (`schedule_id`, `doctor_id`, `date`, `session`, `available_slots`) VALUES
(1, 1, '2025-11-15', 'morning', 10),
(2, 1, '2025-11-16', 'afternoon', 8),
(3, 1, '2025-11-18', 'evening', 0),
(4, 2, '2025-11-17', 'morning', 12),
(5, 2, '2025-11-18', 'afternoon', 10),
(6, 2, '2025-11-20', 'evening', 0),
(7, 11, '2025-11-15', 'morning', 6),
(8, 11, '2025-11-19', 'morning', 5),
(9, 11, '2025-11-21', 'afternoon', 0),
(10, 4, '2025-11-20', 'morning', 0),
(11, 4, '2025-11-21', 'afternoon', 0),
(12, 4, '2025-11-22', 'evening', 4),
(13, 5, '2025-11-15', 'morning', 15),
(14, 5, '2025-11-22', 'afternoon', 9),
(15, 5, '2025-11-23', 'evening', 0),
(16, 6, '2025-11-15', 'morning', 10),
(17, 6, '2025-11-16', 'afternoon', 8),
(18, 6, '2025-11-18', 'evening', 0),
(19, 9, '2025-11-17', 'morning', 12),
(20, 9, '2025-11-18', 'afternoon', 10),
(21, 9, '2025-11-20', 'evening', 0),
(22, 13, '2025-11-15', 'morning', 6),
(23, 13, '2025-11-19', 'morning', 5),
(24, 13, '2025-11-21', 'afternoon', 0),
(25, 16, '2025-11-20', 'morning', 0),
(26, 16, '2025-11-21', 'afternoon', 0),
(27, 16, '2025-11-22', 'evening', 4),
(28, 18, '2025-11-15', 'morning', 15),
(29, 18, '2025-11-22', 'afternoon', 9),
(30, 18, '2025-11-23', 'evening', 0);

-- --------------------------------------------------------

--
-- Table structure for table `specializations`
--

CREATE TABLE `specializations` (
  `specialization_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `amount` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `specializations`
--

INSERT INTO `specializations` (`specialization_id`, `name`, `amount`) VALUES
(1, 'Tim mạch', 180000.00),
(2, 'Sản - Phụ khoa', 150000.00),
(3, 'Tai - Mũi - Họng', 120000.00),
(4, 'Da liễu', 130000.00),
(5, 'Nội tổng quát', 100000.00),
(6, 'Ngoại tổng quát', 160000.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `specialization_id` (`specialization_id`);

--
-- Indexes for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `specializations`
--
ALTER TABLE `specializations`
  ADD PRIMARY KEY (`specialization_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `specializations`
--
ALTER TABLE `specializations`
  MODIFY `specialization_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`specialization_id`);

--
-- Constraints for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  ADD CONSTRAINT `doctor_schedule_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
