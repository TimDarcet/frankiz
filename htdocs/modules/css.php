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
	//Liste des css disponibles
	$dir=opendir(BASE_LOCAL."/css/".$_SESSION['skin']['skin_nom']);
	while($file = readdir($dir)) {
		if(ereg("^(.*)\.css$",$file,$regs))
			echo "<lien titre='{$regs[1]}' url='".BASE_URL."/css/{$_SESSION['skin']['skin_nom']}/{$regs[1]}.css'/>\n";
	}
	closedir($dir);
?>
</module>
