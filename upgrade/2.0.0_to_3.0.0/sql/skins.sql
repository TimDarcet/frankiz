-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 11, 2011 at 06:41 PM
-- Server version: 5.1.51
-- PHP Version: 5.3.4-pl0-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `frankiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `skins`
--

DROP TABLE IF EXISTS `skins`;
CREATE TABLE IF NOT EXISTS `skins` (
  `name` varchar(40) CHARACTER SET utf8 NOT NULL,
  `label` varchar(100) CHARACTER SET utf8 NOT NULL,
  `description` tinytext CHARACTER SET utf8 NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Stores informations about CSS versions';

--
-- Dumping data for table `skins`
--

INSERT INTO `skins` (`name`, `label`, `description`, `date`) VALUES
('default', 'Défaut', 'La skin par défaut de Frankiz3', '2008-08-10'),
('default.mobile', 'Smartphone', 'Cette skin permet d''accéder rapidement et facilement au trombinoscope sur votre téléphone mobile', '2008-08-16');
