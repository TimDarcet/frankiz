<?php
require_once "../include/global.inc.php";

demande_authentification(AUTH_COOKIE);

// Génération de la page
//===============
require_once BASE_FRANKIZ."htdocs/include/page_header.inc.php";

?>
<page id="stat_xnet" titre="Frankiz : Statistiques Xnet">
	<image source="stats/xnet_stats.php?daily" texte="Nombre de connectés ces dernières 24h"/>
	<image source="stats/xnet_stats.php?weekly" texte="Evolution au cours de la semaine"/>
	<image source="stats/xnet_stats.php?monthly" texte="Evolution au cours du mois"/>
	<image source="stats/xnet_stats.php?yearly" texte="Evolution au cours de l'année"/>
	<image source="stats/xnet_clients.php" texte="Répartition des clients"/>
	<image source="stats/xnet_os.php" texte="Répartition des os"/>
	<image source="stats/xnet_serveurs.php" texte="Statistiques des serveurs"/>
</page>
<?php
require_once BASE_FRANKIZ."htdocs/include/page_footer.inc.php";
?>
