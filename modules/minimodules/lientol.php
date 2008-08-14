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
	Affichage d'un lien sur la page d'accueil vers le tol.
	
	$Id$

*/
class LienTolMiniModule extends FrankizMiniModule
{
	public static function init(){
		FrankizMiniModule::register('lienTol', new LienTolMiniModule(), 'run', AUTH_PUBLIC);
	}

	public function run()
	{
		$this->tpl = "minimodules/lien_tol/lien_tol.tpl";
		$this->titre = "Tol";
	}

	public static function check_auth()
	{
		return verifie_permission('interne');
	}
}
//FrankizMiniModule::register_module('lien_tol', 'LienTolMiniModule', "Lien rapide vers le TOL");

?>
