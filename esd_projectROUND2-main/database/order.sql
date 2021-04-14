-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 13, 2021 at 02:17 AM
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
-- Database: `order`
--
CREATE DATABASE IF NOT EXISTS  `order` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `order`;
-- --------------------------------------------------------

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `oid` int(11) NOT NULL AUTO_INCREMENT,
  `group_oid` int(11) NOT NULL,
  `pid` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `cid` int(11) DEFAULT NULL,
  `datetime` timestamp NULL DEFAULT NULL,
  `oStatus` int(11) DEFAULT NULL,
  `dStatus` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pResponse` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`oid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`oid`, `group_oid`, `pid`, `quantity`, `cid`, `datetime`, `oStatus`, `dStatus`, `pResponse`) VALUES
(1, 1, 2, 1, 1, '2021-04-07 07:32:39', 0, 'Unfulfilled', '1'),
(3, 1, 3, 2, 1, '2021-04-07 07:32:39', 0, 'Unfulfilled', '1'),
(4, 1, 3, 2, 1, '2021-04-07 07:32:39', 0, 'Unfulfilled', '1'),
(5, 2, 12, 2, 1, '2021-04-08 09:50:57', 0, 'Unfulfilled', '1'),
(6, 3, 12, 2, 1, '2021-04-08 09:50:57', 0, 'Fulfilled', '1');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
