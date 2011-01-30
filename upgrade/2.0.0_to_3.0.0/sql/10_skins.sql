-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 25, 2011 at 11:08 PM
-- Server version: 5.1.51
-- PHP Version: 5.3.5-pl0-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: 'dev'
--

-- --------------------------------------------------------

--
-- Table structure for table 'skins'
--

DROP TABLE IF EXISTS skins;
CREATE TABLE IF NOT EXISTS skins (
  `name` varchar(40) CHARACTER SET utf8 NOT NULL,
  label varchar(100) CHARACTER SET utf8 NOT NULL,
  description tinytext CHARACTER SET utf8 NOT NULL,
  `date` date NOT NULL,
  visibility tinyint(1) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table 'skins'
--

INSERT INTO skins (`name`, `label`, description, `date`, visibility) VALUES('default', 'Défaut', 'La skin par défaut de Frankiz3', '2008-08-10', 1);
INSERT INTO skins (`name`, `label`, description, `date`, visibility) VALUES('default.mobile', 'Smartphone', 'Cette skin permet d''accéder rapidement et facilement au trombinoscope sur votre téléphone mobile', '2008-08-16', 0);

