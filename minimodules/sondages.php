<?php
/***************************************************************************
 *  Copyright (C) 2004-2013 Binet Réseau                                   *
 *  http://br.binets.fr/                                                   *
 *                                                                         *
 *  This program is free software; you can redistribute it and/or modify   *
 *  it under the terms of the GNU General Public License as published by   *
 *  the Free Software Foundation; either version 2 of the License, or      *
 *  (at your option) any later version.                                    *
 *                                                                         *
 *  This program is distributed in the hope that it will be useful,        *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of         *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          *
 *  GNU General Public License for more details.                           *
 *                                                                         *
 *  You should have received a copy of the GNU General Public License      *
 *  along with this program; if not, write to the Free Software            *
 *  Foundation, Inc.,                                                      *
 *  59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                *
 ***************************************************************************/

class SondagesMiniModule extends FrankizMiniModule
{
	private function get_sondages()
	{
		global $DB_web;

		$sondages = array();
/*		while (list($id,$titre,$date,$restriction) = $DB_web->next_row())
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
*/
		return $sondages;
	}
	
	public function init()
	{
		FrankizMiniModule::register('sondages', AUTH_PUBLIC);
	}

        public function tpl()
        {
            return 'minimodules/sondages/sondages.tpl';
        }

        public function title()
        {
            return 'Sondages';
        }

        public function auth()
        {
            return AUTH_COOKIE;
        }

	public function run()
	{
		global $page, $DB_web;

/*		$DB_web->query("SELECT sondage_id, titre, DATE_FORMAT(perime,'%d/%m'), restriction 
		                  FROM sondage_question
				 WHERE TO_DAYS(perime) - TO_DAYS(NOW()) >=-7");*/
		$this->assign("courants", $this->get_sondages());

/*		$DB_web->query("SELECT sondage_id, titre, DATE_FORMAT(perime,'%d/%m'), restriction 
		                  FROM sondage_question 
				 WHERE TO_DAYS(perime) - TO_DAYS(NOW()) < 0 AND TO_DAYS(perime) - TO_DAYS(NOW()) >= -7");*/
		$this->assign("anciens", $this->get_sondages());
		
/*		$DB_web->query("SELECT sondage_id, titre, DATE_FORMAT(perime,'%d/%m'), restriction
		                  FROM sondage_question 
				 WHERE TO_DAYS(perime) - TO_DAYS(NOW()) < -7 AND eleve_id = {$_SESSION['uid']}");*/
		$this->assign("anciens_user", $this->get_sondages());
	}
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
