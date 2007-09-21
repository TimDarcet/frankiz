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
	Recherche dans le trombino.

	$Id: trombino.php 1943 2007-09-13 21:44:09Z elscouta $

*/

require_once "include/global.inc.php";

demande_authentification(AUTH_INTERNE);

// Recuperation d'une image
if (!empty($_GET['id']))
{
	// les magic quotes, c'est pas cool
	$id = html_entity_decode(stripslashes($_GET['id']), ENT_QUOTES);
	// &apos; n'est pas traduit par html_entity_decode
	$id = str_replace("&apos;", "'", $id);
	
	$file = BASE_BINETS."ik/".basename($id); 

	if (return_file($file))
		exit;
}

not_found();

?>
