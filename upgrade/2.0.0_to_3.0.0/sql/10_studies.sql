DROP TABLE IF EXISTS studies;
CREATE TABLE IF NOT EXISTS studies (
  uid int(10) NOT NULL,
  formation_id int(10) NOT NULL,
  year_in year(4) NOT NULL,
  year_out year(4) NOT NULL,
  promo year(4) NOT NULL,
  forlife varchar(64) NOT NULL,
  UNIQUE KEY forlife_formation (forlife,formation_id),
  KEY forlife (forlife),
  KEY uid (uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
