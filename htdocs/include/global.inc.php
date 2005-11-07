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
	
	$Id$

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
define('ERR_SURNOM_TROP_GRAND',$i++);
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
