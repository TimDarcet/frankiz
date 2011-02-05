DROP TABLE IF EXISTS validate;
CREATE TABLE IF NOT EXISTS validate (
  id int(11) NOT NULL AUTO_INCREMENT,
  writer int(11) NOT NULL,
  `group` int(11) NOT NULL,
  `type` tinytext NOT NULL,
  item text NOT NULL,
  created datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

