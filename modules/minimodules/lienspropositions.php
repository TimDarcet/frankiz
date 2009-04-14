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

class LiensPropositionsMiniModule extends FrankizMiniModule
{
	public static function init()
	{
		FrankizMiniModule::register('lienspropositions', new LiensPropositionsMiniModule(), 'run', AUTH_COOKIE);
	}
	
	public function run()
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
//FrankizMiniModule::register_module("liens_propositions", "LiensPropositionsMiniModule", "Liens pour proposer du contenu sur Frankiz");


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
