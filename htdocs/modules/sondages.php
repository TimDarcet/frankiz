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
	Affichage des liens vers les sondages

	$Log$
	Revision 1.2  2004/11/17 23:46:21  kikx
	Prepa pour le votes des sondages

	Revision 1.1  2004/11/17 22:19:15  kikx
	Pour avoir un module sondage
	

*/

//require_once BASE_LOCAL."/include/qdj.inc.php";

if(est_authentifie(AUTH_MINIMUM)) {
	echo "<module id=\"sondages\" titre=\"Sondages\">\n";

	if(!cache_recuperer('sondages',strtotime(date("Y-m-d",time())))) {
		$DB_web->query("SELECT sondage_id,titre,perime FROM sondage_question WHERE TO_DAYS(perime) - TO_DAYS(NOW()) >=0");
		echo "<p>En Cours</p>" ;
		while(list($id,$titre,$date) = $DB_web->next_row()) {
			echo "<lien id='sondage_encours' titre='$titre (".date("d/m",strtotime($date)).")' url='".BASE_URL."/sondage.php?id=$id'/>\n";
		}
		
		echo "<p>Anciens</p>" ;
		$DB_web->query("SELECT sondage_id,titre,perime FROM sondage_question WHERE TO_DAYS(perime) - TO_DAYS(NOW()) <0 AND TO_DAYS(perime) - TO_DAYS(NOW()) >=-7");
		while(list($id,$titre,$date) = $DB_web->next_row()) {
			echo "<lien id='sondage_ancien' titre='$titre (".date("d/m",strtotime($date)).")' url='".BASE_URL."/sondage.php?id=$id'/>\n";
		}
		
		cache_sauver('sondages');
	}

	echo "</module>\n";
}
?>
