<?php
/*
	Page permettant de modifier son profil dans le trombino et quelques paramètres
	pour le site web.
*/

require_once "../include/global.inc.php";
demande_authentification(AUTH_MAIL);

$message_succes="";

connecter_mysql_frankiz();

// Modification du mot de passe
if(isset($_POST['changer_mdp'])) {
	if($_POST['passwd'] != $_POST['passwd2']) {
		ajoute_erreur(ERR_MDP_DIFFERENTS);
	} else if(strlen($_POST['passwd']) < 8) {
		ajoute_erreur(ERR_MDP_TROP_PETIT);
	} else {
		mysql_query("UPDATE compte_frankiz SET passwd='".md5($_POST['passwd'])."' "
				   ."WHERE eleve_id='".$_SESSION['user']->uid."' ");
		$message_succes="Le mot de passe vient d'être changé.";
	}

// Modification du cookie d'authentification
} else if(isset($_POST['changer_cookie'])) {
	if($_POST['cookie'] == 'oui') {
		// on rajoute le cookie
		$cookie = array('hash'=>nouveau_hash(),'uid'=>$_SESSION['user']->uid);
		mysql_query("UPDATE compte_frankiz SET hash='".$cookie['hash']."' "
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
	// TODO
}

deconnecter_mysql_frankiz();

// Génération du la page XML
require "../include/page_header.inc.php";

?>
<page id="mdp_perdu" titre="Frankiz : modification du profil">
	<h1>Modification de son profil</h1>
<?php
		if(!empty($message_succes))
			echo "<p>$message_succes</p>\n";
		if(a_erreur(ERR_MDP_DIFFERENTS))
			echo "<p>Les valeurs des deux champs n'étaient pas identiques.</p>\n";
		if(a_erreur(ERR_MDP_TROP_PETIT))
			echo "<p>Il faut mettre un mot de passe plus long (au moins 8 caractères).</p>\n";
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
		<champ id="surnom" titre="Surnom" valeur=""/>
		<champ id="email" titre="Email" valeur=""/>
		<bouton id="changer_trombino" titre="Changer"/>
	</formulaire>
</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
