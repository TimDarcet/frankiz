<?php
/*
Question du jour en cours
*/


// Date du jour
$aujourdhui = date("Y-m-d", time()-3025);

$fichier_cache = BASE_LOCAL."/cache/qdj_courante";
$fichier_cache_hier = BASE_LOCAL."/cache/qdj_hier";
$cache_present = 0;

if (file_exists($fichier_cache)){ 
  if(date("Y-m-d", filemtime($fichier_cache)-3025) == $aujourdhui){
    $cache_present = 1;
  }
}

// Date du jour
$aujourdhui = date("Y-m-d", time()-3025);


connecter_mysql_frankiz();

// Récupère la question en cours
$result = mysql_query("SELECT intitule,reponse1,reponse2,compte1,compte2,id FROM questions WHERE date='$aujourdhui' LIMIT 1");
list($intitule,$reponse1,$reponse2,$compte1,$compte2,$id) = mysql_fetch_row($result);
mysql_free_result($result);
$comptetotal=$compte1+$compte2;

// Vérifie si le client a déjà voté à la question en cours
$result = mysql_query("SELECT * FROM votes WHERE ip='$client_ip' AND question='$id'");
$a_vote = mysql_num_rows($result);


// Enregistre le vote
if(!$a_vote && !empty($_GET["vote"])) {
  include "qdj_vote.php";
}
deconnecter_mysql_frankiz();

?>

<qdj type="aujourdhui" visible="<?php echo skin_visible("qdj"); ?>" avote="<?PHP echo $a_vote?1:0 ?>">
	<intitule><?php echo $intitule ?></intitule>
	<reponse1><?php echo $reponse1 ?></reponse1>
	<reponse2><?php echo $reponse2 ?></reponse2>
	<?PHP if($a_vote) { ?>
	<compte1><?php echo $compte1 ?></compte1>
	<compte2><?php echo $compte2 ?></compte2>

        <?php if($comptetotal > 0) { ?>
	<pc_rouje><?php echo number_format(100*$compte1/($comptetotal),2) ?>%</pc_rouje>
	<pc_jone><?php echo number_format(100*$compte2/($comptetotal),2) ?>%</pc_jone>
	<?php }else{ ?>
	<pc_rouje>0%</pc_rouje>
	<pc_jone>0%</pc_jone>
	<?php } ?>	
	<?PHP } ?>
	<id><?php echo $id ?></id>

<qdj_last>
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
          $contenu .= "<dernier><numero>$comptetotal</numero>";
	  $lastip = ip2dns($lastip);
          $contenu .= "<nom>$lastip</nom></dernier>\n";
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
</qdj_last>        
        
        
</qdj>

