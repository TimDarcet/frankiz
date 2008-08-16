=========================
Gestion des skin

ALTER TABLE `compte_frankiz` CHANGE `skin` `skin_old` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `compte_frankiz` ADD `skin` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `eleve_id`;

CREATE TABLE `frankiz3`.`skins` (
`skin_id` INT NOT NULL AUTO_INCREMENT ,
 `skin_base_id` INT NOT NULL ,
 `name` VARCHAR( 40 ) NOT NULL ,
 `title` VARCHAR( 40 ) NOT NULL ,
 `description` TINYTEXT NOT NULL ,
 `screenshot` TEXT NOT NULL ,
 `date` DATE NOT NULL ,
 PRIMARY KEY ( `skin_id` ) ,
 INDEX ( `skin_id` ) 
) ENGINE = MYISAM COMMENT = 'Stores informations about skins'

INSERT INTO `frankiz3`.`skin_css` (
`skin_id` ,
 `name` ,
 `title` ,
 `description` ,
 `screenshot` ,
 `date` 
)
VALUES (
NULL , 'default', 'Skin par défaut', 'La skin par défaut de Frankiz3', '', NOW( ) 
);

INSERT INTO `frankiz3`.`skin_css` (
`skin_id` ,
 `name` ,
 `title` ,
 `description` ,
 `screenshot` ,
 `date` 
)
VALUES (
NULL , 'tranquille', 'Tranquille', 'Une skin \'Tranquille\'', '', NOW( ) 
);

