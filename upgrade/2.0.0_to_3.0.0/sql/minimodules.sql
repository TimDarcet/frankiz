-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 28, 2011 at 01:41 PM
-- Server version: 5.1.51
-- PHP Version: 5.3.5-pl0-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: 'dev'
--

-- --------------------------------------------------------

--
-- Table structure for table 'minimodules'
--

DROP TABLE IF EXISTS minimodules;
CREATE TABLE IF NOT EXISTS minimodules (
  `name` varchar(100) NOT NULL,
  label varchar(200) DEFAULT NULL,
  description text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table 'minimodules'
--

INSERT INTO minimodules (`name`, `label`, description) VALUES('activity', 'Activités du jour', '');
INSERT INTO minimodules (`name`, `label`, description) VALUES('anniversaries', 'Anniversaires', 'Les anniversaires 42');
INSERT INTO minimodules (`name`, `label`, description) VALUES('days', 'Fêtes', 'Les fêtes des saints (et des autres) blih');
INSERT INTO minimodules (`name`, `label`, description) VALUES('groups', 'Mes groupes', 'Liste de ses groupes');
INSERT INTO minimodules (`name`, `label`, description) VALUES('ik', 'IK de la semaine', '');
INSERT INTO minimodules (`name`, `label`, description) VALUES('jtx', 'Video du jour', 'La video du jour par le jtx');
INSERT INTO minimodules (`name`, `label`, description) VALUES('links', 'Liens utiles', '');
INSERT INTO minimodules (`name`, `label`, description) VALUES('meteo', 'Météo', 'Météo du platal');
INSERT INTO minimodules (`name`, `label`, description) VALUES('qdj', 'Question Du Jour', 'La question du jour permet blah blah blah');
INSERT INTO minimodules (`name`, `label`, description) VALUES('quicksearch', 'TOL', 'bluh');
INSERT INTO minimodules (`name`, `label`, description) VALUES('stats', 'Statistiques', 'Pour les admins');
INSERT INTO minimodules (`name`, `label`, description) VALUES('timeleft', 'Temps restant', '');
INSERT INTO minimodules (`name`, `label`, description) VALUES('todo', 'To-Do', 'blah blah');
