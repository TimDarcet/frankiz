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
	Revision 1.3  2004/11/08 10:27:13  pico
	Ajout fonction pour supprimer tout un repertoire

	Revision 1.2  2004/11/07 09:03:09  pico
	Fonction pour zipper
	
	Revision 1.1  2004/11/07 00:07:47  pico
	Utilisation de la fonction unzip pour dezipper une archive
	
	
	
*/

// Décompresse un fichier $file dans le repertoire $dir . $del est un booleen qui dit si le fichier zip doit être supprimé après decompression.
function unzip($file,$dir,$del){
	if((mime_content_type($file) == "application/zip")||(mime_content_type($file) == "application/x-zip")){
			$cde = "/usr/bin/unzip $file -d $dir";
			exec($cde);
			if($del = true) unlink($file);
	}
	else if((mime_content_type($file) == "application/x-compressed-tar")||(mime_content_type($file) == "application/x-gzip")){
			$cde = "cd $dir && /bin/tar zxvf $file";
			exec($cde);
			if($del = true) unlink($file);
	}
	else echo "<warning>Type de fichier non reconnu: ".mime_content_type($file)."</warning>";
}

// Compresse le dossier $dir dans l'archive $file, de type $type
function zip($file,$dir,$type){
	if($type == "zip"){
		$cde = "/usr/bin/zip -r $file $dir";
		exec($cde);
	}
	if($type == "tar"){
		$cde = "/bin/tar czvf $file $dir";
		exec($cde);
	}
}

function deldir($dir) {
	$dh=opendir($dir);
	while ($file=readdir($dh)) {
		if($file!="." && $file!="..") {
			$fullpath=$dir."/".$file;
			if(!is_dir($fullpath)) {
			unlink($fullpath);
			} else {
			deldir($fullpath);
			}
		}
	}
	
	closedir($dh);
	
	if(rmdir($dir)) {
		return true;
	} else {
		return false;
	}
}

?>
