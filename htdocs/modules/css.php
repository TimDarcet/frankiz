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
	Liste des CSS existante compatible avec la skin XSL courante.
	
	$Id$

*/
?>
<module id="liste_css" visible="false">
<?php
	// Parcourt des feuilles de style css
	$dir_css=opendir(BASE_LOCAL."/skins/".$_SESSION['skin']['skin_nom']);
	$description = lire_description_skin(BASE_LOCAL."/skins/".$_SESSION['skin']['skin_nom']);
	while($file_css = readdir($dir_css)) {
		// uniquement pour les dossiers non particuliers
		if(!is_dir(BASE_LOCAL."/skins/".$_SESSION['skin']['skin_nom']."/$file_css") || $file_css == "." || $file_css == ".." ||
			$file_css == ".svn" || $file_css{0} == "#" || $file_css == $description['chemin']) continue;
		
		$description_css = lire_description_css(BASE_LOCAL."/skins/".$_SESSION['skin']['skin_nom']."/$file_css");
		echo "<lien titre=\"$file_css ($description_css)\" url=\"".BASE_URL."/skins/".$_SESSION['skin']['skin_nom']."/$file_css/style.css\"/><br/>";
	}
	closedir($dir_css);
?>
</module>
