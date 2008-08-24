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
	Liens permettants d'accéder aux autres sites de l'école.
	
	$Id$

*/
class LiensUtilesMiniModule extends FrankizMiniModule
{
	public function init()
	{
		FrankizMiniModule::register('liensUtiles', AUTH_PUBLIC);
	}
	
	public function run()
	{
		$this->tpl = "minimodules/liens_utiles/liens_utiles.tpl";
		$this->titre = "Liens Utiles";
	}

	public static function check_auth()
	{
		return true;
	}
}
FrankizMiniModule::register_module("liens_utiles", "LiensUtilesMiniModule", "Liens Utiles");
?>