-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 30, 2011 at 12:35 PM
-- Server version: 5.1.51
-- PHP Version: 5.3.5-pl0-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: 'dev'
--

-- --------------------------------------------------------

--
-- Table structure for table 'castes'
--

DROP TABLE IF EXISTS castes;
CREATE TABLE IF NOT EXISTS castes (
  cid int(11) NOT NULL AUTO_INCREMENT,
  `group` int(11) NOT NULL,
  rights varchar(30) NOT NULL,
  userfilter text,
  PRIMARY KEY (cid),
  UNIQUE KEY gid_rights (`group`,rights)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
