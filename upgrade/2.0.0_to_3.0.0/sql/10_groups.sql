DROP TABLE IF EXISTS groups;
CREATE TABLE IF NOT EXISTS groups (
  gid int(11) NOT NULL AUTO_INCREMENT,
  ns varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  external tinyint(1) NOT NULL,
  leavable tinyint(1) NOT NULL DEFAULT 1,
  visible tinyint(1) NOT NULL,
  image int(11) DEFAULT NULL,
  label varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  score int(11) NOT NULL,
  description text COLLATE utf8_unicode_ci NOT NULL,
  web tinytext COLLATE utf8_unicode_ci NOT NULL,
  mail varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (gid),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
