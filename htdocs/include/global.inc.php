<?php
/*
	$Id$

	Fichier de définition de variables et constantes utiles dans tout le projet.
*/

require_once "erreursphp.inc.php";

// définition générique des différentes bases pour l'accès aux fichiers
foreach(Array('.', '..', '../..', '../../..') as $dir)
if(file_exists("$dir/frankiz.dtd"))
	$href = $dir;

define('BASE_LOCAL',realpath(dirname(__FILE__)."/.."));
define('BASE_URL','http://'.$_SERVER['HTTP_HOST'].'/'.substr((dirname($_SERVER['PHP_SELF']).'/'.$href), 1));
define('BASE_PHOTOS',"http://gwennoz/~pico/photos/");

// Gestion des erreurs
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