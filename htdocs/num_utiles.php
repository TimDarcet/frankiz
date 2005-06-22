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
	Numero utiles
	
	$Id$
	
*/

require_once "include/global.inc.php";

// génération de la page
require "include/page_header.inc.php";
?>
<page id='num_utiles' titre='Frankiz : Numéros utiles'>
<h1>Numeros Utiles</h1>
<?
$DB_web->query("SELECT DISTINCT categorie FROM num_utiles GROUP BY categorie") ;
while(list($categorie) = $DB_web->next_row()) {
?>
	<h2><?=$categorie?></h2>
	<liste id="liste_num" selectionnable="non">
		<entete id="endroit" titre=""/>
		<entete id="poste" titre="num. Poste"/>
<?		
		$DB_web->push_result() ;
		$DB_web->query("SELECT endroit,poste FROM num_utiles WHERE categorie='$categorie' ORDER BY endroit") ;
		while(list($endroit,$poste) = $DB_web->next_row()) {
			echo "\t\t<element id=\"$poste\">\n";
				echo "\t\t\t<colonne id=\"endroit\">$endroit</colonne>\n";
				echo "\t\t\t<colonne id=\"post\">$poste</colonne>\n";
			echo "\t\t</element>\n";
		}
		$DB_web->pop_result();
?>
	</liste>
<?
}
?>
</page>
<?
require_once "include/page_footer.inc.php";
?>