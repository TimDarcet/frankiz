<?php
/*
	Fichier de définition de variables et constantes utiles dans tout le projet.
	
	$Id$
*/

// base des fichiers serveur
define('BASE_LOCAL',realpath(dirname(__FILE__)."/.."));

// base des fichiers par le site web
foreach(Array('.', '..', '../..', '../../..') as $dir)
if(file_exists("$dir/frankiz.dtd"))
	$href = $dir;

// on recupere le chemin absolu (spa tres propre m'enfin...)
$path = substr((dirname($_SERVER['PHP_SELF']).'/'.$href), 1);
define('BASE_URL','http://'.$_SERVER['HTTP_HOST'].'/'.$path);

// chemins locaux utiles
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

// divers fichiers inclus
require_once "global_func.inc.php";
require_once "login.inc.php";
require_once "skin.inc.php";
?>