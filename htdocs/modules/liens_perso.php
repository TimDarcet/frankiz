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
	Affichage des liens personnels.

	$Log$
	Revision 1.9  2004/12/13 20:03:25  pico
	Les liens ne forment pas de blocs, il faut donc le spécifier

	Revision 1.8  2004/12/05 23:19:27  pico
	Evite d'afficher une boite vide
	
	Revision 1.7  2004/11/30 19:56:32  pico
	On a plus besoin du lien vers la conf des liens perso, puisqu'elle est dans la page de préférences
	
	Revision 1.6  2004/11/24 22:08:09  pico
	Ajout lien vers page feed RSS dans le cadre liens_perso
	
	Revision 1.5  2004/11/24 21:09:04  pico
	Sauvegarde avant mise à jour skins
	
	Revision 1.4  2004/11/24 19:00:32  pico
	Bon, là ça devrait être bon...
	
	Revision 1.3  2004/11/24 18:53:31  pico
	J'espère que ça sera fixé cette fois
	
	Revision 1.2  2004/11/24 18:39:02  pico
	WarningFix si variable vide
	
	Revision 1.1  2004/11/24 17:34:23  pico
	Module liens perso + bugfix
	
	Revision 1.1  2004/11/24 16:37:09  pico
	Ajout des news externes en tant que module
	

*/
if(isset($_SESSION['liens_perso']) && !empty($_SESSION['liens_perso']) && count($_SESSION['liens_perso'])>0){
	echo "<module id=\"liens_perso\" titre=\"Liens Perso\">";
	foreach($_SESSION['liens_perso'] as $titre => $url){
		echo "<lien titre=\"$titre\" url=\"$url\" /><br/>";
	}
	echo "</module>";
}
?>