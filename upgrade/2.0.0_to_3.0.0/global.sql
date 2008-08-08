========================
Copie vers la base "frankiz3"

TODO :
CREATE DATABASE `frankiz` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT USAGE ON frankiz.* TO 'web'@'localhost';


DROP TABLE `a_virer_binets` , `a_virer_categ_binet` ;

DELETE FROM `parametres` WHERE CONVERT( `parametres`.`nom` USING utf8 ) = 'lastpromo_oncampus' LIMIT 1 ;
DELETE FROM `parametres` WHERE CONVERT( `parametres`.`nom` USING utf8 ) = 'lastpromo_ontrombino' LIMIT 1 ;
DELETE FROM `parametres` WHERE CONVERT( `parametres`.`nom` USING utf8 ) = 'mail_webmestre' LIMIT 1 ;
DELETE FROM `parametres` WHERE CONVERT( `parametres`.`nom` USING utf8 ) = 'mail_qdjmaster' LIMIT 1 ;
DELETE FROM `parametres` WHERE CONVERT( `parametres`.`nom` USING utf8 ) = 'mail_tolmestre' LIMIT 1 ;
DELETE FROM `parametres` WHERE CONVERT( `parametres`.`nom` USING utf8 ) = 'skin_default' LIMIT 1 ;
DELETE FROM `parametres` WHERE CONVERT( `parametres`.`nom` USING utf8 ) = 'css_default' LIMIT 1 ;
DELETE FROM `parametres` WHERE CONVERT( `parametres`.`nom` USING utf8 ) = 'mail_root' LIMIT 1 ;

