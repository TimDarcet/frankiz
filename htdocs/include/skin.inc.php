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
	Gestion des skins : lecture du cookie contenant les préférences d'affichage
	
	$Log$
	Revision 1.9  2004/11/13 00:12:24  schmurtz
	Ajout du su

	Revision 1.8  2004/11/11 21:15:52  kikx
	Rajout d'un champs dans le trombino pour stocker la skin du mec ...
	le cookie est prioritaire, mais si il n'existe pas ou qu'il a ppartient a quelqu'un d'autre, alors on va cherhcer dans la BDD
	
	Revision 1.7  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.6  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.5  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

// Valeurs par défaut (dans le cas où il n'y a pas de cookie)
$_SESSION['skin'] = array (
	"skin_nom" => "basic",
	"skin_css" => BASE_URL."/css/basic.css",
	
	"skin_parametres" => array(),
	"skin_visible" => array()
);

// Relit les informations de skin et d'affichage
function skin_parse($skin_str) {	
	// Lecture du cookie
	$_SESSION['skin'] = unserialize($skin_str);
	
	// Test de l'existence de la skin et de la CSS
	if( empty($_SESSION['skin']['skin_nom']) || !is_dir(BASE_LOCAL."/skins/".$_SESSION['skin']['skin_nom']) ) {
		$_SESSION['skin']['skin_nom'] = "basic";
		$_SESSION['skin']['skin_parametres'] = array();
	}
	
	if( empty($_SESSION['skin']['skin_css']) )
		$_SESSION['skin']['skin_css'] = BASE_URL."/css/basic.css" /*BASE_LOCAL."/skins/".$_SESSION['skin']['skin_nom']."/style.css"*/;
	
	// Vérification de l'existance de de skin_visible et skin_parametres
	if( !isset($_SESSION['skin']['skin_visible']) )
		$_SESSION['skin']['skin_visible'] = array();
		
	if( !isset($_SESSION['skin']['skin_parametres']) )
		$_SESSION['skin']['skin_parametres'] = array();
}

// Retrouve les données skin
if(est_authentifie(AUTH_MINIMUM)) {
	$DB_trombino->query("SELECT skin FROM eleves WHERE eleve_id='{$_SESSION['user']->uid}'") ;
	if($DB_trombino->num_rows()!=0) {
		list($skin) = $DB_trombino->next_row();
		$cookie = $skin;		// hack bizarre pour être sur que php considère $cookie comme un string
								// ce qui est indispensable pour la fonction base64_encode (si on met
								// directement $skin, php considère que c'est un array alors que c'est faux)
		skin_parse($cookie);
		SetCookie("skin",base64_encode($cookie),time()+3*365*24*3600,"/");
	}

} else if(isset($_COOKIE['skin'])) {
	skin_parse(base64_decode($_COOKIE['skin']));

}

?>