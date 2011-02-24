DROP TABLE IF EXISTS msdnaa_keys;
CREATE TABLE IF NOT EXISTS msdnaa_keys (
  id int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(30) NOT NULL,
  software varchar(15) NOT NULL,
  uid int(11) DEFAULT NULL,
  admin tinyint(1) NOT NULL,
  comments tinytext,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
