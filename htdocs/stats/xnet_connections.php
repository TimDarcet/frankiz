<?php
require_once "../include/global.inc.php";
demande_authentification(AUTH_MINIMUM);

$fp=fopen(BASE_CACHE."stats-xnet",'r');
while(!feof($fp)){
	if(list($date,$nb) = explode(" ",fgets($fp, 4000)))
		if(substr($nb, 0, -1)!='')
			$os[date("H:i",$date)]=substr($nb, 0, -1);
}
fclose($fp);
ksort($os);
reset($os);

// on calcule le nombre de pages vues sur l'ann�e
$max_os = max($os);
// on sp�cifie le type d'image que l'on va cr�er, ici ce sera une image au format PNG
header ("Content-type: image/png");  

// on d�finit la largeur et la hauteur de notre image
$largeur = 550;
$hauteur = 600;

// on cr�e une ressource pour notre image qui aura comme largeur $largeur et $hauteur comme hauteur (on place �galement un or die si la cr�ation se passait mal afin d'avoir un petit message d'alerte)
$im = @ImageCreate ($largeur, $hauteur) or die ("Erreur lors de la cr�ation de l'image");

// on place tout d'abord la couleur blanche dans notre table des couleurs (je vous rappelle donc que le blanc sera notre couleur de fond pour cette image).
$blanc = ImageColorAllocate ($im, 255, 255, 255);  

// on place aussi le noir dans notre palette, ainsi qu'un bleu fonc� et un bleu clair
$noir = ImageColorAllocate ($im, 0, 0, 0);  
$bleu_fonce = ImageColorAllocate ($im, 75, 130, 195);
$bleu_clair = ImageColorAllocate ($im, 95, 160, 240);


// on dessine un trait horizontal pour repr�senter l'axe du temps     
ImageLine ($im, 20, $hauteur-40, $largeur-15, $hauteur-40, $noir);
// on dessine un trait vertical pour repr�senter le nombre de pages vues
ImageLine ($im, 20, 30, 20, $hauteur-40, $noir);

// on affiche les legendes sur les deux axes ainsi que diff�rents textes (note : pour que le script trouve la police verdana, vous devrez placer la police verdana dans un repertoire /fonts/)
imagestring($im, 4, $largeur-70, $hauteur-20, "Heure", $noir);
imagestring($im, 4, 10, $hauteur-20, "Maximum: $max_os", $noir);
imagestring($im, 4, 10, 0, "Nombre de connect�s ces dernieres 24 heures.", $noir);

 
$i=0;
foreach ($os as $nom => $nombre) {
		$i++;
		// on calcule la hauteur du baton
		$hauteurImageRectangle = ceil((($nombre*($hauteur-50))/$max_os));
		Imagerectangle ($im, $i*$largeur/(count($os)+1), $hauteur-$hauteurImageRectangle, ($i+1)*$largeur/(count($os)+1), $hauteur-41, $noir);
		ImageFilledRectangle ($im, $i*$largeur/(count($os)+1), $hauteur-$hauteurImageRectangle+2, ($i+1)*$largeur/(count($os)+1), $hauteur-41-1, $bleu_clair);
		//ImageFilledRectangle ($im, $i*$largeur/(count($os)+1)+6, $hauteur-$hauteurImageRectangle+2, ($i+1)*$largeur/(count($os)+1)-4, $hauteur-41-1, $bleu_clair);
		
}

$i=0;
foreach ($os as $nom => $nombre) {
	$i++;
	if(substr($nom,3,2)=="00"){
		$hauteurImageRectangle = ceil((($nombre*($hauteur-50))/$max_os));
		ImageString ($im, 2, $i*$largeur/(count($os)+1), $hauteur-38, substr($nom,0,2)."h", $noir);
		imagestring($im, 2, $i*$largeur/(count($os)+1),min($hauteur-$hauteurImageRectangle-20,$hauteur-61), $nombre, $noir);
	}
}
// on dessine le tout
Imagepng ($im);
?>
