<?php
require_once "../include/global.inc.php";
demande_authentification(AUTH_MINIMUM);
// on spécifie le type d'image que l'on va créer, ici ce sera une image au format PNG
header ("Content-type: image/png");  

$cache_id="xnet_stats_clients";
if(!cache_recuperer($cache_id, time()-600)) {
	require_once("../include/graph.inc.php");

    $graph = new Graph(550, 590, "Client", "Répartition par clients");
    if (!$graph->valid) {
        echo "Impossible de générer l'image";
		exit;
    }
    
	$DB_xnet->query('SELECT s.name AS clients,
							SUM(c.jone) AS jones, 
							SUM(c.rouje) AS roujes, 
							(COUNT(c.jone) - SUM(c.rouje) - SUM(c.jone)) AS oranjes
		               FROM software AS s
					        RIGHT JOIN
							    (SELECT IF(((options >> 9) & 3) = 2, 1, 0) AS rouje, 
								        IF(((options >> 9) & 3) = 3, 1, 0) AS jone, 
										version
								   FROM clients) 
								AS c USING(version)
				   GROUP BY s.version');

	while(list($nom, $jones, $roujes, $oranjes) = $DB_xnet->next_row()){
		if($nom != '') {
			$graph->addRow($jones, $roujes, $oranjes, $nom);
		}
	}

	$graph->run();

	cache_sauver($cache_id);
}
?>
