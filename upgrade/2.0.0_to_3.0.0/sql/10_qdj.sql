-- --------------------------------------------------------

--
-- Table structure for table 'qdj'
--

DROP TABLE IF EXISTS qdj;
CREATE TABLE IF NOT EXISTS qdj (
  id int(10) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  question tinytext NOT NULL,
  answer1 tinytext NOT NULL,
  answer2 tinytext NOT NULL,
  count1 int(10) unsigned NOT NULL DEFAULT '0',
  count2 int(10) unsigned NOT NULL DEFAULT '0',
  writer int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'qdj_scores'
--

DROP TABLE IF EXISTS qdj_scores;
CREATE TABLE IF NOT EXISTS qdj_scores (
  uid int(11) NOT NULL,
  total decimal(8,2) NOT NULL,
  bonus decimal(8,2) NOT NULL,
  PRIMARY KEY (uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'qdj_votes'
--

DROP TABLE IF EXISTS qdj_votes;
CREATE TABLE IF NOT EXISTS qdj_votes (
  vote_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  qdj int(11) NOT NULL,
  uid int(11) NOT NULL,
  rank smallint(6) NOT NULL,
  rule int(11) NOT NULL,
  PRIMARY KEY (vote_id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
