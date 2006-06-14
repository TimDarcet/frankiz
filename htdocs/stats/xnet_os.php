<?php
require_once "../include/global.inc.php";
demande_authentification(AUTH_MINIMUM);
// on spécifie le type d'image que l'on va créer, ici ce sera une image au format PNG
header ("Content-type: image/png");  

$cache_id="xnet_stats_os";

if(!cache_recuperer($cache_id,time() - 600)) {
	require_once("../include/graph.inc.php");
	$graph = new Graph(550, 600, 'OS', 'Répartition des OS');
	if (!$graph->valid) {
		echo 'Impossible de générer l\'image';
		exit;
	}

	$DB_xnet->query('SELECT IF(os = 1, "Windows 9x", 
								IF(os = 2, "Windows XP", 
									IF(os = 3, "Linux", 
										IF(os = 4, "MacOS Classic", 
											IF(os = 5, "MacOS X", "!!!")
										)
									)
								)
							) AS nom_sysex, 
							SUM(jone) AS jones,
							SUM(rouje) AS roujes,
							(COUNT(*) - SUM(jone) - SUM(rouje)) AS oranjes
					   FROM (SELECT IF(((options >> 9) & 3) = 2, 1, 0) AS rouje,
					                IF(((options >> 9) & 3) = 3, 1, 0) AS jone,
									((options & 0x1c0) >> 6) AS os
							   FROM clients
							  WHERE (NOT status))
							AS p
				   GROUP BY os');

	while(list($nom,$jones, $roujes, $oranjes) = $DB_xnet->next_row()){
		if($nom != '!!!') {
			$graph->addRow($jones, $roujes, $oranjes, $nom);
		}
	}

	$graph->run();
	cache_sauver($cache_id);
}
?>
