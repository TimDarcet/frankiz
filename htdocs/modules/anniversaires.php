<?php
/*
	Affichage des anniversaires avec gestion d'un cache mis à jour une fois par jour.
	
	$Log$
	Revision 1.12  2004/09/20 20:33:47  schmurtz
	Mise en place d'un systeme de cache propre

	Revision 1.11  2004/09/17 16:27:26  schmurtz
	Simplification de l'affichage des anniversaires et correction d'un bug d'affichage.
	
	Revision 1.10  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.9  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

if(est_authentifie(AUTH_MINIMUM)) {
	echo "<module id=\"anniversaires\" titre=\"Anniversaires\">\n";

	if(!cache_recuperer('anniversaires',strtotime(date("Y-m-d",time())))) {
		$DB_trombino->query("SELECT nom,prenom,surnom,promo,mail FROM eleves "
							   ."WHERE MONTH(date_nais)=MONTH(NOW()) AND DAYOFMONTH(date_nais)=DAYOFMONTH(NOW()) "
							   ."AND (promo='2002' OR promo='2003')");
		while(list($nom,$prenom,$surnom,$promo,$mail) = $DB_trombino->next_row())
			echo "\t<eleve nom='$nom' prenom='$prenom' surnom='$surnom' promo='$promo' mail='$mail'/>\n";
		
		cache_sauver('anniversaires');
	}

	echo "</module>\n";
}
