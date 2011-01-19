-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 11, 2011 at 06:09 PM
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
-- Table structure for table `minimodules`
--

DROP TABLE IF EXISTS `minimodules`;
CREATE TABLE IF NOT EXISTS `minimodules` (
  `name` varchar(100) NOT NULL,
  `label` varchar(200) DEFAULT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `minimodules`
--

INSERT INTO `minimodules` (`name`, `label`, `description`) VALUES
('activity', 'Activités du jour', ''),
('anniversaires', 'Anniversaires', 'Les anniversaires 42'),
('days', 'Fêtes', 'Les fêtes des saints (et des autres) blih'),
('groups', 'Mes groupes', 'Liste de ses groupes'),
('ik', 'IK de la semaine', ''),
('jtx', 'Video du jour', 'La video du jour par le jtx'),
('meteo', 'Météo', 'Météo du platal'),
('qdj', 'Question Du Jour', 'La question du jour permet blah blah blah'),
('quicksearch', 'TOL', 'bluh'),
('stats', 'Statistiques', 'Pour les admins'),
('timeleft', 'Temps restant', ''),
('todo', 'To-Do', 'blah blah');
