<?php
/*
	Page permettant de modifier son profil dans le trombino et quelques paramètres
	pour le site web.
	
	TODO modification de sa photo et de ses binets.
	
	$Log$
	Revision 1.13  2004/10/20 19:51:17  kikx
	Structure pour se rajouter des binets

	Revision 1.12  2004/10/20 18:47:07  kikx
	Pour rajouter des lignes non selectionnables dans une liste
	
	Revision 1.11  2004/10/20 11:02:10  kikx
	Permet la suppression des binets dans son profil
	
	Revision 1.10  2004/10/19 20:22:04  schmurtz
	Rajout de messages de reussite dans la page profil
	Fusion des formulaires changement de mot de passe et activation du cookie
	
	Revision 1.9  2004/09/15 23:20:07  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.8  2004/09/15 21:42:21  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

require_once "../include/global.inc.php";
demande_authentification(AUTH_MAIL);

$message_succes="";

// Données sur l'utilisateur
$DB_trombino->query("SELECT eleves.nom,prenom,surnom,mail,login,promo,sections.nom,cie,piece_id FROM eleves INNER JOIN sections USING(section_id) WHERE eleve_id='".$_SESSION['user']->uid."'");
list($nom,$prenom,$surnom,$mail,$login,$promo,$section,$cie,$casert) = $DB_trombino->next_row();

if(isset($_POST['changer_frankiz'])) {
	// Modification du mot de passe
	if($_POST['passwd']=='12345678' && $_POST['passwd2']=='87654321' || empty($_POST['passwd']) && empty($_POST['passwd2'])) {
		// ne rien faire, on garde l'ancien mot de passe
	} else if($_POST['passwd'] != $_POST['passwd2']) {
		ajoute_erreur(ERR_MDP_DIFFERENTS);
	} else if(strlen($_POST['passwd']) < 8) {
		ajoute_erreur(ERR_MDP_TROP_PETIT);
	} else {
		$DB_web->query("UPDATE compte_frankiz SET passwd='".md5($_POST['passwd'])."' "
				   ."WHERE eleve_id='".$_SESSION['user']->uid."' ");
		$message_succes="Le mot de passe vient d'être changé.";
	}

	// Modification du cookie d'authentification
	if($_POST['cookie'] == 'oui') {
		// on rajoute le cookie
		$cookie = array('hash'=>nouveau_hash(),'uid'=>$_SESSION['user']->uid);
		$DB_web->query("UPDATE compte_frankiz SET hash='".$cookie['hash']."' "
				   ."WHERE eleve_id='".$_SESSION['user']->uid."' ");
		SetCookie("auth",base64_encode(serialize($cookie)),time()+3*365*24*3600,"/");
		$_COOKIE['auth'] = "blah";  // hack permetttant de faire marcher le test d'existance du cookie
									// utilisé quelques ligne plus bas sans devoir recharger la page.
		$message_succes.="Le cookie d'authentification a été activé.";
	} else {
		// on supprime le cookie
		SetCookie("auth","",0,"/");
		unset($_COOKIE['auth']);	// hack, cf. au dessus.
		$message_succes.="Le cookie d'authentification a été désactivé.";
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
// Modification de la partie "binets"

if (isset($_POST['mod_binet'])) {
	//$commentaire = $_POST['commentaire'];

	foreach($_POST as $key=>$val) {
		if ($key == "mod_binet") continue ;
		$key = explode("_",$key) ;
		$key = $key[1] ;
		$DB_trombino->query("UPDATE membres SET remarque='$val' WHERE eleve_id={$_SESSION['user']->uid} AND binet_id=$key");
 	}
	$message_succes =  "Modification de la partie binets faite avec succès" ;
}

// Modification de la partie "binets"

if (isset($_POST['suppr_binet'])) {
	$count =0 ;
	if (isset($_POST['elements'])) {
		$ids = "" ;
		foreach($_POST['elements'] as $id => $on) {
			if($on='on') $ids .= (empty($ids) ? "" : ",") . "'$id'";
			$count ++ ;
		}
	}
	if ($count>=1) {
		mysql_query("DELETE FROM membres WHERE binet_id IN ($ids) AND  eleve_id={$_SESSION['user']->uid}");
		$message_succes =  "Suppression de $count binets" ;
	} else {
		$message_succes =  "Aucun binet séléctionné" ;	
	}
}


// Génération du la page XML
require "../include/page_header.inc.php";

?>
<page id="profil" titre="Frankiz : modification du profil">
	<h1>Modification de son profil</h1>
<?php
		if(!empty($message_succes))
			echo "<commentaire>$message_succes</commentaire>\n";
		if(a_erreur(ERR_MDP_DIFFERENTS))
			echo "<warning>Les valeurs des deux champs de mot de passe n'étaient pas identiques.</warning>\n";
		if(a_erreur(ERR_MDP_TROP_PETIT))
			echo "<warning>Il faut mettre un mot de passe plus long (au moins 8 caractères).</warning>\n";
		if(a_erreur(ERR_SURNOM_TROP_PETIT))
			echo "<warning>Il faut mettre un surnom plus long (au moins 2 caractères).</warning>\n";
		if(a_erreur(ERR_EMAIL_NON_VALIDE))
			echo "<warning>L'email n'est pas valide.</warning>\n";
?>
	<formulaire id="mod_frankiz" titre="Modification du compte Frankiz" action="profil/profil.php">
		<textsimple valeur="Ne pas toucher ou laisser vide pour conserver l'ancien mot de passe"/>
		<champ id="passwd" titre="Mot de passe" valeur="12345678"/>
		<champ id="passwd2" titre="Retaper le mot de passe" valeur="87654321"/>
		<choix id="cookie" titre="Utiliser l'authentification par cookie" type="combo"
				valeur="<?php echo empty($_COOKIE['auth'])? 'non' : 'oui' ?>">
			<option titre="Activé" id="oui"/>
			<option titre="Désactivé" id="non"/>
		</choix>
		<bouton id="changer_frankiz" titre="Changer"/>
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
	
	<liste id="liste_binet" selectionnable="oui" action="profil/profil.php" titre="Tes Binets">
		<entete id="binet" titre="Binet"/>
		<entete id="commentaire" titre="Commentaire"/>

<?
		$DB_trombino->query("SELECT nom,binet_id FROM binets  ORDER BY nom ASC");
		$liste_binet = "<choix id=\"liste_binet\"  type=\"combo\" valeur=\"Ajout\">\n" ;
		$liste_binet .= "\t<option titre=\"\" id=\"default\"/>" ;

		while (list($nom_binet,$binet_id) = $DB_trombino->next_row()) { 
			$liste_binet .="\t<option titre=\"$nom_binet\" id=\"$binet_id\"/>\n" ;
		}
		$liste_binet .= "</choix>\n" ;
		$liste_binet .= "<bouton id='ajout_binet' titre='Ajouter'/>\n" ;



		$DB_trombino->query("SELECT membres.remarque,membres.binet_id,binets.nom FROM membres INNER JOIN binets USING(binet_id) WHERE eleve_id={$_SESSION['user']->uid} ORDER BY membres.binet_id ASC");
		while (list($remarque,$binet_id,$nom) = $DB_trombino->next_row()) { ?>
		<element id="<?=$binet_id?>">
			<colonne id="binet"><? echo $nom?> :</colonne>
			<colonne id="commentaire"><champ id="binet_<? echo $binet_id?>" titre="" valeur="<? echo $remarque?>"/></colonne>
		</element>
<?
		 }
?>
		<element id="<?=$binet_id?>" selectionnable="non">
			<colonne id="binet"></colonne>
			<colonne id="commentaire"></colonne>
		</element>
		<element id="<?=$binet_id?>" selectionnable="non">
			<colonne id="binet">Rajouter un binet</colonne>
			<colonne id="commentaire"><?=$liste_binet?></colonne>
		</element>
		<bouton id='suppr_binet' titre='Supprimer' onClick="return window.confirm('Voulez vous vraiment supprimer ce binet ?')"/>
		<bouton id='mod_binet' titre='Changer'/>
	</liste>
	

</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
