<?php
/*
	Copyright (C) 2004 Binet R�seau
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
	Revision 1.7  2005/01/21 16:49:41  pico
	erreur

	Revision 1.6  2005/01/21 16:48:29  pico
	Modifs de chemins
	
	Revision 1.5  2004/12/17 14:26:20  pico
	Pas d'action pour les listes non s�lectionnables
	
	Revision 1.4  2004/12/16 13:00:41  pico
	INNER en LEFT
	
	Revision 1.3  2004/12/15 01:54:43  kikx
	Bug sinon sur la ersion de dev
	
	Revision 1.2  2004/12/15 01:51:04  kikx
	Car finalement on s'en fout des commentaires
	
	Revision 1.1  2004/11/24 12:51:58  kikx
	Oubli de ma part
	


*/

require_once "include/global.inc.php";

// g�n�ration de la page
require "include/page_header.inc.php";
?>
<page id='siteseleves' titre='Frankiz : Sites �l�ves'>
<h1>Sites de certains �l�ves de l'X</h1>
	<liste id="page_eleves" selectionnable="non">
		<entete id="eleves" titre=""/>
<?
		$DB_web->query("SELECT e.eleve_id,e.nom,e.prenom,e.promo,commentaires,e.login FROM sites_eleves LEFT JOIN trombino.eleves as e USING(eleve_id) ORDER BY promo DESC") ;
		while(list($id,$nom,$prenom,$promo,$commentaire,$login) = $DB_web->next_row()) {
			echo "\t\t<element id=\"$id\">\n";
				echo "\t\t\t<colonne id=\"eleves\"><lien id='$id' titre='$prenom $nom ($promo)' url='";
				if($_SESSION['user']->est_authentifie(AUTH_INTERNE)) echo URL_PAGEPERSO;
				else echo "webperso";
				echo "/$login-$promo/'/></colonne>\n";
			echo "\t\t</element>\n";
		}
?>
	</liste>
</page>
<?
require_once "include/page_footer.inc.php";
?>
