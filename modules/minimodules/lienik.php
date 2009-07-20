<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                       *
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

class LienIKMiniModule extends FrankizMiniModule
{
	public static function init()
	{
		FrankizMiniModule::register('lienIK', new LienIKMiniModule(), 'run', AUTH_PUBLIC);
	}

	public function run()
	{
		global $DB_web;

/*		$DB_web->query("SELECT valeur FROM parametres WHERE nom='lienik'");
		list($lienik) = $DB_web->next_row();*/

//		$this->assign("lien_ik_url", "ik.php?id=$lienik");
//		$this->assign("lien_ik_img", "data/ik_thumbnails/$lienik.png");
		$this->tpl = "minimodules/lien_ik/lien_ik.tpl";
		$this->titre = "IK Electronique";
	}

	public static function check_auth()
	{
		return S::checkAuth(AUTH_INTERNE);
	}
}
//FrankizMiniModule::register_module('lien_ik', 'LienIKMiniModule', "Lien vers l'IK électronique");


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
