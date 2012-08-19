--
-- Change id field name in remote table
--
ALTER TABLE `remote` CHANGE `id` `remid` INT( 11 ) NOT NULL AUTO_INCREMENT;

--
-- Change id field name in remote_groups table
--
DROP TABLE IF EXISTS `remote_groups`;
CREATE TABLE IF NOT EXISTS `remote_groups` (
  `remid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  KEY `remid` (`remid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
