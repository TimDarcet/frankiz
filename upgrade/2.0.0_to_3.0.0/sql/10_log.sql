--
-- Table structure for table 'log_actions'
--

DROP TABLE IF EXISTS log_actions;
CREATE TABLE IF NOT EXISTS log_actions (
  id int(11) NOT NULL AUTO_INCREMENT,
  `text` tinytext NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'log_events'
--

DROP TABLE IF EXISTS log_events;
CREATE TABLE IF NOT EXISTS log_events (
  `session` int(11) NOT NULL,
  `action` int(11) NOT NULL,
  `data` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'log_last_session'
--

DROP TABLE IF EXISTS log_last_session;
CREATE TABLE IF NOT EXISTS log_last_session (
  uid int(11) NOT NULL,
  id int(11) NOT NULL,
  PRIMARY KEY (uid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'log_sessions'
--

DROP TABLE IF EXISTS log_sessions;
CREATE TABLE IF NOT EXISTS log_sessions (
  id int(11) NOT NULL AUTO_INCREMENT,
  uid int(11) NOT NULL,
  `host` tinytext NOT NULL,
  ip tinytext NOT NULL,
  forward_ip tinytext,
  forward_host tinytext,
  browser tinytext NOT NULL,
  suid int(11) DEFAULT NULL,
  flags set('proxy') NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
