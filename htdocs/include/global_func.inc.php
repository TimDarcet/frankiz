<?php
/*
	Divers fonctions pouvant être utile dans n'importe quelles pages.
	
	$Id$
*/

/*
	Affiche un identifiant à la place de la valeur et stocke la valeur dans un tableau.
	L'utilisation de cette fonction est indipensable pour afficher du texte tel quel, sans
	qu'il soit modifié par l'application du fichier de skin XSL.
*/


function afficher_identifiant($valeur) {
	global $donnees, $identifiant;  // $donnees contient tous les identifiants,
	                                // défini dans page_header.inc.php

	$identifiant ++;		// Incrementation du numero d'identification
	$donnees["^$identifiant$"] = $valeur;	// Enregistrement de la clef
	return "^$identifiant$";
}

/*
	Convertion d'une adresse IP en adresse DNS sans indication de domaine.
*/

function ip2dns($ip) {
	$dns = gethostbyaddr($ip);
	$dns = ereg_replace("\.polytechnique\.fr","",$dns);
	$dns = ereg_replace("\.eleves","",$dns);
	
	return substr($dns,0,8) == "129.104." ? "Anonyme..." : $dns;
}

/*
	Accès aux bases MySQL
*/

function connecter_mysql_tol() {
	mysql_connect("heol", "web", "kouije?");
	mysql_select_db("trombinopico");
}

function deconnecter_mysql_tol() {
	mysql_close();
}

function connecter_mysql_frankiz() {
	mysql_connect("frankiz", "web", "kokouije?.");
	mysql_select_db("frankiz2_tmp");
}

function deconnecter_mysql_frankiz() {
	mysql_close();
}

/*
	Gestion des erreurs
*/

$_ERREURS = array();

function a_erreur($err) {
	global $_ERREURS;
	return isset($_ERREURS[$err]);
}

function ajoute_erreur($err) {
	global $_ERREURS;
	$_ERREURS[$err] = $err;
}

function aucune_erreur() {
	global $_ERREURS;
	return count($_ERREURS) == 0;
}

function nouveau_hash() {
    $fp = fopen('/dev/urandom', 'r');
    $hash = md5(fread($fp, 16));
    fclose($fp);
    return $hash;
}

?>