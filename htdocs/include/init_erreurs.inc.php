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
	Gestion des erreurs php et mysql�:
	- on �vite de les afficher � l'utilisateur.
	- on les affiches en haut de la page pour les webmestres, met on ne les fait pas appara�tre
	  dans la sortie XML.
	- affichage des requ�tes mysql en commentaire dans
	
	$Log$
	Revision 1.6  2005/02/08 21:57:56  pico
	Correction bug #62

	Revision 1.5  2004/12/16 16:45:14  schmurtz
	Correction d'un bug dans la gestion des authentifications par cookies
	Ajout de fonctionnalitees de log d'erreur de connexions ou lors des bugs
	affichant une page "y a un bug, contacter l'admin"
	
	Revision 1.4  2004/12/14 14:18:12  schmurtz
	Suppression de la page de doc wiki : doc directement dans les pages concernees.
	
	Revision 1.3  2004/11/29 17:27:32  schmurtz
	Modifications esthetiques.
	Nettoyage de vielles balises qui trainaient.
	
	Revision 1.2  2004/11/27 20:16:55  pico
	Eviter le formatage dans les balises <note> <commentaire> et <warning> lorsque ce n'est pas necessaire
	
	Revision 1.1  2004/11/25 00:44:35  schmurtz
	Ajout de init_ devant les fichier d'include servant d'initialisation de page
	Permet de mieux les distinguer des autres fichiers d'include ne faisant que definir
	des fonctions.
	
	Revision 1.11  2004/11/16 14:54:12  schmurtz
	Affichage des erreurs "Parse Error"
	permet de loguer des infos autre que les commandes SQL (pour debugage)
	
	Revision 1.10  2004/11/02 20:41:22  kikx
	Code d'erreur mysql
	
	Revision 1.9  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.8  2004/09/17 11:00:07  schmurtz
	Bug dans l'affichage des erreurs
	
	Revision 1.7  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.6  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

error_reporting(E_ERROR|E_CORE_ERROR|E_COMPILE_ERROR|E_PARSE);
	// TODO actuellement on n'arrive pas � r�cup�rer ces erreurs, donc on les affiches quand
	// m�me � l'utilisateur.

set_error_handler("ajouter_erreur_php");

$_ERREURS_PHPMYSQL = array();	// contient des�: array('errname'=>"",'errmsg'=>"",'file'=>"",'line'=>"",'query'=>"")
$_ERREUR_FATAL = false;			// indique si une erreur fatale est survenue, si c'est le cas on affiche pas la page
								// pour �viter d'afficher une demi page qui fera de toute fa�on planter le xslt.
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
	2048				=> "Not strict"		// E_STRICT, PHP 5 uniquement
);

// Ajout d'une erreur dans la liste des erreurs
function ajouter_erreur_php($errno, $errmsg, $file, $line, $vars) {
	global $_ERREURS_PHPMYSQL, $_ERREURS_PHP_NOMS, $_ERREUR_FATAL;
	$_ERREURS_PHPMYSQL[] = array(
		'errname'	=> "PHP {$_ERREURS_PHP_NOMS[$errno]}",
		'errmsg'	=> $errmsg,
		'file'		=> $file,
		'line'		=> $line,
		'query'		=> ""
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

// Ajout dans les logs de d�bogage
function ajouter_debug_log($string) {
	global $_DEBUG_LOG;
	$_DEBUG_LOG[] = $string;
}

// Ajout dans les logs d'acc�s. Ces logs sont conserv�s dans la version en prod du site.
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
			echo "<p><b>{$erreur['errname']}</b> : {$erreur['errmsg']}"
				. (!empty($erreur['query']) ? " in query <b>\"{$erreur['query']}\"</b>" : "")
 				. (!empty($erreur['file']) ? " in <b>{$erreur['file']}</b> on line <b>{$erreur['line']}</b>" : "")
				. "</p>\n";

		foreach($_DEBUG_LOG as $entree)
			echo "<!-- $entree -->\n";
	
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
				." Pour informer le Webmestre de cette erreur et expliquer la manipulation qui l'a d�clench�e,"
				." cliquez <a href=\"mailto:".MAIL_WEBMESTRE."?Subject=%5BFrankiz%20Erreur%20$timestamp%5D%20\">ici</a>.</warning>\n";
			exit;
		}
	}
}

?>
