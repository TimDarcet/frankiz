<?php
/*
	Gestion de la création d'un compte et de la perte de mot de passe.
*/

require "../include/global.inc.php";

$mail_envoye = false;

if(!empty($_REQUEST['loginpoly'])) {
	connecter_mysql_frankiz();
	$resultat = mysql_query("SELECT eleve_id,login,prenom,nom,promo FROM eleves "
						   ."WHERE login='".$_REQUEST['loginpoly']."' ORDER BY promo DESC LIMIT 1");
	if(mysql_num_rows($resultat) == 1) {
		list($id,$login,$prenom,$nom,$promo) = mysql_fetch_row($resultat);
		mysql_free_result($resultat);
		$hash = nouveau_hash();
		
		// Si le compte existe déjà on met à jour le hash, sinon on crée le compte
		//mysql_query("INSERT INTO compte_frankiz SET eleve_id='$id',passwd='',perms='',hash='$hash',hashstamp=NOW()+3600*6 "
		//		   ."ON DUPLICATE KEY UPDATE hash='$hash',hashstamp=NOW()+3600*6");
		// (MySQL 4.1 uniquement)
		$resultat = mysql_query("SELECT 0 FROM compte_frankiz WHERE eleve_id='$id'");
		if(mysql_num_rows($resultat) > 0)
			mysql_query("UPDATE compte_frankiz SET hash='$hash',hashstamp=DATE_ADD(NOW(),INTERVAL 6 HOUR) WHERE eleve_id='$id'");
		else
			mysql_query("INSERT INTO compte_frankiz SET eleve_id='$id',passwd='',perms='',hash='$hash',hashstamp=NOW()+3600*6");
		mysql_free_result($resultat);
		
		// Envoie le mail contenant l'url avec le hash
		$contenu = "Pour te connecter sur Frankiz, il te suffit de cliquer sur le\n".
				   "lien ci-dessous :\n\n".
				   "	[ ".$_SERVER['SERVER_NAME']."/profil/profil.php?uid=${id}&hash=${hash} ]\n\n".
				   "N'oublie pas ensuite de modifier ton mot de passe.";
		mail("$nom $prenom <${login}@poly.polytechnique.fr>","[Frankiz] Création de compte/perte de mot de passe",$contenu);
		$mail_envoye = true;
		
	} else {
		mysql_free_result($resultat);
		ajoute_erreur(ERR_LOGINPOLY);
	}
	deconnecter_mysql_frankiz();
}

require "../include/page_header.inc.php";
echo "<page id='mdp_perdu' titre='Frankiz : creation de compte/perte de mot de passe'>\n";

if($mail_envoye) { ?>
	<p>Le mail a été envoyé avec succès à l'adresse <?php echo $login?>@poly.polytechnique.fr.
	Il te permettra de te connecter une fois au site web Frankiz pour changer ton mot de passe
	ou choisir ton mot de passe si tu n'en a pas encore défini un.</p>
	
<?php } else { ?>
	<?php if(a_erreur(ERR_LOGINPOLY)) echo "<p>Le login que tu a donné n'existe pas.</p>\n"?>
	<formulaire id="mdp_perdu" titre="Perte de mot de passe/ouverture de compte" action="profil/mdp_perdu.php">
		<commentaire>Si tu souhaites créer ton compte Frankiz, ou si tu as perdu ton mot de passe, entre ton
		login poly dans le champs si dessous. Tu receveras dans les minutes qui suivent un mail
		te permettant d'accéder à la partie réservée de Frankiz. Une fois authentifié grâce
		au lien contenu dans le mail, n'oublie pas de changer ton mot de passe.</commentaire>
		<champ id="loginpoly" titre="Login poly" valeur=""/>
		<bouton id="valider" titre="Valider"/>
	</formulaire>
<?php }

echo "</page>\n";
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
