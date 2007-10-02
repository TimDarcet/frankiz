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
	
	$Id$

*/

if(est_authentifie(AUTH_MINIMUM)) {

	echo "<module id=\"tour_kawa\" titre=\"Tour Kawa\">\n";
	$tour_existe = false;
	
	// Génération des tours kawa
	$jour = array("Aujourd'hui","Demain");
	for ($i = 0; $i <= 1; $i++) {
		$DB_web->query("SELECT sections.nom FROM kawa LEFT JOIN trombino.sections ON kawa.section_id=sections.section_id WHERE (date=\"".date("Y-m-d",time()+$i * 3600 *24)."\")");
		list($groupe)=$DB_web->next_row();
		
		if(strcasecmp("personne", $groupe) != 0 && $groupe != "") {
			// si c'est le premier tour kawa, on ouvre la liste
			if(!$tour_existe) echo "<liste id=\"tour_kawa\" selectionnable=\"non\">\n";
			
			echo "<element id=\"$i\">";
			echo "<colonne id=\"jour\">$jour[$i]</colonne>";
			echo "<colonne id=\"kawa\">$groupe</colonne>";
			echo "</element>\n";

			$tour_existe = true;
		}
	}

	if($tour_existe) {
		// il y a eu des tours kawa : on ferme la liste
		echo "</liste>\n";
	} else {
		// il n'y a pas de tour kawa prévu
		echo "<p>Pas d'attribution de tour kawa.</p>\n";
	}

	echo "</module>\n";
}
?>
