<?php
/*
	$Id$
	
	Affichage des résultats de la DQJ de la veille.
	
	$Log$
	Revision 1.4  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"

*/

require_once BASE_LOCAL."/include/qdj.inc.php";

if(est_authentifie(AUTH_MINIMUM)) {
	qdj_affiche(true,true);
}
?>