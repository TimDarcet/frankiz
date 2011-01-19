-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 19, 2011 at 10:14 PM
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
-- Table structure for table 'images'
--

DROP TABLE IF EXISTS images;
CREATE TABLE images (
  iid int(11) NOT NULL AUTO_INCREMENT,
  gid int(11) NOT NULL,
  mime varchar(25) CHARACTER SET utf8 NOT NULL,
  x smallint(5) unsigned NOT NULL,
  y smallint(5) unsigned NOT NULL,
  label varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  description text CHARACTER SET utf8 NOT NULL,
  micro blob NOT NULL,
  small blob,
  `full` mediumblob NOT NULL,
  seen int(11) NOT NULL,
  lastseen datetime NOT NULL,
  PRIMARY KEY (iid)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;
