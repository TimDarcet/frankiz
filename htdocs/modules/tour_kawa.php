<?php
/*
	Gestion du tour kawa.
	
	$Log$
	Revision 1.6  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")

	Revision 1.5  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

if(est_authentifie(AUTH_MINIMUM)) {

	// Génération des tours kawa
	$kawa = array("","");
	for ($i = 1; $i <= 2; $i++) {
		$DB_web->query("SELECT groupe FROM kawa.jour WHERE (jour=\"".(unixtojd(time())+12+$i)."\")");
		$row=$DB_web->next_row();
		$jour = array("Aujourd'hui : ","Demain : ");
		if (strcasecmp("personne", $row[0]) != 0 && $row[0]!="") 
			$kawa[$i-1] = $row[0];
	}

	$jour = array("Aujourd'hui","Demain");
	if ($kawa[0] != "" || $kawa[1] != "") {
		echo "<module id=\"tour_kawa\" titre=\"Tour Kawa\">\n";
		
		for ($i = 0; $i <= 1; $i++)
			if ($kawa[$i] != "")
				echo "<p>".$jour[$i]." : ".$kawa[$i]."</p>\n";

		echo "</module>\n";
	}
}
?>