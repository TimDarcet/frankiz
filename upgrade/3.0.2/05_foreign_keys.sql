--
-- account, account.group may be null
--
ALTER TABLE `account` CHANGE `group` `group` INT(11) NULL;
UPDATE `account` SET `group` = NULL WHERE `group` = 0;
UPDATE `account` SET `original` = NULL WHERE `original` = 0;
UPDATE `account` SET `photo` = NULL WHERE `photo` = 0;
UPDATE `account` SET `skin` = 'default' WHERE `skin` = '';
ALTER TABLE `account`
    ADD CONSTRAINT `fk_account_group`
        FOREIGN KEY (`group`) REFERENCES `groups` (`gid`)
        ON DELETE NO ACTION ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_account_original`
        FOREIGN KEY (`original`) REFERENCES `images` (`iid`)
        ON DELETE NO ACTION ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_account_photo`
        FOREIGN KEY (`photo`) REFERENCES `images` (`iid`)
        ON DELETE NO ACTION ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_account_skin`
        FOREIGN KEY (`skin`) REFERENCES `skins` (`name`)
        ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- activities
--
ALTER TABLE `activities`
    ADD CONSTRAINT `fk_activities_target`
        FOREIGN KEY (`target`) REFERENCES `castes` (`cid`)
        ON DELETE NO ACTION ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_activities_origin`
        FOREIGN KEY (`origin`) REFERENCES `groups` (`gid`)
        ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- activities_instances
--
DELETE FROM `activities_instances` WHERE NOT EXISTS (
   SELECT * FROM `activities` AS `a` WHERE `a`.`aid` = `activities_instances`.`activity`
);
ALTER TABLE `activities_instances`
    ADD CONSTRAINT `fk_activities_instances_activity`
        FOREIGN KEY (`activity`) REFERENCES `activities` (`aid`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_activities_instances_writer`
        FOREIGN KEY (`writer`) REFERENCES `account` (`uid`)
        ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- activities_participants
--
DELETE FROM `activities_participants` WHERE NOT EXISTS (
   SELECT * FROM `activities` AS `a` WHERE `a`.`aid` = `activities_participants`.`id`
);
ALTER TABLE `activities_participants`
    DROP INDEX `id`,
    ADD PRIMARY KEY (`id`, `participant`),
    ADD INDEX `fk_activities_participants_id` (`id`),
    ADD CONSTRAINT `fk_activities_participants_id`
        FOREIGN KEY (`id`) REFERENCES `activities` (`aid`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_activities_participants_participant`
        FOREIGN KEY (`participant`) REFERENCES `account` (`uid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- castes
--
DELETE FROM `castes` WHERE `group` = 0;
ALTER TABLE `castes`
    ADD INDEX `fk_castes_group` (`group`),
    ADD CONSTRAINT `fk_castes_group`
        FOREIGN KEY (`group`) REFERENCES `groups` (`gid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- castes_dependencies
--
DELETE FROM `castes_dependencies` WHERE NOT EXISTS (
   SELECT * FROM `castes` AS `c` WHERE `c`.`cid` = `castes_dependencies`.`cid`
);
ALTER TABLE `castes_dependencies`
    DROP INDEX `cid`,
    DROP INDEX `cid_2`,
    ADD PRIMARY KEY (`cid`, `type`, `id`),
    ADD INDEX `fk_castes_dependencies_cid` (`cid`),
    ADD CONSTRAINT `fk_castes_dependencies_cid`
        FOREIGN KEY (`cid`) REFERENCES `castes` (`cid`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_castes_dependencies_id`
        FOREIGN KEY (`id`) REFERENCES `castes` (`cid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- castes_users
--
DELETE FROM `castes_users` WHERE NOT EXISTS (
   SELECT * FROM `account` AS `a` WHERE `a`.`uid` = `castes_users`.`uid`
);
ALTER TABLE `castes_users`
    DROP INDEX `cid`,
    DROP INDEX `cid_2`,
    ADD PRIMARY KEY (`cid`, `uid`),
    ADD INDEX `fk_castes_users_cid` (`cid`),
    ADD CONSTRAINT `fk_castes_users_cid`
        FOREIGN KEY (`cid`) REFERENCES `castes` (`cid`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_castes_users_uid`
        FOREIGN KEY (`uid`) REFERENCES `account` (`uid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- formations_platal
--
ALTER TABLE `formations_platal`
    ADD CONSTRAINT `fk_formations_platal_formation_id`
        FOREIGN KEY (`formation_id`) REFERENCES `formations` (`formation_id`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- groups
--
ALTER TABLE `groups`
    ADD CONSTRAINT `fk_groups_image`
        FOREIGN KEY (`image`) REFERENCES `images` (`iid`)
        ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- images
--
ALTER TABLE `images`
    ADD CONSTRAINT `fk_images_caste`
        FOREIGN KEY (`caste`) REFERENCES `castes` (`cid`)
        ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- images_size
--
ALTER TABLE `images_sizes`
    DROP INDEX `iid`,
    DROP INDEX `iid_2`,
    ADD PRIMARY KEY (`iid`, `size`),
    ADD INDEX `fk_images_sizes_iid` (`iid`),
    ADD CONSTRAINT `fk_images_sizes_iid`
        FOREIGN KEY (`iid`) REFERENCES `images` (`iid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- ips
--
DELETE FROM `ips` WHERE NOT EXISTS (
   SELECT * FROM `rooms` AS `r` WHERE `r`.`rid` = `ips`.`room`
);
ALTER TABLE `ips`
    DROP INDEX `rid`,
    ADD CONSTRAINT `fk_ips_room`
        FOREIGN KEY (`room`) REFERENCES `rooms` (`rid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- laf
--
ALTER TABLE `laf`
    ADD CONSTRAINT `fk_laf_uid`
        FOREIGN KEY (`uid`) REFERENCES `account` (`uid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- links
--
ALTER TABLE `links`
    ADD CONSTRAINT `fk_links_image`
        FOREIGN KEY (`image`) REFERENCES `images` (`iid`)
        ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- log_events
--
ALTER TABLE `log_events`
    ADD CONSTRAINT `fk_log_events_action`
        FOREIGN KEY (`action`) REFERENCES `log_actions` (`id`)
        ON DELETE NO ACTION ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_log_events_session`
        FOREIGN KEY (`session`) REFERENCES `log_sessions` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- log_last_sessions
--
ALTER TABLE `log_last_sessions`
    ADD CONSTRAINT `fk_log_last_sessions_uid`
        FOREIGN KEY (`uid`) REFERENCES `account` (`uid`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_log_last_sessions_id`
        FOREIGN KEY (`id`) REFERENCES `log_sessions` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- log_sessions
--
ALTER TABLE `log_sessions`
    ADD CONSTRAINT `fk_log_sessions_uid`
        FOREIGN KEY (`uid`) REFERENCES `account` (`uid`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_log_sessions_suid`
        FOREIGN KEY (`suid`) REFERENCES `account` (`uid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- msdnaa_keys
--
DELETE FROM `msdnaa_keys` WHERE NOT EXISTS (
   SELECT * FROM `account` AS `a` WHERE `a`.`uid` = `msdnaa_keys`.`uid`
);
ALTER TABLE `msdnaa_keys`
    ADD CONSTRAINT `fk_msdnaa_keys_uid`
        FOREIGN KEY (`uid`) REFERENCES `account` (`uid`)
        ON DELETE NO ACTION ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_msdnaa_keys_gid`
        FOREIGN KEY (`gid`) REFERENCES `groups` (`gid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- news
--
UPDATE `news` SET `image` = NULL WHERE NOT EXISTS (
   SELECT * FROM `images` AS `i` WHERE `i`.`iid` = `news`.`image`
);
ALTER TABLE `news`
    ADD CONSTRAINT `fk_news_writer`
        FOREIGN KEY (`writer`) REFERENCES `account` (`uid`)
        ON DELETE NO ACTION ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_news_target`
        FOREIGN KEY (`target`) REFERENCES `castes` (`cid`)
        ON DELETE NO ACTION ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_news_image`
        FOREIGN KEY (`image`) REFERENCES `images` (`iid`)
        ON DELETE NO ACTION ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_news_origin`
        FOREIGN KEY (`origin`) REFERENCES `groups` (`gid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- news_read
--
DELETE FROM `news_read` WHERE NOT EXISTS (
   SELECT * FROM `news` AS `n` WHERE `n`.`id` = `news_read`.`news`
);
ALTER TABLE `news_read`
    DROP INDEX `uid`,
    ADD PRIMARY KEY (`uid`, `news`),
    ADD INDEX `fk_news_read_uid` (`uid`),
    ADD CONSTRAINT `fk_news_read_uid`
        FOREIGN KEY (`uid`) REFERENCES `account` (`uid`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_news_read_news`
        FOREIGN KEY (`news`) REFERENCES `news` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- news_star
--
DELETE FROM `news_star` WHERE NOT EXISTS (
   SELECT * FROM `news` AS `n` WHERE `n`.`id` = `news_star`.`news`
);
ALTER TABLE `news_star`
    DROP INDEX `uid`,
    ADD PRIMARY KEY (`uid`, `news`),
    ADD INDEX `fk_news_star_uid` (`uid`),
    ADD CONSTRAINT `fk_news_star_uid`
        FOREIGN KEY (`uid`) REFERENCES `account` (`uid`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_news_star_news`
        FOREIGN KEY (`news`) REFERENCES `news` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- poly
--
ALTER TABLE `poly`
    ADD CONSTRAINT `fk_poly_uid`
        FOREIGN KEY (`uid`) REFERENCES `account` (`uid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- qdj
--
ALTER TABLE `qdj`
    ADD CONSTRAINT `fk_qdj_writer`
        FOREIGN KEY (`writer`) REFERENCES `account` (`uid`)
        ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- qdj_scores
--
ALTER TABLE `qdj_scores`
    ADD CONSTRAINT `fk_qdj_scores_uid`
        FOREIGN KEY (`uid`) REFERENCES `account` (`uid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- qdj_votes
--
ALTER TABLE `qdj_votes`
    ADD CONSTRAINT `fk_qdj_votes_qdj`
        FOREIGN KEY (`qdj`) REFERENCES `qdj` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_qdj_votes_uid`
        FOREIGN KEY (`uid`) REFERENCES `account` (`uid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- remote_groups
--
ALTER TABLE `remote_groups`
    DROP INDEX `remid`,
    ADD PRIMARY KEY (`remid`, `gid`),
    ADD INDEX `fk_remote_groups_remid` (`remid`),
    ADD CONSTRAINT `fk_remote_groups_remid`
        FOREIGN KEY (`remid`) REFERENCES `remote` (`remid`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_remote_groups_gid`
        FOREIGN KEY (`gid`) REFERENCES `groups` (`gid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- room_groups
--
ALTER TABLE `rooms_groups`
    DROP INDEX `gid`,
    ADD PRIMARY KEY (`rid`, `gid`),
    ADD INDEX `rid` (`rid`),
    ADD CONSTRAINT `fk_rooms_groups_rid`
        FOREIGN KEY (`rid`) REFERENCES `rooms` (`rid`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_rooms_groups_gid`
        FOREIGN KEY (`gid`) REFERENCES `groups` (`gid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- rooms_users
--
ALTER TABLE `rooms_users`
    DROP INDEX `uid`,
    ADD PRIMARY KEY (`rid`, `uid`),
    ADD INDEX `fk_rooms_users_rid` (`rid`),
    ADD CONSTRAINT `fk_rooms_users_rid`
        FOREIGN KEY (`rid`) REFERENCES `rooms` (`rid`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_rooms_users_uid`
        FOREIGN KEY (`uid`) REFERENCES `account` (`uid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- studies
--
ALTER TABLE `studies`
    ADD INDEX `fk_studies_formation_id` (`formation_id`),
    ADD CONSTRAINT `fk_studies_uid`
        FOREIGN KEY (`uid`) REFERENCES `account` (`uid`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_studies_formation_id`
        FOREIGN KEY (`formation_id`) REFERENCES `formations` (`formation_id`)
        ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- todo
--
ALTER TABLE `todo`
    ADD CONSTRAINT `fk_todo_uid`
        FOREIGN KEY (`uid`) REFERENCES `account` (`uid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- users_comments
--
ALTER TABLE `users_comments`
    DROP INDEX `uid`,
    ADD PRIMARY KEY (`uid`, `gid`),
    ADD INDEX `uid` (`uid`),
    ADD CONSTRAINT `fk_users_comments_uid`
        FOREIGN KEY (`uid`) REFERENCES `account` (`uid`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_users_comments_gid`
        FOREIGN KEY (`gid`) REFERENCES `groups` (`gid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- users_defaultfilters
--
ALTER TABLE `users_defaultfilters`
    DROP INDEX `uid`,
    ADD PRIMARY KEY (`uid`),
    ADD CONSTRAINT `fk_users_defaultfilters_uid`
        FOREIGN KEY (`uid`) REFERENCES `account` (`uid`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- users_minimodules
--
DELETE FROM `users_minimodules` WHERE NOT EXISTS (
   SELECT * FROM `account` AS `a` WHERE `a`.`uid` = `users_minimodules`.`uid`
);
ALTER TABLE `users_minimodules`
    CHANGE `name` `name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    DROP INDEX `uid`,
    ADD PRIMARY KEY (`uid`, `name`),
    ADD INDEX `uid` (`uid`),
    ADD CONSTRAINT `fk_users_minimodules_uid`
        FOREIGN KEY (`uid`) REFERENCES `account` (`uid`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_users_minimodules_name`
        FOREIGN KEY (`name`) REFERENCES `minimodules` (`name`)
        ON DELETE CASCADE ON UPDATE CASCADE;

--
-- validate
--
ALTER TABLE `validate`
    ADD CONSTRAINT `fk_validate_writer`
        FOREIGN KEY (`writer`) REFERENCES `account` (`uid`)
        ON DELETE NO ACTION ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_validate_group`
        FOREIGN KEY (`group`) REFERENCES `groups` (`gid`)
        ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- wiki_version
--
ALTER TABLE `wiki_version`
    DROP INDEX `wid`,
    ADD PRIMARY KEY (`wid`, `version`),
    ADD INDEX `fk_wiki_version_wid` (`wid`),
    ADD CONSTRAINT `fk_wiki_version_wid`
        FOREIGN KEY (`wid`) REFERENCES `wiki` (`wid`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_wiki_version_writer`
        FOREIGN KEY (`writer`) REFERENCES `account` (`uid`)
        ON DELETE NO ACTION ON UPDATE CASCADE;
