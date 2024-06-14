-- phpMyAdmin SQL Dump
-- version 5.3.0-dev+20221220.e5e070c814
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2024 at 05:31 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_connection`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_codes`
--

CREATE TABLE `access_codes` (
  `id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `code` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `access_codes`
--

INSERT INTO `access_codes` (`id`, `role`, `code`) VALUES
(6, 'doctor', 'b3666d14ca079417ba6c2a99f079b2ac'),
(7, 'admin', '0192023a7bbd73250516f069df18b500');

-- --------------------------------------------------------

--
-- Table structure for table `activation_tokens`
--

CREATE TABLE `activation_tokens` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `activation_tokens`
--

INSERT INTO `activation_tokens` (`id`, `token`, `patient_id`, `created_at`) VALUES
(6, '8a1bce6cbfcb902d22e2922250176b77', 62, '2024-05-14 19:10:46');

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `address_id` int(11) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `patient_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`address_id`, `address_line1`, `address_line2`, `city`, `state`, `postcode`, `patient_id`) VALUES
(13, 'taman selesa', 'jalan silat harimau 21', 'jb', 'Johor', '81300', 70),
(14, 'Kelantan 1', 'Shah alam subang jaya', 'kelanatan', 'Pulau Pinang', '785469', 75),
(15, 'taman selesa', 'jalan silat harimau 21', 'jb', 'WP Putrajaya', '81300', 79),
(16, 'taman selesa', 'jalan silat harimau 21', 'jb', 'WP Putrajaya', '81300', 80),
(17, 'taman selesa', 'jalan silat harimau 21', 'jb', 'WP Putrajaya', '81300', 81),
(18, 'taman selesa', 'jalan silat harimau 21', '', NULL, NULL, 82),
(19, 'taman selesa', 'jalan silat harimau 21', '', NULL, NULL, 83),
(20, 'Hang Lekir', 'Nusa Bestari', '', NULL, NULL, 94),
(21, 'taman selesa', 'jalan silat harimau 21', '', NULL, NULL, 103),
(22, 'sri bayu apartment jalan silat harimau 21', 'Nusa Bestari', '', NULL, NULL, 107),
(23, 'taman selesa', 'jalan silat harimau 21', '', NULL, NULL, 110),
(24, 'sri bayu apartment jalan silat harimau 21', 'Nusa Bestari', '', NULL, NULL, 111),
(25, 'sri bayu apartment jalan silat harimau 21', 'Nusa Bestari', '', NULL, NULL, 112),
(26, 'sri bayu apartment jalan silat harimau 21', 'tun hussein onn', '', NULL, NULL, 113),
(27, 'A4-12 Jalan Silat Harimau 21, Bandar Selesa Jaya,81300 Skudai,Johor', 'JALAN SILAT HARIMAU 21,', 'JB', 'Johor', '81300', 119),
(28, 'sri bayu apartment jalan silat harimau 21', 'Shah alam subang jaya', '', NULL, NULL, 134),
(29, 'sri bayu apartment jalan silat harimau 21', 'Shah alam subang jaya', '', NULL, NULL, 135),
(30, 'Nusa bestari 2 ', 'terranganu', 'Unknown', '', '', 136),
(31, 'tan sri yakob ', 'nusa bestari 1', 'Unknown', '', '', 62),
(32, 'sri bayu apartment jalan silat harimau 21', 'Shah alam subang jaya', '', NULL, NULL, 137),
(33, 'sri bayu apartment jalan silat harimau 21', 'Shah alam subang jaya', 'Unknown', '', '', 137),
(34, 'sri bayu apartment jalan silat harimau 21', 'Shah alam subang jaya', '', NULL, NULL, 138),
(35, 'A4-12 Jalan Silat Harimau 21, Bandar Selesa Jaya,81300 Skudai,Johor', 'JALAN SILAT HARIMAU 21,', 'Unknown', '', '', 119),
(38, 'A4-12 Jalan Silat Harimau 21, Bandar Selesa Jaya,81300 Skudai,Johor', 'JALAN SILAT HARIMAU 21,', 'Unknown', '', '', 119),
(39, 'sri bayu apartment jalan silat harimau 21', 'Shah alam subang jaya', 'Unknown', '', '', 135),
(40, '01245789654', 'tun hussein onn', '', NULL, NULL, 143);

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(4, 'admin', '$2y$10$UK4ul8hCQPQpzSmyydeX.eBssKVSRxDQGgXNQcmIOYrwOc4DcUPeC'),
(5, 'admin1', '9ff178e70e057bb93a593ec62aff3a68a1f7a28a2ead6d7a8e36ce2b4843e9e6');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `appointment_time` datetime NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appointment_id`, `patient_id`, `doctor_id`, `service_id`, `appointment_time`, `phone`, `email`, `status`) VALUES
(167, 119, 73, 2, '2024-06-15 17:30:00', '145789654', NULL, 'Pending'),
(169, 136, 73, 4, '2024-06-15 16:50:00', '0127788965', 'ndheshini@gmail.com', 'Pending'),
(170, 135, 73, 3, '2024-06-15 19:01:00', '0103843391', NULL, 'Pending'),
(183, 142, 72, 2, '2024-06-16 11:31:00', '01245796654', NULL, 'Pending'),
(185, 144, 81, 3, '2024-06-16 17:46:00', '0167766391', 'ndheshini@gmail.com', 'Pending'),
(186, 144, 81, 2, '2024-06-16 04:45:00', '0167766391', 'ndheshini@gmail.com', 'Pending'),
(187, 144, 81, 1, '2024-06-15 17:52:00', '0127788965', 'ndheshini@gmail.com', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `booked_slots`
--

CREATE TABLE `booked_slots` (
  `booking_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `appointment_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` varchar(50) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `gender` enum('male','female') NOT NULL,
  `home_address` varchar(255) NOT NULL,
  `religion` varchar(50) NOT NULL,
  `identity_card` varchar(50) NOT NULL,
  `dateOfBirth` date DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `login_attempts` int(11) DEFAULT NULL,
  `account_locked` tinyint(1) DEFAULT NULL,
  `lock_time` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `verification_code_expires` datetime DEFAULT NULL,
  `max_appointments_per_day` int(11) DEFAULT 10,
  `user_type` varchar(10) NOT NULL DEFAULT 'doctor',
  `status` varchar(20) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `username`, `email`, `phone`, `password`, `verification_code`, `verified`, `gender`, `home_address`, `religion`, `identity_card`, `dateOfBirth`, `first_name`, `last_name`, `login_attempts`, `account_locked`, `lock_time`, `last_login`, `verification_code_expires`, `max_appointments_per_day`, `user_type`, `status`) VALUES
(72, 'Teddy00!', 'teddyboy2910@gmail.com', '5689784589', '$2y$10$FR5O.Kni11d6.N.IfTp/pO3c0.L0MPdsXW3tBMJJjf2EWqOiFcNUS', '663302b84765e', 1, 'male', 'A4-12 SRI BAYU APARTMENT ', 'Christian', '001029070112', '2000-10-24', 'Bavan Raj', 'Thever', 0, 0, NULL, '2024-06-14 19:25:45', '2024-05-02 05:05:24', 3, 'doctor', 'Inactive'),
(73, 'Persheela22!', 'persha1473@gmail.com', '0103843391', '$2y$10$pFs/CfPrE.qc/yyrnCDkxOgkAP3ZWx3e85glTR8wn4ZN.zAh0ZfGq', '6643b905da5bb', 1, 'female', 'Taman tun aminah', 'Buddhist', '011407070244', '2008-06-29', 'Persheela', 'Nagalingam', 0, NULL, NULL, '2024-06-14 05:43:27', '2024-05-14 21:19:29', 3, 'doctor', 'On Hold'),
(81, 'Dheshini11!', 'dheshini.sheesh@gmail.com', '0167766391', '$2y$10$SQPLKYBgP1D4DTIQ2qvmPuhnfjVAdcCIJu1UaEWc8BVNQh9hs/U.W', '666c38b804686', 1, 'male', '', '', '', NULL, '', '', 0, NULL, NULL, '2024-06-14 20:47:30', '2024-06-14 14:35:00', 10, 'doctor', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_availability`
--

CREATE TABLE `doctor_availability` (
  `availability_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `doctor_availability`
--

INSERT INTO `doctor_availability` (`availability_id`, `doctor_id`, `date`, `start_time`, `end_time`, `service_id`) VALUES
(88, 73, '2024-06-02', '08:10:00', '08:30:00', 6),
(89, 73, '2024-06-03', '09:50:00', '10:10:00', 1),
(90, 73, '2024-06-03', '09:50:00', '10:10:00', 2),
(91, 73, '2024-06-04', '11:53:00', '12:13:00', 4),
(143, 72, '2024-06-08', '10:50:00', '21:40:00', 3),
(144, 72, '2024-06-10', '09:00:00', '22:00:00', 12),
(145, 72, '2024-06-10', '09:00:00', '22:00:00', 13),
(146, 72, '2024-06-10', '09:00:00', '22:00:00', 14),
(147, 72, '2024-06-05', '10:55:00', '18:00:00', 1),
(148, 72, '2024-06-05', '10:55:00', '18:00:00', 7),
(149, 72, '2024-06-05', '10:55:00', '18:00:00', 8),
(150, 72, '2024-06-04', '08:00:00', '22:00:00', 13),
(151, 72, '2024-06-07', '08:10:00', '21:40:00', 1),
(152, 72, '2024-06-07', '08:10:00', '21:40:00', 2),
(153, 72, '2024-06-07', '08:10:00', '21:40:00', 3),
(154, 72, '2024-06-07', '08:10:00', '21:40:00', 4),
(155, 72, '2024-06-07', '08:10:00', '21:40:00', 7),
(156, 72, '2024-06-09', '10:40:00', '20:40:00', 1),
(157, 72, '2024-06-09', '10:40:00', '20:40:00', 2),
(158, 72, '2024-06-09', '10:40:00', '20:40:00', 3),
(159, 72, '2024-06-09', '10:40:00', '20:40:00', 4),
(160, 73, '2024-06-11', '08:50:00', '22:00:00', 1),
(161, 73, '2024-06-11', '08:50:00', '22:00:00', 2),
(162, 73, '2024-06-11', '08:50:00', '22:00:00', 3),
(163, 73, '2024-06-11', '08:50:00', '22:00:00', 4),
(164, 73, '2024-06-11', '08:50:00', '22:00:00', 7),
(165, 73, '2024-06-11', '08:50:00', '22:00:00', 8),
(166, 73, '2024-06-11', '08:50:00', '22:00:00', 9),
(167, 73, '2024-06-11', '08:50:00', '22:00:00', 10),
(168, 73, '2024-06-11', '08:50:00', '22:00:00', 13),
(169, 73, '2024-06-12', '10:40:00', '22:00:00', 10),
(170, 73, '2024-06-12', '10:40:00', '22:00:00', 11),
(171, 73, '2024-06-12', '10:40:00', '22:00:00', 13),
(172, 73, '2024-06-12', '10:40:00', '22:00:00', 14),
(173, 73, '2024-06-12', '10:40:00', '22:00:00', 15),
(174, NULL, '2024-06-11', '08:40:00', '21:40:00', 1),
(175, NULL, '2024-06-11', '08:40:00', '21:40:00', 2),
(176, 72, '2024-06-11', '09:40:00', '20:40:00', 1),
(177, 72, '2024-06-11', '09:40:00', '20:40:00', 2),
(178, 72, '2024-06-11', '09:40:00', '20:40:00', 3),
(179, 72, '2024-06-11', '09:40:00', '20:40:00', 4),
(180, 72, '2024-06-11', '09:40:00', '20:40:00', 16),
(181, 73, '2024-06-15', '08:46:00', '21:50:00', 1),
(182, 73, '2024-06-15', '08:46:00', '21:50:00', 2),
(183, 73, '2024-06-15', '08:46:00', '21:50:00', 3),
(184, 73, '2024-06-15', '08:46:00', '21:50:00', 4),
(185, 73, '2024-06-15', '08:46:00', '21:50:00', 7),
(186, 73, '2024-06-15', '08:46:00', '21:50:00', 10),
(187, 72, '2024-06-16', '10:30:00', '17:30:00', 1),
(188, 72, '2024-06-16', '10:30:00', '17:30:00', 2),
(189, 72, '2024-06-16', '10:30:00', '17:30:00', 3),
(190, 72, '2024-06-16', '10:30:00', '17:30:00', 4),
(191, 81, '2024-06-16', '10:40:00', '21:40:00', 1),
(192, 81, '2024-06-16', '10:40:00', '21:40:00', 2),
(193, 81, '2024-06-16', '10:40:00', '21:40:00', 3),
(194, 81, '2024-06-16', '10:40:00', '21:40:00', 4),
(195, 81, '2024-06-16', '10:40:00', '21:40:00', 7),
(196, NULL, '2024-06-15', '09:50:00', '21:46:00', 1),
(197, NULL, '2024-06-15', '09:50:00', '21:46:00', 2),
(198, NULL, '2024-06-15', '11:48:00', '21:47:00', 1),
(199, NULL, '2024-06-15', '11:48:00', '21:47:00', 2),
(200, NULL, '2024-06-15', '11:48:00', '21:47:00', 3),
(201, NULL, '2024-06-15', '11:48:00', '21:47:00', 4),
(202, 81, '2024-06-15', '12:50:00', '21:49:00', 1),
(203, 81, '2024-06-15', '12:50:00', '21:49:00', 2),
(204, 81, '2024-06-15', '12:50:00', '21:49:00', 3),
(205, 81, '2024-06-15', '12:50:00', '21:49:00', 4),
(206, 81, '2024-06-17', '15:50:00', '21:51:00', 1),
(207, 81, '2024-06-17', '15:50:00', '21:51:00', 2),
(208, 81, '2024-06-17', '15:50:00', '21:51:00', 3),
(209, 81, '2024-06-17', '15:50:00', '21:51:00', 4);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_availability_services`
--

CREATE TABLE `doctor_availability_services` (
  `availability_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `type` enum('feedback','inquiry') NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `patient_id`, `type`, `message`, `created_at`) VALUES
(7, 24, 'inquiry', 'pok', '2024-04-24 18:46:21'),
(8, 52, 'inquiry', '', '2024-05-02 03:10:46'),
(9, 119, 'inquiry', 'ok', '2024-06-06 03:29:54'),
(10, 140, 'inquiry', 'qa', '2024-06-14 10:58:36'),
(11, 141, 'inquiry', 'OKOO', '2024-06-14 11:49:16'),
(12, 142, 'inquiry', 'okok', '2024-06-14 12:03:41');

-- --------------------------------------------------------

--
-- Table structure for table `latest_news`
--

CREATE TABLE `latest_news` (
  `id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `medical_record_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `record_date` date NOT NULL,
  `symptoms` text DEFAULT NULL,
  `vital_signs` text DEFAULT NULL,
  `examination_findings` text DEFAULT NULL,
  `treatment_plan` text DEFAULT NULL,
  `follow_up_instructions` text DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nric_passport` varchar(20) DEFAULT NULL,
  `phone` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `home_phone` varchar(255) DEFAULT NULL,
  `office_phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `verification_code` varchar(50) DEFAULT NULL,
  `verify_code` varchar(255) NOT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `gender` varchar(10) NOT NULL,
  `verification_timestamp` datetime DEFAULT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `account_locked` tinyint(1) DEFAULT 0,
  `lock_time` datetime DEFAULT NULL,
  `verification_code_expires` datetime DEFAULT NULL,
  `emergency_number` varchar(255) NOT NULL,
  `added_by_admin` int(11) DEFAULT 0,
  `otp` varchar(10) DEFAULT NULL,
  `otp_expires` datetime DEFAULT NULL,
  `temporary_password` tinyint(1) DEFAULT 0,
  `temporary_password_timestamp` datetime DEFAULT NULL,
  `race` varchar(50) NOT NULL,
  `user_type` varchar(10) NOT NULL DEFAULT 'patient'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `username`, `email`, `nric_passport`, `phone`, `password`, `first_name`, `last_name`, `home_phone`, `office_phone`, `date_of_birth`, `verification_code`, `verify_code`, `verified`, `gender`, `verification_timestamp`, `login_attempts`, `last_login`, `account_locked`, `lock_time`, `verification_code_expires`, `emergency_number`, `added_by_admin`, `otp`, `otp_expires`, `temporary_password`, `temporary_password_timestamp`, `race`, `user_type`) VALUES
(62, 'Dheshini', 'radi@gmail.com', '012478965', '0167766391', '', 'Olimathi ', 'Supramaniam', NULL, NULL, '2024-05-15', NULL, '', 0, 'Male', NULL, 0, NULL, 0, NULL, NULL, '0167766391', 0, NULL, NULL, 0, NULL, 'Indian', 'patient'),
(137, 'Oli29!', 'oli@gmail.com', '7506090705700', '0103843391', '', 'oli', '', NULL, NULL, '2024-06-18', NULL, '', 0, 'Male', NULL, 0, NULL, 0, NULL, NULL, '', 0, NULL, NULL, 0, NULL, 'Chinese', 'patient'),
(143, 'Dheshini122', 'dheshini.sheesh@gmail.com', '001029070112', '0147896587', '', NULL, NULL, NULL, NULL, '2024-06-14', NULL, '', 0, 'male', NULL, 0, NULL, 0, NULL, NULL, '', 0, NULL, NULL, 0, NULL, 'chinese', 'patient'),
(144, 'Dheshini29!', 'ndheshini@gmail.com', NULL, '0167766391', '$2y$10$mV6sMSRyKKSjoKdO7QamMOWv3xxB1PaH8s118YnX2Vw8znxwqnH.S', NULL, NULL, NULL, NULL, NULL, '666c39ec5881e', '', 1, '', NULL, 0, '2024-06-14 20:48:20', 0, NULL, '2024-06-14 14:41:08', '', 0, NULL, NULL, 0, NULL, '', 'patient');

-- --------------------------------------------------------

--
-- Table structure for table `patient_records`
--

CREATE TABLE `patient_records` (
  `record_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `record_date` date NOT NULL,
  `medical_surgical_family_history` text DEFAULT NULL,
  `surgery_year` varchar(4) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `past_medical_history` text DEFAULT NULL,
  `clinical_summary` text DEFAULT NULL,
  `allergies_specify` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `name`, `description`) VALUES
(1, 'ANC', 'Antenatal Care'),
(2, 'Minor Surgery', 'These procedures may include the removal of skin tags, moles, cysts, or other minor skin lesions.'),
(3, 'Medical Check Up', 'BMI\r\nBlood pressure\r\nUrine Test'),
(4, 'Dressing', 'Cleaning Dirt And Dressing For Feet For Diabetes'),
(7, 'General Consultation', 'General consultations with a physician for diagnosis and treatment of common illnesses, routine check-ups, health screenings, and preventive care'),
(8, 'Vaccinations and Immunizations', 'Flu shots, MMR (measles, mumps, rubella), tetanus, and travel-specific vaccines like hepatitis A and B, typhoid, and yellow fever.'),
(9, 'Women\'s Health Services', 'Pap smears, breast examinations, contraceptive advice, pregnancy tests, prenatal care, and menopause management.'),
(10, 'Pediatric Care', 'Children, and adolescents, including regular health check-ups, growth and development assessments, vaccinations, and treatment for common childhood illnesses and conditions.'),
(11, 'Chronic Disease Management', 'Diabetes, hypertension, asthma, and cardiovascular diseases.'),
(13, 'Mental Health Services', 'Counseling and therapy services for individuals dealing with mental health issues such as anxiety, depression, stress, and other emotional difficulties'),
(14, 'Dermatology Services', 'Acne, eczema, psoriasis, and skin infections.'),
(15, 'Nutrition and Weight Management', 'Diet plans, lifestyle advice, and support for achieving and maintaining a healthy weight.'),
(16, 'Travel Medicine', 'Pre-travel consultations, vaccinations, and health advice for individuals traveling to different parts of the world.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activation_tokens`
--
ALTER TABLE `activation_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`address_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `booked_slots`
--
ALTER TABLE `booked_slots`
  ADD PRIMARY KEY (`booking_id`),
  ADD UNIQUE KEY `unique_booking` (`doctor_id`,`date`,`appointment_time`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  ADD PRIMARY KEY (`availability_id`),
  ADD KEY `fk_doctor_availability_doctor_id` (`doctor_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `latest_news`
--
ALTER TABLE `latest_news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`medical_record_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username_2` (`username`,`email`),
  ADD UNIQUE KEY `username_3` (`username`),
  ADD UNIQUE KEY `unique_username` (`username`);

--
-- Indexes for table `patient_records`
--
ALTER TABLE `patient_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activation_tokens`
--
ALTER TABLE `activation_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT for table `booked_slots`
--
ALTER TABLE `booked_slots`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  MODIFY `availability_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=210;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `latest_news`
--
ALTER TABLE `latest_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `medical_record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `patient_records`
--
ALTER TABLE `patient_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activation_tokens`
--
ALTER TABLE `activation_tokens`
  ADD CONSTRAINT `activation_tokens_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`);

--
-- Constraints for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  ADD CONSTRAINT `fk_doctor_availability_doctor_id` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`);

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`);

--
-- Constraints for table `patient_records`
--
ALTER TABLE `patient_records`
  ADD CONSTRAINT `patient_records_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`appointment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_records_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
