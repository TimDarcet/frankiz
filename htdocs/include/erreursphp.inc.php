<?php
/*
	$Id$
	
	Gestion des erreurs php :
	- on évite de les afficher à l'utilisateur.
	- on les affiches en haut de la page pour les webmestres, met on ne les fait pas apparaître
	  dans la sortie XML.
*/

error_reporting(E_ERROR);
set_error_handler("gestionnaire_erreurs_php");

$_ERREURS_PHP = array();
$_ERREURS_PHP_NOMS = array(
	E_ERROR				=> "Fatal error",
	E_WARNING			=> "Warning",
	E_PARSE				=> "Parse error",
	E_NOTICE			=> "Notice",
	E_CORE_ERROR		=> "Core error",
	E_CORE_WARNING		=> "Core warning",
	E_COMPILE_ERROR		=> "Compile error",
	E_COMPILE_WARNING	=> "Compile warning",
	E_USER_ERROR		=> "User error",
	E_USER_WARNING		=> "User warning",
	E_USER_NOTICE		=> "User notice",
	2048				=> "Not strict"		// E_STRICT, PHP 5 uniquement
);

function gestionnaire_erreurs_php($errno, $errmsg, $file, $line, $vars) {
	global $_ERREURS_PHP;
	$_ERREURS_PHP[] = array(
		'errno'		=> $errno,
		'errmsg'	=> $errmsg,
		'file'		=> $file,
		'line'		=> $line,
		'vars'		=> $vars
	);
}

function affiche_erreurs_php() {
	global $_ERREURS_PHP, $_ERREURS_PHP_NOMS;
	if( count($_ERREURS_PHP) == 0 ) return;
	
	foreach($_ERREURS_PHP as $erreur)
		echo "<p><b>{$_ERREURS_PHP_NOMS[$erreur['errno']]}</b> : {$erreur['errmsg']} in <b>{$erreur['file']}</b> on line <b>{$erreur['line']}</b></p>\n";
}

?>