<?php
/*
	$Id$
	
	Gestion de la liste des binets.
*/
	
// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')) {
	header("Location: ".BASE_URL."/admin/");
	exit;
}

// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="admin_binets" titre="Frankiz : liste des binets">
</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
