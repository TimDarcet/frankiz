<?php
/*
	$Id$
	
	Affichage des résultats de la DQJ de la veille.
*/

require_once BASE_LOCAL."/include/qdj.inc.php";

if(est_authentifie(AUTH_MINIMUM)) {
	connecter_mysql_frankiz();
	qdj_affiche(true,true);
	deconnecter_mysql_frankiz();	
}
?>