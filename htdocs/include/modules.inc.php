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
	Inclu les modules nécessaires.
	
	Il est possible d'en modifier le comportement à l'aide de variables GET :
	- 'modules[blahblah]' :	'on' pour forcé l'affichage du module 'blahblah',
							'off' pour forcer le non affichage
	- 'modules[tous]' :		valeur par défaut pour l'affichage des modules

	$Log$
	Revision 1.12  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site

	Revision 1.11  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.10  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

// Cette directive "global" est utile dans le cas où cette page est inclue depuis
// header.inc.php lui même inclu depuis la fonction demande_authentification()
// de login.inc.php. En effet, dans ce cas PHP considère que l'on est dans une fonction
// donc empèche l'accès direct aux variables globales.
global $DB_web,$DB_trombino,$DB_admin;

function existant_et_egal_a($variable,$index,$valeur) {
	return isset($variable[$index]) && $variable[$index] == $valeur;
}

if(!isset($_GET['modules']))
	$_GET['modules'] = array();

foreach(liste_modules() as $module => $modifiable)
	if(		!existant_et_egal_a($_GET['modules'],'tous','off')
			&& (!$modifiable || !existant_et_egal_a($_SESSION['skin']['skin_visible'],$module,false))
		||
			existant_et_egal_a($_GET['modules'],'tous','on')
			&& !existant_et_egal_a($_GET['modules'],$module,'off') )
		require BASE_LOCAL."/modules/$module.php";
?>