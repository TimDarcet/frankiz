<?php
// En-tetes
require "../include/page_header.inc.php";


// Récupération du contenu de la page (en XML)
echo "<page id='trombino' titre='Frankiz : Trombino'>\n";
require BASE_LOCAL."/include/modules.inc.php";

echo "<contenu>\n";
require BASE_LOCAL."/trombino/trombino.php";
echo "</contenu>\n";

echo "</page>\n";


// Applique les transformations
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
