<?php



function qdj_affiche($jour,$action){
	if($jour=="hier"){
		$date = date("Y-m-d", time()-3025-24*3600);
		$fichier_cache = BASE_LOCAL."/cache/qdj_hier";
		$cache_present = 0;
	}elseif($jour=="aujourdhui"){
		$date = date("Y-m-d", time()-3025);
		$fichier_cache = BASE_LOCAL."/cache/qdj_courante";
		$fichier_cache_hier = BASE_LOCAL."/cache/qdj_hier";
		$cache_present = 0;
	}

	if(file_exists($fichier_cache) && date("Y-m-d", filemtime($fichier_cache)) == ($hier)){
  	// Supprime le cache périmé
  		unlink($fichier_cache_hier);
	}
	if (file_exists($fichier_cache)){ 
		if(date("Y-m-d", filemtime($fichier_cache)) == date("Y-m-d", time()-3025)){
    			$cache_present = 1;
  		}
	}
	
	connecter_mysql_frankiz();

	$result = mysql_query("SELECT intitule,reponse1,reponse2,compte1,compte2,id FROM questions WHERE date='$date' LIMIT 1");
	list($intitule,$reponse1,$reponse2,$compte1,$compte2,$id) = mysql_fetch_row($result);
	mysql_free_result($result);
	$comptetotal = $compte1 + $compte2;
	deconnecter_mysql_frankiz();
?>

	<module id="qdj" titre="QDJ<?php if($jour=="hier") echo " d'hier"?>" visible="<?php echo skin_visible("qdj_$jour");?>">
		<qdj type="<? echo $jour ?>" id="<? echo $id ?>" 
		<? if($action !="") echo " action=\"$action\""; ?>>
			<question><?php echo $intitule ?></question>
			<reponse id="1" votes="<?php echo $compte1 ?>"><?php echo $reponse1 ?></reponse>
			<reponse id="2" votes="<?php echo $compte2 ?>"><?php echo $reponse2 ?></reponse>

<?php
		if($cache_present){

			readfile($fichier_cache);

		}else{
			connecter_mysql_frankiz();
			// Récupère les résultats de la question en cours
			$result = mysql_query("SELECT ip FROM votes WHERE question='$id' ORDER BY time DESC LIMIT 20");
			$last=array();
			for($i=0;$i<20 && $last[] = array_pop(mysql_fetch_row($result));$i++);
			mysql_free_result($result);
			reset($last);
			deconnecter_mysql_frankiz();
 
 			$contenu = "";
 			while(list($key,$lastip)=each($last)){ 
				if(isset($lastip)){
					$contenu .= "<dernier ordre=\"$comptetotal\">";
					$lastip = ip2dns($lastip);
					$contenu .= "$lastip</dernier>\n";
					$comptetotal--;
				} 
			} 
			$contenu .= "\n";
			echo $contenu;  
			$file = fopen($fichier_cache, 'w');
			fwrite($file, $contenu);
			fclose($file);  
	}

?>

		</qdj>
	</module>
<?php
}

function qdj_test_vote(){
	global $client_ip;
	connecter_mysql_frankiz();
	
	$date = date("Y-m-d", time()-3025);
	
	// Récupère la question en cours
	$result = mysql_query("SELECT questions.id FROM questions , votes WHERE questions.date='$date' and votes.question = questions.id and votes.ip='$client_ip' LIMIT 1");
	// Vérifie si le client a déjà voté à la question en cours
	$a_vote = mysql_num_rows($result);
	
	deconnecter_mysql_frankiz();

	// Enregistre le vote
	if(!$a_vote && !empty($_GET["vote"])) {
  		qdj_vote();
	}elseif(!$a_vote){ qdj_affiche("aujourdhui","?vote=");}
	else{
		qdj_affiche("aujourdhui","");
	}
}

function qdj_vote(){
	global $client_ip;
	$vote = $_GET['vote'];
	$date = date("Y-m-d", time()-3025);
	$fichier_cache = BASE_LOCAL."/cache/qdj_courante";
	$fichier_cache_hier = BASE_LOCAL."/cache/qdj_hier";
	$heure_rel = time() - strtotime($date);
	
	if (client_eleve($client_ip)) {
		connecter_mysql_frankiz();
		$result = mysql_query("SELECT id FROM questions WHERE date='$date' LIMIT 1");
		$id = mysql_result($result,0);
		mysql_free_result($result);
		
		$query = "UPDATE questions SET compte".$vote." = compte".$vote."+1 WHERE id=$id";
		mysql_query($query);
		
		
		mysql_query("INSERT INTO votes SET date='$date',question=$id, time=$heure_rel, ip='$client_ip'");
        
        
		if(date("Y-m-d", filemtime($fichier_cache)) == ($aujourdhui-3600*24)){
			unlink($fichier_cache_hier);
		}
        	
		// On update le cache de la question en cours
		unlink($fichier_cache);
		$cache_present = 1; 
		$a_vote = 1;
		deconnecter_mysql_frankiz();
	}
	qdj_affiche("aujourdhui","");
}

qdj_test_vote();

qdj_affiche("hier","");
?>