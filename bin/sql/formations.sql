-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 11, 2011 at 07:31 PM
-- Server version: 5.1.51
-- PHP Version: 5.3.4-pl0-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dev_riton`
--

-- --------------------------------------------------------

--
-- Table structure for table `formations`
--

DROP TABLE IF EXISTS `formations`;
CREATE TABLE IF NOT EXISTS `formations` (
  `formation_id` int(10) NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `title` varchar(64) NOT NULL,
  `abbrev` varchar(10) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  PRIMARY KEY (`formation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `formations`
--

INSERT INTO `formations` (`formation_id`, `domain`, `title`, `abbrev`) VALUES
(0, 'frankiz.net', 'Frankiz', 'fkz'),
(1, 'polytechnique.edu', 'Polytechnique', 'x'),
(2, 'poly.polytechnique.fr', 'Poly', 'poly');
