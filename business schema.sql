-- business
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
CREATE DATABASE IF NOT EXISTS  `business` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `business`;

CREATE TABLE business (
BID INT  primary key AUTO_INCREMENT,
BName VARCHAR (128),
Email VARCHAR (128),
Password VARCHAR (128),
Paypal VARCHAR (128),
BAddress VARCHAR (128),
BDescription VARCHAR (128)
);