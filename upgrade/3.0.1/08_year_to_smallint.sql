--
-- Use SMALLINT instead of YEAR(4) for years, because YEAR ranges only 1901-2155
--
ALTER TABLE `formations_platal` CHANGE `year` `year` SMALLINT NOT NULL;
ALTER TABLE `studies` CHANGE `year_in` `year_in` SMALLINT NOT NULL;
ALTER TABLE `studies` CHANGE `year_out` `year_out` SMALLINT NOT NULL;
ALTER TABLE `studies` CHANGE `promo` `promo` SMALLINT NOT NULL;
