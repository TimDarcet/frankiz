<?php
/*
	Affichage de la QDJ. Le fait d'avoir un fichier inclus permet d'éviter d'avoir du code
	dupliquer dans qdj.php et qdj_hier.php. Ça évite aussi de devoir regrouper les deux modules
	dans le même fichier (et donc passer autre le principe 1 module = 1 fichier).
	
	TODO traiter le cas ou le qdj master est à la bourre (garder l'ancienne qdj par exemple).
	
	$Log$
	Revision 1.8  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")

	Revision 1.7  2004/09/15 23:01:21  schmurtz
	Bug de la qdj : renvoie maintenant sur la page courante (et non index.php)
	
	Revision 1.6  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

function qdj_affiche($hier,$deja_vote) {
	global $DB_web;
	$date = date("Y-m-d", time()-3025 - ($hier ? 24*3600 : 0));
	$fichier_cache = BASE_LOCAL."/cache/qdj_".($hier?"hier":"courante");
	
	$DB_web->query("SELECT question,reponse1,reponse2,compte1,compte2 FROM qdj WHERE date='$date' LIMIT 1");
	list($question,$reponse1,$reponse2,$compte1,$compte2) = $DB_web->next_row();
?>

	<module id="<?php echo $hier ? 'qdj_hier' : 'qdj' ?>" titre="QDJ<?php if($hier) echo ' d\'hier' ?>">
		<qdj type="<?php echo $hier ? 'aujourdhui' : 'hier' ?>" id="<?php echo $date?>" <?php if(!$deja_vote && !$hier) echo " action=\"{$_SERVER['PHP_SELF']}?qdj=$date&amp;vote=\""; ?>>
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
				$DB_web->query("SELECT ordre,nom,prenom,surnom FROM qdj_votes LEFT JOIN trombino.eleves USING(eleve_id) WHERE date='$date' ORDER BY ordre DESC LIMIT 20");
				$contenu = "";
				while(list($ordre,$nom,$prenom,$surnom) = $DB_web->next_row())
					$contenu .= "<dernier ordre=\"$ordre\">".(empty($surnom) ? $prenom.' '.substr($nom,0,1).'.' : $surnom)."</dernier>\n";
				
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