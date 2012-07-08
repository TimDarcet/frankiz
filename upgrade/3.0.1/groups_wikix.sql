--
-- Add wikix field in groups table
--
ALTER TABLE groups ADD COLUMN wikix tinytext COLLATE utf8_unicode_ci NOT NULL;
