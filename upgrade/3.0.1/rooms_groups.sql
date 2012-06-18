--
-- Table structure for table `rooms_groups`
--

DROP TABLE IF EXISTS `rooms_groups`;
CREATE TABLE IF NOT EXISTS `rooms_groups` (
  `rid` varchar(250) NOT NULL,
  `gid` int(11) NOT NULL,
  `open` tinyint(1) NOT NULL,
  UNIQUE KEY `gid` (`gid`,`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
