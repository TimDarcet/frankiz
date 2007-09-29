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

class FetesMiniModule extends FrankizMiniModule
{
	public function __construct()
	{
		global $globals, $DB_trombino;

		$DB_trombino->query("SELECT prenom 
		                       FROM fetes 
				      WHERE MONTH(date) = MONTH(NOW()) AND DAYOFMONTH(date) = DAYOFMONTH(NOW())");

		$fetes = array();
		while (list($prenom) = $DB_trombino->next_row())
			$fetes[] = $prenom;
	
		$globals->smarty->assign("fetes", $fetes);
		$this->tpl = "minimodules/fetes/fetes.tpl";
		$this->titre = "Fêtes du jour";
	}

	public static function check_auth()
	{
		return est_authentifie(AUTH_INTERNE);
	}
}
?>
