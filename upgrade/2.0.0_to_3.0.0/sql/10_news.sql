DROP TABLE IF EXISTS news;
CREATE TABLE IF NOT EXISTS news (
  id int(11) NOT NULL AUTO_INCREMENT,
  writer int(11) NOT NULL,
  target int(11) NOT NULL,
  image int(11) DEFAULT NULL,
  origin int(11) DEFAULT NULL,
  title tinytext NOT NULL,
  content text NOT NULL,
  `begin` datetime NOT NULL,
  `end` datetime NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS news_read;
CREATE TABLE IF NOT EXISTS news_read (
  uid int(11) NOT NULL,
  news int(11) NOT NULL,
  `time` datetime NOT NULL,
  UNIQUE KEY uid (uid,news)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS news_star;
CREATE TABLE IF NOT EXISTS news_star (
  uid int(11) NOT NULL,
  news int(11) NOT NULL,
  `time` datetime NOT NULL,
  UNIQUE KEY uid (uid,news)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
