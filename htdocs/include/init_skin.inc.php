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
	Gestion du stockage et de la relecture de la skin de l'utilisateur. On utilise
	les principes suivants :
	- les données sont stockées dans la variable de session 'skin'
	
	Deux autres variables de session permettent de stocker des informations sur l'utilisateur :
	- 'rss', contenant la liste des flux rss
	- 'liens_rss', contenant la liste des liens perso.

	
	$Id$

*/

require_once "user.inc.php";
require_once "skin.inc.php";

/*
	Recharge les données de skin si elles ne sont pas chargées (ça arrive lorsqu'un utilisateur arrive sur le
	site et n'est pas logué) ou si l'utilisateur vient de se loguer
*/
if( !isset($_SESSION['skin']) || nouveau_login() ) {
	
	// Si l'utilisateur est authentifié, chercher dans la BD
	if(est_authentifie(AUTH_MINIMUM)) {
		ajouter_debug_log("Chargement de la skin depuis la BD.");
		$DB_web->query("SELECT skin FROM compte_frankiz WHERE eleve_id='{$_SESSION['user']->uid}'") ;
		if($DB_web->num_rows()!=0) {
			list($skin) = $DB_web->next_row();
			$cookie = $skin;		// hack bizarre pour être sur que php considère $cookie comme un string
//			$cookie = "sdbr2k3";
									// ce qui est indispensable pour la fonction base64_encode (si on met
									// directement $skin, php considère que c'est un array alors que c'est faux)
			skin_parse($cookie);
			SetCookie("skin",base64_encode($cookie),time()+3*365*24*3600,"/");
		}
		
	// Sinon on cherche dans un cookie
	} else if(isset($_COOKIE['skin'])) {
		ajouter_debug_log("Chargement de la skin depuis le cookie.");
		skin_parse(base64_decode($_COOKIE['skin']));
		
	}
	
	// Si vraiment on ne trouve pas, ou si une erreur c'est produite avant, on utilise des
	// valeurs par défaut
	if(!isset($_SESSION['skin'])) {
		ajouter_debug_log("Chargement de la skin depuis les valeurs par défaut.");
		unset($_SESSION['skin']);
		skin_valider();
	}
}

/*
	D'autres informations sur l'utilisateur, stockées dans une variable de session.
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
