<?php
/*
	$Id$
	
	Affichage de la QDJ actuelle et gestion des votes.
	
	TODO traiter le cas ou le qdj master est à la bourre (garder l'ancienne qdj par exemple).
*/

require_once BASE_LOCAL."/include/qdj.inc.php";

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
	if(isset($_GET['qdj']) && date_aujourdhui==$_GET['qdj'] && !$a_vote && ($_GET['vote']==1 || $_GET['vote']==2)) {
		unlink(BASE_LOCAL."/cache/qdj_courante");
		mysql_query("LOCK TABLE qdj_votes WRITE");
		mysql_query("SELECT @max:=IFNULL(MAX(ordre),0) FROM qdj_votes WHERE date='$date_aujourdhui'");
		mysql_query("INSERT INTO qdj_votes SET date='$date_aujourdhui',eleve_id='".$_SESSION['user']->uid."',ordre=@max+1");
		mysql_query("UNLOCK TABLES");
		mysql_query("UPDATE qdj SET compte".$_GET['vote']."=compte".$_GET['vote']."+1 WHERE date='$date_aujourdhui'");
		$a_vote = true;
	}

	// Affichage de la QDJ courante 
	qdj_affiche(false,$a_vote);		

	deconnecter_mysql_frankiz();
}
?>
