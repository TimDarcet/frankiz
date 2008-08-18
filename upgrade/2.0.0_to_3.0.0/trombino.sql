=====================
Gestion des données élèves (binets, promo, ...)

CREATE TABLE `frankiz3`.`binets` (
`binet_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
 `nom` TINYTEXT NOT NULL ,
 `folder` TEXT NOT NULL ,
 `description` TEXT NOT NULL ,
 `catego_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',
 `exterieur` TINYINT( 1 ) NOT NULL DEFAULT '0',
 `image` LONGBLOB,
 `format` TINYTEXT,
 `http` TINYTEXT,
 PRIMARY KEY ( `binet_id` ) 
) ENGINE = MYISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT =399;

 INSERT INTO `frankiz3`.`binets` 
SELECT * 
FROM `trombino`.`binets` ;

CREATE TABLE `frankiz3`.`sections` (
`section_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
 `nom` TINYTEXT NOT NULL ,
 `existe` TINYINT( 1 ) NOT NULL DEFAULT '1',
 `newsgroup` VARCHAR( 100 ) DEFAULT '',
 `bar` VARCHAR( 7 ) DEFAULT NULL ,
 PRIMARY KEY ( `section_id` ) 
) ENGINE = MYISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT =20;

 INSERT INTO `frankiz3`.`sections` 
SELECT * 
FROM `trombino`.`sections` ;

CREATE TABLE `frankiz3`.`nations` (
`nation_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
 `name` VARCHAR( 50 ) DEFAULT NULL ,
 `existe` TINYINT( 1 ) DEFAULT '1',
 PRIMARY KEY ( `nation_id` ) 
) ENGINE = MYISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT =51;

 INSERT INTO `frankiz3`.`nations` 
SELECT * 
FROM `trombino`.`nations` ;

CREATE TABLE `frankiz3`.`arpwatch_log` (
`mac` VARCHAR( 17 ) NOT NULL DEFAULT '',
 `ip` VARCHAR( 15 ) NOT NULL DEFAULT '',
 `dns` TINYTEXT NOT NULL ,
 `ts` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
 `traite` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0',
 `comment` TEXT NOT NULL ,
 KEY `mac` ( `mac` ) ,
 KEY `ip` ( `ip` ) 
) ENGINE = MYISAM DEFAULT CHARSET = latin1;

 INSERT INTO `frankiz3`.`arpwatch_log` 
SELECT * 
FROM `admin`.`arpwatch_log` ;

CREATE TABLE `frankiz3`.`prises` (
`prise_id` VARCHAR( 10 ) NOT NULL DEFAULT '',
 `piece_id` VARCHAR( 7 ) NOT NULL DEFAULT '',
 `ip` VARCHAR( 15 ) NOT NULL DEFAULT '',
 `type` ENUM( 'principale', 'secondaire' ) NOT NULL DEFAULT 'secondaire',
 PRIMARY KEY ( `ip` ) 
) ENGINE = MYISAM DEFAULT CHARSET = latin1;

 INSERT INTO `frankiz3`.`prises` 
SELECT * 
FROM `admin`.`prises` ;

CREATE TABLE `frankiz3`.`pieces` (
`piece_id` VARCHAR( 7 ) NOT NULL DEFAULT '',
 `tel` VARCHAR( 4 ) DEFAULT NULL ,
 `bat_id` CHAR( 2 ) NOT NULL DEFAULT '',
 `etage` TINYINT( 2 ) NOT NULL DEFAULT '0',
 `comment` TEXT,
 PRIMARY KEY ( `piece_id` ) 
) ENGINE = MYISAM DEFAULT CHARSET = latin1;

 INSERT INTO `frankiz3`.`pieces` 
SELECT * 
FROM `admin`.`pieces` ;

CREATE TABLE `frankiz3`.`membres` (
`binet_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',
 `eleve_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',
 `remarque` TINYTEXT,
 PRIMARY KEY ( `binet_id` , `eleve_id` ) 
) ENGINE = MYISAM DEFAULT CHARSET = utf8;

 INSERT INTO `frankiz3`.`membres` 
SELECT * 
FROM `trombino`.`membres` ;


CREATE TABLE `frankiz3`.`clients` (
`iconid` VARCHAR( 8 ) DEFAULT NULL ,
 `username` VARCHAR( 64 ) NOT NULL DEFAULT '',
 `password` VARCHAR( 64 ) NOT NULL DEFAULT '',
 `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
 `lastip` VARCHAR( 16 ) DEFAULT NULL ,
 `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
 `status` SMALLINT( 6 ) NOT NULL DEFAULT '0',
 `isconnected` TINYINT( 4 ) NOT NULL DEFAULT '0',
 `options` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0',
 `version` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0',
 UNIQUE KEY `id` ( `id` ) ,
 KEY `username` ( `username` ) ,
 KEY `lastip` ( `lastip` ) 
) ENGINE = MYISAM DEFAULT CHARSET = latin1 AUTO_INCREMENT =1884;

 INSERT INTO `frankiz3`.`clients` 
SELECT * 
FROM `xnet`.`clients` ;

CREATE TABLE `frankiz3`.`software` (
`version` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',
 `capabilities` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',
 `name` VARCHAR( 64 ) NOT NULL DEFAULT '',
 PRIMARY KEY ( `version` ) 
) ENGINE = MYISAM DEFAULT CHARSET = latin1;

 INSERT INTO `frankiz3`.`software` 
SELECT * 
FROM `xnet`.`software` ;

CREATE TABLE `frankiz3`.`arpwatch_vendors` (
`debut_mac` VARCHAR( 8 ) NOT NULL DEFAULT '',
 `vendor` TINYTEXT NOT NULL ,
 PRIMARY KEY ( `debut_mac` ) 
) ENGINE = MYISAM DEFAULT CHARSET = latin1;

 INSERT INTO `frankiz3`.`arpwatch_vendors` 
SELECT * 
FROM `admin`.`arpwatch_vendors` ;


