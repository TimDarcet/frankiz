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
	Gestion de la session PHP.
	
	Les informations sur l'utilisateur sont stockées dans une variable de session,
	$_SESSION['rss'], contenant la liste des flux rss que veut afficher l'utilisateur
	$_SESSION['liens_rss'], contenant la liste des liens perso que veut afficher l'utilisateur.
	
	$Log$
	Revision 1.1  2004/11/24 15:55:34  pico
	Code pour gérer les liens perso + les rss au lancement de la session

*/

if( !isset($_SESSION['rss']) || nouveau_login() ) {
	$_SESSION['rss'] = array();
	$DB_web->query("SELECT liens_rss FROM compte_frankiz WHERE eleve_id='{$_SESSION['user']->uid}'") ;
	if($DB_web->num_rows()!=0) {
			list($rss) = $DB_web->next_row();
			$_SESSION['rss'] =  unserialize($rss);
	}
}

if( !isset($_SESSION['liens_perso']) || nouveau_login() ) {
	$_SESSION['liens_perso'] = array();
	$DB_web->query("SELECT liens_perso FROM compte_frankiz WHERE eleve_id='{$_SESSION['user']->uid}'") ;
	if($DB_web->num_rows()!=0) {
			list($liens) = $DB_web->next_row();
			$_SESSION['liens_perso'] =  unserialize($liens);
	}
}
?>