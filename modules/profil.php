<?php
/*
	Copyright (C) 2007 Binet Réseau
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
	Module d'annonces

	$Id: annonces.php 1969 2007-09-29 13:02:41Z elscouta $

*/
require_once BASE_FRANKIZ."htdocs/include/minimodules.inc.php";

class ProfilModule extends PLModule
{
	public function handlers()
	{
		return array('profil/skin'               => $this->make_hook('skin', AUTH_MINIMUM),
			     'profil/skin/change_skin'   => $this->make_hook('skin_change', AUTH_MINIMUM),
			     'profil/skin/change_params' => $this->make_hook('skin_params', AUTH_MINIMUM));
	}

	function handler_skin(&$page)
	{
		// Recupére la liste des mini modules et verifie leur visibilite
		$minimodule_list = FrankizMiniModule::get_minimodule_list();
		$my_minimodule_list = array();
		foreach ($minimodule_list as $id => $desc)
			$my_minimodule_list[] = array('id'          => $id,
			                              'est_visible' => $_SESSION['skin']->est_minimodule_visible($id),
						      'desc'        => $desc);

		$page->assign('liste_skins', Skin::get_skin_list());
		$page->assign('liste_minimodules', $my_minimodule_list);
		$page->assign('title', "Choix de la skin");
		$page->changeTpl("profil/skin.tpl");
	}

	function handler_skin_change(&$page)
	{
		$skin = $_SESSION['skin']->change_skin($_REQUEST['newskin']);

		if ($skin)
		{
			$cookie = $skin->serialize();
			SetCookie(FRANKIZ_SESSION_NAME."_skin", base64_encode($cookie), time() + 3*365*24*3600);
			$DB_web->query("UPDATE compte_frankiz SET skin='$cookie' WHERE eleve_id='{$_SESSION['user']->uid}'");
		}

		$this->handler_skin($page);
	}

	function handler_skin_params(&$page)
	{
		$minimodule_list = FrankizMiniModule::get_minimodule_list();
		foreach (array_keys($minimodule_list) as $module)
			$_SESSION['skin']->set_minimodule_visible($module, isset($_REQUEST["vis_$module"]));
	
		$this->handler_skin($page);
	}
}
?>

