-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 13, 2021 at 02:05 AM
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
-- Database: `business`
--
CREATE DATABASE IF NOT EXISTS  `business` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `business`;
-- --------------------------------------------------------

--
-- Table structure for table `business`
--

DROP TABLE IF EXISTS `business`;
CREATE TABLE IF NOT EXISTS `business` (
  `bid` int(11) NOT NULL AUTO_INCREMENT,
  `bname` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `paypal` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bdescription` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`bid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business`
--

INSERT INTO `business` (`bid`, `bname`, `email`, `password`, `paypal`, `address`, `bdescription`) VALUES
(1, 'Shopee', 'shopee@abc.com', '7dd29a9c9643fd524e1b4360964b89ce59914e68d1fd1ab04dd61fbaaabc58e579dcffb5b7454ab01e586c8ae98e538b5d6e0ff3ae7dd442de7333486dc9df1a', 'shopee@adc.com', 'Blk 124 SMU Avenue 1', 'business desc'),
(2, 'Scentopia', 'scent@def.com', '7dd29a9c9643fd524e1b4360964b89ce59914e68d1fd1ab04dd61fbaaabc58e579dcffb5b7454ab01e586c8ae98e538b5d6e0ff3ae7dd442de7333486dc9df1a', 'scent@def.com', 'Blk 300 SMU Avenue 1', 'We sell handmade soap and scented candles made with natual ingredients.'),
(4, 'clowncircus', 'clowncircus@def.com', '7dd29a9c9643fd524e1b4360964b89ce59914e68d1fd1ab04dd61fbaaabc58e579dcffb5b7454ab01e586c8ae98e538b5d6e0ff3ae7dd442de7333486dc9df1a', 'clowncircus@def.com', '2162  Cedarstone Drive', 'Clown suits'),
(6, 'Ayataka', 'ayataka@cola.com', 'cf6df2bd3eabe3445aa9e63e1ad311dc554c6e219f6e221d87ef40b5cfde8e5975fe446131422e60461ac7e8db47889266bcffe8b76f6f733ea98429b5b4925d', 'ayataka@cola1.com', 'ayataka@cola.com', 'ayataka@cola.com');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
