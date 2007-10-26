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
	Page de gestion des skins. Chaque skin est contenu dans un dossier dans $serveur_base/skins/.
	Le fichier skin.xsl contient le code XSLT pour convertir le code XML et le fichier description.xsl
	contient des informations importantes pour l'application de la transformation XSL et la configuration
	de la skin par l'utilisateur.
	
	Toutes les configurations de l'utilisateur sont stockées dans un cookie. Ce cookie est l'encodage
	en base64 de la version sérialisée d'une structure de la forme :
	array (
		[skin_nom]  => «nom de la skin»
		[skin_css]  => «nom du fichier css»
		[skin_parametres] => array (
			[«param 1] => «valeur»
			[«param 2»] => «valeur»
		)
		[skin_visible] => array (
			[«module 1»] => «true/false»
			[«module 2»] => «true/false»
		)
	)
	
	$Id$

*/

require_once "../include/page_header.inc.php";

require_once "../../modules/profil.php";
if (!empty($_REQUEST['OK_skin'])) {
	call('ProfilModule', 'profil/skin/change_skin');
} else if (!empty($_REQUEST['OK_param'])) {
	call('ProfilModule', 'profil/skin/change_params');
} else {
	call('ProfilModule', 'profil/skin');
}


// Applique les transformations
require_once "../include/page_footer.inc.php";
?>
