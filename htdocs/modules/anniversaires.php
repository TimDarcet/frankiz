<?php
/*
	Affichage des anniversaires avec gestion d'un cache mis � jour une fois par jour.
	
	$Id$
*/

if(est_authentifie(AUTH_MINIMUM)) {
	echo "<module id=\"anniversaires\" titre=\"Anniversaires\">\n";

	$fichier_cache = BASE_LOCAL."/cache/anniversaires";

	if(file_exists($fichier_cache) && date("Y-m-d", filemtime($fichier_cache)) == date("Y-m-d",time())) {
		readfile($fichier_cache);
		
	} else {
		connecter_mysql_frankiz();
		$resultat = mysql_query("SELECT nom,prenom,surnom,section,cie,promo,login,mail FROM eleves "
							   ."WHERE MONTH(date_nais)=MONTH(NOW()) AND DAYOFMONTH(date_nais)=DAYOFMONTH(NOW()) "
							   ."AND (promo='2002' OR promo='2003')");
		$contenu = "";
		while(list($nom,$prenom,$surnom,$section,$cie,$promo,$login,$mail) = mysql_fetch_row($resultat)) {
			$contenu .= "\t<eleve nom='$nom' prenom='$prenom' surnom='$surnom' section='$section' cie='$cie'"
					   ." promo='$promo' login='$login' mail='$mail'/>\n";
		}
		mysql_free_result($resultat);
		deconnecter_mysql_frankiz();
		
		$file = fopen($fichier_cache, 'w');
		fwrite($file, $contenu);
		fclose($file);                 

		echo $contenu;
	}

	echo "</module>\n";
}
