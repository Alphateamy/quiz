-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2023 at 02:10 AM
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
-- Database: `online_quiz_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_quiz`
--

CREATE TABLE `tbl_quiz` (
  `tbl_quiz_id` int(11) NOT NULL,
  `quiz_question` text NOT NULL,
  `option_a` text NOT NULL,
  `option_b` text NOT NULL,
  `option_c` text NOT NULL,
  `option_d` text NOT NULL,
  `correct_answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_quiz`
--

INSERT INTO `tbl_quiz` (`tbl_quiz_id`, `quiz_question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`) VALUES
(1, 'What is HTML stands for?', 'How To Make Lumpia', 'Hyper Tronic Mongo Logic', 'Hard To Make Love', 'HyperText Markup Language', 'D'),
(2, 'What is the original acronym of PHP?', 'Hypertext Preprocessor', 'Personal Home Page', 'Programming Happy Pill', 'None of the above', 'B'),
(3, 'CSS is fundamental to?', 'Databases', 'Web design', 'Server-side', 'None of the above', 'B');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_result`
--

CREATE TABLE `tbl_result` (
  `tbl_result_id` int(11) NOT NULL,
  `quiz_taker` text NOT NULL,
  `year_section` text NOT NULL,
  `total_score` int(11) NOT NULL,
  `date_taken` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_quiz`
--
ALTER TABLE `tbl_quiz`
  ADD PRIMARY KEY (`tbl_quiz_id`);

--
-- Indexes for table `tbl_result`
--
ALTER TABLE `tbl_result`
  ADD PRIMARY KEY (`tbl_result_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_quiz`
--
ALTER TABLE `tbl_quiz`
  MODIFY `tbl_quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_result`
--
ALTER TABLE `tbl_result`
  MODIFY `tbl_result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('teacher','student') NOT NULL,
  `passcode` varchar(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `tbl_passcodes`
--

CREATE TABLE `tbl_passcodes` (
  `passcode_id` int(11) NOT NULL AUTO_INCREMENT,
  `passcode` varchar(4) NOT NULL,
  `year` int(4) NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`passcode_id`),
  UNIQUE KEY `passcode` (`passcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_passcodes`
--

INSERT INTO `tbl_passcodes` (`passcode`, `year`, `is_used`) VALUES
('2020', 2020, 0),
('2021', 2021, 0),
('2022', 2022, 0),
('2023', 2023, 0),
('2024', 2024, 0),
('2025', 2025, 0),
('2026', 2026, 0),
('2027', 2027, 0),
('2028', 2028, 0),
('2029', 2029, 0),
('2030', 2030, 0),
('2031', 2031, 0),
('2032', 2032, 0),
('2033', 2033, 0),
('2034', 2034, 0),
('2035', 2035, 0),
('2036', 2036, 0),
('2037', 2037, 0),
('2038', 2038, 0),
('2039', 2039, 0),
('2040', 2040, 0);

-- Table structure for table `tbl_passcode_counter`
--

CREATE TABLE `tbl_passcode_counter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `next_passcode` int(4) NOT NULL DEFAULT 2020,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert initial counter value
INSERT INTO `tbl_passcode_counter` (`next_passcode`) VALUES (2020);

-- Create admin table
CREATE TABLE `tbl_admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default admin account
INSERT INTO `tbl_admin` (`email`, `password`) 
VALUES ('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
