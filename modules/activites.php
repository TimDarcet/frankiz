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

class ActivitesModule extends PLModule
{
	function handlers()
	{
		return array("activites" => $this->make_hook("activites", AUTH_PUBLIC));
	}
	
	function handler_activites(&$page)
	{
		global $DB_web;
	
		$page->assign('title', 'Frankiz : activités de la semaine');
		$page->changeTpl("activites/activites.tpl");
		
		// Etat Bob
		$page->assign('bob_ouvert', getEtatBob());

		// Etat Kes
		$DB_web->query("SELECT valeur FROM parametres WHERE nom='kes'");
		list($valeur_kes) = $DB_web->next_row();
		$page->assign('kes_ouverte', $valeur_kes);

		// Autres activités
		if (!FrankizSession::est_interne()) 
			$exterieur_rule = "AND  exterieur = '1'";
		else
			$exterieur_rule = "";
		
		$DB_web->query("SELECT  affiche_id, titre, url, DATE(date), date, description 
		                  FROM  affiches 
				 WHERE  DATEDIFF(date, NOW()) < 7
				   AND  DATEDIFF(date, NOW()) >= 0
				        $exterieur_rule
			      ORDER BY  date");

		while (list($id, $titre, $url, $date, $datetime, $texte) = $DB_web->next_row()) 
		{
			$activites[$date][] = array('id' => $id,
					            'titre' => $titre,
						    'url' => $url,
						    'date' => $datetime,
						    'texte' => $texte);
		}
		$page->assign('activites', $activites);
	}
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
