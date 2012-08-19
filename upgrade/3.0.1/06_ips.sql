--
-- Rename room field in ips table
--
ALTER TABLE `ips` CHANGE `rid` `room` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
