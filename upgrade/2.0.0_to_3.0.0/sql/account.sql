-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 19, 2011 at 07:25 PM
-- Server version: 5.1.51
-- PHP Version: 5.3.5-pl0-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

DROP TABLE IF EXISTS account;
CREATE TABLE IF NOT EXISTS account (
  uid int(10) NOT NULL AUTO_INCREMENT,
  hruid varchar(255) CHARACTER SET ascii NOT NULL,
  `password` varchar(120) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  gid int(11) NOT NULL,
  perms enum('admin','web','support','kes','none') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
  state enum('active','pending','unregistered','disabled') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `hash` varchar(34) CHARACTER SET ascii NOT NULL,
  hashstamp datetime NOT NULL,
  hash_rss varchar(34) CHARACTER SET ascii NOT NULL,
  email_format enum('text','html') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  skin varchar(40) NOT NULL,
  firstname tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  lastname tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  nickname tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  email varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  gender enum('man','woman') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'man',
  birthdate date NOT NULL DEFAULT '0000-00-00',
  next_birthday date NOT NULL DEFAULT '0000-00-00',
  cellphone char(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  original int(11) NOT NULL DEFAULT '0',
  photo int(11) NOT NULL DEFAULT '0',
  `comment` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  on_platal tinyint(1) NOT NULL,
  PRIMARY KEY (uid),
  UNIQUE KEY hruid (hruid)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`uid`, `hruid`, `password`, `gid`, `perms`, `state`, `hash`, `hashstamp`, `hash_rss`, `email_format`, `skin`, `firstname`, `lastname`, `nickname`, `bestalias`, `gender`, `birthdate`, `next_birthday`, `cellphone`, `original`, `photo`, `comment`, `on_platal`) VALUES
(0, 'anonymous', '', 0, 'none', 'disabled', '', '0000-00-00 00:00:00', '', 'text', 'default', '', '', '', '', 'woman', '0000-00-00', '0000-00-00', '', 0, 0, 'Anonymous User', 0);
