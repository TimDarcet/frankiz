-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 26, 2011 at 01:03 AM
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
-- Table structure for table 'surveys'
--

DROP TABLE IF EXISTS surveys;
CREATE TABLE IF NOT EXISTS surveys (
  sid int(11) NOT NULL AUTO_INCREMENT,
  writer int(11) NOT NULL,
  origin int(11) NOT NULL,
  target int(11) NOT NULL,
  title varchar(250) NOT NULL,
  description text NOT NULL,
  `begin` datetime NOT NULL,
  `end` datetime NOT NULL,
  anonymous tinyint(1) NOT NULL,
  PRIMARY KEY (sid)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'surveys_answers'
--

DROP TABLE IF EXISTS surveys_answers;
CREATE TABLE IF NOT EXISTS surveys_answers (
  ssid varchar(23) NOT NULL,
  qid int(11) NOT NULL,
  datas text NOT NULL,
  UNIQUE KEY ssid (ssid,qid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'surveys_questions'
--

DROP TABLE IF EXISTS surveys_questions;
CREATE TABLE IF NOT EXISTS surveys_questions (
  qid int(11) NOT NULL AUTO_INCREMENT,
  survey int(11) NOT NULL,
  rank int(11) NOT NULL,
  label text NOT NULL,
  description text,
  mandatory tinyint(1) NOT NULL,
  `type` varchar(50) NOT NULL,
  datas text NOT NULL,
  PRIMARY KEY (qid),
  KEY sid (survey)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'surveys_sessions'
--

DROP TABLE IF EXISTS surveys_sessions;
CREATE TABLE IF NOT EXISTS surveys_sessions (
  uid int(11) NOT NULL,
  sid int(11) NOT NULL,
  ssid varchar(23) NOT NULL,
  UNIQUE KEY uid_sid (uid,sid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
