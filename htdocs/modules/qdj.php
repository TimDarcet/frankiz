<?php
/*
	$Id$
	
	Affichage et gestion de la QDJ.
	
	TODO traiter le cas ou le qdj master est à la bourre (garder l'ancienne qdj par exemple).
*/


function qdj_affiche($hier,$deja_vote) {
	$date = date("Y-m-d", time()-3025 - ($hier ? 24*3600 : 0));
	$jour = $hier ? 'aujourdhui' : 'hier';
	$fichier_cache = BASE_LOCAL."/cache/qdj_".($hier?"hier":"courante");
	
	connecter_mysql_frankiz();  // TODO correction moche d'un bug
	$result = mysql_query("SELECT question,reponse1,reponse2,compte1,compte2 FROM qdj WHERE date='$date' LIMIT 1");
	list($question,$reponse1,$reponse2,$compte1,$compte2) = mysql_fetch_row($result);
	mysql_free_result($result);
?>

	<module id="qdj_<?php echo $jour ?>" titre="QDJ<?php if($hier) echo ' d\'hier' ?>" visible="<?php echo skin_visible("qdj_$jour");?>">
		<qdj type="<?php echo $jour?>" id="<?php echo $id?>" <?php if(!$deja_vote && !$hier) echo " action=\"?qdj=$date&amp;vote=\""; ?>>
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

if(est_authentifie(AUTH_MINIMUM)) {
	connecter_mysql_frankiz();

	// Nettoyage du cache si on a changé de jour
	if(file_exists(BASE_LOCAL."/cache/qdj_hier") && filemtime(BASE_LOCAL."/cache/qdj_hier") < time()-3025-24*3600)
		unlink(BASE_LOCAL."/cache/qdj_hier");

	if(file_exists(BASE_LOCAL."/cache/qdj_courante") && filemtime(BASE_LOCAL."/cache/qdj_courante") < time()-3025)
		unlink(BASE_LOCAL."/cache/qdj_courante");

	// On cherche si l'utilisateur a déjà voté ou non
	$date_aujourdhui = date("Y-m-d", time()-3025);
	$result = mysql_query("SELECT 0 FROM qdj_votes WHERE date='$date_aujourdhui' and eleve_id='".$_SESSION['user']->uid."' LIMIT 1");
	$a_vote = mysql_num_rows($result) != 0;

	// Gestion du vote
	if($date_aujourdhui==$_GET['qdj'] && !$a_vote && ($_GET['vote']==1 || $_GET['vote']==2)) {
		unlink(BASE_LOCAL."/cache/qdj_courante");
		mysql_query("LOCK TABLE qdj_votes WRITE");
		mysql_query("SELECT @max:=IFNULL(MAX(ordre),0) FROM qdj_votes WHERE date='$date_aujourdhui'");
		mysql_query("INSERT INTO qdj_votes SET date='$date_aujourdhui',eleve_id='".$_SESSION['user']->uid."',ordre=@max+1");
		mysql_query("UNLOCK TABLES");
		mysql_query("UPDATE qdj SET compte".$_GET['vote']."=compte".$_GET['vote']."+1 WHERE date='$date_aujourdhui'");
		$a_vote = true;
	}

	// Affichage de la QDJ courante puis les résultats de celle de la veille
	qdj_affiche(false,$a_vote);
	qdj_affiche(true,true);

	deconnecter_mysql_frankiz();
}
?>
