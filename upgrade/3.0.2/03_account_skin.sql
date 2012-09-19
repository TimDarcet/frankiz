--
-- Add default value for account.skin, to validate the foreign key
--
ALTER TABLE `account` CHANGE `skin` `skin` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'default';
