<?php
/*
	Page qui permet aux utilisateurs de demander le rajout d'une annonce
*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MINIMUM);

$DB_trombino->query("SELECT nom,prenom,surnom,mail,login,promo FROM eleves WHERE eleve_id='".$_SESSION['user']->uid."'");
list($nom,$prenom,$surnom,$mail,$login,$promo) = $DB_trombino->next_row();

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="propoz_annonce" titre="Frankiz : Propose une annonce">
<h1>Proposition d'annonce</h1>

 <?

// On teste l'affichage de l'annonce pour voir à quoi ça ressemble

if (isset($_POST['test'])) {
?>
	<annonce titre="<?php  if (isset($_POST['titre'])) echo $_POST['titre'] ; ?>" 
			categorie=""
			auteur="<?php echo empty($surnom) ? $prenom.' '.$nom : $surnom .' (X'.$promo.')'?>"
			date="">
			<? if (isset($_POST['text'])) echo "<![CDATA[".$_POST['text']."]]>" ;?>
	</annonce>
<?
}

// On valide l'annonce et en envoie un mail aux webmestres pour les prévenir 
if (isset($_POST['valid'])) {

	$tempo = explode("proposition",$_SERVER['REQUEST_URI']) ;

	$DB_valid->query("INSERT INTO annonces SET eleve_id='".$_SESSION['user']->uid."', titre='".$_POST['titre']."',contenu='".$_POST['text']."'");
	
	$contenu = "$prenom $nom a demandé la validation d'une annonce : \n".
				$_POST['titre']."\n\n".
				"Pour valider ou non cette demande va sur la page suivante : \n".
				"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_annonce.php\n\n" .
				"Très BR-ement\n" .
				"L'automate :)\n"  ;
				
	mail("Admin Frankiz <gruson@poly.polytechnique.fr>","[Frankiz] Validation d'une annonce",$contenu);



?>
	<commentaire>
		<p>Tu as demandé à un webmestre de valider ton annonce</p>
		<p>Il faut compter 24h pour que ton annonce soit prise en compte par notre système</p>		
		<p>&nbsp;</p>		
		<p>Nous te remercions d'avoir soumis une annonce et nous essayerons d'y répondre le plus rapidement possible</p>		
	</commentaire>
<?	
} else {
// Zone de saisie de l'annonce
?>

	<formulaire id="propoz_annonce" titre="Ton annonce" action="proposition/annonce.php">
		<champ id="titre" titre="Le titre" valeur="<? if (isset($_POST['titre'])) echo $_POST['titre'] ;?>"/>
		<zonetext id="text" titre="Le texte" valeur="<? if (isset($_POST['text'])) echo $_POST['text'] ;?>"/>
		<textsimple valeur="Ta signature sera automatiquement généré"/>
		<bouton id='test' titre='Tester'/>
		<bouton id='valid' titre='Valider' onClick="return window.confirm('Voulez vous vraiment valider votre annonce ?')"/>
	</formulaire>
<?
}
?>

</page>
<?php

require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
