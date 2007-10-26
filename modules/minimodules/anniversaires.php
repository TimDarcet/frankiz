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
	Affichage des anniversaires avec gestion d'un cache mis à jour une fois par jour.
	
	$Id$

*/

class AnniversairesMiniModule extends FrankizMiniModule
{
	public function __construct()
	{
		global $page, $DB_web, $DB_trombino;

		$DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus'");
		list($promo_temp) = $DB_web->next_row();

		$DB_trombino->query("SELECT nom,prenom,surnom,promo,mail,login 
				       FROM eleves 
				      WHERE MONTH(date_nais) = MONTH(NOW()) AND 
				            DAYOFMONTH(date_nais) = DAYOFMONTH(NOW()) AND 
					    (promo=$promo_temp OR promo=($promo_temp-1)) 
			           ORDER BY promo;");
	
		$anniversaires = array($promo_temp-1 => array(),
				       $promo_temp   => array());
		while(list($nom, $prenom, $surnom, $promo, $mail, $login) = $DB_trombino->next_row())
			$anniversaires[$promo][] = array('nom'    => $nom,
							 'prenom' => $prenom,
							 'surnom' => $surnom,
							 'mail'   => $mail,
							 'login'  => $login); 
		
		$page->assign('anniversaires', $anniversaires);
		$this->tpl = "minimodules/anniversaires/anniversaires.tpl";
		$this->titre = "Joyeux anniversaire!";
	}

	public static function check_auth()
	{
		return est_authentifie(AUTH_INTERNE);
	}
}
FrankizMiniModule::register_module('anniversaires', 'AnniversairesMiniModule', "Anniversaires du Jour");

?>