<?php
/*
	$Id$
	
	Page permettant de modifier son profil dans le trombino et quelques paramètres
	pour le site web.
	
	TODO modification de sa photo et de ses binets.
	
	$Log$
	Revision 1.8  2004/09/15 21:42:21  schmurtz
	Commentaires et ajout de la variable cvs "Log"

*/

require_once "../include/global.inc.php";
demande_authentification(AUTH_MAIL);

$message_succes="";


// Données sur l'utilisateur
$DB_trombino->query("SELECT eleves.nom,prenom,surnom,mail,login,promo,sections.nom,cie,piece_id FROM eleves INNER JOIN sections USING(section_id) WHERE eleve_id='".$_SESSION['user']->uid."'");
list($nom,$prenom,$surnom,$mail,$login,$promo,$section,$cie,$casert) = $DB_trombino->next_row();

// Modification du mot de passe
if(isset($_POST['changer_mdp'])) {
	if($_POST['passwd'] != $_POST['passwd2']) {
		ajoute_erreur(ERR_MDP_DIFFERENTS);
	} else if(strlen($_POST['passwd']) < 8) {
		ajoute_erreur(ERR_MDP_TROP_PETIT);
	} else {
		$DB_web->query("UPDATE compte_frankiz SET passwd='".md5($_POST['passwd'])."' "
				   ."WHERE eleve_id='".$_SESSION['user']->uid."' ");
		$message_succes="Le mot de passe vient d'être changé.";
	}

// Modification du cookie d'authentification
} else if(isset($_POST['changer_cookie'])) {
	if($_POST['cookie'] == 'oui') {
		// on rajoute le cookie
		$cookie = array('hash'=>nouveau_hash(),'uid'=>$_SESSION['user']->uid);
		$DB_web->query("UPDATE compte_frankiz SET hash='".$cookie['hash']."' "
				   ."WHERE eleve_id='".$_SESSION['user']->uid."' ");
		SetCookie("auth",base64_encode(serialize($cookie)),time()+3*365*24*3600,"/");
		$_COOKIE['auth'] = "blah";  // hack permetttant de faire marcher le test d'existance du cookie
									// utilisé quelques ligne plus bas sans devoir recharger la page.
	} else {
		// on supprime le cookie
		SetCookie("auth","",0,"/");
		unset($_COOKIE['auth']);	// hack, cf. au dessus.
	}

// Modification de la fiche du trombino
} else if(isset($_POST['changer_trombino'])) {
	if(strlen($_POST['surnom']) < 2 && !empty($_POST['surnom']))
		ajoute_erreur(ERR_SURNOM_TROP_PETIT);
		
	if($_POST['email'] == "$login@poly" || $_POST['email'] == "$login@poly.polytechnique.fr")
		$_POST['email'] = "";
	if(!ereg("^[a-zA-Z0-9_+.-]+@[a-zA-Z0-9.-]+$",$_POST['email']) && !empty($_POST['email']))
		ajoute_erreur(ERR_EMAIL_NON_VALIDE);
	
	if(aucune_erreur()) {
		$surnom = $_POST['surnom'];
		$mail = $_POST['email'];
		$DB_trombino->query("UPDATE eleves SET surnom='$surnom',mail=".(empty($mail)?"NULL":"'$mail'")." WHERE eleve_id='".$_SESSION['user']->uid."'");
		$message_succes="L'email et le surnom ont été modifié.";
	}
}


// Génération du la page XML
require "../include/page_header.inc.php";

?>
<page id="profil" titre="Frankiz : modification du profil">
	<h1>Modification de son profil</h1>
<?php
		if(!empty($message_succes))
			echo "<p>$message_succes</p>\n";
		if(a_erreur(ERR_MDP_DIFFERENTS))
			echo "<p>Les valeurs des deux champs n'étaient pas identiques.</p>\n";
		if(a_erreur(ERR_MDP_TROP_PETIT))
			echo "<p>Il faut mettre un mot de passe plus long (au moins 8 caractères).</p>\n";
		if(a_erreur(ERR_SURNOM_TROP_PETIT))
			echo "<p>Il faut mettre un surnom plus long (au moins 2 caractères).</p>\n";
		if(a_erreur(ERR_EMAIL_NON_VALIDE))
			echo "<p>L'email n'est pas valide.</p>\n";
?>
	<formulaire id="mod_mdp" titre="Changement de mot de passe" action="profil/profil.php">
		<champ id="passwd" titre="Mot de passe" valeur=""/>
		<champ id="passwd2" titre="Retaper le mot de passe" valeur=""/>
		<bouton id="changer_mdp" titre="Changer"/>
	</formulaire>
	
	<formulaire id="mod_cookie" titre="Cookie d'authentification" action="profil/profil.php">
		<choix id="cookie" titre="Utiliser l'authentification par cookie" type="combo"
				valeur="<?php echo empty($_COOKIE['auth'])? 'non' : 'oui' ?>">
			<option titre="Activé" id="oui"/>
			<option titre="Désactivé" id="non"/>
		</choix>
		<bouton id="changer_cookie" titre="Changer"/>
	</formulaire>
	
	<formulaire id="mod_trombino" titre="Changement de la fiche trombino" action="profil/profil.php">
		<champ id="nom" titre="Nom" valeur="<?php echo $nom.' '.$prenom ?>" modifiable="non"/>
		<champ id="loginpoly" titre="Login poly" valeur="<?php echo $login ?>" modifiable="non"/>
		<champ id="promo" titre="Promo" valeur="<?php echo $promo ?>" modifiable="non"/>
		<champ id="section" titre="Section" valeur="<?php echo $section.' (compagnie '.$cie.')' ?>" modifiable="non"/>
		<champ id="casert" titre="Kazert" valeur="<?php echo $casert ?>" modifiable="non"/>
		<champ id="surnom" titre="Surnom" valeur="<?php echo $surnom ?>"/>
		<champ id="email" titre="Email" valeur="<?php echo empty($mail) ? $login.'@poly.polytechnique.fr' : $mail ?>"/>
		<bouton id="changer_trombino" titre="Changer"/>
	</formulaire>
</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
