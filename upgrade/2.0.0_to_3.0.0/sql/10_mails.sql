
DROP TABLE IF EXISTS mails;
CREATE TABLE IF NOT EXISTS mails (
  id int(11) NOT NULL AUTO_INCREMENT,
  processed datetime DEFAULT NULL,
  done datetime DEFAULT NULL,
  target tinytext NOT NULL,
  writer tinytext NOT NULL,
  writername tinytext NOT NULL,
  title tinytext NOT NULL,
  body text NOT NULL,
  ishtml tinyint(1) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
