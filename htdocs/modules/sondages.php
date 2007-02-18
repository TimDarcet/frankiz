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

	$Id$

*/

if(est_authentifie(AUTH_MINIMUM)) {
	echo "<module id=\"sondages\" titre=\"Sondages\">\n";
	if(!cache_recuperer('sondages',strtotime(date("Y-m-d",time())))) {
		$DB_web->query("SELECT sondage_id,titre,perime FROM sondage_question WHERE TO_DAYS(perime) - TO_DAYS(NOW()) >=-7");
		if($DB_web->num_rows()>0){
			$DB_web->query("SELECT sondage_id,titre,DATE_FORMAT(perime,'%d/%m'),restriction FROM sondage_question WHERE TO_DAYS(perime) - TO_DAYS(NOW()) >=0");
			if($DB_web->num_rows()>0){
				echo "<p>En Cours</p>" ;
				while(list($id,$titre,$date,$restriction) = $DB_web->next_row()) {

					if ($restriction != "aucune") {
						$restr = explode("_",$restriction);
						if ($restr[0]=="promo") $restriction_nom = $restr[1];
						if ($restr[0]=="section") {
							$DB_trombino->query("SELECT nom FROM sections WHERE section_id = $restr[1]");
							list($restriction_nom) = $DB_trombino->next_row();
						}
						if ($restr[0]=="binet") {
							$DB_trombino->query("SELECT nom FROM binets WHERE binet_id = $restr[1]");
							list($restriction_nom) = $DB_trombino->next_row();
						}
						$restriction_nom = "[".$restriction_nom."] ";
					}
					else {$restriction_nom = "";}

					echo "<lien id='sondage_encours' titre='$restriction_nom$titre ($date)' url='sondage.php?id=$id'/><br/>\n";
				}
			}
			
			$DB_web->query("SELECT sondage_id,titre,DATE_FORMAT(perime,'%d/%m'),restriction FROM sondage_question WHERE TO_DAYS(perime) - TO_DAYS(NOW()) <0 AND TO_DAYS(perime) - TO_DAYS(NOW()) >=-7");
			if($DB_web->num_rows()>0){
				echo "<p>Anciens</p>" ;
				while(list($id,$titre,$date,$restriction) = $DB_web->next_row()) {

					if ($restriction != "aucune") {
						$restr = explode("_",$restriction);
						if ($restr[0]=="promo") $restriction_nom = $restr[1];
						if ($restr[0]=="section") {
							$DB_trombino->query("SELECT nom FROM sections WHERE section_id = $restr[1]");
							list($restriction_nom) = $DB_trombino->next_row();
						}
						if ($restr[0]=="binet") {
							$DB_trombino->query("SELECT nom FROM binets WHERE binet_id = $restr[1]");
							list($restriction_nom) = $DB_trombino->next_row();
						}
						$restriction_nom = "[".$restriction_nom."] ";
					}
					else {$restriction_nom = "";}

					echo "<lien id='sondage_ancien' titre='$restriction_nom$titre ($date)' url='sondage.php?id=$id'/><br/>\n";
				}
			}
		}
		cache_sauver('sondages');
	}

	$DB_web->query("SELECT sondage_id,titre,DATE_FORMAT(perime,'%d/%m') FROM sondage_question WHERE TO_DAYS(perime) - TO_DAYS(NOW()) < -7 AND eleve_id = ".$_SESSION['user']->uid);
	if ($DB_web->num_rows() > 0) {
		echo "<p>Mes anciens sondages</p>";
		while (list($id,$titre,$date) = $DB_web->next_row()) {
			echo "<lien id='sondage_ancien' titre='$titre' url='sondage.php?id=$id' /><br />\n";
		}
	}
	echo "</module>";
}
?>
