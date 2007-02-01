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
	Page de gestion "automatique" de l'état du bob

	$Id: etat_bob.php 1788 2006-11-19 23:32:53Z alakazam $
	
*/

require_once "../../include/global.inc.php";
$ip = ip_get();
$requete = "SELECT `piece_id`  FROM prises WHERE ip = '$ip'";
$DB_admin->query($requete);
header("Content-type: image/gif");
if($DB_admin->num_rows() > 0){
  list($kzert)= $DB_admin->next_row();
  $id = substr($kzert, 0, 2);
  $list = array("70", "71", "72", "73", "74", "75", "76", "77", "78", "79", "80", "A4", "D6", "09", "10", "11", "12", "17"); 
  if (in_array($id, $list)){
    readfile("./anims/anim".$id.".gif");
  }
  else{
    readfile("./anims/animbob.gif");
  }
}
else{
  readfile("./anims/animbob.gif");
}


?>
