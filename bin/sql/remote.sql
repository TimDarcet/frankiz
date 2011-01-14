-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 14, 2011 at 05:01 PM
-- Server version: 5.1.51
-- PHP Version: 5.3.4-pl0-gentoo

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
-- Table structure for table 'remote'
--

DROP TABLE IF EXISTS remote;
CREATE TABLE IF NOT EXISTS remote (
  id int(11) NOT NULL AUTO_INCREMENT,
  site varchar(250) NOT NULL,
  privkey varchar(200) NOT NULL,
  rights varchar(250) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table 'remote_groups'
--

DROP TABLE IF EXISTS remote_groups;
CREATE TABLE IF NOT EXISTS remote_groups (
  remote_id int(11) NOT NULL,
  `name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
