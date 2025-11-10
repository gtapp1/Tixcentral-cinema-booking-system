-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2025 at 10:12 AM
-- Server version: 8.0.43
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tixcentral2`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `schedule_id` int NOT NULL,
  `reference` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('PENDING','CONFIRMED','CANCELLED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_seats`
--

CREATE TABLE `booking_seats` (
  `booking_id` int NOT NULL,
  `seat_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `address`, `city`) VALUES
(1, 'SM North EDSA', 'North Avenue, corner Epifanio de los Santos Ave, Quezon City, 1100 Metro Manila', 'Quezon City'),
(2, 'SM Mall of Asia', 'Seaside Blvd, Pasay City, 1300 Metro Manila', 'Pasay City'),
(3, 'Ayala Malls Glorietta', 'Ayala Center, Makati City, 1226 Metro Manila', 'Makati City');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `synopsis` text COLLATE utf8mb4_unicode_ci,
  `runtime_minutes` int DEFAULT NULL,
  `genre` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poster_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `featured` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `title`, `synopsis`, `runtime_minutes`, `genre`, `poster_path`, `hero_image_path`, `featured`) VALUES
(1, 'Parasite', 'The other members of the poor Kim family see an opportunity when their son starts working for the rich Park family. Soon, they find ways to work within the same household and live a parasitic life.', 132, ' Thriller/Comedy', 'assets/images/posters/parasite.jpg', 'assets/images/hero/parasite_hero.jpg', 0),
(2, 'Edge of Tomorrow', 'an untrained military officer, Major William Cage, gets caught in a time loop after being killed in battle against an alien race called the Mimics. He is forced to relive the same brutal day over and over, but with each reset, his combat skills improve. He partners with an elite soldier, Rita Vrataski, to find a way to defeat the aliens and end the war. ', 113, 'Action/Sci-fi ‧', 'assets/images/posters/edge_of_tomorrow.jpg', 'assets/images/hero/edge_of_tomorrow_hero.jpg', 0),
(3, 'Avengers: Endgame', 'After Thanos erases half of all life, the surviving Avengers are scattered and broken. Five years later, they must reunite and find a way to reverse his actions, leading them to use time travel to retrieve the Infinity Stones from the past in a final, desperate attempt to save the universe. ', 181, 'Action/Sci-fi ', 'assets/images/posters/avengers_endgame.jpg', 'assets/images/hero/avengers_endgame.jpg', 0),
(4, 'Spider-Man: No Way Home\r\n', 'After his identity is revealed to the world, Peter Parker asks Doctor Strange to cast a spell to make people forget he is Spider-Man. The spell goes wrong, tearing open the multiverse and pulling in villains from previous Spider-Man universes, like Green Goblin, Doctor Octopus, and Electro. Peter must then work with his friends and eventually his alternate-universe versions (played by Tobey Maguire and Andrew Garfield) to stop the villains and fix the multiversal damage. ', 148, 'Action/Fantasy', 'assets/images/posters/spiderman_nwh.jpg', 'assets/images/hero/spiderman_nwh_hero.jpg', 0),
(5, 'The Hunger Games', 'In what was once North America, the Capitol of Panem maintains its hold on its 12 districts by forcing them each to select a boy and a girl, called Tributes, to compete in a nationally televised event called the Hunger Games. Every citizen must watch as the youths fight to the death until only one remains. District 12 Tribute Katniss Everdeen (Jennifer Lawrence) has little to rely on, other than her hunting skills and sharp instincts, in an arena where she must weigh survival against love.', 142, 'Action/Adventure', 'assets/images/posters/thehungergames.jpg', 'assets/images/hero/thehungergames_hero.jpg', 0),
(6, 'Star Wars: The Rise of Skywalker\r\n', 'When it\'s discovered that the evil Emperor Palpatine did not die at the hands of Darth Vader, the rebels must race against the clock to find out his whereabouts. Finn and Poe lead the Resistance to put a stop to the First Order\'s plans to form a new Empire, while Rey anticipates her inevitable confrontation with Kylo Ren.', 142, 'Sci-fi/Action', 'assets/images/posters/starwars.jpg', 'assets/images/hero/starwars_hero.jpg', 0),
(7, 'No Game No Life: Zero\r\n', 'Amid the chaos and destruction of the Ancient War, a young man leads humanity toward the peaceful tomorrow his heart believes in.', 107, 'Fantasy/Adventure', 'assets/images/posters/nogamenolifezero.jpg', 'assets/images/hero/nogamenolifezero_hero.jpg', 0),
(8, 'Demon Slayer: Kimetsu No Yaiba The Movie: Infinity Castle\r\n', 'Following the events at the Butterfly Mansion, Tanjiro Kamado, Zenitsu Agatsuma, and Inosuke Hashibira are assigned a new mission: to board the \"Mugen Train\" and investigate the mysterious disappearance of over 40 people. They are joined by Kyojuro Rengoku, the powerful Flame Hashira (one of the nine highest-ranking Demon Slayers), who is investigating a connection between the train and the Hinokami Kagura breathing technique used by Tanjiro\'s family. ', 155, 'Action/Adventure', 'assets/images/posters/demonslayermovie.jpg', 'assets/images/hero/demonslayermovie_hero.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `method` enum('GCASH','MAYA','CARD') COLLATE utf8mb4_unicode_ci NOT NULL,
  `details_masked` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('PAID','FAILED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PAID',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int NOT NULL,
  `movie_id` int NOT NULL,
  `branch_id` int NOT NULL,
  `show_datetime` datetime NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '250.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `movie_id`, `branch_id`, `show_datetime`, `price`) VALUES
(418, 8, 1, '2025-11-15 13:00:00', 250.00),
(642, 8, 1, '2025-11-15 16:00:00', 250.00),
(643, 8, 2, '2025-11-15 13:00:00', 250.00),
(644, 8, 2, '2025-11-15 16:00:00', 250.00),
(647, 8, 3, '2025-11-15 12:00:00', 275.00),
(648, 8, 3, '2025-11-15 18:00:00', 275.00),
(649, 7, 1, '2025-11-15 13:00:00', 250.00),
(650, 7, 1, '2025-11-15 16:00:00', 250.00),
(651, 7, 2, '2025-11-15 13:00:00', 250.00),
(652, 7, 2, '2025-11-15 16:00:00', 250.00),
(654, 7, 3, '2025-11-15 12:00:00', 275.00),
(655, 7, 3, '2025-11-15 18:00:00', 275.00),
(656, 6, 1, '2025-11-15 13:00:00', 250.00),
(657, 6, 1, '2025-11-15 16:00:00', 250.00),
(658, 6, 2, '2025-11-15 13:00:00', 250.00),
(659, 6, 2, '2025-11-15 16:00:00', 250.00),
(661, 6, 3, '2025-11-15 12:00:00', 275.00),
(662, 6, 3, '2025-11-15 18:00:00', 275.00),
(670, 5, 1, '2025-11-15 13:00:00', 250.00),
(671, 5, 1, '2025-11-15 16:00:00', 250.00),
(672, 5, 2, '2025-11-15 13:00:00', 250.00),
(673, 5, 2, '2025-11-15 16:00:00', 250.00),
(674, 5, 3, '2025-11-15 12:00:00', 275.00),
(675, 5, 3, '2025-11-15 18:00:00', 275.00),
(676, 4, 1, '2025-11-15 13:00:00', 250.00),
(677, 4, 1, '2025-11-15 16:00:00', 250.00),
(678, 4, 2, '2025-11-15 13:00:00', 250.00),
(679, 4, 2, '2025-11-15 16:00:00', 250.00),
(680, 4, 3, '2025-11-15 12:00:00', 275.00),
(681, 4, 3, '2025-11-15 18:00:00', 275.00),
(682, 3, 1, '2025-11-15 13:00:00', 250.00),
(683, 3, 1, '2025-11-15 16:00:00', 250.00),
(684, 3, 2, '2025-11-15 13:00:00', 250.00),
(685, 3, 2, '2025-11-15 16:00:00', 250.00),
(686, 3, 3, '2025-11-15 12:00:00', 275.00),
(687, 3, 3, '2025-11-15 18:00:00', 275.00),
(688, 2, 1, '2025-11-15 13:00:00', 250.00),
(689, 2, 1, '2025-11-15 16:00:00', 250.00),
(690, 2, 2, '2025-11-15 13:00:00', 250.00),
(691, 2, 2, '2025-11-15 16:00:00', 250.00),
(692, 2, 3, '2025-11-15 12:00:00', 275.00),
(693, 2, 3, '2025-11-15 18:00:00', 275.00),
(694, 1, 1, '2025-11-15 13:00:00', 250.00),
(695, 1, 1, '2025-11-15 16:00:00', 250.00),
(696, 1, 2, '2025-11-15 13:00:00', 250.00),
(697, 1, 2, '2025-11-15 16:00:00', 250.00),
(698, 1, 3, '2025-11-15 12:00:00', 275.00),
(699, 1, 3, '2025-11-15 18:00:00', 275.00);

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `id` int NOT NULL,
  `label` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `row_char` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `col_number` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`id`, `label`, `row_char`, `col_number`) VALUES
(1, 'A1', 'A', 1),
(2, 'A2', 'A', 2),
(3, 'A3', 'A', 3),
(4, 'A4', 'A', 4),
(5, 'A5', 'A', 5),
(6, 'A6', 'A', 6),
(7, 'A7', 'A', 7),
(8, 'A8', 'A', 8),
(9, 'A9', 'A', 9),
(10, 'A10', 'A', 10),
(11, 'A11', 'A', 11),
(12, 'A12', 'A', 12),
(13, 'B1', 'B', 1),
(14, 'B2', 'B', 2),
(15, 'B3', 'B', 3),
(16, 'B4', 'B', 4),
(17, 'B5', 'B', 5),
(18, 'B6', 'B', 6),
(19, 'B7', 'B', 7),
(20, 'B8', 'B', 8),
(21, 'B9', 'B', 9),
(22, 'B10', 'B', 10),
(23, 'B11', 'B', 11),
(24, 'B12', 'B', 12),
(25, 'C1', 'C', 1),
(26, 'C2', 'C', 2),
(27, 'C3', 'C', 3),
(28, 'C4', 'C', 4),
(29, 'C5', 'C', 5),
(30, 'C6', 'C', 6),
(31, 'C7', 'C', 7),
(32, 'C8', 'C', 8),
(33, 'C9', 'C', 9),
(34, 'C10', 'C', 10),
(35, 'C11', 'C', 11),
(36, 'C12', 'C', 12),
(37, 'D1', 'D', 1),
(38, 'D2', 'D', 2),
(39, 'D3', 'D', 3),
(40, 'D4', 'D', 4),
(41, 'D5', 'D', 5),
(42, 'D6', 'D', 6),
(43, 'D7', 'D', 7),
(44, 'D8', 'D', 8),
(45, 'D9', 'D', 9),
(46, 'D10', 'D', 10),
(47, 'D11', 'D', 11),
(48, 'D12', 'D', 12),
(49, 'E1', 'E', 1),
(50, 'E2', 'E', 2),
(51, 'E3', 'E', 3),
(52, 'E4', 'E', 4),
(53, 'E5', 'E', 5),
(54, 'E6', 'E', 6),
(55, 'E7', 'E', 7),
(56, 'E8', 'E', 8),
(57, 'E9', 'E', 9),
(58, 'E10', 'E', 10),
(59, 'E11', 'E', 11),
(60, 'E12', 'E', 12),
(61, 'F1', 'F', 1),
(62, 'F2', 'F', 2),
(63, 'F3', 'F', 3),
(64, 'F4', 'F', 4),
(65, 'F5', 'F', 5),
(66, 'F6', 'F', 6),
(67, 'F7', 'F', 7),
(68, 'F8', 'F', 8),
(69, 'F9', 'F', 9),
(70, 'F10', 'F', 10),
(71, 'F11', 'F', 11),
(72, 'F12', 'F', 12),
(73, 'G1', 'G', 1),
(74, 'G2', 'G', 2),
(75, 'G3', 'G', 3),
(76, 'G4', 'G', 4),
(77, 'G5', 'G', 5),
(78, 'G6', 'G', 6),
(79, 'G7', 'G', 7),
(80, 'G8', 'G', 8),
(81, 'G9', 'G', 9),
(82, 'G10', 'G', 10),
(83, 'G11', 'G', 11),
(84, 'G12', 'G', 12),
(85, 'H1', 'H', 1),
(86, 'H2', 'H', 2),
(87, 'H3', 'H', 3),
(88, 'H4', 'H', 4),
(89, 'H5', 'H', 5),
(90, 'H6', 'H', 6),
(91, 'H7', 'H', 7),
(92, 'H8', 'H', 8),
(93, 'H9', 'H', 9),
(94, 'H10', 'H', 10),
(95, 'H11', 'H', 11),
(96, 'H12', 'H', 12);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `created_at`) VALUES
(4, 'Gerald Tapel', 'geraldctapel@gmail.com', '$2y$10$/VNacGRXhQ6HMhzRqP0ehu9P0.FQIuUUXO8GqIRAzJdoph9Ig.AF6', '2025-11-10 06:36:23'),
(5, 'Jathniel Gerolaga', 'therealcytus@gmail.com', '$2y$10$Ru6SwmEYaI8Rn00qCmM/HeFmAw3/uY3ZoSXVDcsk.ZAbAr0s6rbK6', '2025-11-10 06:38:11'),
(6, 'Lorenz Josh Silva', 'silva10lorenz@gmail.com', '$2y$10$.yhZPtCOQoqyZJnufi8zNO63IVpg.CE04pSM2wCaupF338nNXQW7.', '2025-11-10 06:38:33'),
(7, 'Rica Sola', 'ricasola@gmail.com', '$2y$10$C.ha42TBMrGyotzAyasK2eeVa35YUs2sZ1yzLRu5UPzdb63.peBGK', '2025-11-10 06:38:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD PRIMARY KEY (`booking_id`,`seat_id`),
  ADD KEY `seat_id` (`seat_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `show_datetime` (`show_datetime`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `label` (`label`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=700;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD CONSTRAINT `booking_seats_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_seats_ibfk_2` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
