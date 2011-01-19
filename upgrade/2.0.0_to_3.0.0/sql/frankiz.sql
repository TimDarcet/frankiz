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
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
CREATE TABLE IF NOT EXISTS `activities` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `target` int(11) NOT NULL,
  `origin` int(11) DEFAULT NULL,
  `title` tinytext NOT NULL,
  `description` text NOT NULL,
  `days` set('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL COMMENT 'for use with strtotime',
  `default_begin` time DEFAULT NULL,
  `default_end` time DEFAULT NULL,
  `priv` tinyint(1) NOT NULL,
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `activities_instances`
--

DROP TABLE IF EXISTS `activities_instances`;
CREATE TABLE IF NOT EXISTS `activities_instances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) NOT NULL,
  `writer` int(11) NOT NULL,
  `comment` text NOT NULL,
  `begin` datetime NOT NULL,
  `end` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `activities_participants`
--

DROP TABLE IF EXISTS `activities_participants`;
CREATE TABLE IF NOT EXISTS `activities_participants` (
  `id` int(11) NOT NULL,
  `participant` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`,`participant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `castes`
--

DROP TABLE IF EXISTS `castes`;
CREATE TABLE IF NOT EXISTS `castes` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL,
  `rights` varchar(30) NOT NULL,
  `userfilter` text,
  PRIMARY KEY (`cid`),
  UNIQUE KEY `gid_rights` (`gid`,`rights`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `castes_dependencies`
--

DROP TABLE IF EXISTS `castes_dependencies`;
CREATE TABLE IF NOT EXISTS `castes_dependencies` (
  `cid` int(11) NOT NULL,
  `type` varchar(30) NOT NULL,
  `id` int(11) NOT NULL,
  UNIQUE KEY `cid` (`cid`,`type`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `castes_users`
--

DROP TABLE IF EXISTS `castes_users`;
CREATE TABLE IF NOT EXISTS `castes_users` (
  `cid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  UNIQUE KEY `cid` (`cid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `ns` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `external` tinyint(1) NOT NULL,
  `priv` tinyint(1) NOT NULL,
  `leavable` tinyint(1) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `image` int(11) NOT NULL,
  `label` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `score` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `web` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `mail` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`gid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=121 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

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

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `writer` int(11) NOT NULL,
  `target` int(11) NOT NULL,
  `iid` int(11) NOT NULL,
  `origin` int(11) DEFAULT NULL COMMENT 'original group',
  `title` tinytext NOT NULL,
  `content` text NOT NULL,
  `begin` date NOT NULL,
  `end` date NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=46 ;

-- --------------------------------------------------------

--
-- Table structure for table `news_read`
--

DROP TABLE IF EXISTS `news_read`;
CREATE TABLE IF NOT EXISTS `news_read` (
  `uid` int(11) NOT NULL,
  `news` int(11) NOT NULL,
  `time` datetime NOT NULL,
  UNIQUE KEY `uid` (`uid`,`news`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `news_star`
--

DROP TABLE IF EXISTS `news_star`;
CREATE TABLE IF NOT EXISTS `news_star` (
  `uid` int(11) NOT NULL,
  `news` int(11) NOT NULL,
  `time` datetime NOT NULL,
  UNIQUE KEY `uid` (`uid`,`news`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `promos_on_platal`
--

DROP TABLE IF EXISTS `promos_on_platal`;
CREATE TABLE IF NOT EXISTS `promos_on_platal` (
  `formation_id` int(11) NOT NULL,
  `promo` int(11) NOT NULL,
  `on_platal` tinyint(1) NOT NULL,
  UNIQUE KEY `formation_id` (`formation_id`,`promo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `qdj`
--

DROP TABLE IF EXISTS `qdj`;
CREATE TABLE IF NOT EXISTS `qdj` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `question` tinytext NOT NULL,
  `answer1` tinytext NOT NULL,
  `answer2` tinytext NOT NULL,
  `count1` int(10) unsigned NOT NULL DEFAULT '0',
  `count2` int(10) unsigned NOT NULL DEFAULT '0',
  `writer` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1952 ;

-- --------------------------------------------------------

--
-- Table structure for table `qdj_scores`
--

DROP TABLE IF EXISTS `qdj_scores`;
CREATE TABLE IF NOT EXISTS `qdj_scores` (
  `uid` int(11) NOT NULL,
  `total` decimal(8,2) NOT NULL,
  `bonus` decimal(8,2) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `qdj_votes`
--

DROP TABLE IF EXISTS `qdj_votes`;
CREATE TABLE IF NOT EXISTS `qdj_votes` (
  `vote_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `qdj` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `rank` smallint(6) NOT NULL,
  `rule` int(11) NOT NULL,
  PRIMARY KEY (`vote_id`),
  UNIQUE KEY `qdj` (`qdj`,`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
-- Table structure for table `skins`
--

DROP TABLE IF EXISTS `skins`;
CREATE TABLE IF NOT EXISTS `skins` (
  `name` varchar(40) CHARACTER SET utf8 NOT NULL,
  `label` varchar(100) CHARACTER SET utf8 NOT NULL,
  `description` tinytext CHARACTER SET utf8 NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Stores informations about CSS versions';

-- --------------------------------------------------------

--
-- Table structure for table `studies`
--

DROP TABLE IF EXISTS `studies`;
CREATE TABLE IF NOT EXISTS `studies` (
  `uid` int(10) NOT NULL,
  `formation_id` int(10) NOT NULL,
  `year_in` year(4) NOT NULL,
  `year_out` year(4) NOT NULL,
  `promo` year(4) NOT NULL,
  `forlife` varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=198 ;

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
  `name` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `col` enum('COL_LEFT','COL_MIDDLE','COL_RIGHT','COL_FLOAT') NOT NULL DEFAULT 'COL_FLOAT',
  `row` tinyint(4) NOT NULL,
  UNIQUE KEY `uid` (`uid`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `validate`
--

DROP TABLE IF EXISTS `validate`;
CREATE TABLE IF NOT EXISTS `validate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `type` tinytext NOT NULL,
  `item` text NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `wiki`
--

DROP TABLE IF EXISTS `wiki`;
CREATE TABLE IF NOT EXISTS `wiki` (
  `wid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `comments` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`wid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=25 ;

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
  `content` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
