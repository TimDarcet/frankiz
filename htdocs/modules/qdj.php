<?php
/*
	Affichage de la QDJ actuelle et gestion des votes.
	
	TODO traiter le cas ou le qdj master est à la bourre (garder l'ancienne qdj par exemple).

	$Log$
	Revision 1.8  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")

	Revision 1.7  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

require_once BASE_LOCAL."/include/qdj.inc.php";

if(est_authentifie(AUTH_MINIMUM)) {

	// Nettoyage du cache si on a changé de jour
	if(file_exists(BASE_LOCAL."/cache/qdj_hier") && filemtime(BASE_LOCAL."/cache/qdj_hier") < time()-3025-24*3600)
		unlink(BASE_LOCAL."/cache/qdj_hier");

	if(file_exists(BASE_LOCAL."/cache/qdj_courante") && filemtime(BASE_LOCAL."/cache/qdj_courante") < time()-3025)
		unlink(BASE_LOCAL."/cache/qdj_courante");

	// On cherche si l'utilisateur a déjà voté ou non
	$date_aujourdhui = date("Y-m-d", time()-3025);
	$DB_web->query("SELECT 0 FROM qdj_votes WHERE date='$date_aujourdhui' and eleve_id='".$_SESSION['user']->uid."' LIMIT 1");
	$a_vote = $DB_web->num_rows() != 0;

	// Gestion du vote
	if(isset($_GET['qdj']) && $date_aujourdhui==$_GET['qdj'] && !$a_vote && ($_GET['vote']==1 || $_GET['vote']==2)) {
		unlink(BASE_LOCAL."/cache/qdj_courante");
		$DB_web->query("LOCK TABLE qdj_votes WRITE");
		$DB_web->query("SELECT @max:=IFNULL(MAX(ordre),0) FROM qdj_votes WHERE date='$date_aujourdhui'");
		$DB_web->query("INSERT INTO qdj_votes SET date='$date_aujourdhui',eleve_id='".$_SESSION['user']->uid."',ordre=@max+1");
		$DB_web->query("UNLOCK TABLES");
		$DB_web->query("UPDATE qdj SET compte".$_GET['vote']."=compte".$_GET['vote']."+1 WHERE date='$date_aujourdhui'");
		rediriger_vers("/");
	}

	// Affichage de la QDJ courante 
	qdj_affiche(false,$a_vote);		

}
?>
