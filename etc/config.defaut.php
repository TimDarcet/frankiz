<?php
/*
	Copyright (C) 2004 Binet Réseau
	http://www.polytechnique.fr/eleves/binets/br/
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/
/*
	Fichier de configuration du site. C'est le seul fichier à mondifier
	lors d'un installation.
	
	$Log$
	Revision 1.1  2005/06/11 16:31:33  nc
	Pas de mots de passe ou d'autre Ã©lÃ©ment scritiques sur la cvs !

	Revision 1.17  2005/03/07 17:13:18  pico
	On supprime l'adresse spammée
	
	Revision 1.16  2005/02/06 22:51:07  pico
	Pour une faq avec des images
	
	Revision 1.15  2005/01/21 23:02:47  pico
	Postage licence sur les news
	
	Revision 1.14  2005/01/18 19:50:01  pico
	Ajout MailPromo
	
	Revision 1.13  2005/01/18 17:09:36  pico
	Pour que la kes reçoive les notif de mail promo
	
	Revision 1.12  2005/01/11 14:35:58  pico
	Binets externes
	
	Revision 1.11  2005/01/11 13:34:25  pico
	Sites persos visibles depuis l'extérieur
	
	Revision 1.10  2004/12/17 20:54:13  pico
	Changement des alias mail
	
	Revision 1.9  2004/12/17 18:36:34  pico
	Ajout mail admin@windows
	
	Revision 1.8  2004/12/17 15:50:41  schmurtz
	Desactivation de l'affichage des erreurs
	
	Revision 1.7  2004/12/17 12:45:50  pico
	Mal prez
	
	Revision 1.6  2004/12/16 16:54:58  schmurtz
	Installation du lien symbolique config.php -> config.php.prod sur frankiz
	
	Revision 1.5  2004/12/16 16:45:14  schmurtz
	Correction d'un bug dans la gestion des authentifications par cookies
	Ajout de fonctionnalitees de log d'erreur de connections ou lors des bugs
	affichant une page "y a un bug, contacter l'admin"
	
	Revision 1.4  2004/12/15 19:26:08  kikx
	Les mails promo devrait fonctionner now ...
	
	Revision 1.3  2004/12/15 15:44:34  fruneau
	Reste des trucs qui diffèrent avec la conf de prod actuelle
	
	Kikx, Schmurtz, pico, je vous laisse regarder...
	
	Revision 1.2  2004/12/15 15:40:16  fruneau
	Synchronisation de config.php avec celui qui était utilisé
	
	Revision 1.1  2004/12/15 15:31:17  fruneau
	Donne une structure
	config.php.prod
	config.php.dev
	en remplacement du config.php...
*/


// ATTENTION
// Ce fichier n'est qu'une base pour écrire le config.php

// Timezone
define('TIMEZONE', "Europe/Paris");

// Compte www.weather.com
define('WEATHER_DOT_COM',"http://xoap.weather.com/weather/local/**********");

// URL AppleOnCampus
define('PARTENAIRES_AOC_URL', "");

// Configuration du site
define('AFFICHER_LES_ERREURS',0); 
define('BASE_DATA',"/home/frankiz2/data/");
define('BASE_PHOTOS',"/home/frankiz2/data/photos/");
define('BASE_CACHE',"/home/frankiz2/cache/");
define('BASE_BINETS',"/home/frankiz2/binets/");
define('BASE_BINETS_EXT',"/home/frankiz2/htdocs/binets/");
define('BASE_PAGESPERSOS',"/home/frankiz2/webperso/");
define('BASE_PAGESPERSOS_EXT',"/home/frankiz2/htdocs/webperso/");

define('URL_DATA',BASE_URL.'/data/');
define('URL_BINETS','http://binets.frankiz.eleves.polytechnique.fr/');
define('URL_PAGEPERSO','http://perso.frankiz.eleves.polytechnique.fr/');

define('LOG_ERROR',"/var/log/apache2/frankiz.error");
define('LOG_ACCESS',"/var/log/apache2/frankiz.access");	// connection/déconnection, erreur de login

define('DATA_DIR_LOCAL',BASE_DATA);	// pour compatibilité
define('DATA_DIR_URL',URL_DATA);

define('DSI_URL',"reseaux.polytechnique.fr") ;

// Emails
$i = -1 ;
define('MAIL_WEBMESTRE',"web@frankiz.polytechnique.fr");
define('WEBMESTRE_ID',$i--);
define('MAIL_QDJMASTER',"qdj@frankiz.polytechnique.fr");
define('QDJMASTER_ID',$i--);
define('MAIL_PREZ',"prez@frankiz.polytechnique.fr");
define('PREZ_ID',$i--);
define('MAIL_ROOT',"root@frankiz.polytechnique.fr");
define('ROOT_ID',$i--);
define('MAIL_MAILPROMO',"mailpromo@frankiz.polytechnique.fr");
define('MAILPROMO_ID',$i--);
define('MAIL_BR',"br@frankiz");
define('BR_ID',$i--);

define('MAIL_CONTACT',"eleves@polytechnique.fr");
define('CONTACT_ID',$i--);
define('MAIL_FAQMESTRE',"faq@frankiz");
define('FAQMESTRE_ID',$i--);
define('MAIL_TROMBINOMEN',"tol@frankiz");
define('TROMBINOMEN_ID',$i--);
define('MAIL_WINDOWS',"msdnaa-licences@frankiz");
define('WINDOWS_ID',$i--);

define('STRINGMAIL_ID',$i--);

// Nombres de jours affichés dans la page des annonces 
define('MAX_PEREMPTION',8);

// Connexions aux bases mysql
$DB_xnet = new DB("frankiz2","xnet","web","**********");
$DB_faq = new DB("frankiz","frankiz2","web","**********");
$DB_web = new DB("frankiz","frankiz2","web","**********");
$DB_admin = new DB("frankiz","admin","web","**********");
$DB_trombino = new DB("frankiz","trombino","web","**********");
$DB_valid = new DB("frankiz","a_valider","web","**********");
$DB_msdnaa = new DB("frankiz","msdnaa","web","**********");
$DB_wifi = new DB("lunedenn", "Radius", "web", "**********");

?>
