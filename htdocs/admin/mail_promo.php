<?php
/*
	Mail promo permettant l'envoie de pi�ce jointes et de formatage HTML
	
	$Log$
	Revision 1.2  2004/10/04 22:48:54  kikx
	Modification mineur de la page d'envoie de mail promo !

	Revision 1.1  2004/10/04 21:21:10  kikx
	oubli desol�
	
	
*/

// En-tetes
require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");

// G�n�ration de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="admin_mailpromo" titre="Frankiz : Envoie des mails promos">


	<formulaire id="mail_promo" titre="Mail Promo" action="admin/mail_promo.php">
		<zonetext titre="Mail" id="mail" valeur="<? if (isset($_REQUEST['mail'])) echo $_REQUEST['mail']?>" />
		<commentaire>
		Vous pouvez rajouter des pi�ces jointes mais faites le avec parcimonie car �a boul�tise le serveur
		</commentaire>
		<champ id="file" titre="Pi�ce jointe" valeur=""/>
		<bouton titre="Mise � jour" id="upload"/>
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
