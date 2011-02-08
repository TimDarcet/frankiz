DROP TABLE IF EXISTS castes;
CREATE TABLE IF NOT EXISTS castes (
  cid int(11) NOT NULL AUTO_INCREMENT,
  `group` int(11) NOT NULL,
  rights varchar(30) NOT NULL,
  userfilter text,
  PRIMARY KEY (cid),
  UNIQUE KEY gid_rights (`group`,rights)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS castes_dependencies;
CREATE TABLE IF NOT EXISTS castes_dependencies (
  cid int(11) NOT NULL,
  `type` varchar(30) NOT NULL,
  id int(11) NOT NULL,
  UNIQUE KEY cid (cid,`type`,id),
  KEY id (id),
  KEY cid_2 (cid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
