--
-- Add an open field in rooms table
--
ALTER TABLE `rooms` ADD COLUMN `open` tinyint(1) NOT NULL DEFAULT '0';

--
-- Drop open field in rooms_groups table
--
ALTER TABLE `rooms_groups` DROP `open`;
