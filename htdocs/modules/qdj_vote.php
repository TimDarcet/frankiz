<?
    $heure_rel = time() - strtotime($aujourdhui);
    if (client_eleve($client_ip)) {
	$query = "update questions set 
                  compte".$vote."1 = compte".$vote."1+1 where id=$id";
        mysql_query($query);
    	mysql_query("insert into votes set date='$aujourdhui',question=$id, time=$heure_rel, ip='$client_ip'");
        
        
        if(date("Y-m-d", filemtime($fichier_cache)) == ($aujourdhui-3600*24)){
         unlink($fichier_cache_hier);
        }
        
        // On update le cache de la question en cours
        $result = mysql_query("SELECT ip FROM votes WHERE question='$id' ORDER BY time DESC LIMIT 20");
        $last=array();
        for($i=0;$i<20 && $last[] = array_pop(mysql_fetch_row($result));$i++);
        mysql_free_result($result);
        reset($last);

    
        $contenu .= "";
        while(list($key,$lastip)=each($last)){ 
            if(isset($lastip)){
                $contenu .= "<dernier><numero>$comptetotal</numero>";
                $lastip = ip2dns($lastip);
                $contenu .= "<nom>$lastip</nom></dernier>\n";
                $comptetotal--;
            } 
        } 
        $contenu .= "\n";
        $file = fopen($fichier_cache, 'w');
        fwrite($file, $contenu);
        fclose($file);
        $cache_present = 1; 
        $a_vote = 1;
    }
  

?>
