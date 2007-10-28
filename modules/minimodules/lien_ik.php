<?php
/*
	Copyright (C) 2006 Binet Réseau
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
class LienIKMiniModule extends FrankizMiniModule
{
	public function __construct()
	{
		global $DB_web;

		$DB_web->query("SELECT valeur FROM parametres WHERE nom='lienik'");
		list($lienik) = $DB_web->next_row();

		$this->assign("lien_ik_url", "ik.php?id=$lienik");
		$this->assign("lien_ik_img", "data/ik_thumbnails/$lienik.png");
		$this->tpl = "minimodules/lien_ik/lien_ik.tpl";
		$this->titre = "IK Electronique";
	}

	public static function check_auth()
	{
		return verifie_permission('interne');
	}
}
FrankizMiniModule::register_module('lien_ik', 'LienIKMiniModule', "Lien vers l'IK électronique");

?>
