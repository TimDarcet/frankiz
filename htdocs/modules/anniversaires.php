<?php
/*
	$Id$
	
	Affichage des anniversaires avec gestion d'un cache mis à jour une fois par jour.
	
	$Log$
	Revision 1.9  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"

*/

if(est_authentifie(AUTH_MINIMUM)) {
	echo "<module id=\"anniversaires\" titre=\"Anniversaires\">\n";

	$fichier_cache = BASE_LOCAL."/cache/anniversaires";

	if(file_exists($fichier_cache) && date("Y-m-d", filemtime($fichier_cache)) == date("Y-m-d",time())) {
		readfile($fichier_cache);
		
	} else {
		$DB_trombino->query("SELECT eleves.nom,prenom,surnom,sections.nom,cie,promo,login,mail FROM eleves INNER JOIN sections USING(section_id)"
							   ."WHERE MONTH(date_nais)=MONTH(NOW()) AND DAYOFMONTH(date_nais)=DAYOFMONTH(NOW()) "
							   ."AND (promo='2002' OR promo='2003')");
		$contenu = "";
		while(list($nom,$prenom,$surnom,$section,$cie,$promo,$login,$mail) = $DB_trombino->next_row()) {
			$contenu .= "\t<eleve nom='$nom' prenom='$prenom' surnom='$surnom' section='$section' cie='$cie'"
					   ." promo='$promo' login='$login' mail='$mail'/>\n";
		}
		
		$file = fopen($fichier_cache, 'w');
		fwrite($file, $contenu);
		fclose($file);                 

		echo $contenu;
	}

	echo "</module>\n";
}
