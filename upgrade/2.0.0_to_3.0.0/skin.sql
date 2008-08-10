=========================
Gestion des skin


CREATE TABLE `frankiz3`.`skin_base` (
`skin_base_id` INT NOT NULL AUTO_INCREMENT ,
 `name` VARCHAR( 40 ) NOT NULL ,
 `title` VARCHAR( 40 ) NOT NULL ,
 `params` TEXT NOT NULL ,
 PRIMARY KEY ( `skin_base_id` ) ,
 INDEX ( `skin_base_id` ) 
) ENGINE = MYISAM COMMENT = 'Stores informations about skin bases'

CREATE TABLE `frankiz3`.`skin_css` (
`skin_css_id` INT NOT NULL AUTO_INCREMENT ,
 `skin_base_id` INT NOT NULL ,
 `name` VARCHAR( 40 ) NOT NULL ,
 `title` VARCHAR( 40 ) NOT NULL ,
 `description` TINYTEXT NOT NULL ,
 `screenshot` TEXT NOT NULL ,
 `date` DATE NOT NULL ,
 PRIMARY KEY ( `skin_css_id` ) ,
 INDEX ( `skin_css_id` ) 
) ENGINE = MYISAM COMMENT = 'Stores informations about CSS versions'

INSERT INTO `frankiz3`.`skin_css` (
`skin_css_id` ,
 `skin_base_id` ,
 `name` ,
 `title` ,
 `description` ,
 `screenshot` ,
 `date` 
)
VALUES (
NULL , '1', 'default', 'Skin par défaut', 'La skin par défaut de Frankiz3', '', NOW( ) 
);

INSERT INTO `frankiz3`.`skin_base` (
`skin_base_id` ,
 `name` ,
 `title` ,
 `params` 
)
VALUES (
NULL , 'default', 'Le modèle par défaut de frankiz3', ''
);

