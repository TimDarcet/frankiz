<?php
// En-tetes
require_once "../include/global.inc.php";

demande_authentification(AUTH_FORT);
if(!verifie_permission("admin")) {
	header("Location: ".BASE_URL."/");
	exit;
}

require_once BASE_LOCAL."/include/page_header.inc.php";


// Récupération du contenu de la page (en XML)
echo "<page id='accueil' titre='Frankiz : admin'>\n";
require BASE_LOCAL."/include/modules.inc.php";

?>
<contenu>
	<h1>Admin Frankiz</h1>

</contenu>
</page>
<?php

// Applique les transformations
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
