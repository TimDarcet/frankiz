DROP TABLE IF EXISTS images;
CREATE TABLE IF NOT EXISTS images (
  iid int(11) NOT NULL AUTO_INCREMENT,
  caste int(11) NOT NULL,
  label varchar(200) DEFAULT NULL,
  seen int(11) NOT NULL,
  lastseen datetime NOT NULL,
  PRIMARY KEY (iid)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS images_sizes;
CREATE TABLE IF NOT EXISTS images_sizes (
  iid int(11) NOT NULL,
  size tinyint(4) NOT NULL,
  mime tinyint(4) NOT NULL,
  x smallint(6) NOT NULL,
  y smallint(6) NOT NULL,
  `data` longblob NOT NULL,
  UNIQUE KEY iid (iid,size)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
