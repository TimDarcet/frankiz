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
	Gestion des erreurs php et mysql :
	- on évite de les afficher à l'utilisateur.
	- on les affiches en haut de la page pour les webmestres, met on ne les fait pas apparaître
	  dans la sortie XML.
	- affichage des requètes mysql en commentaire dans
	
	$Id$
	
*/

error_reporting(E_ERROR|E_CORE_ERROR|E_COMPILE_ERROR|E_PARSE);
	// TODO actuellement on n'arrive pas à récupérer ces erreurs, donc on les affiches quand
	// même à l'utilisateur.

set_error_handler("ajouter_erreur_php");

$_ERREURS_PHPMYSQL = array();	// contient des : array('errname'=>"",'errmsg'=>"",'file'=>"",'line'=>"",'query'=>"")
$_ERREUR_FATAL = false;			// indique si une erreur fatale est survenue, si c'est le cas on affiche pas la page
								// pour éviter d'afficher une demi page qui fera de toute façon planter le xslt.
$_DEBUG_LOG = array();			// contient des : string.

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
	E_STRICT			=> "Not strict"		// E_STRICT, PHP 5 uniquement
);

// Ajout d'une erreur dans la liste des erreurs
function ajouter_erreur_php($errno, $errmsg, $file, $line, $vars) {
	global $_ERREURS_PHPMYSQL, $_ERREURS_PHP_NOMS, $_ERREUR_FATAL;
	$_ERREURS_PHPMYSQL[] = array(
		'errname'	=> "PHP {$_ERREURS_PHP_NOMS[$errno]}",
		'errmsg'	=> $errmsg,
		'file'		=> $file,
		'line'		=> $line,
		'query'		=> $vars
	);
	$_ERREUR_FATAL = $_ERREUR_FATAL || ($errno!=E_NOTICE && $errno<E_USER_NOTICE);
}

function ajouter_erreur_mysql($query) {
	global $_ERREURS_PHPMYSQL,$_ERREUR_FATAL;
	$_ERREURS_PHPMYSQL[] = array(
		'errname'	=> "MYSQL Error ",
		'errmsg'	=> mysql_error()." (".mysql_errno().")",
		'file'		=> "",
		'line'		=> "",
		'query'		=> $query
	);
	$_ERREUR_FATAL = true;
}

// Ajout dans les logs de débogage
function ajouter_debug_log($string) {
	global $_DEBUG_LOG;
	$_DEBUG_LOG[] = $string;
}

// Ajout dans les logs d'accès. Ces logs sont conservés dans la version en prod du site.
function ajouter_access_log($string) {
	$file = fopen(LOG_ACCESS, 'a');
	fwrite($file, "[".date("d/m/Y H:i:s",time())."] ".$string."\n");
	fclose($file);
}

// Affichage des erreurs
function affiche_erreurs_php() {
	global $_ERREURS_PHPMYSQL,$_DEBUG_LOG,$_ERREUR_FATAL;
	
	if(AFFICHER_LES_ERREURS) {
		foreach($_ERREURS_PHPMYSQL as $erreur)
			echo '<p><b>'.$erreur['errname'].'</b> : '.$erreur['errmsg']
				. (!empty($erreur['query']) ? ' in query <b>"'.$erreur['query'].'"</b>' : '')
 				. (!empty($erreur['file']) ? ' in <b>'.$erreur['file'].'</b> on line <b>'.$erreur['line'].'</b>' : '')
				. '</p>\n';
	} else if($_ERREUR_FATAL) {
		// On log l'erreur
		$timestamp = time();
		$message = "==================================================\n"
				 . "Date: ".date("d/m/Y H:i:s",$timestamp)." - $timestamp\n"
				 . "URL: {$_SERVER['REQUEST_URI']}\n"
				 . "Client: {$_SERVER['HTTP_USER_AGENT']}\n\n";
		
		
		foreach($_ERREURS_PHPMYSQL as $erreur)
				$message .= "{$erreur['errname']} : {$erreur['errmsg']}"
						  . (empty($erreur['query']) ? " in query \"{$erreur['query']}\"" : "")
						  . (empty($erreur['file']) ? " in {$erreur['file']} on line {$erreur['line']}" : "")
						  . "\n";
		
		// On stocke le message dans un fichier de log
		$file = fopen(LOG_ERROR, 'a');
		fwrite($file, $message);
		fclose($file);
		
		if($_ERREUR_FATAL) {
			echo "<warning>Une erreur inconnue est survenue.\n"
				." Pour informer le Webmestre de cette erreur et expliquer la manipulation qui l'a déclenchée,"
				." cliquez <a href=\"mailto:".MAIL_WEBMESTRE."?Subject=%5BFrankiz%20Erreur%20$timestamp%5D%20\">ici</a>.</warning>\n";
			exit;
		}
	}
}

function affiche_debug_php() {
	global $_DEBUG_LOG;
	if(AFFICHER_LES_ERREURS) {
		foreach($_DEBUG_LOG as $entree) {
			echo "<!-- ".$entree." -->\n";
		}
	}
}
?>
