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
    
	$DB_xnet->query('SELECT p1.name AS clients, COUNT(p2.jone) AS users, SUM(p2.rouje) AS roujes, SUM(p2.jone) AS jones, (COUNT(p2.jone) - SUM(p2.rouje) - SUM(p2.jone)) AS oranjes
		               FROM software AS p1
					        RIGHT JOIN
							    (SELECT if(((options >> 9) & 3) = 2, 1, 0) AS rouje, if(((options >> 9) & 3) = 3, 1, 0) AS jone, version
								   FROM clients) 
								AS p2 USING(version)
				   GROUP BY p1.version');

	while(list($nom, $nb, $roujes, $jones, $oranjes) = $DB_xnet->next_row()){
		if($nom != '') {
			$graph->addRow($jones, $roujes, $oranjes, $nom);
		}
	}

	$graph->run();

//	cache_sauver($cache_id);
}
?>
