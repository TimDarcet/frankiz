<?php
/*
	$Id$
	
	Gestion du tour kawa.
*/

if(est_authentifie(AUTH_MINIMUM)) {
	connecter_mysql_frankiz();

	// Génération des tours kawa
	$kawa = array("","");
	for ($i = 1; $i <= 2; $i++) {
		$result = mysql_query("SELECT groupe FROM kawa.jour WHERE (jour=\"".(unixtojd(time())+12+$i)."\")");
		$row=mysql_fetch_row($result);
		$jour = array("Aujourd'hui : ","Demain : ");
		if (strcasecmp("personne", $row[0]) != 0 && $row[0]!="") 
			$kawa[$i-1] = $row[0];
		mysql_free_result($result);
	}
	deconnecter_mysql_frankiz();

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