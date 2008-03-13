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
	Liens permettants de contacter les webmestres et faire des demandes.
	
	$Id$

*/
class LiensPropositionsMiniModule extends FrankizMiniModule
{
	public function __construct()
	{
		$this->tpl = "minimodules/liens_propositions/liens_propositions.tpl";
		$this->titre = "Contribuer";
	}

	public static function check_auth()
	{
		return true;
	return FrankizSession::est_authentifie(AUTH_COOKIE);
	}
}
FrankizMiniModule::register_module("liens_propositions", "LiensPropositionsMiniModule", "Liens pour proposer du contenu sur Frankiz");

?>