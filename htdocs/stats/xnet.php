<?php
require_once "../include/global.inc.php";


// G�n�ration de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="stat_xnet" titre="Frankiz : Statistiques Xnet">
	<image source="stats/xnet_connections.php" texte="Nombre de connect�s ces derni�res 24h"/>
	<image source="stats/xnet_clients.php" texte="R�partition des clients"/>
	<image source="stats/xnet_os.php" texte="R�partition des os"/>
	<image source="stats/xnet_serveurs.php" texte="Statistiques des serveurs"/>
</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
