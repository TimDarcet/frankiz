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
	$Id$
	
*/
require_once "include/global.inc.php";
demande_permission('interne');

if(isset($_REQUEST["graph"])){
	header ("Content-type: image/png");
	$nom=array(" ","1er","2e","3e","13","42","69","pi","ip","bonus","qdj");
	for($i=1; $i<11; $i++){
		$nb[$nom[$i]] = (isset($_REQUEST["nb$i"])?$_REQUEST["nb$i"]:0);
	}
	// on calcule le nombre de pages vues sur l'année
	$max_nb = max($nb);
	// on définit la largeur et la hauteur de notre image
	$largeur = 300;
	$hauteur = 82;
	//on crée une ressource pour notre image qui aura comme largeur $largeur et $hauteur comme hauteur (on place également un or die si la création se passait mal afin d'avoir un petit message d'alerte)
	$im = @ImageCreate ($largeur, $hauteur) or die ("Erreur lors de la création de l'image");
	$blanc = ImageColorAllocate ($im, 255, 255, 255);  
	$noir = ImageColorAllocate ($im, 0, 0, 0);  
	$bleu_fonce = ImageColorAllocate ($im, 75, 130, 195);
	$bleu_clair = ImageColorAllocate ($im, 95, 160, 240);
	// on dessine un trait horizontal pour représenter l'axe du temps     
	ImageLine ($im, 10, $hauteur-20, $largeur, $hauteur-20, $noir);
	// on affiche le numéro des règles
	$i=0;
	foreach ($nb as $nom => $nombre) {
		$i++;
		if(strlen($nom) >3){
			$decalage = (strlen($nom) - 3) * 4;
		}else{
			$decalage = 0;
		}
			
		ImageString ($im, 2, $i*$largeur/(count($nb)+1) - $decalage, $hauteur-18, $nom, $noir);
		//$i++;
//		ImageString ($im, 2, $i*$largeur/(count($nb)+1), $hauteur-18, '('.$nombre.')', $noir);
	}
	$i=0;
	foreach ($nb as $nom => $nombre) {
			$i++;
			// on calcule la hauteur du baton
			$hauteurImageRectangle = ceil((($nombre*($hauteur-32))/$max_nb));
			ImageFilledRectangle ($im, $i*$largeur/(count($nb)+1), $hauteur-$hauteurImageRectangle-20, $i*$largeur/(count($nb)+1)+14, $hauteur-21, $noir);
			ImageFilledRectangle ($im, $i*$largeur/(count($nb)+1)+2, $hauteur-$hauteurImageRectangle+2-20, $i*$largeur/(count($nb)+1)+12, $hauteur-21-1, $bleu_fonce);
			ImageFilledRectangle ($im, $i*$largeur/(count($nb)+1)+6, $hauteur-$hauteurImageRectangle+2-20, $i*$largeur/(count($nb)+1)+8, $hauteur-21-1, $bleu_clair);
			if($nombre <10){
				ImageString($im, 2, $i*$largeur/(count($nb)+1)+5, $hauteur-$hauteurImageRectangle-20-12, $nombre, $noir);
			}
			elseif($nombre<100){
				ImageString($im, 2, $i*$largeur/(count($nb)+1)+2, $hauteur-$hauteurImageRectangle-20-12, $nombre, $noir);
			}
			else{
				ImageString($im, 2, $i*$largeur/(count($nb)+1)-1, $hauteur-$hauteurImageRectangle-20-12, $nombre, $noir);
			}
			//$i++;
	}
	// on dessine le tout
	imagecolortransparent ($im,$blanc);
	Imagepng ($im);
}
?>
