-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 16, 2013 at 01:07 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `capitalis_ld28`
--

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE IF NOT EXISTS `city` (
  `city_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `city_name` text NOT NULL,
  `planet_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`city_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`city_id`, `city_name`, `planet_id`) VALUES
(1, 'Ruby City', 1),
(2, 'Emerald City', 1),
(3, 'Sapphire City', 1);

-- --------------------------------------------------------

--
-- Table structure for table `floor`
--

CREATE TABLE IF NOT EXISTS `floor` (
  `floor_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `plot_id` bigint(20) unsigned NOT NULL,
  `surface_y` int(11) NOT NULL DEFAULT '0',
  `floor_type_key` char(1) NOT NULL,
  `capacity` smallint(5) unsigned NOT NULL DEFAULT '3',
  `rent` int(11) NOT NULL DEFAULT '0',
  `wages` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`floor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;



-- --------------------------------------------------------

--
-- Table structure for table `person`
--

CREATE TABLE IF NOT EXISTS `person` (
  `person_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `player_id` bigint(20) unsigned DEFAULT NULL,
  `home_floor_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `work_floor_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `current_location_type` varchar(20) NOT NULL DEFAULT 'plot',
  `current_location_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `action` varchar(20) NOT NULL DEFAULT 'wait',
  `destination_location_type` varchar(20) NOT NULL DEFAULT 'plot',
  `destination_location_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `gender_key` char(1) NOT NULL DEFAULT 'M',
  `coins` int(11) NOT NULL DEFAULT '0',
  `schedule_offset` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`person_id`),
  UNIQUE KEY `player_id` (`player_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;



-- --------------------------------------------------------

--
-- Table structure for table `planet`
--

CREATE TABLE IF NOT EXISTS `planet` (
  `planet_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `planet_name` varchar(100) NOT NULL,
  `tick` bigint(20) unsigned NOT NULL DEFAULT '0',
  `last_tick_datetime` datetime NOT NULL,
  PRIMARY KEY (`planet_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `planet`
--

INSERT INTO `planet` (`planet_id`, `planet_name`, `tick`, `last_tick_datetime`) VALUES
(1, 'Capitalis', 1, '2013-12-16 01:05:34');

-- --------------------------------------------------------

--
-- Table structure for table `player`
--

CREATE TABLE IF NOT EXISTS `player` (
  `player_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `last_login_datetime` datetime NOT NULL,
  PRIMARY KEY (`player_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;



-- --------------------------------------------------------

--
-- Table structure for table `plot`
--

CREATE TABLE IF NOT EXISTS `plot` (
  `plot_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `planet_id` bigint(20) unsigned NOT NULL,
  `surface_x` int(11) NOT NULL,
  `surface_width` int(10) unsigned NOT NULL,
  `city_id` bigint(20) unsigned DEFAULT NULL,
  `owner_person_id` bigint(20) unsigned DEFAULT NULL,
  `founder_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`plot_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=301 ;

--
-- Dumping data for table `plot`
--

INSERT INTO `plot` (`plot_id`, `planet_id`, `surface_x`, `surface_width`, `city_id`, `owner_person_id`, `founder_name`) VALUES
(1, 1, 0, 2, 1, NULL, NULL),
(2, 1, 1, 2, 1, NULL, NULL),
(3, 1, 2, 2, 1, NULL, NULL),
(4, 1, 3, 2, 1, NULL, NULL),
(5, 1, 4, 2, 1, NULL, NULL),
(6, 1, 5, 2, 1, NULL, NULL),
(7, 1, 6, 2, 1, NULL, NULL),
(8, 1, 7, 2, 1, NULL, NULL),
(9, 1, 8, 2, 1, NULL, NULL),
(10, 1, 9, 2, 1, NULL, NULL),
(11, 1, 10, 2, 1, NULL, NULL),
(12, 1, 11, 2, 1, NULL, NULL),
(13, 1, 12, 2, 1, NULL, NULL),
(14, 1, 13, 2, 1, NULL, NULL),
(15, 1, 14, 2, 1, NULL, NULL),
(16, 1, 15, 2, 1, NULL, NULL),
(17, 1, 16, 2, 1, NULL, NULL),
(18, 1, 17, 2, 1, NULL, NULL),
(19, 1, 18, 2, 1, NULL, NULL),
(20, 1, 19, 2, 1, NULL, NULL),
(21, 1, 20, 2, 1, NULL, NULL),
(22, 1, 21, 2, 1, NULL, NULL),
(23, 1, 22, 2, 1, NULL, NULL),
(24, 1, 23, 2, 1, NULL, NULL),
(25, 1, 24, 2, 1, NULL, NULL),
(26, 1, 25, 2, 1, NULL, NULL),
(27, 1, 26, 2, 1, NULL, NULL),
(28, 1, 27, 2, 1, NULL, NULL),
(29, 1, 28, 2, 1, NULL, NULL),
(30, 1, 29, 2, 1, NULL, NULL),
(31, 1, 30, 2, 1, NULL, NULL),
(32, 1, 31, 2, 1, NULL, NULL),
(33, 1, 32, 2, 1, NULL, NULL),
(34, 1, 33, 2, 1, NULL, NULL),
(35, 1, 34, 2, 1, NULL, NULL),
(36, 1, 35, 2, 1, NULL, NULL),
(37, 1, 36, 2, 1, NULL, NULL),
(38, 1, 37, 2, 1, NULL, NULL),
(39, 1, 38, 2, 1, NULL, NULL),
(40, 1, 39, 2, 1, NULL, NULL),
(41, 1, 40, 2, 1, NULL, NULL),
(42, 1, 41, 2, 1, NULL, NULL),
(43, 1, 42, 2, 1, NULL, NULL),
(44, 1, 43, 2, 1, NULL, NULL),
(45, 1, 44, 2, 1, NULL, NULL),
(46, 1, 45, 2, 1, NULL, NULL),
(47, 1, 46, 2, 1, NULL, NULL),
(48, 1, 47, 2, 1, NULL, NULL),
(49, 1, 48, 2, 1, NULL, NULL),
(50, 1, 49, 2, 1, NULL, NULL),
(51, 1, 50, 2, 1, NULL, NULL),
(52, 1, 51, 2, 1, NULL, NULL),
(53, 1, 52, 2, 1, NULL, NULL),
(54, 1, 53, 2, 1, NULL, NULL),
(55, 1, 54, 2, 1, NULL, NULL),
(56, 1, 55, 2, 1, NULL, NULL),
(57, 1, 56, 2, 1, NULL, NULL),
(58, 1, 57, 2, 1, NULL, NULL),
(59, 1, 58, 2, 1, NULL, NULL),
(60, 1, 59, 2, 1, NULL, NULL),
(61, 1, 60, 2, 1, NULL, NULL),
(62, 1, 61, 2, 1, NULL, NULL),
(63, 1, 62, 2, 1, NULL, NULL),
(64, 1, 63, 2, 1, NULL, NULL),
(65, 1, 64, 2, 1, NULL, NULL),
(66, 1, 65, 2, 1, NULL, NULL),
(67, 1, 66, 2, 1, NULL, NULL),
(68, 1, 67, 2, 1, NULL, NULL),
(69, 1, 68, 2, 1, NULL, NULL),
(70, 1, 69, 2, 1, NULL, NULL),
(71, 1, 70, 2, 1, NULL, NULL),
(72, 1, 71, 2, 1, NULL, NULL),
(73, 1, 72, 2, 1, NULL, NULL),
(74, 1, 73, 2, 1, NULL, NULL),
(75, 1, 74, 2, 1, NULL, NULL),
(76, 1, 75, 2, 1, NULL, NULL),
(77, 1, 76, 2, 1, NULL, NULL),
(78, 1, 77, 2, 1, NULL, NULL),
(79, 1, 78, 2, 1, NULL, NULL),
(80, 1, 79, 2, 1, NULL, NULL),
(81, 1, 80, 2, 1, NULL, NULL),
(82, 1, 81, 2, 1, NULL, NULL),
(83, 1, 82, 2, 1, NULL, NULL),
(84, 1, 83, 2, 1, NULL, NULL),
(85, 1, 84, 2, 1, NULL, NULL),
(86, 1, 85, 2, 1, NULL, NULL),
(87, 1, 86, 2, 1, NULL, NULL),
(88, 1, 87, 2, 1, NULL, NULL),
(89, 1, 88, 2, 1, NULL, NULL),
(90, 1, 89, 2, 1, NULL, NULL),
(91, 1, 90, 2, 1, NULL, NULL),
(92, 1, 91, 2, 1, NULL, NULL),
(93, 1, 92, 2, 1, NULL, NULL),
(94, 1, 93, 2, 1, NULL, NULL),
(95, 1, 94, 2, 1, NULL, NULL),
(96, 1, 95, 2, 1, NULL, NULL),
(97, 1, 96, 2, 1, NULL, NULL),
(98, 1, 97, 2, 1, NULL, NULL),
(99, 1, 98, 2, 1, NULL, NULL),
(100, 1, 99, 2, 1, NULL, NULL),
(101, 1, 100, 2, 2, NULL, NULL),
(102, 1, 101, 2, 2, NULL, NULL),
(103, 1, 102, 2, 2, NULL, NULL),
(104, 1, 103, 2, 2, NULL, NULL),
(105, 1, 104, 2, 2, NULL, NULL),
(106, 1, 105, 2, 2, NULL, NULL),
(107, 1, 106, 2, 2, NULL, NULL),
(108, 1, 107, 2, 2, NULL, NULL),
(109, 1, 108, 2, 2, NULL, NULL),
(110, 1, 109, 2, 2, NULL, NULL),
(111, 1, 110, 2, 2, NULL, NULL),
(112, 1, 111, 2, 2, NULL, NULL),
(113, 1, 112, 2, 2, NULL, NULL),
(114, 1, 113, 2, 2, NULL, NULL),
(115, 1, 114, 2, 2, NULL, NULL),
(116, 1, 115, 2, 2, NULL, NULL),
(117, 1, 116, 2, 2, NULL, NULL),
(118, 1, 117, 2, 2, NULL, NULL),
(119, 1, 118, 2, 2, NULL, NULL),
(120, 1, 119, 2, 2, NULL, NULL),
(121, 1, 120, 2, 2, NULL, NULL),
(122, 1, 121, 2, 2, NULL, NULL),
(123, 1, 122, 2, 2, NULL, NULL),
(124, 1, 123, 2, 2, NULL, NULL),
(125, 1, 124, 2, 2, NULL, NULL),
(126, 1, 125, 2, 2, NULL, NULL),
(127, 1, 126, 2, 2, NULL, NULL),
(128, 1, 127, 2, 2, NULL, NULL),
(129, 1, 128, 2, 2, NULL, NULL),
(130, 1, 129, 2, 2, NULL, NULL),
(131, 1, 130, 2, 2, NULL, NULL),
(132, 1, 131, 2, 2, NULL, NULL),
(133, 1, 132, 2, 2, NULL, NULL),
(134, 1, 133, 2, 2, NULL, NULL),
(135, 1, 134, 2, 2, NULL, NULL),
(136, 1, 135, 2, 2, NULL, NULL),
(137, 1, 136, 2, 2, NULL, NULL),
(138, 1, 137, 2, 2, NULL, NULL),
(139, 1, 138, 2, 2, NULL, NULL),
(140, 1, 139, 2, 2, NULL, NULL),
(141, 1, 140, 2, 2, NULL, NULL),
(142, 1, 141, 2, 2, NULL, NULL),
(143, 1, 142, 2, 2, NULL, NULL),
(144, 1, 143, 2, 2, NULL, NULL),
(145, 1, 144, 2, 2, NULL, NULL),
(146, 1, 145, 2, 2, NULL, NULL),
(147, 1, 146, 2, 2, NULL, NULL),
(148, 1, 147, 2, 2, NULL, NULL),
(149, 1, 148, 2, 2, NULL, NULL),
(150, 1, 149, 2, 2, NULL, NULL),
(151, 1, 150, 2, 2, NULL, NULL),
(152, 1, 151, 2, 2, NULL, NULL),
(153, 1, 152, 2, 2, NULL, NULL),
(154, 1, 153, 2, 2, NULL, NULL),
(155, 1, 154, 2, 2, NULL, NULL),
(156, 1, 155, 2, 2, NULL, NULL),
(157, 1, 156, 2, 2, NULL, NULL),
(158, 1, 157, 2, 2, NULL, NULL),
(159, 1, 158, 2, 2, NULL, NULL),
(160, 1, 159, 2, 2, NULL, NULL),
(161, 1, 160, 2, 2, NULL, NULL),
(162, 1, 161, 2, 2, NULL, NULL),
(163, 1, 162, 2, 2, NULL, NULL),
(164, 1, 163, 2, 2, NULL, NULL),
(165, 1, 164, 2, 2, NULL, NULL),
(166, 1, 165, 2, 2, NULL, NULL),
(167, 1, 166, 2, 2, NULL, NULL),
(168, 1, 167, 2, 2, NULL, NULL),
(169, 1, 168, 2, 2, NULL, NULL),
(170, 1, 169, 2, 2, NULL, NULL),
(171, 1, 170, 2, 2, NULL, NULL),
(172, 1, 171, 2, 2, NULL, NULL),
(173, 1, 172, 2, 2, NULL, NULL),
(174, 1, 173, 2, 2, NULL, NULL),
(175, 1, 174, 2, 2, NULL, NULL),
(176, 1, 175, 2, 2, NULL, NULL),
(177, 1, 176, 2, 2, NULL, NULL),
(178, 1, 177, 2, 2, NULL, NULL),
(179, 1, 178, 2, 2, NULL, NULL),
(180, 1, 179, 2, 2, NULL, NULL),
(181, 1, 180, 2, 2, NULL, NULL),
(182, 1, 181, 2, 2, NULL, NULL),
(183, 1, 182, 2, 2, NULL, NULL),
(184, 1, 183, 2, 2, NULL, NULL),
(185, 1, 184, 2, 2, NULL, NULL),
(186, 1, 185, 2, 2, NULL, NULL),
(187, 1, 186, 2, 2, NULL, NULL),
(188, 1, 187, 2, 2, NULL, NULL),
(189, 1, 188, 2, 2, NULL, NULL),
(190, 1, 189, 2, 2, NULL, NULL),
(191, 1, 190, 2, 2, NULL, NULL),
(192, 1, 191, 2, 2, NULL, NULL),
(193, 1, 192, 2, 2, NULL, NULL),
(194, 1, 193, 2, 2, NULL, NULL),
(195, 1, 194, 2, 2, NULL, NULL),
(196, 1, 195, 2, 2, NULL, NULL),
(197, 1, 196, 2, 2, NULL, NULL),
(198, 1, 197, 2, 2, NULL, NULL),
(199, 1, 198, 2, 2, NULL, NULL),
(200, 1, 199, 2, 2, NULL, NULL),
(201, 1, 200, 2, 3, NULL, NULL),
(202, 1, 201, 2, 3, NULL, NULL),
(203, 1, 202, 2, 3, NULL, NULL),
(204, 1, 203, 2, 3, NULL, NULL),
(205, 1, 204, 2, 3, NULL, NULL),
(206, 1, 205, 2, 3, NULL, NULL),
(207, 1, 206, 2, 3, NULL, NULL),
(208, 1, 207, 2, 3, NULL, NULL),
(209, 1, 208, 2, 3, NULL, NULL),
(210, 1, 209, 2, 3, NULL, NULL),
(211, 1, 210, 2, 3, NULL, NULL),
(212, 1, 211, 2, 3, NULL, NULL),
(213, 1, 212, 2, 3, NULL, NULL),
(214, 1, 213, 2, 3, NULL, NULL),
(215, 1, 214, 2, 3, NULL, NULL),
(216, 1, 215, 2, 3, NULL, NULL),
(217, 1, 216, 2, 3, NULL, NULL),
(218, 1, 217, 2, 3, NULL, NULL),
(219, 1, 218, 2, 3, NULL, NULL),
(220, 1, 219, 2, 3, NULL, NULL),
(221, 1, 220, 2, 3, NULL, NULL),
(222, 1, 221, 2, 3, NULL, NULL),
(223, 1, 222, 2, 3, NULL, NULL),
(224, 1, 223, 2, 3, NULL, NULL),
(225, 1, 224, 2, 3, NULL, NULL),
(226, 1, 225, 2, 3, NULL, NULL),
(227, 1, 226, 2, 3, NULL, NULL),
(228, 1, 227, 2, 3, NULL, NULL),
(229, 1, 228, 2, 3, NULL, NULL),
(230, 1, 229, 2, 3, NULL, NULL),
(231, 1, 230, 2, 3, NULL, NULL),
(232, 1, 231, 2, 3, NULL, NULL),
(233, 1, 232, 2, 3, NULL, NULL),
(234, 1, 233, 2, 3, NULL, NULL),
(235, 1, 234, 2, 3, NULL, NULL),
(236, 1, 235, 2, 3, NULL, NULL),
(237, 1, 236, 2, 3, NULL, NULL),
(238, 1, 237, 2, 3, NULL, NULL),
(239, 1, 238, 2, 3, NULL, NULL),
(240, 1, 239, 2, 3, NULL, NULL),
(241, 1, 240, 2, 3, NULL, NULL),
(242, 1, 241, 2, 3, NULL, NULL),
(243, 1, 242, 2, 3, NULL, NULL),
(244, 1, 243, 2, 3, NULL, NULL),
(245, 1, 244, 2, 3, NULL, NULL),
(246, 1, 245, 2, 3, NULL, NULL),
(247, 1, 246, 2, 3, NULL, NULL),
(248, 1, 247, 2, 3, NULL, NULL),
(249, 1, 248, 2, 3, NULL, NULL),
(250, 1, 249, 2, 3, NULL, NULL),
(251, 1, 250, 2, 3, NULL, NULL),
(252, 1, 251, 2, 3, NULL, NULL),
(253, 1, 252, 2, 3, NULL, NULL),
(254, 1, 253, 2, 3, NULL, NULL),
(255, 1, 254, 2, 3, NULL, NULL),
(256, 1, 255, 2, 3, NULL, NULL),
(257, 1, 256, 2, 3, NULL, NULL),
(258, 1, 257, 2, 3, NULL, NULL),
(259, 1, 258, 2, 3, NULL, NULL),
(260, 1, 259, 2, 3, NULL, NULL),
(261, 1, 260, 2, 3, NULL, NULL),
(262, 1, 261, 2, 3, NULL, NULL),
(263, 1, 262, 2, 3, NULL, NULL),
(264, 1, 263, 2, 3, NULL, NULL),
(265, 1, 264, 2, 3, NULL, NULL),
(266, 1, 265, 2, 3, NULL, NULL),
(267, 1, 266, 2, 3, NULL, NULL),
(268, 1, 267, 2, 3, NULL, NULL),
(269, 1, 268, 2, 3, NULL, NULL),
(270, 1, 269, 2, 3, NULL, NULL),
(271, 1, 270, 2, 3, NULL, NULL),
(272, 1, 271, 2, 3, NULL, NULL),
(273, 1, 272, 2, 3, NULL, NULL),
(274, 1, 273, 2, 3, NULL, NULL),
(275, 1, 274, 2, 3, NULL, NULL),
(276, 1, 275, 2, 3, NULL, NULL),
(277, 1, 276, 2, 3, NULL, NULL),
(278, 1, 277, 2, 3, NULL, NULL),
(279, 1, 278, 2, 3, NULL, NULL),
(280, 1, 279, 2, 3, NULL, NULL),
(281, 1, 280, 2, 3, NULL, NULL),
(282, 1, 281, 2, 3, NULL, NULL),
(283, 1, 282, 2, 3, NULL, NULL),
(284, 1, 283, 2, 3, NULL, NULL),
(285, 1, 284, 2, 3, NULL, NULL),
(286, 1, 285, 2, 3, NULL, NULL),
(287, 1, 286, 2, 3, NULL, NULL),
(288, 1, 287, 2, 3, NULL, NULL),
(289, 1, 288, 2, 3, NULL, NULL),
(290, 1, 289, 2, 3, NULL, NULL),
(291, 1, 290, 2, 3, NULL, NULL),
(292, 1, 291, 2, 3, NULL, NULL),
(293, 1, 292, 2, 3, NULL, NULL),
(294, 1, 293, 2, 3, NULL, NULL),
(295, 1, 294, 2, 3, NULL, NULL),
(296, 1, 295, 2, 3, NULL, NULL),
(297, 1, 296, 2, 3, NULL, NULL),
(298, 1, 297, 2, 3, NULL, NULL),
(299, 1, 298, 2, 3, NULL, NULL),
(300, 1, 299, 2, 3, NULL, NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
