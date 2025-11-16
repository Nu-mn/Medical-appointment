-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2025 at 02:45 PM
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
-- Database: `studentservice`
--

-- --------------------------------------------------------

--
-- Table structure for table `studentfee`
--

CREATE TABLE `studentfee` (
  `tuition_id` int(11) NOT NULL,
  `student_id` varchar(10) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `due_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `studentfee`
--

INSERT INTO `studentfee` (`tuition_id`, `student_id`, `amount`, `status`, `due_date`) VALUES
(1, '52300005', 1500000.00, 'unpaid', '2025-10-05'),
(2, '52300014', 1400000.00, 'unpaid', '2025-10-06'),
(3, '52300048', 1600000.00, 'unpaid', '2025-10-07'),
(4, '62300001', 1300000.00, 'unpaid', '2025-10-08'),
(5, 'H2300002', 1200000.00, 'unpaid', '2025-10-09'),
(6, '32300003', 1450000.00, 'unpaid', '2025-10-10'),
(7, '02300004', 1350000.00, 'unpaid', '2025-10-11'),
(8, '42300006', 1550000.00, 'unpaid', '2025-10-12'),
(9, '02300007', 1250000.00, 'unpaid', '2025-10-13'),
(10, 'H2300008', 1450000.00, 'unpaid', '2025-10-14');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `mssv` varchar(10) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `mssv`, `fullname`, `department`) VALUES
(1, '52300005', 'Trương Lê Hiếu An', 'Công nghệ thông tin'),
(2, '52300014', 'Nguyễn Thị Anh Đào', 'Công nghệ thông tin'),
(3, '52300048', 'Trần Thị Mỹ Nữ', 'Công nghệ thông tin'),
(4, '62300001', 'Lê Văn A', 'Kỹ thuật'),
(5, 'H2300002', 'Phạm Thị B', 'Dược'),
(6, '32300003', 'Hoàng Văn C', 'Kinh doanh'),
(7, '02300004', 'Đỗ Thị D', 'Ngôn ngữ'),
(8, '42300006', 'Ngô Văn E', 'Môi trường'),
(9, '02300007', 'Vũ Thị F', 'Ngôn ngữ'),
(10, 'H2300008', 'Trần Văn G', 'Dược');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `studentfee`
--
ALTER TABLE `studentfee`
  ADD PRIMARY KEY (`tuition_id`),
  ADD KEY `fk_student` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mssv` (`mssv`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `studentfee`
--
ALTER TABLE `studentfee`
  MODIFY `tuition_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `studentfee`
--
ALTER TABLE `studentfee`
  ADD CONSTRAINT `fk_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`mssv`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
