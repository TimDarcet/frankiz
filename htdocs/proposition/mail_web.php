<?php
/*
	Page permettant de contacter les Webmestres (si on est loggué)
	
	$Log$
	Revision 1.1  2004/09/20 08:29:24  kikx
	Rajout d'une page pour envoyer des mail d'amour a ses webmestres adorés

	
*/

require_once "../include/global.inc.php";
demande_authentification(AUTH_MINIMUM);

$eleve_id=$_SESSION['user']->uid;

// Génération du la page XML
require "../include/page_header.inc.php";
?>
<page id="mail_web" titre="Frankiz : Contacter les Webmestres">
<?
if (!isset($_POST['envoyer'])) {
?>

	<formulaire id="mail" titre="Envoie un mail aux webmestres" action="proposition/mail_web.php">
		<zonetext titre="Ton texte" id="texte" valeur="" />
		<bouton titre="Envoyer" id="envoyer"/>
	</formulaire>
<?
} else {
		$DB_trombino->query("SELECT nom,prenom,promo FROM eleves WHERE eleve_id='{$_SESSION['user']->uid}'");
		list($nom,$prenom,$promo)=$DB_trombino->next_row();

		$tempo = explode("proposition",$_SERVER['REQUEST_URI']) ;
		
		$trans = get_html_translation_table(HTML_ENTITIES);
		$trans = array_flip($trans);
		$original = strtr($_POST['texte'], $trans);
		$original = str_replace('&apos;',"'", $original) ;
		
		$contenu = "$prenom $nom (X$promo) a écrit aux webmestres le texte d'amour suivant: \n".
					$original."\n\n";
					
		mail("Admin Frankiz <gruson@poly.polytechnique.fr>","[Frankiz] Mail pour le webmestre",$contenu);

?>
		<commentaire>Nous avons bien pris en compte ta demande. Nous allons la traiter dans les plus brefs delais :)</commentaire>
<?
}
?>
</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php"; ?>
