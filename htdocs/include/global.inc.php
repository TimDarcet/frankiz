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
	Revision 1.40  2004/11/24 22:12:57  schmurtz
	Regroupement des fonctions zip unzip deldir et download dans le meme fichier

	Revision 1.39  2004/11/24 20:26:38  schmurtz
	Reorganisation des skins (affichage melange skin/css + depacement des css)
	
	Revision 1.38  2004/11/24 16:37:09  pico
	Ajout des news externes en tant que module
	
	Revision 1.37  2004/11/24 15:55:34  pico
	Code pour gérer les liens perso + les rss au lancement de la session
	
	Revision 1.36  2004/11/24 12:51:02  kikx
	Pour commencer la compatibilité wiki
	
	Revision 1.35  2004/11/09 19:42:15  pico
	Passage de tous les paramètres de conf dans etc/config.php
	
	
*/

// Définition générique des différentes bases pour l'accès aux fichiers
foreach(Array('.', '..', '../..', '../../..') as $dir)
if(file_exists("$dir/frankiz.dtd"))
	$href = $dir;

define('BASE_LOCAL',realpath(dirname(__FILE__)."/.."));
define('BASE_URL','http://'.$_SERVER['HTTP_HOST'].'/'.substr((dirname($_SERVER['PHP_SELF']).'/'.$href), 1));

// Connexions aux bases mysql
require_once "mysql.inc.php";

require_once BASE_LOCAL."/../etc/config.php";

// Gestion des erreurs PHP et MySQL
// Il est important d'inclure ce fichier le plus tôt possible, mais comme il a besoin
// des paramètres du site on ne l'inclu que maintenant.
require_once "erreursphp.inc.php";	// TODO : mettre avant l'ouverture des connexions aux bases mysql

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

// divers fichiers inclus pour effectuer des actions avant l'affichage de la page
require_once "global_func.inc.php";
require_once "mail.inc.php";			// TODO : supprimer et mettre dans les fichiers qui ont en vraiment besoin
require_once "login.inc.php";
require_once "init_skin.inc.php";
require_once "wiki.inc.php";			// TODO : supprimer et mettre dans les fichiers qui ont en vraiment besoin
require_once "param_session.inc.php";	// TODO : supprimer et mettre dans les fichiers qui ont en vraiment besoin
require_once "rss_func.inc.php";		// TODO : supprimer et mettre dans les fichiers qui ont en vraiment besoin

?>
