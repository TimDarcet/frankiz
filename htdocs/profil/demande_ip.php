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

	<formulaire id="demande" titre="Demande d'une nouvelle IP" action="profil/demande_ip.php">
		<commentaire>Tu vas demander une nouvelle ip : Explique nous pourquoi tu en as besoin de cette ip supplémentaire (par exemple : 2 ordinateurs, tu vis en couple ...)</commentaire>
		<zonetext titre="Raison" id="raison" valeur="" />
		<bouton titre="Demander" id="demander"/>
	</formulaire>




</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php"; ?>
