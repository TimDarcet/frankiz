<?php
/*
	$Id$
	
	Gestion des erreurs php :
	- on évite de les afficher à l'utilisateur.
	- on les affiches en haut de la page pour les webmestres, met on ne les fait pas apparaître
	  dans la sortie XML.
*/

error_reporting(0);
set_error_handler("gestionnaire_erreurs_php");

$_ERREURS_PHP = array();
$_ERREURS_PHP_NOMS = array(
	E_ERROR				=> "FATAL ERROR",
	E_WARNING			=> "WARNING",
	E_PARSE				=> "PARSE ERROR",
	E_NOTICE			=> "NOTICE",
	E_CORE_ERROR		=> "CORE FATAL ERROR",
	E_CORE_WARNING		=> "CORE WARNING",
	E_COMPILE_ERROR		=> "COMPILE FATAL ERROR",
	E_COMPILE_WARNING	=> "COMPILE WARNING",
	E_USER_ERROR		=> "USER ERROR",
	E_USER_WARNING		=> "USER WARNING",
	E_USER_NOTICE		=> "USER NOTICE",
	2048				=> "NOT STRICT"		// E_STRICT, PHP 5 uniquement
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
	
	if($errno == E_ERROR || $errno == E_CORE_ERROR || $errno == E_COMPILE_ERROR || $errno == E_USER_ERROR) {
		affiche_erreurs_php();
		exit(1);
	}
}

function affiche_erreurs_php() {
	global $_ERREURS_PHP, $_ERREURS_PHP_NOMS;
	if( count($_ERREURS_PHP) == 0 ) return;
	
	foreach($_ERREURS_PHP as $erreur)
		echo "<p><b>PHP {$_ERREURS_PHP_NOMS[$erreur['errno']]}</b> in file {$erreur['file']} line {$erreur['line']} : {$erreur['errmsg']}</p>\n";
}

?>