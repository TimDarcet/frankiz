--
-- Key columns are not NULL
--
ALTER TABLE `groups` CHANGE `name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

--
-- PRIMARY key in tables ips and studies
--
ALTER TABLE `ips`
    DROP INDEX `ip`,
    ADD PRIMARY KEY (`ip`);
ALTER TABLE `studies` ADD PRIMARY KEY (`uid`, `formation_id`);

--
-- Some other unique keys
--
ALTER TABLE `formations` ADD UNIQUE `abbrev` (`abbrev`);
ALTER TABLE `remote` ADD UNIQUE `site` (`site`);
ALTER TABLE `wiki` ADD UNIQUE `name` (`name`);
