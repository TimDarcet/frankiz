-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 01, 2011 at 03:00 AM
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
-- Table structure for table 'tol_errors'
--

DROP TABLE IF EXISTS tol_errors;
CREATE TABLE IF NOT EXISTS tol_errors (
  id int(11) NOT NULL,
  stamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  error mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
