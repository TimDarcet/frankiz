<?php
/*
	Fichier de définition de variables et constantes utiles dans tout le projet.
	C'est de ce script que sont inclus tous les fichiers inclus pour toutes les pages
	et _qui inclus du code en dehors de fonctions_ comme erreursphp.inc.php, login.inc.php,
	skin.inc.php… mais pas user.inc.php, xml.inc.php…
	
	$Log$
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
define('BASE_PHOTOS',"http://gwennoz/~pico/photos/");
define('MAIL_WEBMESTRE',"webmestre@frankiz.polytechnique.fr");

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

// Connexions aux bases mysql
require_once "mysql.inc.php";
$DB_web = new DB("frankiz","frankiz2_tmp","web","kokouije?.");
$DB_admin = new DB("frankiz","admin","web","kokouije?.");
$DB_trombino = new DB("frankiz","trombino","web","kokouije?.");

// divers fichiers inclus
require_once "global_func.inc.php";
require_once "login.inc.php";
require_once "skin.inc.php";

?>