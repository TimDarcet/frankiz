<?php
/*
	Copyright (C) 2004 Binet R�seau
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
	Fichier de d�finition de variables et constantes utiles dans tout le projet.
	C'est de ce script que sont inclus tous les fichiers inclus pour toutes les pages
	et _qui inclus du code en dehors de fonctions_ comme erreursphp.inc.php, login.inc.php,
	skin.inc.php mais pas user.inc.php, xml.inc.php
	
	$Log$
	Revision 1.36  2004/11/24 12:51:02  kikx
	Pour commencer la compatibilit� wiki

	Revision 1.35  2004/11/09 19:42:15  pico
	Passage de tous les param�tres de conf dans etc/config.php
	
	
*/

// D�finition g�n�rique des diff�rentes bases pour l'acc�s aux fichiers
foreach(Array('.', '..', '../..', '../../..') as $dir)
if(file_exists("$dir/frankiz.dtd"))
	$href = $dir;

define('BASE_LOCAL',realpath(dirname(__FILE__)."/.."));
define('BASE_URL','http://'.$_SERVER['HTTP_HOST'].'/'.substr((dirname($_SERVER['PHP_SELF']).'/'.$href), 1));

// Connexions aux bases mysql
require_once "mysql.inc.php";

require_once BASE_LOCAL."/../etc/config.php";

// Gestion des erreurs PHP et MySQL
// Il est important d'inclure ce fichier le plus t�t possible, mais comme il a besoin
// des param�tres du site on ne l'inclu que maintenant.
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

// Nettoyage des �l�ments r�cup�r�s par $_POST, $_GET et $_REQUEST
function nettoyage_balise($tableau) {
	foreach($tableau as $cle => $valeur)
		if(is_array($valeur)) $tableau[$cle] = nettoyage_balise($valeur);
		else $tableau[$cle] = str_replace(array('&','<','>','\'','"','\\'),array('&amp;','&lt;','&gt;','&apos;','&quot;',''),$valeur);
	return $tableau;
}

$_GET = nettoyage_balise($_GET);
$_POST = nettoyage_balise($_POST);
$_REQUEST = nettoyage_balise($_REQUEST);

// divers fichiers inclus
require_once "global_func.inc.php";
require_once "mail.inc.php";
require_once "login.inc.php";
require_once "skin.inc.php";
require_once "compress.inc.php";
require_once "wiki.inc.php";

?>
