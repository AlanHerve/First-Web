-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 27, 2023 at 06:27 PM
-- Server version: 5.7.11
-- PHP Version: 5.6.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hobbysharedatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `ID` int(11) NOT NULL,
  `CONTENT` text,
  `POST` int(11) NOT NULL,
  `OWNERID` int(11) NOT NULL,
  `TIME` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `OWNER_NAME` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`ID`, `CONTENT`, `POST`, `OWNERID`, `TIME`, `OWNER_NAME`) VALUES
(15, 'Oh my god, there was a DLC, so good as well !', 1, 1, '2023-04-26 11:33:32', 'User1'),
(16, 'If you really like these kind of games, play TOEM', 1, 2, '2023-04-26 11:33:39', 'Hobby'),
(17, 'hey', 2, 1, '2023-04-26 12:24:44', 'User1'),
(18, 'if someone wants to cook with me let me know', 2, 1, '2023-04-26 13:03:28', 'User1');

-- --------------------------------------------------------

--
-- Table structure for table `hobby_list`
--

CREATE TABLE `hobby_list` (
  `ID` int(11) NOT NULL,
  `HOBBY_NAME` varchar(30) NOT NULL,
  `IMAGE` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hobby_list`
--

INSERT INTO `hobby_list` (`ID`, `HOBBY_NAME`, `IMAGE`) VALUES
(1, 'Pottery', 'Pottery.jpg'),
(2, 'Crochet', 'Crochet.jpg'),
(3, 'Drawing', 'Drawing.PNG'),
(4, 'Guitar', 'Guitar.png'),
(5, 'Hiking', 'Hiking.png'),
(6, 'Soccer', 'Football2.PNG'),
(7, 'Cooking', 'Cooking.jpg'),
(8, 'Sculpture', 'Sculpture.jpg'),
(9, 'Board Games', 'Jeux.jpg'),
(10, 'Photo', 'Photo.jpg'),
(11, 'Painting', 'Painting.jpg'),
(12, 'Biking', 'Biking.jpg'),
(13, 'Video Games', 'videogame.jpg'),
(14, 'Climbing', 'Climbing.jpg'),
(15, 'Geocache', 'Geocache.jpg'),
(16, 'Sowing', 'Sowing.PNG'),
(17, 'Embroidery', 'Embroidery.jpg'),
(18, 'Scrapbooking', 'Scrapbooking.jpg'),
(19, 'Reading', 'Reading.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `hobby_post`
--

CREATE TABLE `hobby_post` (
  `ID` int(11) NOT NULL,
  `HOBBY_NAME` varchar(30) NOT NULL,
  `EXPERIENCE` varchar(30) DEFAULT 'Débutant',
  `FREQUENCY` varchar(30) DEFAULT NULL,
  `AVAILABLE` tinyint(1) DEFAULT '0',
  `IMAGE` varchar(60) DEFAULT NULL,
  `OWNER` int(11) NOT NULL,
  `DESCRIPTION` text,
  `TYPEID` int(11) DEFAULT NULL,
  `TIME` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `MODIFIED` int(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hobby_post`
--

INSERT INTO `hobby_post` (`ID`, `HOBBY_NAME`, `EXPERIENCE`, `FREQUENCY`, `AVAILABLE`, `IMAGE`, `OWNER`, `DESCRIPTION`, `TYPEID`, `TIME`, `MODIFIED`) VALUES
(1, 'Cooking', 'Beginner', 'Daily', 1, NULL, 1, 'I enjoy cooking quite a lot and would like to share it with someone', 7, '2023-04-26 13:24:48', 1),
(2, 'Video Games', 'Beginner', 'Beginner', 1, NULL, 1, 'When I do not have time to cook, I play video Games', 13, '2023-04-26 11:09:13', 0),
(3, 'Pottery', 'Advanced', 'Rarely', 0, NULL, 2, '', 1, '2023-04-26 13:54:06', 1),
(4, 'Crochet', 'Beginner', 'Beginner', 1, NULL, 2, '', 2, '2023-04-26 11:19:37', 0),
(5, 'Drawing', 'Beginner', 'Beginner', 1, NULL, 2, '', 3, '2023-04-26 11:19:39', 0),
(6, 'Guitar', 'Beginner', 'Beginner', 1, NULL, 2, '', 4, '2023-04-26 11:19:42', 0),
(7, 'Hiking', 'Beginner', 'Beginner', 1, NULL, 2, '', 5, '2023-04-26 11:19:45', 0),
(8, 'Soccer', 'Beginner', 'Beginner', 1, NULL, 2, '', 6, '2023-04-26 11:19:48', 0),
(9, 'Cooking', 'Beginner', 'Beginner', 1, NULL, 2, '', 7, '2023-04-26 11:19:50', 0),
(10, 'Sculpture', 'Beginner', 'Beginner', 1, NULL, 2, '', 8, '2023-04-26 11:19:53', 0),
(11, 'Board Games', 'Beginner', 'Beginner', 1, NULL, 2, '', 9, '2023-04-26 11:19:56', 0),
(12, 'Photo', 'Beginner', 'Beginner', 1, NULL, 2, '', 10, '2023-04-26 11:19:59', 0),
(13, 'Painting', 'Beginner', 'Beginner', 1, NULL, 2, '', 11, '2023-04-26 11:20:01', 0),
(14, 'Biking', 'Beginner', 'Beginner', 1, NULL, 2, '', 12, '2023-04-26 11:20:05', 0),
(15, 'Video Games', 'Beginner', 'Beginner', 1, NULL, 2, '', 13, '2023-04-26 11:20:08', 0),
(16, 'Climbing', 'Beginner', 'Beginner', 1, NULL, 2, '', 14, '2023-04-26 11:20:11', 0),
(17, 'Geocache', 'Beginner', 'Beginner', 1, NULL, 2, '', 15, '2023-04-26 11:20:15', 0),
(18, 'Couture', 'Beginner', 'Beginner', 1, NULL, 2, '', 16, '2023-04-26 11:20:18', 0),
(19, 'Embroidery', 'Beginner', 'Beginner', 1, NULL, 2, '', 17, '2023-04-26 11:20:22', 0),
(20, 'Scrapbooking', 'Beginner', 'Beginner', 1, NULL, 2, '', 18, '2023-04-26 11:20:25', 0),
(21, 'Reading', 'Beginner', 'Beginner', 1, NULL, 2, '', 19, '2023-04-26 11:20:28', 0),
(22, 'Pottery', 'Beginner', 'Beginner', 1, NULL, 1, '', 1, '2023-04-27 18:14:31', 0);

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `POST_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`ID`, `USER_ID`, `POST_ID`) VALUES
(1, 1, 1),
(2, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `ID` int(11) NOT NULL,
  `OWNER1` int(11) NOT NULL,
  `OWNER2` int(11) NOT NULL,
  `CONTENT` text,
  `TIME` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`ID`, `OWNER1`, `OWNER2`, `CONTENT`, `TIME`) VALUES
(1, 1, 2, 'hi, I\'ve seen what you said about TOEM', '2023-04-26 11:25:35'),
(2, 1, 2, 'what kind of game is it ?', '2023-04-26 11:25:42'),
(3, 2, 1, 'oh just a nice little exploration game', '2023-04-26 13:50:52'),
(4, 2, 1, 'you have to take photos of nature', '2023-04-26 13:51:02'),
(5, 2, 1, 'and solve simple puzzles', '2023-04-26 13:51:15');

-- --------------------------------------------------------

--
-- Table structure for table `regular_post`
--

CREATE TABLE `regular_post` (
  `ID` int(11) NOT NULL,
  `HOBBY_NAME` varchar(30) NOT NULL,
  `DESCRIPTION` text,
  `IMAGE1` varchar(60) DEFAULT NULL,
  `IMAGE2` varchar(60) DEFAULT NULL,
  `IMAGE3` varchar(60) DEFAULT NULL,
  `IMAGE4` varchar(60) DEFAULT NULL,
  `OWNER` int(11) NOT NULL,
  `TYPEID` int(11) DEFAULT NULL,
  `MODIFIED` int(1) DEFAULT '0',
  `LIKES` int(11) DEFAULT '0',
  `TIME` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `regular_post`
--

INSERT INTO `regular_post` (`ID`, `HOBBY_NAME`, `DESCRIPTION`, `IMAGE1`, `IMAGE2`, `IMAGE3`, `IMAGE4`, `OWNER`, `TYPEID`, `MODIFIED`, `LIKES`, `TIME`) VALUES
(1, 'Video Games', 'Hey guys, I just played one of the best games of all times : Outer Wilds !', 'outer-wilds-echoes-of-the-eye.jpg', NULL, NULL, NULL, 1, 13, 0, 2, '2023-04-26 11:22:19'),
(2, 'Cooking', 'This description was modified via the edit page', NULL, NULL, NULL, NULL, 1, 7, 1, 0, '2023-04-27 18:19:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `NAME` varchar(30) NOT NULL,
  `NICKNAME` varchar(30) DEFAULT NULL,
  `PASSWORD` varchar(60) NOT NULL,
  `EMAIL` varchar(60) NOT NULL,
  `AVATAR` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `NAME`, `NICKNAME`, `PASSWORD`, `EMAIL`, `AVATAR`) VALUES
(1, 'User1', 'First', '7fb55ed0b7a30342ba6da306428cae04', 'first@gmail.com', NULL),
(2, 'Hobby 2.0', 'Conoisseur of hobbies', '80aa111ef6400d42f3d1b30036396404', 'hobby@gmail.com', 'Drawing.PNG');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `hobby_list`
--
ALTER TABLE `hobby_list`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `hobby_post`
--
ALTER TABLE `hobby_post`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `regular_post`
--
ALTER TABLE `regular_post`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `hobby_list`
--
ALTER TABLE `hobby_list`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `hobby_post`
--
ALTER TABLE `hobby_post`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `regular_post`
--
ALTER TABLE `regular_post`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
