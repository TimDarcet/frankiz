<?php
/*
	$Id$
	
	Affichage de la QDJ.
	
	TODO traiter le cas ou le qdj master est à la bourre (garder l'ancienne qdj par exemple).
*/

function qdj_affiche($hier,$deja_vote) {
	$date = date("Y-m-d", time()-3025 - ($hier ? 24*3600 : 0));
	$fichier_cache = BASE_LOCAL."/cache/qdj_".($hier?"hier":"courante");
	
	$result = mysql_query("SELECT question,reponse1,reponse2,compte1,compte2 FROM qdj WHERE date='$date' LIMIT 1");
	list($question,$reponse1,$reponse2,$compte1,$compte2) = mysql_fetch_row($result);
	mysql_free_result($result);
?>

	<module id="<?php echo $hier ? 'qdj_hier' : 'qdj' ?>" titre="QDJ<?php if($hier) echo ' d\'hier' ?>">
		<qdj type="<?php echo $hier ? 'aujourdhui' : 'hier' ?>" id="<?php echo $date?>" <?php if(!$deja_vote && !$hier) echo " action=\"?qdj=$date&amp;vote=\""; ?>>
			<question><?php echo $question ?></question>
			<reponse id="1" votes="<?php echo $compte1?>"><?php echo $reponse1?></reponse>
			<reponse id="2" votes="<?php echo $compte2?>"><?php echo $reponse2?></reponse>
<?php
			// Récupération des noms des derniers votants à la question en cours
			if(file_exists($fichier_cache)) {
				// utilisation du cache
				readfile($fichier_cache);

			} else {
				// interrogation de la base de données
				connecter_mysql_frankiz();
				$result = mysql_query("SELECT ordre,nom,prenom,surnom FROM qdj_votes LEFT JOIN eleves USING(eleve_id) WHERE date='$date' ORDER BY ordre DESC LIMIT 20");
				$contenu = "";
				while(list($ordre,$nom,$prenom,$surnom) = mysql_fetch_row($result))
					$contenu .= "<dernier ordre=\"$ordre\">".(empty($surnom) ? $prenom.' '.substr($nom,0,1).'.' : $surnom)."</dernier>\n";
				mysql_free_result($result);
				deconnecter_mysql_frankiz();
				
				// affichage
				echo $contenu;  
				
				// mise en cache
				$file = fopen($fichier_cache, 'w');
				fwrite($file, $contenu);
				fclose($file);
			}
?>
		</qdj>
	</module>
<?php
}
?>