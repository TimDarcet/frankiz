<?php
/*
	Divers fonctions pouvant �tre utile dans n'importe quelles pages.
	Pas de fonctionnalit�s sp�cifiques � quelques pages.

	$Log$
	Revision 1.13  2004/09/17 17:24:20  kikx
	Creation d'un couriel() qui permet d'envoyer des mail

	Revision 1.12  2004/09/17 15:27:08  schmurtz
	Suppression de la fonction suppression qui ne sert pas.
	
	Revision 1.11  2004/09/16 15:32:56  schmurtz
	Suppression de la fonction afficher_identifiant(), utilisation de <![CDATA[......]]> à la place.
	
	Revision 1.10  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.9  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

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
	envoie un mail
*/
function couriel($eleve_id,$titre,$contenu) {
	$DB_trombino->query("SELECT nom,prenom,mail,login FROM eleves WHERE eleve_id='$eleve_id'") ;
	list($nom, $prenom, $mail, $login) = $DB_valid->next_row())  ;
	if (empty($mail)) $mail=$login."@poly.polytechnique.fr" ;
	mail("$prenom $nom <$mail>",$titre,$contenu) ;
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
