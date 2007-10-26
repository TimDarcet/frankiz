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
	Script de création de la partie activités contenant des images type "affiche".
	
	$Id$

*/
class ActivitesMiniModule extends FrankizMiniModule
{
	public function __construct()
	{
		global $globals, $DB_web;
		
		$DB_web->query("SELECT affiche_id, titre, url, date, exterieur 
				  FROM affiches 
				 WHERE TO_DAYS(date) = TO_DAYS(NOW())
			      ORDER BY date");

		$activites = array();
		while ($row = $DB_web->next_row())
		{
			$activites[] = array('titre' => $row['titre'],
					     'url' => $row['url'],
					     'date' => $row['date'],
					     'exterieur' => $row['exterieur'],
					     'image' => $row['affiche_id']);
		}

		if (!getEtatBob() && count($activites) == 0)
			return;

		$globals->smarty->assign('activites_etat_bob', getEtatBob());
		$globals->smarty->assign('activites' , $activites);
		$this->tpl = "minimodules/activites/activites.tpl";
		$this->titre = "Activités";
	}

	public static function check_auth()
	{
		return est_authentifie(AUTH_INTERNE);
	}
}
FrankizMiniModule::register_module('activites', "ActivitesMiniModule", "Activités du jour");

?>
