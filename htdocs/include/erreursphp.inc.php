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

$_ERREURS_PHPMYSQL = array();
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
	global $_ERREURS_PHPMYSQL, $_ERREURS_PHP_NOMS;
	$_ERREURS_PHPMYSQL[] = array(
		'errname'	=> "PHP {$_ERREURS_PHP_NOMS[$errno]}",
		'errmsg'	=> $errmsg,
		'file'		=> $file,
		'line'		=> $line
	);
}

function affiche_erreurs_php() {
	global $_ERREURS_PHPMYSQL;
	if( count($_ERREURS_PHPMYSQL) == 0 ) return;
	
	foreach($_ERREURS_PHPMYSQL as $erreur)
		echo "<p><b>{$erreur['errname']}</b> : {$erreur['errmsg']} in <b>{$erreur['file']}</b> on line <b>{$erreur['line']}</b></p>\n";
}

?>