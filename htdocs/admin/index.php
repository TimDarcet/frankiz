<?php
// En-tetes
require_once "../include/global.inc.php";

demande_authentification(AUTH_FORT);
if(!verifie_permission("admin")) {
	header("Location: ".BASE_URL."/");
	exit;
}

// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="accueil" titre="Frankiz : admin">
	<h1>Admin Frankiz</h1>
</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
