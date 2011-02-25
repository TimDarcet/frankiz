--
-- Table structure for table 'log_actions'
--

DROP TABLE IF EXISTS log_actions;
CREATE TABLE IF NOT EXISTS log_actions (
  id int(11) NOT NULL AUTO_INCREMENT,
  `text` tinytext NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'log_events'
--

DROP TABLE IF EXISTS log_events;
CREATE TABLE IF NOT EXISTS log_events (
  `session` int(11) NOT NULL,
  `action` int(11) NOT NULL,
  stamps timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'log_last_sessions'
--

DROP TABLE IF EXISTS log_last_sessions;
CREATE TABLE IF NOT EXISTS log_last_sessions (
  uid int(11) NOT NULL,
  id int(11) NOT NULL,
  PRIMARY KEY (uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

INSERT INTO log_actions (id, `text`) VALUES(1, 'suid_start');
INSERT INTO log_actions (id, `text`) VALUES(2, 'auth_ok');
INSERT INTO log_actions (id, `text`) VALUES(3, 'auth_fail');
INSERT INTO log_actions (id, `text`) VALUES(10, 'groups/insert');
INSERT INTO log_actions (id, `text`) VALUES(11, 'groups/admin');
INSERT INTO log_actions (id, `text`) VALUES(12, 'groups/admin/rights');
INSERT INTO log_actions (id, `text`) VALUES(13, 'admin/su');
INSERT INTO log_actions (id, `text`) VALUES(14, 'admin/validate');

