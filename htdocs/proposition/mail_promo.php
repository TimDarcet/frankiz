<?php
/*
	Mail promo permettant l'envoie de pièce jointes et de formatage HTML
	
	$Log$
	Revision 1.2  2004/10/04 22:55:25  kikx
	Modification pour permettre aux personnes de poster des mails promos

	Revision 1.1  2004/10/04 22:51:48  kikx
	Modification de l'endroit de stockage
	
	Revision 1.2  2004/10/04 22:48:54  kikx
	Modification mineur de la page d'envoie de mail promo !
	
	Revision 1.1  2004/10/04 21:21:10  kikx
	oubli desolé
	
	
*/

// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MINIMUM);

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="admin_mailpromo" titre="Frankiz : Envoie des mails promos">


	<formulaire id="mail_promo" titre="Mail Promo" action="proposition/mail_promo.php">
		<zonetext titre="Mail" id="mail" valeur="<? if (isset($_REQUEST['mail'])) echo $_REQUEST['mail']?>" />
		<textsimple valeur="Ton fichier ne doit pas dépasser 500ko car sinon elle ne sera pas téléchargée"/>
		<champ id="file" titre="Pièce jointe" valeur="" taille="500000"/>
		<bouton titre="Mise à jour" id="upload"/>
		<bouton titre="Envoyer" id="envoie"  onClick="return window.confirm('Voulez vous vraiment envoyer ce mail ?')"/>
	</formulaire>
<?
if (isset($_REQUEST['upload'])) {
?>
	<cadre  titre="Mail Promo visualisation" >
		<? if (isset($_REQUEST['mail'])) echo $_REQUEST['mail']?>&lt;br&gt;
	</cadre>
<?
}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
