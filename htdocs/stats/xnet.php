<?php
require_once "../include/global.inc.php";


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="stat_xnet" titre="Frankiz : Statistiques Xnet">
	<image source="stats/xnet_connections.php" texte="Nombre de connectés ces dernières 24h"/>
	<image source="stats/xnet_clients.php" texte="Répartition des clients"/>
	<image source="stats/xnet_os.php" texte="Répartition des os"/>
	<image source="stats/xnet_serveurs.php" texte="Statistiques des serveurs"/>
</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
