<?php
/*
	Page permettant de faire une demande d'adresse IP supplémentaire pour mettre
	une seconde machine dans son casert.
	
	$Log$
	Revision 1.6  2004/09/15 23:20:07  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")

	Revision 1.5  2004/09/15 21:42:21  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

require_once "../include/global.inc.php";
demande_authentification(AUTH_FORT);

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
	$DB_admin->query("SELECT 0 FROM validations_ip WHERE eleve_id='{$_SESSION['user']->uid}'");
	if ($DB_admin->num_rows()>0){
?>

		<warning>Tu as déjà fait une demande</warning>
		<p>Tu ne peux pas faire plusieurs demandes à la fois ...</p>
		<p>Attends que les BRmen te valident la première pour en faire une seconde si cela est justifié</p>
	
<?
	} else {
		$DB_admin->query("INSERT validations_ip SET raison='{$_POST['raison']}', eleve_id='{$_SESSION['user']->uid}'");
		
		// Envoie du mail au webmestre pour le prévenir d'une demande d'ip
		$DB_trombino->query("SELECT nom,prenom FROM eleves WHERE eleve_id='{$_SESSION['user']->uid}'");
		list($nom,$prenom)=$DB_trombino->next_row();
		
		$tempo = explode("profil",$_SERVER['REQUEST_URI']) ;
		
		$contenu = "$prenom $nom a demandé une nouvelle ip pour la raison suivante : \n".
					stripslashes($_POST['raison'])."\n\n".
					"Pour valider ou non cette demande va sur la page suivante : \n".
					"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_ip.php\n\n" .
					"Très BR-ement\n" .
					"L'automate :)\n"  ;
					
		mail("Admin Frankiz <gruson@poly.polytechnique.fr>","[Frankiz] Demande d'une nouvelle ip",$contenu);
	
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
