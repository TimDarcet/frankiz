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
	
	$Log$
	Revision 1.12  2004/12/06 17:45:06  pico
	Correction choix css alternatives

	Revision 1.11  2004/12/06 14:54:06  pico
	Remet l'affichage des css alternatives (perdu lors du passage en /nomskin/nomcss)
	
	Revision 1.10  2004/11/06 10:23:15  pico
	BugFix au niveau de l'affichage du choix de css
	
	Lorsqu'on change de skin, la css est la css "style.css" du répertoire de la skin.
	Cela permet d'éviter de garder la css d'une autre skin, sinon ça rend tout pas beau.
	
	Revision 1.9  2004/11/06 10:14:12  pico
	Voilà, c'est bon
	
	Revision 1.8  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.7  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.6  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
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
			$file_css == "CVS" || $file_css{0} == "#" || $file_css == $description['chemin']) continue;
		
		$description_css = lire_description_css(BASE_LOCAL."/skins/".$_SESSION['skin']['skin_nom']."/$file_css");
		echo "<lien titre=\"$file_css ($description_css)\" url=\"".BASE_URL."/skins/".$_SESSION['skin']['skin_nom']."/$file_css/style.css\"/>";
	}
	closedir($dir_css);
?>
</module>
