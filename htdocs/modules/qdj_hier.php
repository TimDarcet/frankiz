<?php
/*
	$Id$
	
	Affichage des résultats de la DQJ de la veille.
*/

require_once BASE_LOCAL."/include/qdj.inc.php";

if(est_authentifie(AUTH_MINIMUM)) {
	qdj_affiche(true,true);
}
?>