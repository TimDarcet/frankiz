<?php
/*
	$Id$
	
	Page permettant de modifier ses informations relatives au réseau interne de l'x : le nom de
	ses machines, son compte xnet.
	
	TODO faire la page
*/

require_once "../include/global.inc.php";
demande_authentification(AUTH_FORT);
connecter_mysql_frankiz();

$eleve_id=$_SESSION['user']->uid;

// Génération du la page XML
require "../include/page_header.inc.php";
?>
<page id="profil_reseau" titre="Frankiz : Demande d'une nouvelle ip">
<?
if (!isset($_POST['demander'])) {
?>

	<formulaire id="demande" titre="Demande d'une nouvelle IP" action="profil/demande_ip.php">
		<commentaire>Tu vas demander une nouvelle ip : Explique nous pourquoi tu en as besoin de cette ip supplémentaire (par exemple : 2 ordinateurs, tu vis en couple ...)</commentaire>
		<zonetext titre="Raison" id="raison" valeur="" />
		<bouton titre="Demander" id="demander"/>
	</formulaire>
<?
} else {
	$resultat = mysql_query("SELECT * FROM ip_ajout WHERE eleve_id='".$_SESSION['user']->uid."' AND valider=0");
	if (mysql_num_rows($resultat)>0){
?>

		<warning>Tu as déjà fait une demande</warning>
		<p>Tu ne peux pas faire plusieurs demandes à la fois ...</p>
		<p>Attends que les BRmen te valident la première pour en faire une seconde si cela est justifié</p>
	
<?
	} else {

		mysql_query("INSERT ip_ajout SET raison='".$_POST['raison']."', eleve_id='".$_SESSION['user']->uid."',valider=0");
		// Envoie du mail au webmestre pour le prévenir d'une demande d'ip
		$resultat = mysql_query("SELECT nom,prenom FROM eleves WHERE eleve_id='".$_SESSION['user']->uid."'");
		list($nom,$prenom)=mysql_fetch_row($resultat);
		
		$tempo = explode("profil",$_SERVER['REQUEST_URI']) ;
		
		$contenu = "$prenom $nom a demandé une nouvelle ip pour la raison suivante : \n".
					stripslashes($_POST['raison'])."\n\n".
					"Pour valider ou non cette demande va sur la page suivante : \n".
					"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_ip.php\n\n" .
					"Très BR-ement\n" .
					"L'automate :)\n"  ;
					
			mail("Admin FrankizII <gruson@poly.polytechnique.fr>","[Frankiz] Demande d'une nouvelle ip",$contenu);
	
?>

		<p>Nous avons bien pris en compte ta demande pour la raison suivante ci dessous. Nous allons la traiter dans les plus brefs delais :)</p>
		<p>&nbsp;</p>
		<p>Raison de la demande :</p> 
		<commentaire>
			<? echo stripslashes($_POST['raison']) ;?>
		</commentaire>
	
<?
	}
}
?>
</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php"; ?>
