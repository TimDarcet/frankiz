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
	Revision 1.5  2004/11/22 23:07:28  kikx
	Rajout de lines vers les pages perso

	Revision 1.4  2004/11/22 18:59:31  kikx
	Pour gérer son site perso
	
	Revision 1.3  2004/11/13 00:10:14  schmurtz
	Il FAUT mettre les entetes de licence GPL dans les nouveaux fichiers
	Faut aussi eviter d'utiliser une structure d'entetes perso qui casse l'homogeneite
	des fichiers de code et en complique donc la lisibilite.
	
*/

// ================ ATTENTION ==============
/*
	Élements à ne pas mettre dans la distribution libre du code car il y a les mot de passe
	et les comptes www.weather.com
*/

define('WEATHER_DOT_COM',"http://xoap.weather.com/weather/local/FRXX0076?prod=xoap&par=1006415841&key=5064537abefac140&unit=m&cc=*&dayf=8");

// ================== FIN ==================

// Configuration du site

define('AFFICHER_LES_ERREURS',$_SERVER["SERVER_ADDR"] == "129.104.201.52"); // seulement sur gwennoz
define('BASE_DATA',"/home/frankiz2/data/");				// TODO Gérer le truc proprement.
define('BASE_PHOTOS',"/home/frankiz2/data/photos/");
define('BASE_CACHE',"/home/frankiz2/cache/");
define('BASE_BINETS',"/home/frankiz2/binets/");
define('BASE_PAGESPERSOS',"/home/frankiz2/webperso/");

define('URL_DATA','http://'.$_SERVER['HTTP_HOST'].'/frankiz2/data/');
define('URL_PAGEPERSO','http://'.$_SERVER['HTTP_HOST'].'/frankiz2/webperso/');

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

?>
