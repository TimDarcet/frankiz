<?php
/*
	$Id$
	
	Page permettant de modifier ses informations relatives au r�seau interne de l'x�: le nom de
	ses machines, son compte xnet.
	
	TODO faire la page
*/

require_once "../include/global.inc.php";
demande_authentification(AUTH_FORT);

// G�n�ration du la page XML
require "../include/page_header.inc.php";
?>
<page id="profil_reseau" titre="Frankiz : modification du profil r�seau">
	<h2>Nom de ses machines</h2>
	<p>En construction�</p>
	<h2>Compte Xnet (mot de passe)</h2>
	<p>En construction�</p>
</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php"; ?>
