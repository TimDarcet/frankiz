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
	Page d'accueil de frankiz pour les personnes non loguées.
	
	$Log$
	Revision 1.9  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site

	Revision 1.8  2004/09/15 23:19:45  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.7  2004/09/15 21:42:15  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

require_once "include/global.inc.php";
if (est_authentifie(AUTH_COOKIE)) 
	rediriger_vers("/annonces.php");

// génération de la page
require "include/page_header.inc.php";
echo "<page id='accueil' titre='Frankiz : accueil'>\n";
?>

<h2>Bienvenue sur Frankiz</h2>

<p>&nbsp;</p>
<p>Voici la nouvelle page élève qui est en construction...</p>
<p>Si tu veux te connecter et accéder à la partie réservée aux élèves alors clique sur ce <a href="login.php">lien</a></p>
<p>Sinon navigue sur cette page en utilisant les liens qui se situe un peu partout sur cette page :)</p>
<?
echo "</page>\n";
require_once "include/page_footer.inc.php";
?>