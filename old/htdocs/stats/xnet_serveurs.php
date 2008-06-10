<?php
require_once "../include/global.inc.php";
demande_authentification(AUTH_COOKIE);
// on spécifie le type d'image que l'on va créer, ici ce sera une image au format PNG
header ("Content-type: image/png");  

$cache_id="xnet_stats_serveurs";

if(!cache_recuperer($cache_id,time()-600)) {
	require_once("../include/graph.inc.php");
	$graph = new Graph(550, 600, 'Serveur', 'Nombre de serveurs');
	if (!$graph->valid) {
		echo 'Impossible de créer le graphique';
		exit;
	}

	$serveurs = Array('Samba' => '0x1', 'FTP' => '0x2', 'Web' => '0x8', 'News' => '0x10');
	foreach ($serveurs as $nom => $val) {
		$DB_xnet->query('SELECT SUM(jone) AS jones, 
								SUM(rouje) AS roujes,
								(COUNT(jone) - SUM(jone) - SUM(rouje)) AS oranjes
		                   FROM (SELECT IF(((options >> 9) & 3) = 2, 1, 0) AS rouje,
										IF(((options >> 9) & 3) = 3, 1, 0) AS jone
						           FROM clients
						          WHERE (options & ' . $val . '))
								AS c');
		list($jones, $roujes, $oranjes) = $DB_xnet->next_row();
		$graph->addRow($jones, $roujes, $oranjes, $nom);
	}

	$graph->run();
	cache_sauver($cache_id);
}
?>
