<?
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
	Affichage de flux rss externes sous forme de module.

	$Log$
	Revision 1.2  2004/11/24 18:39:02  pico
	WarningFix si variable vide

	Revision 1.1  2004/11/24 17:34:23  pico
	Module liens perso + bugfix
	
	Revision 1.1  2004/11/24 16:37:09  pico
	Ajout des news externes en tant que module
	

*/
$liens = $_SESSION['liens_perso'];
if(!empty($liens){
	echo "<module id=\"liens_perso\" titre=\"Liens Perso\">";
	
	foreach($liens as $titre => $url){
		echo "<lien titre=\"$titre\" url=\"$url\" />";
	}
	
	echo "</module>";
}
?>