-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 20, 2011 at 04:06 PM
-- Server version: 5.1.51
-- PHP Version: 5.3.5-pl0-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: 'dev'
--

-- --------------------------------------------------------

--
-- Table structure for table 'images'
--

DROP TABLE IF EXISTS images;
CREATE TABLE IF NOT EXISTS images (
  iid int(11) NOT NULL AUTO_INCREMENT,
  gid int(11) NOT NULL,
  mime varchar(25) CHARACTER SET utf8 NOT NULL,
  x smallint(5) unsigned NOT NULL,
  y smallint(5) unsigned NOT NULL,
  label varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  description text CHARACTER SET utf8 NOT NULL,
  micro blob,
  small blob,
  `full` mediumblob NOT NULL,
  seen int(11) NOT NULL,
  lastseen datetime NOT NULL,
  PRIMARY KEY (iid)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

