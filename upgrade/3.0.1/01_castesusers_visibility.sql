--
-- Add a visibility field in castes_users column
--
ALTER TABLE castes_users ADD COLUMN visibility int(11) NOT NULL DEFAULT '0';
