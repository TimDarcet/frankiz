<?php
/*
	Divers fonctions pouvant �tre utile dans n'importe quelles pages.
	Pas de fonctionnalit�s sp�cifiques � quelques pages.

	$Log$
	Revision 1.10  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")

	Revision 1.9  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

/*
	Affiche un identifiant � la place de la valeur et stocke la valeur dans un tableau.
	L'utilisation de cette fonction est indipensable pour afficher du texte tel quel, sans
	qu'il soit modifi� par l'application du fichier de skin XSL.
*/

function afficher_identifiant($valeur) {
	global $donnees, $identifiant;  // $donnees contient tous les identifiants,
	                                // d�fini dans page_header.inc.php

	$identifiant ++;		// Incrementation du numero d'identification
	$donnees["^$identifiant$"] = $valeur;	// Enregistrement de la clef
	return "^$identifiant$";
}

/*
	Affiche une page demandant la confirmation lors de la suppression d'une entr�e !
	(c'est plus secure)
	TODO � terminer
*/

function suppression() {
	$post = "?" ;
	foreach($_POST as $key=>$val) {
 		//$post .=  $key."=".$val.""; 
 	}
	echo "<warning>" ;
	echo "<b>ATTENTION</b> : Vous voulez supprimer des entr�es" ;
	echo "</warning>" ;
	return false ;
	
}

/*
	Gestion des erreurs dans les formulaires
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
	Cr�e un hash al�atoire de 16 caract�res.
*/
function nouveau_hash() {
    $fp = fopen('/dev/urandom', 'r');
    $hash = md5(fread($fp, 16));
    fclose($fp);
    return $hash;
}

/*
	Envoi les donn�es n�cessaire pour faire une redirection vers la page donn�e.
	Arr�te l'ex�cution du code PHP.
*/
function rediriger_vers($page) {
	header("Location: ".BASE_URL.$page);
	echo "<p>Si ton navigateur n'est pas automatiquement redirig�, <a href=\"".BASE_URL.$page."\">cliques ici</a>.</p>";
	exit;
}

/*
	Renvoi la liste des modules disponibles sous la forme d'une liste�:
		"nom du fichier moins le .php" => "Nom affichable du module"
	
	Si le nom affichage est vide, cela signifie que le module est toujours visible.
*/
function liste_modules() {
	return array(
		"css"				=> "",
		"liens_navigation"	=> "",
		"liens_contacts"	=> "",
		"liens_ecole"		=> "Liens �cole",
		"qdj"				=> "Question du jour",
		"qdj_hier"			=> "Question de la veille",
		"activites"			=> "Activit�s",
		"tour_kawa"			=> "Tours kawa",
		"anniversaires"		=> "Anniversaires",
		"stats"				=> "Statistiques");
}

?>
