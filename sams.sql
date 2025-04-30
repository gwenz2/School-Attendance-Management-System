-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2024 at 04:15 PM
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
-- Database: `sams`
--

-- --------------------------------------------------------

--
-- Table structure for table `absence_requests`
--

CREATE TABLE `absence_requests` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(255) NOT NULL,
  `status` enum('approve','disapprove','pending') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absence_requests`
--

INSERT INTO `absence_requests` (`id`, `student_id`, `subject_name`, `topic`, `description`, `attachment_path`, `request_date`, `created_by`, `status`) VALUES
(3, 9999, 'science', 'sg', 'sfff', NULL, '2024-11-28 16:42:07', 101, 'approve'),
(5, 9999, 'English', 'Sick', 'not feeling well', NULL, '2024-11-29 15:14:45', 101, 'approve');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(255) NOT NULL,
  `s_id` int(255) NOT NULL,
  `s_name` varchar(255) NOT NULL,
  `sub_name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','late','excuse','absent','pending') NOT NULL DEFAULT 'present',
  `created_by` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `s_id`, `s_name`, `sub_name`, `date`, `status`, `created_by`) VALUES
(30, 89999, 'jenn', 'Math', '2024-11-29', 'present', 100),
(31, 9999, 'Mia Albay', 'English', '2024-11-29', 'absent', 101);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('unread','read') DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `student_id`, `message`, `date`, `status`) VALUES
(11, 89999, 'Your child was marked as Present for the subject Math on 2024-11-29.', '2024-11-29 15:13:24', 'unread'),
(12, 9999, 'Your child was marked as Absent for the subject English on 2024-11-29.', '2024-11-29 15:13:59', 'unread'),
(13, 9999, 'Your child’s absence request for the subject English has been approved.', '2024-11-29 15:14:56', 'unread');

-- --------------------------------------------------------

--
-- Table structure for table `student_info`
--

CREATE TABLE `student_info` (
  `s_id` int(255) NOT NULL,
  `s_name` varchar(255) NOT NULL,
  `p_name` varchar(255) NOT NULL,
  `s_pin` int(255) NOT NULL DEFAULT 12345,
  `created_by` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_info`
--

INSERT INTO `student_info` (`s_id`, `s_name`, `p_name`, `s_pin`, `created_by`) VALUES
(9999, 'Mia Albay', 'Tomas', 12345, 101),
(89999, 'jenn', 'tomas', 12345, 100);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_number` int(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `mnumber` varchar(255) NOT NULL,
  `sub_name` varchar(255) NOT NULL,
  `tpassword` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_number`, `fullname`, `mnumber`, `sub_name`, `tpassword`) VALUES
(100, 'Rena', '09988998', 'Math', '12345'),
(101, 'Hermo', '54656', 'English', '12345');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absence_requests`
--
ALTER TABLE `absence_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `fk_created_by` (`created_by`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_attendance_s_id` (`s_id`),
  ADD KEY `fk_attendance_s_name` (`s_name`),
  ADD KEY `fk_attendance_c_by` (`created_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `student_info`
--
ALTER TABLE `student_info`
  ADD PRIMARY KEY (`s_id`),
  ADD KEY `s_name` (`s_name`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absence_requests`
--
ALTER TABLE `absence_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_number` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absence_requests`
--
ALTER TABLE `absence_requests`
  ADD CONSTRAINT `absence_requests_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student_info` (`s_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_created_by` FOREIGN KEY (`created_by`) REFERENCES `user` (`id_number`) ON DELETE CASCADE;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_attendance_c_by` FOREIGN KEY (`created_by`) REFERENCES `user` (`id_number`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attendance_s_id` FOREIGN KEY (`s_id`) REFERENCES `student_info` (`s_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attendance_s_name` FOREIGN KEY (`s_name`) REFERENCES `student_info` (`s_name`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student_info` (`s_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
