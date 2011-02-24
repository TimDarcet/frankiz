-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 19, 2011 at 08:35 PM
-- Server version: 5.1.51
-- PHP Version: 5.3.5-pl0-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: 'dev'
--

-- --------------------------------------------------------

--
-- Table structure for table 'formations'
--

DROP TABLE IF EXISTS formations;
CREATE TABLE IF NOT EXISTS formations (
  formation_id int(10) NOT NULL AUTO_INCREMENT,
  domain varchar(255) NOT NULL,
  label varchar(64) NOT NULL,
  abbrev varchar(10) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY (formation_id),
  KEY domain (domain)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table 'formations'
--

INSERT INTO formations (formation_id, domain, `label`, abbrev, description) VALUES
(1, 'polytechnique.edu', 'Polytechnicien', 'x', ''),
(2, 'poly.polytechnique.fr', 'Anciens comptes', 'poly', ''),
(3, 'polytechnique.edu', 'Master de l''X', 'master', ''),
(4, 'polytechnique.edu', 'Doctorants de l''X', 'doc', ''),
(5, 'polytechnique.edu', 'PEI', 'pei', ''),
(7, 'frankiz.net', 'Frankiz', 'fkz', '');

