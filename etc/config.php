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

define('AFFICHER_LES_ERREURS',$_SERVER["SERVER_ADDR"] == "129.104.201.52"); // seulement sur gwennoz
define('BASE_DATA',"/home/frankiz2/data/");				// TODO Gérer le truc proprement.
define('BASE_PHOTOS',"/home/frankiz2/data/photos/");
define('BASE_CACHE',"/home/frankiz2/cache/");
define('BASE_BINETS',"/home/frankiz2/binets/");

define('URL_DATA','http://'.$_SERVER['HTTP_HOST'].'/frankiz2/data/');

define('DATA_DIR_LOCAL',BASE_DATA);	// pour compatibilité
define('DATA_DIR_URL',URL_DATA);

// Emails

$i = -1 ;
define('MAIL_WEBMESTRE',"kikx@frankiz.polytechnique.fr");
define('WEBMESTRE_ID',$i--);
define('MAIL_QDJMASTER',"eric.gruson@polytechnique.fr");
define('QDJMASTER_ID',$i--);
define('MAIL_PREZ',"eric@melix.org");
define('PREZ_ID',$i--);
define('MAIL_ROOT',"gruson@poly");
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
