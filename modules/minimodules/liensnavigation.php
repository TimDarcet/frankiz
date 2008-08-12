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
	Liens de navigation dans le site web.	
	
	$Id$

*/

class LiensNavigationMiniModule extends FrankizMiniModule
{
	public function init()
	{
		FrankizMiniModule::register('liensNavigation', AUTH_PUBLIC);
	}

	public function run()
	{
		$this->tpl = "minimodules/liens_navigation/main.tpl";
		$this->header_tpl = "minimodules/liens_navigation/header.tpl";
		$this->titre = "Navigation dans le site";
	}

	public static function check_auth()
	{
		return true;
	}
}
FrankizMiniModule::register_module('liens_navigation', "LiensNavigationMiniModule", "Liens pour naviguer sur le site (indispensable)");

?>
