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
	Revision 1.9  2005/01/04 23:11:29  pico
	Bugfix ?

	Revision 1.8  2004/11/24 23:07:59  pico
	Là !²
	
	Revision 1.7  2004/11/24 23:06:46  pico
	BugFix
	
	Revision 1.6  2004/11/24 22:56:18  schmurtz
	Inclusion de wiki.inc.php par les fichiers concerne uniquement et non de facon
	globale pour tous les fichiers.
	
	Revision 1.5  2004/11/24 19:00:32  pico
	Bon, là ça devrait être bon...
	
	Revision 1.4  2004/11/24 18:53:31  pico
	J'espère que ça sera fixé cette fois
	
	Revision 1.3  2004/11/24 18:39:02  pico
	WarningFix si variable vide
	
	Revision 1.2  2004/11/24 17:34:23  pico
	Module liens perso + bugfix
	
	Revision 1.1  2004/11/24 16:37:09  pico
	Ajout des news externes en tant que module
	

*/

require_once BASE_LOCAL."/include/rss_func.inc.php";
$liens = $_SESSION['rss'];
if(is_array($liens)){
	foreach($liens as $value => $mode){
		if($mode == 'module'){
			list($mode,$value) = explode("_",$value,2);
			rss_xml($value,"sommaire");
		}
	}
}
?>