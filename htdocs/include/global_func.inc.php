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

// Demande la confirmation lors d'une suppression d'une entrée !
// (c'est plus secure)
function suppression() {
	global $HTTP_POST_VARS;
	$post = "?" ;
	foreach($_POST as $key=>$val) {
 		//$post .=  $key."=".$val.""; 
 	}
	echo "<warning>" ;
	echo "<b>ATTENTION</b> : Vous voulez supprimer des entrées" ;
	echo "</warning>" ;
	return false ;
	
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

/*
	Autres
*/
function nouveau_hash() {
    $fp = fopen('/dev/urandom', 'r');
    $hash = md5(fread($fp, 16));
    fclose($fp);
    return $hash;
}

function rediriger_vers($page) {
	header("Location: ".BASE_URL.$page);
	echo "<p>Si ton navigateur n'est pas automatiquement redirigé, <a href=\"".BASE_URL.$page."\">cliques ici</a>.</p>";
	exit;
}
?>