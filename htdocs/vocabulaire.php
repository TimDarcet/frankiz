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
	Vocabulaire de l'X
	
	$Log$
	Revision 1.2  2004/11/29 17:27:32  schmurtz
	Modifications esthetiques.
	Nettoyage de vielles balises qui trainaient.

	Revision 1.1  2004/10/31 22:14:52  kikx
	Oubli
*/

require_once "include/global.inc.php";

// génération de la page
require "include/page_header.inc.php";
?>
<page id='vocabulaire' titre='Frankiz : vocabulaire'>
<h1>Vocabulaire propre à l'X</h1>
	<liste id="liste_voc" selectionnable="non" action="vocabulaire.php">
		<entete id="mot" titre="Expression"/>
		<entete id="description" titre="Description"/>
<?
		$DB_web->query("SELECT vocab_id,mot,explication FROM vocabulaire ORDER BY mot") ;
		while(list($vocab_id,$mot,$explication) = $DB_web->next_row()) {
			echo "\t\t<element id=\"$vocab_id\">\n";
				echo "\t\t\t<colonne id=\"mot\">$mot</colonne>\n";
				echo "\t\t\t<colonne id=\"explication\">$explication</colonne>\n";
			echo "\t\t</element>\n";
		}
?>
	</liste>
</page>
<?
require_once "include/page_footer.inc.php";
?>