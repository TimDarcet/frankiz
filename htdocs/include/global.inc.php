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
	Fichier de définition de variables et constantes utiles dans tout le projet.
	C'est de ce script que sont inclus tous les fichiers inclus pour toutes les pages
	et _qui inclus du code en dehors de fonctions_ comme erreursphp.inc.php, login.inc.php,
	skin.inc.php mais pas user.inc.php, xml.inc.php
	
	$Log$
	Revision 1.34  2004/11/08 08:47:57  kikx
	Pour la gestion online des sites de binets

	Revision 1.33  2004/11/07 00:09:13  pico
	Include du fichier contenant les fonctions de compression
	
	Revision 1.32  2004/11/06 15:09:18  pico
	Correction d'un commit de merde, désolé
	
	Revision 1.30  2004/11/04 16:36:42  schmurtz
	Modifications cosmetiques
	
	Revision 1.29  2004/10/29 15:41:48  kikx
	Passage des mail en HTML pour les ip
	
	Revision 1.28  2004/10/29 14:38:37  kikx
	Mise en format HTML des mails pour les validation de la qdj, des mails promos, et des annonces
	
	Revision 1.27  2004/10/29 14:09:10  kikx
	Envoie des mail en HTML pour la validation des affiche
	
	Revision 1.26  2004/10/28 16:08:14  kikx
	Ne fait qu'une page de fonctions pour la météo car sinon ça devient ingérable
	
	Revision 1.25  2004/10/22 06:52:38  pico
	Bdd xnet
	
	Revision 1.24  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.23  2004/10/21 12:24:48  kikx
	Correction d'un bug suite a un commit
	
	Revision 1.22  2004/10/17 17:27:07  pico
	Ajout bdd de la faq
	
	Revision 1.21  2004/10/16 01:18:00  schmurtz
	Utilisation d'un chemin d'acces absolu pour partager les images et les caches entre
	tous les developpeurs. Cela suppose que tout le monde test ce qu'il developpe sur
	gwennoz, et permet de disposer de toutes les images actuelles (photos, annonces...)
	
	Revision 1.20  2004/10/15 23:28:00  schmurtz
	Suppression du dossier data de la CVS pour le remplacer par un lien symbolique
	Tous les developpeurs partage alors les memes data (photos du trombino, images des
	annonces...)
	
	Revision 1.19  2004/10/15 22:03:07  kikx
	Mise en place d'une page pour la gestion des sites des binets
	
	Revision 1.18  2004/10/07 22:52:20  kikx
	Correction de la page des activites (modules + proposition + administration)
		rajout de variables globales : DATA_DIR_LOCAL
						DATA_DIR_URL
	
	Comme ca si ca change, on est safe :)
	
	Revision 1.17  2004/10/04 21:48:08  schmurtz
	Modification des chemins d'acceÌ€s vers divers eÌleÌments.
	
	Revision 1.16  2004/09/20 20:33:47  schmurtz
	Mise en place d'un systeme de cache propre
	
	Revision 1.15  2004/09/18 16:22:26  kikx
	micro bug fix
	
	Revision 1.13  2004/09/17 14:19:58  kikx
	Page de demande d'annonce terminé
	Ajout d'une page de validations d'annonces
	
	Revision 1.12  2004/09/17 13:12:18  schmurtz
	Suppression des <![CDATA[...]>> car les donneÌes des GET et POST (et donc de la base de donneÌes) sont maintenant eÌchappeÌes avec des &amp; &lt; &apos;...
	
	Revision 1.11  2004/09/17 11:15:21  schmurtz
	Echappement des caracteres < > & dans les variables $_POST $_GET $_REQUEST
	
	Revision 1.10  2004/09/17 09:05:32  kikx
	La personne peut maintenant rajouter une annonce
	Ceci dit je ne comprend pas trop comment on protège les champs avec les <!CDATA
	-> j'ai laisser ca comme ca mais faudra modifier
	
	Revision 1.9  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.8  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

// Définition générique des différentes bases pour l'accès aux fichiers
foreach(Array('.', '..', '../..', '../../..') as $dir)
if(file_exists("$dir/frankiz.dtd"))
	$href = $dir;

define('BASE_LOCAL',realpath(dirname(__FILE__)."/.."));
define('BASE_URL','http://'.$_SERVER['HTTP_HOST'].'/'.substr((dirname($_SERVER['PHP_SELF']).'/'.$href), 1));

// Configuration du site
define('AFFICHER_LES_ERREURS',$_SERVER["SERVER_ADDR"] == "129.104.201.52");	// seulement sur gwennoz
define('BASE_DATA',"/home/frankiz2/data/");				// TODO Gérer le truc proprement.
define('BASE_PHOTOS',"/home/frankiz2/data/photos/");
define('BASE_CACHE',"/home/frankiz2/cache/");
define('BASE_BINETS',BASE_LOCAL."/../binets/");
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

// Gestion des erreurs PHP et MySQL
// Il est important d'inclure ce fichier le plus tôt possible, mais comme il a besoin
// des paramètres du site on ne l'inclu que maintenant.
require_once "erreursphp.inc.php";

// Gestion des erreurs dans les formulaires
$i=1;
define('ERR_LOGIN',$i++);
define('ERR_MAILLOGIN',$i++);
define('ERR_LOGINPOLY',$i++);
define('ERR_MDP_DIFFERENTS',$i++);
define('ERR_MDP_TROP_PETIT',$i++);
define('ERR_SURNOM_TROP_PETIT',$i++);
define('ERR_EMAIL_NON_VALIDE',$i++);
define('ERR_TROP_COURT',$i++);
define('ERR_SELECTION_VIDE',$i++);

// Nettoyage des éléments récupérés par $_POST, $_GET et $_REQUEST
function nettoyage_balise($tableau) {
	foreach($tableau as $cle => $valeur)
		if(is_array($valeur)) $tableau[$cle] = nettoyage_balise($valeur);
		else $tableau[$cle] = str_replace(array('&','<','>','\'','"','\\'),array('&amp;','&lt;','&gt;','&apos;','&quot;',''),$valeur);
	return $tableau;
}

$_GET = nettoyage_balise($_GET);
$_POST = nettoyage_balise($_POST);
$_REQUEST = nettoyage_balise($_REQUEST);

/*
	Élements à ne pas mettre dans la distribution libre du code car il y a les mot de passe
	et les comptes www.weather.com
*/

// Connexions aux bases mysql
require_once "mysql.inc.php";
$DB_xnet = new DB("frankiz2","xnet","web","kokouije?.");
$DB_faq = new DB("frankiz","faq","web","kokouije?.");
$DB_web = new DB("frankiz","frankiz2_tmp","web","kokouije?.");
$DB_admin = new DB("frankiz","admin","web","kokouije?.");
$DB_trombino = new DB("frankiz","trombino","web","kokouije?.");
$DB_valid = new DB("frankiz","a_valider","web","kokouije?.");

// Météo
define('WEATHER_DOT_COM',"http://xoap.weather.com/weather/local/FRXX0076?prod=xoap&par=1006415841&key=5064537abefac140&unit=m&cc=*&dayf=8");

// divers fichiers inclus
require_once "global_func.inc.php";
require_once "mail.inc.php";
require_once "login.inc.php";
require_once "skin.inc.php";
require_once "compress.inc.php";

?>
