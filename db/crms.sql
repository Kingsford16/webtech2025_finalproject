-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2025 at 11:41 PM
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
-- Database: `crms`
--

DROP DATABASE IF EXISTS crms;
CREATE DATABASE crms;
USE crms;

-- --------------------------------------------------------

--
-- Table structure for table `approvals`
--

CREATE TABLE `approvals` (
  `app_id` int(11) NOT NULL,
  `app_satus` varchar(50) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approvals`
--

INSERT INTO `approvals` (`app_id`, `app_satus`) VALUES
(1, 'approved'),
(2, 'pending'),
(3, 'denied');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rm_id` int(11) DEFAULT NULL,
  `res_id` int(11) NOT NULL,
  `et_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `pro_id` int(11) NOT NULL,
  `capacity` int(11) NOT NULL,
  `event_date` date NOT NULL,
  `purpose` varchar(5000) NOT NULL,
  `datetime_of_booking` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currentstatus`
--

CREATE TABLE `currentstatus` (
  `cs_id` int(11) NOT NULL,
  `cs_status` varchar(50) NOT NULL DEFAULT 'inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `currentstatus`
--

INSERT INTO `currentstatus` (`cs_id`, `cs_status`) VALUES
(1, 'active'),
(2, 'inactive');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `dep_id` int(11) NOT NULL,
  `dep_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`dep_id`, `dep_name`) VALUES
(1, 'HR'),
(2, 'Facilities'),
(3, 'Library'),
(4, 'Engineering'),
(5, 'CSIS'),
(6, 'IT Support Center'),
(7, 'Humanities'),
(8, 'Business Administration');

-- --------------------------------------------------------

--
-- Table structure for table `durations`
--

CREATE TABLE `durations` (
  `dur_id` int(11) NOT NULL,
  `dur_category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `durations`
--

INSERT INTO `durations` (`dur_id`, `dur_category`) VALUES
(2, '1.5'),
(1, '3');

-- --------------------------------------------------------

--
-- Table structure for table `eventimes`
--

CREATE TABLE `eventimes` (
  `eventimes_id` int(11) NOT NULL,
  `hour_period` int(11) NOT NULL,
  `times` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eventimes`
--

INSERT INTO `eventimes` (`eventimes_id`, `hour_period`, `times`) VALUES
(1, 2, '8:00 - 9:30'),
(2, 2, '9:45 - 11:15'),
(3, 2, '11:30 - 13:00'),
(4, 2, '13:15 - 14:45'),
(5, 2, '15:00 - 16:30'),
(6, 2, '16:45 - 18:15'),
(7, 2, '18:30 - 20:00'),
(8, 1, '8:00 - 11:15'),
(9, 1, '11:30 - 14:45'),
(10, 1, '15:00 - 18:15'),
(11, 1, '9:45 - 13:00'),
(12, 1, '13:15 - 16:30'),
(13, 1, '16:45 - 20:00');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `loc_id` int(11) NOT NULL,
  `loc_name` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`loc_id`, `loc_name`) VALUES
(1, 'Warren Library - External'),
(2, 'Bioengineering Lab'),
(3, 'King Engineering Block'),
(4, 'New Hostel Area'),
(5, 'Old Basketball Court'),
(6, 'Hallmark'),
(7, 'Fablab'),
(8, 'Hostel Main Entrance'),
(9, 'Warren Library - Internal');

-- --------------------------------------------------------

--
-- Table structure for table `progress`
--

CREATE TABLE `progress` (
  `pro_id` int(11) NOT NULL,
  `pro_status` varchar(50) NOT NULL DEFAULT 'uncompleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `progress`
--

INSERT INTO `progress` (`pro_id`, `pro_status`) VALUES
(1, 'completed'),
(2, 'uncompleted'),
(3, 'cancelled');

-- --------------------------------------------------------

--
-- Table structure for table `resmanagers`
--

CREATE TABLE `resmanagers` (
  `rm_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `dep_id` int(11) NOT NULL,
  `res_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `res_id` int(11) NOT NULL,
  `res_status` int(11) NOT NULL,
  `loc_id` int(11) NOT NULL,
  `res_name` varchar(50) NOT NULL,
  `res_img` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`res_id`, `res_status`, `loc_id`, `res_name`, `res_img`) VALUES
(27, 2, 1, 'Student Hangout', '1764866557_student_hang_out.jpg'),
(29, 2, 3, 'Green Lounge', '1764844285_693162fd5d1c8.jpg'),
(32, 2, 4, 'Tawiah Roof Top', '1764886376_693207685c8ff.jpg'),
(33, 2, 4, 'Hostel 2D Roof Top', '1764886421_6932079516a23.jpg'),
(38, 2, 9, 'Seminar Room 1', '1764886574_6932082ee604e.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'administrator'),
(2, 'resource_manager'),
(3, 'student');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_role` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `staff_or_student_id` varchar(10) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(300) NOT NULL,
  `user_img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approvals`
--
ALTER TABLE `approvals`
  ADD PRIMARY KEY (`app_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id_fk` (`user_id`),
  ADD KEY `res_id_fk` (`res_id`),
  ADD KEY `et_id_fk` (`et_id`),
  ADD KEY `app_id_fk` (`app_id`),
  ADD KEY `pro_id_fk` (`pro_id`),
  ADD KEY `fk_for_rm_id` (`rm_id`);

--
-- Indexes for table `currentstatus`
--
ALTER TABLE `currentstatus`
  ADD PRIMARY KEY (`cs_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`dep_id`);

--
-- Indexes for table `durations`
--
ALTER TABLE `durations`
  ADD PRIMARY KEY (`dur_id`),
  ADD UNIQUE KEY `dur_category` (`dur_category`);

--
-- Indexes for table `eventimes`
--
ALTER TABLE `eventimes`
  ADD PRIMARY KEY (`eventimes_id`),
  ADD UNIQUE KEY `time` (`times`),
  ADD KEY `dur_id_fk` (`hour_period`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`loc_id`);

--
-- Indexes for table `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`pro_id`);

--
-- Indexes for table `resmanagers`
--
ALTER TABLE `resmanagers`
  ADD PRIMARY KEY (`rm_id`),
  ADD KEY `dep_id_fk` (`dep_id`),
  ADD KEY `fk_for_res_id` (`res_id`),
  ADD KEY `fk_for_user_id` (`user_id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`res_id`),
  ADD UNIQUE KEY `res_name` (`res_name`),
  ADD KEY `cs_id_fk` (`res_status`),
  ADD KEY `loc_id_fk` (`loc_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `student_id` (`staff_or_student_id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `role_id_fk` (`user_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approvals`
--
ALTER TABLE `approvals`
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `currentstatus`
--
ALTER TABLE `currentstatus`
  MODIFY `cs_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `dep_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `durations`
--
ALTER TABLE `durations`
  MODIFY `dur_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `eventimes`
--
ALTER TABLE `eventimes`
  MODIFY `eventimes_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `loc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `progress`
--
ALTER TABLE `progress`
  MODIFY `pro_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `resmanagers`
--
ALTER TABLE `resmanagers`
  MODIFY `rm_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `res_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `app_id_fk` FOREIGN KEY (`app_id`) REFERENCES `approvals` (`app_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `et_id_fk` FOREIGN KEY (`et_id`) REFERENCES `eventimes` (`eventimes_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_for_rm_id` FOREIGN KEY (`rm_id`) REFERENCES `resmanagers` (`rm_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pro_id_fk` FOREIGN KEY (`pro_id`) REFERENCES `progress` (`pro_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `res_id_fk` FOREIGN KEY (`res_id`) REFERENCES `resources` (`res_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `eventimes`
--
ALTER TABLE `eventimes`
  ADD CONSTRAINT `dur_id_fk` FOREIGN KEY (`hour_period`) REFERENCES `durations` (`dur_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `resmanagers`
--
ALTER TABLE `resmanagers`
  ADD CONSTRAINT `dep_id_fk` FOREIGN KEY (`dep_id`) REFERENCES `departments` (`dep_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_for_res_id` FOREIGN KEY (`res_id`) REFERENCES `resources` (`res_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_for_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `cs_id_fk` FOREIGN KEY (`res_status`) REFERENCES `currentstatus` (`cs_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `loc_id_fk` FOREIGN KEY (`loc_id`) REFERENCES `locations` (`loc_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `role_id_fk` FOREIGN KEY (`user_role`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
