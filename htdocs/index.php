<?php

require_once "include/global.inc.php";
demande_authentification(AUTH_MINIMUM);

require "include/page_header.inc.php";

echo "<page id='accueil' titre='Frankiz : accueil'>\n";
require BASE_LOCAL."/include/modules.inc.php";

echo "<contenu>\n";
require BASE_LOCAL."/modules/annonces.php";
echo "</contenu>\n";

echo "</page>\n";

require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
