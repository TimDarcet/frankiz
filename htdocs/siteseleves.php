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
	Sites Eleves
	
	$Log$
	Revision 1.2  2004/12/15 01:51:04  kikx
	Car finalement on s'en fout des commentaires

	Revision 1.1  2004/11/24 12:51:58  kikx
	Oubli de ma part
	


*/

require_once "include/global.inc.php";

// génération de la page
require "include/page_header.inc.php";
?>
<page id='siteseleves' titre='Frankiz : Sites élèves'>
<h1>Sites de certains élèves de l'X</h1>
	<liste id="page_eleves" selectionnable="non" action="vocabulaire.php">
		<entete id="eleves" titre=""/>
<?
		$DB_web->query("SELECT e.eleve_id,e.nom,e.prenom,e.promo,commentaires,e.login FROM sites_eleves INNER JOIN trombino.eleves as e USING(eleve_id) ORDER BY promo DESC") ;
		while(list($id,$nom,$prenom,$promo,$commentaire,$login) = $DB_web->next_row()) {
			echo "\t\t<element id=\"$id\">\n";
				echo "\t\t\t<colonne id=\"eleves\"><lien id='$id' titre='$prenom $nom ($promo)' url='".URL_PAGEPERSO."$login-$promo/'/></colonne>\n";
			echo "\t\t</element>\n";
		}
?>
	</liste>
</page>
<?
require_once "include/page_footer.inc.php";
?>