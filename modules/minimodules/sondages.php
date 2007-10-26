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
class SondagesMiniModule extends FrankizMiniModule
{
	private function get_sondages()
	{
		global $DB_web;

		$sondages = array();
		while (list($id,$titre,$date,$restriction) = $DB_web->next_row())
		{
			$restriction_nom = "";
			
			$restr = explode("_",$restriction);
			if ($restr[0]=="promo") {
				$restriction_nom = $restr[1];
			}

			if ($restr[0]=="section") {
				$DB_trombino->query("SELECT nom FROM sections WHERE section_id = $restr[1]");
				list($restriction_nom) = $DB_trombino->next_row();
			}
				
			if ($restr[0]=="binet") {
				$DB_trombino->query("SELECT nom FROM binets WHERE binet_id = $restr[1]");
				list($restriction_nom) = $DB_trombino->next_row();
			}

			$sondages[] = array('titre' => $titre,
					    'date' => $date,
					    'url' => "sondage.php?id=$id",
					    'restriction' => $restriction);
		}

		return $sondages;
	}
	
	public function __construct()
	{
		global $page, $DB_web;

		$DB_web->query("SELECT sondage_id, titre, DATE_FORMAT(perime,'%d/%m'), restriction 
		                  FROM sondage_question
				 WHERE TO_DAYS(perime) - TO_DAYS(NOW()) >=-7");
		$page->assign("sondages_courants", $this->get_sondages());

		$DB_web->query("SELECT sondage_id, titre, DATE_FORMAT(perime,'%d/%m'), restriction 
		                  FROM sondage_question 
				 WHERE TO_DAYS(perime) - TO_DAYS(NOW()) < 0 AND TO_DAYS(perime) - TO_DAYS(NOW()) >= -7");
		$page->assign("sondages_anciens", $this->get_sondages());
		
		$DB_web->query("SELECT sondage_id, titre, DATE_FORMAT(perime,'%d/%m'), restriction
		                  FROM sondage_question 
				 WHERE TO_DAYS(perime) - TO_DAYS(NOW()) < -7 AND eleve_id = {$_SESSION['user']->uid}");
		$page->assign("sondages_anciens_user", $this->get_sondages());

		$this->tpl = "minimodules/sondages/sondages.tpl";
		$this->titre = "Sondages";
	}

	public static function check_auth()
	{
		return est_authentifie(AUTH_MINIMUM);
	}
}
FrankizMiniModule::register_module("sondages", "SondagesMiniModule", "Sondages");
?>