<?php
// +----------------------------------------------------------------------
// | PHP Source                                                           
// +----------------------------------------------------------------------
// | Copyright (C) 2004 by BR <br@frankiz>
// +----------------------------------------------------------------------
// |
// | Copyright: See COPYING file that comes with this distribution
// +----------------------------------------------------------------------
//




// Configuration du site

define('AFFICHER_LES_ERREURS',1);	// seulement sur gwennoz
define('BASE_DATA',"/var/www/frankiz2/data/");				// TODO Gérer le truc proprement.
define('BASE_PHOTOS',"/var/www/frankiz2/data/photos/");
define('BASE_CACHE',"/var/www/frankiz2/cache/");
define('BASE_BINETS',BASE_LOCAL."/../binets/");

define('URL_DATA','http://'.$_SERVER['HTTP_HOST'].'/frankiz2/data/');

define('DATA_DIR_LOCAL',BASE_DATA);	// pour compatibilité
define('DATA_DIR_URL',URL_DATA);

define('MAIL_WEBMESTRE',"pico@localhost");

// Emails

$i = -1 ;
define('WEBMESTRE_ID',$i--);
define('MAIL_QDJMASTER',"pico@localhost");
define('QDJMASTER_ID',$i--);
define('MAIL_PREZ',"pico@localhost");
define('PREZ_ID',$i--);
define('MAIL_ROOT',"pico@localhost");
define('ROOT_ID',$i--);

define('MAIL_CONTACT',"eleves@polytechnique.fr");


// Nombres de jours affichés dans la page des annonces 

define('MAX_PEREMPTION',8);

// Connexions aux bases mysql
$DB_xnet = new DB("frankiz2","xnet","web","kokouije?.");
$DB_faq = new DB("frankiz","faq","web","kokouije?.");
$DB_web = new DB("frankiz","frankiz2_tmp","web","kokouije?.");
$DB_admin = new DB("frankiz","admin","web","kokouije?.");
$DB_trombino = new DB("frankiz","trombino","web","kokouije?.");
$DB_valid = new DB("frankiz","a_valider","web","kokouije?.");


/*
	Élements à ne pas mettre dans la distribution libre du code car il y a les mot de passe
	et les comptes www.weather.com
*/

// Météo
define('WEATHER_DOT_COM',"http://xoap.weather.com/weather/local/FRXX0076?prod=xoap&par=1006415841&key=5064537abefac140&unit=m&cc=*&dayf=8");

?>
