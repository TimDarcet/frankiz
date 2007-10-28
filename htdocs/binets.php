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
	Affichage de la liste des binets ayant un site web.

	$Id$

*/
require_once "include/global.inc.php";

// demande_authentification(AUTH_COOKIE);

// Récupération d'une image
if(isset($_REQUEST['image'])){
	$DB_trombino->query("SELECT image,format FROM binets WHERE binet_id='{$_REQUEST['id']}'");
	list($image,$format) = $DB_trombino->next_row() ;
	header("content-type: $format");
	echo $image;
	exit;
}


// Affichage de la liste des binets
require BASE_LOCAL."/include/page_header.inc.php";
require BASE_MODULES."/trombino.php";
call ("TrombinoModule", "binets");
require "include/page_footer.inc.php" ?>
