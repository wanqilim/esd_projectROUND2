SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `book`
--
CREATE DATABASE IF NOT EXISTS  `order` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `order`;

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
    `oid` INT(11) NOT NULL AUTO_INCREMENT,
    `pid` INT  ,
    `quantity` INT,
    `cid` INT,
    `datetime` TIMESTAMP,
    `oStatus` INT,
    `dStatus` VARCHAR (128),
    `pResponse` VARCHAR (128),
    PRIMARY KEY (`oid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 


