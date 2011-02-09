-- --------------------------------------------------------

--
-- Table structure for table 'activities'
--

DROP TABLE IF EXISTS activities;
CREATE TABLE IF NOT EXISTS activities (
  aid int(11) NOT NULL AUTO_INCREMENT,
  target int(11) NOT NULL,
  origin int(11) DEFAULT NULL,
  title tinytext NOT NULL,
  description text NOT NULL,
  days set('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL COMMENT 'for use with strtotime',
  default_begin time DEFAULT NULL,
  default_end time DEFAULT NULL,
  PRIMARY KEY (aid)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'activities_instances'
--

DROP TABLE IF EXISTS activities_instances;
CREATE TABLE IF NOT EXISTS activities_instances (
  id int(11) NOT NULL AUTO_INCREMENT,
  activity int(11) NOT NULL,
  writer int(11) NOT NULL,
  `comment` text NOT NULL,
  `begin` datetime NOT NULL,
  `end` datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'activities_participants'
--

DROP TABLE IF EXISTS activities_participants;
CREATE TABLE IF NOT EXISTS activities_participants (
  id int(11) NOT NULL,
  participant int(11) NOT NULL,
  UNIQUE KEY id (id,participant)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
