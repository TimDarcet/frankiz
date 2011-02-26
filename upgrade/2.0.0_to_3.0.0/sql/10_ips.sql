
DROP TABLE IF EXISTS ips;
CREATE TABLE IF NOT EXISTS ips (
  ip varchar(15) NOT NULL,
  plug varchar(10) NOT NULL,
  rid varchar(100) NOT NULL,
  `comment` varchar(200) NOT NULL,
  KEY rid (rid),
  KEY ip (ip)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

