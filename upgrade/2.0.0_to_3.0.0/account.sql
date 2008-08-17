=========================
Gestion des comptes (perms, hash, ...)

ALTER TABLE `compte_frankiz` CHANGE `perms` `perms_old` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `compte_frankiz` ADD `perms` SET( 'admin', 'tol', 'support', 'kes', 'xshare' ) NOT NULL AFTER `passwd` ;
