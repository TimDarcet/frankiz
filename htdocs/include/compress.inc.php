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
	Fonctions permettant de zipper/dezipper un fichier
	
	$Log$
	Revision 1.7  2004/11/22 18:38:03  pico
	Changements fonction de décompression (ne cherche plus sur les mimetypes)

	Revision 1.6  2004/11/16 14:02:37  pico
	- Nouvelle fonction qui permet de dl le contenu d'un répertoire
	- Mise en place sur la page de la FAQ
	
	Revision 1.5  2004/11/08 12:03:32  pico
	Ben zut, il a pas voulu me commiter celle là !
	
	Revision 1.4  2004/11/08 11:48:26  pico
	Modif fonction deldir
	
	Revision 1.3  2004/11/08 10:27:13  pico
	Ajout fonction pour supprimer tout un repertoire
	
	Revision 1.2  2004/11/07 09:03:09  pico
	Fonction pour zipper
	
	Revision 1.1  2004/11/07 00:07:47  pico
	Utilisation de la fonction unzip pour dezipper une archive
	
	
	
*/

// Décompresse un fichier $file dans le repertoire $dir . $del est un booleen qui dit si le fichier zip doit être supprimé après decompression.
function unzip($file,$dir,$del){
	if (!ereg("^[a-ZA-Z0-9]+[/.zip]$",basename($file))) {
			$cde = "/usr/bin/unzip $file -d $dir";
			exec($cde);
			if($del = true) unlink($file);
	}
	else if (!ereg("^[a-ZA-Z0-9]+[/.tar.gz|/.tgz]$",basename($file))){
			$cde = "cd $dir && /bin/tar zxvf $file";
			exec($cde);
			if($del = true) unlink($file);
	}
	else if (!ereg("^[a-ZA-Z0-9]+[/.tar.bz2]$",basename($file))){
			$cde = "cd $dir && /bin/tar jxvf $file";
			exec($cde);
			if($del = true) unlink($file);
	}
	else echo "<warning>Type de fichier non reconnu: ".mime_content_type($file)."</warning>";
}

// Compresse le dossier $dir dans l'archive $file, de type $type
function zip($file,$dir,$type){
	if($type == "zip"){
		$cde = "cd $dir && /usr/bin/zip -r $file *";
		exec($cde);
	}
	if($type == "tar"){
		$cde = "cd $dir && /bin/tar zcvf $file.tar.gz *";
		exec($cde);
	}
}

?>
