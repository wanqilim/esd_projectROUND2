-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 13, 2021 at 02:04 AM
-- Server version: 8.0.18
-- PHP Version: 7.4.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `customer`
--
CREATE DATABASE IF NOT EXISTS  `customer` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `customer`;
-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
CREATE TABLE IF NOT EXISTS `customer` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `cname` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `paypal` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`cid`, `cname`, `email`, `password`, `paypal`, `address`) VALUES
(1, 'Amy Tan', 'amy@abc.com', '7dd29a9c9643fd524e1b4360964b89ce59914e68d1fd1ab04dd61fbaaabc58e579dcffb5b7454ab01e586c8ae98e538b5d6e0ff3ae7dd442de7333486dc9df1a', 'amy@abc.com', 'Blk 123 SMU Avenue 1'),
(2, 'Tommy Tan', 'tom@abc.com', '7dd29a9c9643fd524e1b4360964b89ce59914e68d1fd1ab04dd61fbaaabc58e579dcffb5b7454ab01e586c8ae98e538b5d6e0ff3ae7dd442de7333486dc9df1a', 'tom@abc.com', 'Blk 123 SMU Avenue 1'),
(3, 'Wanqi', 'wanqilim99@gmail.com', '7dd29a9c9643fd524e1b4360964b89ce59914e68d1fd1ab04dd61fbaaabc58e579dcffb5b7454ab01e586c8ae98e538b5d6e0ff3ae7dd442de7333486dc9df1a', 'wanqilim99@gmail.com', '1758  Stroop Hill Road'),
(4, 'Clown', 'wanqilim99@hotmail.com', '7dd29a9c9643fd524e1b4360964b89ce59914e68d1fd1ab04dd61fbaaabc58e579dcffb5b7454ab01e586c8ae98e538b5d6e0ff3ae7dd442de7333486dc9df1a', 'wanqilim99@business.example.com', '161 Lavender St #02-07, 338750, Singapore'),
(5, NULL, 'amy@abc.com', '7dd29a9c9643fd524e1b4360964b89ce59914e68d1fd1ab04dd61fbaaabc58e579dcffb5b7454ab01e586c8ae98e538b5d6e0ff3ae7dd442de7333486dc9df1a', NULL, NULL),
(6, 'fish', 'fish@abc.com', 'cf6df2bd3eabe3445aa9e63e1ad311dc554c6e219f6e221d87ef40b5cfde8e5975fe446131422e60461ac7e8db47889266bcffe8b76f6f733ea98429b5b4925d', NULL, 'fish@abc.com'),
(7, 'fish2', 'fish2@abc.com', 'cf6df2bd3eabe3445aa9e63e1ad311dc554c6e219f6e221d87ef40b5cfde8e5975fe446131422e60461ac7e8db47889266bcffe8b76f6f733ea98429b5b4925d', NULL, 'fish@abc.com'),
(8, 'cat', 'cat@abc.com', 'cf6df2bd3eabe3445aa9e63e1ad311dc554c6e219f6e221d87ef40b5cfde8e5975fe446131422e60461ac7e8db47889266bcffe8b76f6f733ea98429b5b4925d', 'cat@abc.com', 'cat@abc.com');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
