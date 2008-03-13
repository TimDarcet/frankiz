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
	Page permettant de modifier son profil dans le trombino et quelques paramètres
	pour le site web.
	
	TODO modification de sa photo et de ses binets.
	
	$Id$

*/

require_once "../include/page_header.inc.php";
require_once "../../modules/profil.php";

// Récupération d'une image
if((isset($_REQUEST['image'])) && ($_REQUEST['image'] == "true") && ($_REQUEST['image'] != ""))
{
	demande_authentification(AUTH_MDP);
	ob_end();
	$size = getimagesize(BASE_DATA."trombino/a_valider_".$_REQUEST['id']);
	header("Content-type: {$size['mime']}");
	readfile(BASE_DATA."trombino/a_valider_".$_REQUEST['id']);
	exit;
}

if (isset($_POST['changer_frankiz']))
	call('ProfilModule', 'profil/fkz/change_mdp');
else if(isset($_POST['changer_trombino']))
	call('ProfilModule', 'profil/fkz/change_tol');
else if (isset($_POST['mod_binet']))
 	call('ProfilModule', 'profil/fkz/change_binet');
else if (isset($_POST['suppr_binet']))
	call('ProfilModule', 'profil/fkz/suppr_binet');
else if (isset($_POST['add_binet']))
	call('ProfilModule', 'profil/fkz/add_binet');
else
	call('ProfilModule', 'profil/fkz');

require_once BASE_FRANKIZ."include/page_footer.inc.php";
?>
