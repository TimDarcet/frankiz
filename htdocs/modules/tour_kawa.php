<?php
/*
	Copyright (C) 2004 Binet Réseau
	http://www.polytechnique.fr/eleves/binets/br/
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/
/*
	Gestion du tour kawa.
	
	$Log$
	Revision 1.7  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site

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