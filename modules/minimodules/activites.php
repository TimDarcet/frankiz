<?php
/***************************************************************************
 *  Copyright (C) 2008 Binet Réseau                                       *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                     *
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

class ActivitesMiniModule extends FrankizMiniModule
{
	public static function init(){
		FrankizMiniModule::register('activites', new ActivitesMiniModule(), 'run', AUTH_PUBLIC); 
	}
	
	public function run()
	{
		global $DB_web;
		
	/*	$DB_web->query("SELECT affiche_id, titre, url, date, exterieur 
				  FROM affiches 
				 WHERE TO_DAYS(date) = TO_DAYS(NOW())
			      ORDER BY date");*/

		$activites = array();
/*		while (list($affiche_id, $titre, $url, $date, $exterieur) = $DB_web->next_row())
		{
			$activites[] = array('titre' => $titre,
					     'url' => $url,
					     'date' => $date,
					     'exterieur' => $exterieur,
					     'image' => "data/affiches/$affiche_id");
		}
*/
/*		if (!getEtatBob() && count($activites) == 0)
			return;
*/
//		$this->assign('activites_etat_bob', getEtatBob());
        $this->assign('activites_etat_bob', true);
		$this->assign('activites' , $activites);
		$this->tpl = "minimodules/activites/activites.tpl";
		$this->titre = "Activités";
	}

	public static function check_auth()
	{
		return true;
	}
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
