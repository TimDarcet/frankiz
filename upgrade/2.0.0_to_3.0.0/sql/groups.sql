-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 20, 2011 at 02:22 AM
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
-- Table structure for table 'groups'
--

DROP TABLE IF EXISTS groups;
CREATE TABLE groups (
  gid int(11) NOT NULL AUTO_INCREMENT,
  ns varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  external tinyint(1) NOT NULL,
  priv tinyint(1) NOT NULL,
  leavable tinyint(1) NOT NULL,
  visible tinyint(1) NOT NULL,
  image int(11) NOT NULL,
  label varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  score int(11) NOT NULL,
  description text COLLATE utf8_unicode_ci NOT NULL,
  web tinytext COLLATE utf8_unicode_ci NOT NULL,
  mail varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (gid),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
