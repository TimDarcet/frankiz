<?php
/*
	Mail promo permettant l'envoie de pièce jointes et de formatage HTML
	
	$Log$
	Revision 1.1  2004/10/04 21:21:10  kikx
	oubli desolé

	
*/

// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="admin_mailpromo" titre="Frankiz : Envoie des mails promos">

	<formulaire id="mail_promo" titre="Mail Promo" action="admin/mail_promo.php">
		<champ titre="From" id="from" valeur="<? if (isset($_POST['from'])) echo $_POST['from']?>" />
		<champ titre="To" id="to" valeur="<? if (isset($_POST['to'])) echo $_POST['to']?>" />
		<zonetext titre="Mail" id="mail" valeur="<? if (isset($_POST['mail'])) echo $_POST['mail']?>" />
		<bouton titre="Envoyer" id="envoie"  onClick="return window.confirm('Voulez vous vraiment envoyer ce mail ?')"/>
	</formulaire>


</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
