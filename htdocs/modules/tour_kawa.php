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
	Revision 1.12  2004/12/16 13:00:42  pico
	INNER en LEFT

	Revision 1.11  2004/12/07 21:54:09  pico
	Interface d'ajout des tours kawa pour le bob
	
	Revision 1.10  2004/12/07 20:56:35  pico
	Changement de la base de données de gestion des tours kawa
	
	Revision 1.9  2004/11/24 13:05:23  schmurtz
	Ajout d'un attribut type='discret' pour les liste et formulaire, afin d'avoir
	une presentation par defaut sans gros cadres autour.
	
	Revision 1.8  2004/11/05 08:29:23  pico
	Mise en forme de la sortie xml du tour kawa:
	on balançait du texte formaté, ce qui n'était du coup que très peu skinable, j'ai mis ça sous la forme d'une liste, ce sera plus pratique
	(la skin basique affiche ça, la skin pico aussi, la skin défaut n'affiche pas encore les tours kawa...)
	
	Revision 1.7  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.6  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.5  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
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
