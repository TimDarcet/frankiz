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
	Revision 1.51  2005/04/13 17:10:00  pico
	Passage de tous les fichiers en utf8.

	Revision 1.50  2005/03/31 17:17:01  pico
	Frankiz exterieur
	
	Revision 1.49  2005/01/26 22:15:32  schmurtz
	Oups, maintenant c'est vraiment corrige
	
	Revision 1.48  2005/01/26 21:56:39  schmurtz
	Gestion de l'echappement des \
	
	Revision 1.47  2005/01/25 14:53:43  pico
	Modifications relatives à la version de prod, à l'accès extérieur, tout ça...
	
	Revision 1.46  2004/12/02 21:26:23  pico
	Base URL: distinction pour éviter les http://frankiz//index.php
	
	Revision 1.45  2004/11/26 16:12:47  pico
	La Faq utilise la $DB_faq au lieu de $DB_web
	
	Revision 1.44  2004/11/25 01:33:45  schmurtz
	re
	
	Revision 1.43  2004/11/25 01:31:55  schmurtz
	Pour debuguer
	
	Revision 1.42  2004/11/25 00:44:35  schmurtz
	Ajout de init_ devant les fichier d'include servant d'initialisation de page
	Permet de mieux les distinguer des autres fichiers d'include ne faisant que definir
	des fonctions.
	
	Revision 1.41  2004/11/24 22:56:18  schmurtz
	Inclusion de wiki.inc.php par les fichiers concerne uniquement et non de facon
	globale pour tous les fichiers.
	
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
$https = ($_SERVER['HTTPS']=='on')? 'https':'http';
define('BASE_LOCAL',realpath(dirname(__FILE__)."/.."));
// Base URL: distinction pour éviter les http://frankiz//index.php
if($_SERVER["HTTP_X_FORWARDED_HOST"]=="www.polytechnique.fr")
        define('BASE_URL',$https.'://www.polytechnique.fr/eleves');
else if(substr((dirname($_SERVER['PHP_SELF'])), 1))
	define('BASE_URL',$https.'://'.$_SERVER['HTTP_HOST'].'/'.substr((dirname($_SERVER['PHP_SELF']).'/'.$href), 1));
else
	define('BASE_URL',$https.'://'.$_SERVER['HTTP_HOST'].'/'.$href);



// Connexions aux bases mysql
require_once "mysql.inc.php";

// Gestion des erreurs PHP et MySQL
// Il est important d'inclure ce fichier le plus tôt possible, mais comme il a besoin
// des paramètres du site on ne l'inclu que maintenant.
require_once "init_erreurs.inc.php";	// TODO : mettre avant l'ouverture des connexions aux bases mysql

require_once BASE_LOCAL."/../etc/config.php";


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
		else $tableau[$cle] = str_replace(array('&','<','>','\\\'','\\"','\\\\'),array('&amp;','&lt;','&gt;','&apos;','&quot;','&#92;'),$valeur);
// Si l'échappement automatique de PHP n'est pas activé :
//		else $tableau[$cle] = str_replace(array('&','<','>','\'','"','\\'),array('&amp;','&lt;','&gt;','&apos;','&quot;','&#92;'),$valeur);
	return $tableau;
}

$_GET = nettoyage_balise($_GET);
$_POST = nettoyage_balise($_POST);
$_REQUEST = nettoyage_balise($_REQUEST);

// divers fichiers inclus pour effectuer des actions avant l'affichage de la page
require_once "global_func.inc.php";
require_once "mail.inc.php";
require_once "init_login.inc.php";
require_once "init_skin.inc.php";

ajouter_debug_log(var_export($_GET,true));
ajouter_debug_log(var_export($_POST,true));
ajouter_debug_log(var_export($_COOKIE,true));
ajouter_debug_log(var_export($_SESSION,true));
?>
