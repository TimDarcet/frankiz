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
	Fonctions permettant de zipper/dezipper un fichier, et de download un dossier
	sous forme d'archive.
	
	$Id$

*/

/*
	Décompresse un fichier $file dans le repertoire $dir. $del est un booleen qui dit si le
	fichier zip doit être supprimé après decompression.
*/
function unzip($file,$dir,$del){
	if (eregi("(.zip)$",basename($file))) {
			$cde = "/usr/bin/unzip $file -d $dir";
			exec($cde);
			if($del = true) unlink($file);
	}
	else if (eregi("(.tar.gz|.tgz)$",basename($file))){
			$cde = "cd $dir && /bin/tar zxvf $file";
			exec($cde);
			if($del = true) unlink($file);
	}
	else if (eregi("(.tar)$",basename($file))){
			$cde = "cd $dir && /bin/tar xvf $file";
			exec($cde);
			if($del = true) unlink($file);
	}
	else if (eregi("(.tar.bz2)$",basename($file))){
			$cde = "cd $dir && /bin/tar jxvf $file";
			exec($cde);
			if($del = true) unlink($file);
	}
	else return false;
}

/*
	Compresse le dossier $dir dans l'archive $file, de type $type
*/
function zip($file,$dir,$type){
	if($type == "zip"){
		$cde = "cd $dir && /usr/bin/zip -r $file.zip *";
		exec($cde);
	}
	if($type == "tar"){
		$cde = "cd $dir && /bin/tar cvf $file.tar *";
		exec($cde);
	}
	if($type == "tar.gz"){
		$cde = "cd $dir && /bin/tar zcvf $file.tar.gz *";
		exec($cde);
	}
}

/*
	Supprime un répertoire complet et renvoit true lorsque tout c'est bien passé
*/
function deldir($dir) {
	if (!file_exists($dir)) {
		return false;
	}
	if (is_file($dir)) {
		return unlink($dir);
	}
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

/*
	Zippe et envoit le contenu d'un répertoire
*/
function download($dir,$type = 'zip', $filename = "temp"){
	$file = "/tmp/".$filename;
	zip($file,$dir,$type);
	header("Content-type: application/force-download");
	header("Content-Disposition: attachment; filename=$filename.$type");
	readfile($file.".".$type);
}

?>
