<?php
/*
	$Id$
	
	Gestion des utilisateurs et de leur droits. L'accès se fait par le trombino,
	après une recherche.
	
	ATTENTION : il n'y a volontairement pas de pages web d'administration permettant l'ajout
	ou la suppression d'un utilisateur. En effet, il n'y a aucune raison de supprimer un utilisateur,
	et pour l'ajout d'utilisateur, l'opération a lieu par bloc pour toute une promo or c'est beaucoup
	plus facile de le faire via un fichier de commande MySQL que par une interface web.
	
	L'ID de l'utilisateur à modifier est passer dans le paramètre GET 'user'.
*/
	
// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')) {
	header("Location: ".BASE_URL."/admin/");
	exit;
}
connecter_mysql_frankiz();

// On vérifie que la personne envoie bien l'id sinon ca sert a rien ...
if(!isset($_GET['id'])) {
	header("Location: ".BASE_URL."/admin/");
	exit;
}



// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="admin_user" titre="Frankiz : gestion des utilisateurs">

<?

$id = $_GET['id'] ;
// Traitement des demandes de modifications !
//============================

// Modification de la partie "général"

if (isset($_POST['mod_generale'])) {
	$nom = $_POST['nom'];
	$prenom = $_POST['prenom'];
	$surnom = $_POST['surnom'];
	$date_nais = $_POST['date_nais'];
	$sexe = $_POST['sexe'];
	$piece_id = $_POST['piece_id'];
	$section_id = $_POST['section_id'];
	$cie = $_POST['cie'];
	$promo = $_POST['promo'];
	$login = $_POST['login'];
	$mail = $_POST['mail'];
	mysql_query("UPDATE eleves SET nom='$nom', prenom='$prenom', surnom='$surnom', date_nais='$date_nais', sexe='$sexe', piece_id='$piece_id', section_id='$section_id', cie='$cie', promo='$promo', login='$login', mail='$mail' WHERE eleve_id=$id ");
	
	echo "Modification de la partie générale faite avec succès" ;
}

// Modification de la partie "binets"

if (isset($_POST['mod_binet'])) {
	//$commentaire = $_POST['commentaire'];

	foreach($_POST as $key=>$val) {
		if ($key == "mod_binet") break ;
		$key = explode("_",$key) ;
		$key = $key[1] ;
		mysql_query("UPDATE membres SET remarque='$val' WHERE eleve_id=$id AND binet_id=$key");
 	}
	echo "Modification de la partie binets faite avec succès" ;
}

// Modification de la partie "compte FrankizII"

if (isset($_POST['mod_compte_fkz'])) {
	if ($_POST['pass']!="") {
		$pass2 = md5($_POST['pass']) ;
		mysql_query("UPDATE compte_frankiz SET passwd='$pass2' WHERE eleve_id=$id");
		echo "<p>Modification du mot de passe réalisée correctement</p>" ;
	}
	$perms = $_POST['perms'] ;
	mysql_query("UPDATE compte_frankiz SET perms='$perms' WHERE eleve_id=$id");

	echo "Modification de la partie Compte FrankizII faite avec succès" ;
}

// Modification de ses variables génériques
?>
	<formulaire id="user_general" titre="Général" action="admin/user.php?id=<? echo $id?>">
<?
		$result = mysql_query("SELECT nom,prenom,surnom,date_nais,sexe,piece_id,section_id,cie,promo,login,mail FROM eleves WHERE eleve_id=$id ORDER BY nom ASC");
		
		list($nom,$prenom,$surnom,$date_nais,$sexe,$piece_id,$section,$cie,$promo,$login,$mail) = mysql_fetch_row($result) ;
?>
		<champ id="nom" titre="Nom" valeur="<? echo $nom?>"/>
		<champ id='prenom' titre='Prénom' valeur='<? echo $prenom?>'/>
		<champ id='surnom' titre='Surnom' valeur='<? echo $surnom?>'/>
		<champ id='login' titre='Login' valeur='<? echo $login?>'/>
		<champ id='date_nais' titre='Date de naissance' valeur='<? echo $date_nais?>'/>
		<champ id='sexe' titre='Sexe' valeur='<? echo $sexe?>'/>
		<champ id='piece_id' titre='Ksert' valeur='<? echo $piece_id?>'/>
		<champ id='section_id' titre='Section' valeur='<? echo $section?>'/>
		<champ id='cie' titre='Cie' valeur='<? echo $cie?>'/>		
		<champ id='promo' titre='Promo' valeur='<? echo $promo?>'/>
		<champ id='mail' titre='Mail' valeur='<? echo $mail?>'/>
		
		<bouton id='mod_generale' titre='Changer'/>
<?		
		mysql_free_result($result);
?>
	</formulaire>
	
<?
// Modification de ses binets et des commentaires sur les binets  
?>
	<formulaire id="user_binets" titre="Ses binets" action="admin/user.php?id=<? echo $id?>">
<?
		$result = mysql_query("SELECT membres.remarque,membres.binet_id,binets.nom FROM membres INNER JOIN binets ON membres.binet_id=binets.binet_id WHERE eleve_id=$id ORDER BY membres.binet_id ASC");
		
		while (list($remarque,$binet_id,$nom) = mysql_fetch_row($result)) {
?>
			<champ id="binet_<? echo $binet_id?>" titre="<? echo $nom?>" valeur="<? echo $remarque?>"/>
<?
		 }
?>
		<bouton id='mod_binet' titre='Changer'/>
	</formulaire>
	
<?
// Modification de ses préferences FrankizII
?>
	<formulaire id="user_compt_fkz" titre="Compte Frankiz" action="admin/user.php?id=<? echo $id?>">
<?
		$result = mysql_query("SELECT perms FROM compte_frankiz WHERE eleve_id=$id");
		
		list($perms) = mysql_fetch_row($result) ;
?>
		<champ id="pass" titre="Mot de passe" valeur=""/>
		<commentaire>Pour le mot de passe : Si vous le laissez vide, il ne sera pas modifié !</commentaire>
		<champ id='perms' titre='Permissions' valeur='<? echo $perms?>'/>
		
		<bouton id='mod_compte_fkz' titre='Changer'/>
<?		
		mysql_free_result($result);
?>
	</formulaire>

</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
