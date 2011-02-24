-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 11, 2011 at 06:42 PM
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
-- Table structure for table `brs_profiles`
--

DROP TABLE IF EXISTS `brs_profiles`;
CREATE TABLE IF NOT EXISTS `brs_profiles` (
  `uid` int(11) NOT NULL,
  `sig` tinytext NOT NULL,
  `tree_unread` tinytext NOT NULL,
  `tree_read` tinytext NOT NULL,
  `last_seen` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `flags` set('threads','automaj') CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `brs_subs`
--

DROP TABLE IF EXISTS `brs_subs`;
CREATE TABLE IF NOT EXISTS `brs_subs` (
  `uid` int(11) NOT NULL,
  `forum` tinytext NOT NULL COMMENT 'name of the forum subscribed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `laf`
--

DROP TABLE IF EXISTS `laf`;
CREATE TABLE IF NOT EXISTS `laf` (
  `oid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `lost` datetime DEFAULT NULL,
  `found` datetime DEFAULT NULL,
  `description` tinytext NOT NULL,
  `context` tinytext NOT NULL,
  PRIMARY KEY (`oid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `poly`
--

DROP TABLE IF EXISTS `poly`;
CREATE TABLE IF NOT EXISTS `poly` (
  `uid` int(11) NOT NULL,
  `poly` varchar(200) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `prises`
--

DROP TABLE IF EXISTS `prises`;
CREATE TABLE IF NOT EXISTS `prises` (
  `prise_id` varchar(10) NOT NULL DEFAULT '',
  `piece_id` varchar(7) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `type` enum('principale','secondaire') NOT NULL DEFAULT 'secondaire'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

DROP TABLE IF EXISTS `requests`;
CREATE TABLE IF NOT EXISTS `requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` tinytext NOT NULL,
  `data` text NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `requests_answers`
--

DROP TABLE IF EXISTS `requests_answers`;
CREATE TABLE IF NOT EXISTS `requests_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` enum('essai') NOT NULL,
  `title` varchar(50) NOT NULL,
  `answer` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE IF NOT EXISTS `rooms` (
  `rid` varchar(100) CHARACTER SET utf8 NOT NULL,
  `phone` varchar(4) CHARACTER SET utf8 NOT NULL,
  `comment` varchar(200) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rooms_essai`
--

DROP TABLE IF EXISTS `rooms_essai`;
CREATE TABLE IF NOT EXISTS `rooms_essai` (
  `rid` varchar(100) CHARACTER SET utf8 NOT NULL,
  `phone` varchar(4) CHARACTER SET utf8 NOT NULL,
  `comment` varchar(200) CHARACTER SET utf8 NOT NULL,
  `building` tinytext NOT NULL,
  `coord_left` int(11) NOT NULL,
  `coord_top` int(11) NOT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rooms_groups`
--

DROP TABLE IF EXISTS `rooms_groups`;
CREATE TABLE IF NOT EXISTS `rooms_groups` (
  `rid` varchar(250) NOT NULL,
  `gid` int(11) NOT NULL,
  UNIQUE KEY `gid` (`gid`,`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rooms_users`
--

DROP TABLE IF EXISTS `rooms_users`;
CREATE TABLE IF NOT EXISTS `rooms_users` (
  `rid` varchar(250) NOT NULL,
  `uid` int(11) NOT NULL,
  UNIQUE KEY `uid` (`uid`,`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `todo`
--

DROP TABLE IF EXISTS `todo`;
CREATE TABLE IF NOT EXISTS `todo` (
  `todo_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `sent` datetime NOT NULL,
  `checked` tinyint(4) NOT NULL,
  `tobedone` varchar(255) NOT NULL,
  PRIMARY KEY (`todo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `trombino_master`
--

DROP TABLE IF EXISTS `trombino_master`;
CREATE TABLE IF NOT EXISTS `trombino_master` (
  `uid` int(10) NOT NULL,
  `departement_id` int(10) NOT NULL,
  `section_id` int(10) NOT NULL,
  KEY `eleve_id` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `trombino_x`
--

DROP TABLE IF EXISTS `trombino_x`;
CREATE TABLE IF NOT EXISTS `trombino_x` (
  `uid` int(10) NOT NULL,
  `section_id` int(10) NOT NULL,
  `compagnie` int(10) NOT NULL,
  KEY `eleve_id` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_comments`
--

DROP TABLE IF EXISTS `users_comments`;
CREATE TABLE IF NOT EXISTS `users_comments` (
  `uid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `comment` tinytext NOT NULL,
  UNIQUE KEY `uid` (`uid`,`gid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_minimodules`
--

DROP TABLE IF EXISTS `users_minimodules`;
CREATE TABLE IF NOT EXISTS `users_minimodules` (
  `uid` int(11) NOT NULL,
  `name` varchar(150) CHARACTER SET utf8 NOT NULL,
  `col` enum('COL_LEFT','COL_MIDDLE','COL_RIGHT','COL_FLOAT') NOT NULL DEFAULT 'COL_FLOAT',
  `row` tinyint(4) NOT NULL,
  UNIQUE KEY `uid` (`uid`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wiki`
--

DROP TABLE IF EXISTS `wiki`;
CREATE TABLE IF NOT EXISTS `wiki` (
  `wid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`wid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wiki_version`
--

DROP TABLE IF EXISTS `wiki_version`;
CREATE TABLE IF NOT EXISTS `wiki_version` (
  `wid` int(11) NOT NULL,
  `version` int(11) NOT NULL,
  `wrote` datetime NOT NULL,
  `writer` int(11) NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
