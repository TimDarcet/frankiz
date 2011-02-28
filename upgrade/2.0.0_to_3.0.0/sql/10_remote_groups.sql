DROP TABLE IF EXISTS remote_groups;
CREATE TABLE IF NOT EXISTS remote_groups (
  remote_id int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  KEY remote_id (remote_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
