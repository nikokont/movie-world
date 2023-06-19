-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2023 at 12:44 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `movie_world`
--

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `movie_id` int(10) NOT NULL,
  `movie_title` varchar(100) NOT NULL,
  `movie_description` longtext NOT NULL,
  `movie_uploaded_date` date NOT NULL,
  `movie_uploaded_by` int(10) NOT NULL,
  `movie_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`movie_id`, `movie_title`, `movie_description`, `movie_uploaded_date`, `movie_uploaded_by`, `movie_photo`) VALUES
(10, 'Logan', 'In A Future Where Mutants Are Nearly Extinct, An Elderly And Weary Logan Leads A Quiet Life. But When Laura, A Mutant Child Pursued By Scientists, Comes To Him For Help, He Must Get Her To Safety.', '2023-06-18', 3, 'uploads/MV5BYzc5MTU4N2EtYTkyMi00NjdhLTg3NWEtMTY4OTEyMzJhZTAzXkEyXkFqcGdeQXVyNjc1NTYyMjg@._V1_FMjpg_UX1000_.jpg'),
(11, 'John Wick', 'An Ex-hitman Comes Out Of Retirement To Track Down The Gangsters Who Killed His Dog And Stole His Car.', '2023-06-18', 4, 'uploads/81F5PF9oHhL._AC_SL1500_.jpg'),
(12, 'Joker', 'The Rise Of Arthur Fleck, From Aspiring Stand-up Comedian And Pariah To Gotham\'s Clown Prince And Leader Of The Revolution.', '2023-06-18', 3, 'uploads/amirhosein-naseri-new-age.jpg'),
(13, 'Guardians Of The Galaxy', 'The Adventures Of A Band Of Space Warriors Who Work To Protect The Universe From The Evil Overlord Thanos.', '2023-06-18', 3, 'uploads/71lbFfxfMtL._AC_UF894,1000_QL80_.jpg'),
(14, 'The Dark Knight', 'When The Menace Known As The Joker Wreaks Havoc And Chaos On The People Of Gotham, Batman Must Accept One Of The Greatest Psychological And Physical Tests Of His Ability To Fight Injustice.', '2023-06-18', 3, 'uploads/MV5BMTMxNTMwODM0NF5BMl5BanBnXkFtZTcwODAyMTk2Mw@@._V1_FMjpg_UX1000_.jpg'),
(15, 'The Matrix', 'When A Beautiful Stranger Leads Computer Hacker Neo To A Forbidding Underworld, He Discovers The Shocking Truth--the Life He Knows Is The Elaborate Deception Of An Evil Cyber-intelligence.', '2023-06-18', 4, 'uploads/The_Matrix-iconic-movie-posters.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `user_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `user_email`, `user_password`) VALUES
(3, 'johndoe', 'doe@example.com', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441'),
(4, 'nikokont', 'nikokont@yahoo.com', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441');

-- --------------------------------------------------------

--
-- Table structure for table `users_vote`
--

CREATE TABLE `users_vote` (
  `user_vote_id` int(20) NOT NULL,
  `user_vote_like_hate` int(1) NOT NULL,
  `user_vote_user` int(10) NOT NULL,
  `user_vote_movie` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_vote`
--

INSERT INTO `users_vote` (`user_vote_id`, `user_vote_like_hate`, `user_vote_user`, `user_vote_movie`) VALUES
(36, 0, 4, 14),
(37, 1, 4, 13),
(38, 1, 4, 10);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`movie_id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `movie_uploaded_by` (`movie_uploaded_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users_vote`
--
ALTER TABLE `users_vote`
  ADD PRIMARY KEY (`user_vote_id`),
  ADD KEY `user_vote_user` (`user_vote_user`),
  ADD KEY `user_vote_movie` (`user_vote_movie`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `movie_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users_vote`
--
ALTER TABLE `users_vote`
  MODIFY `user_vote_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `movies`
--
ALTER TABLE `movies`
  ADD CONSTRAINT `movies_ibfk_1` FOREIGN KEY (`movie_uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_vote`
--
ALTER TABLE `users_vote`
  ADD CONSTRAINT `users_vote_ibfk_1` FOREIGN KEY (`user_vote_user`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_vote_ibfk_2` FOREIGN KEY (`user_vote_movie`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
