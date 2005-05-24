<?php
require_once "../include/global.inc.php";
demande_authentification(AUTH_MINIMUM);
// on spécifie le type d'image que l'on va créer, ici ce sera une image au format PNG
header ("Content-type: image/png");  

$cache_id="xnet_stats_connections";

if(!cache_recuperer($cache_id,time()-600)) {

$fp=fopen(BASE_CACHE."stats-xnet",'r');
while(!feof($fp)){
	if(list($date,$nb) = explode(" ",fgets($fp, 4000)))
		if(substr($nb, 0, -1)!='')
			$os[date("Ymd H:i",$date)]=substr($nb, 0, -1);
}
fclose($fp);
ksort($os);
reset($os);

// on calcule le nombre de pages vues sur l'année
$max_os = max($os);


// on définit la largeur et la hauteur de notre image
$largeur = 550;
$hauteur = 600;

// on crée une ressource pour notre image qui aura comme largeur $largeur et $hauteur comme hauteur (on place également un or die si la création se passait mal afin d'avoir un petit message d'alerte)
$im = @ImageCreate ($largeur, $hauteur) or die ("Erreur lors de la création de l'image");

// on place tout d'abord la couleur blanche dans notre table des couleurs (je vous rappelle donc que le blanc sera notre couleur de fond pour cette image).
$blanc = ImageColorAllocate ($im, 255, 255, 255);  

// on place aussi le noir dans notre palette, ainsi qu'un bleu foncé et un bleu clair
$noir = ImageColorAllocate ($im, 0, 0, 0);  
$bleu_fonce = ImageColorAllocate ($im, 75, 130, 195);
$bleu_clair = ImageColorAllocate ($im, 95, 160, 240);


// on dessine un trait horizontal pour représenter l'axe du temps     
$i=0;
while($i<$max_os){
	$hauteurImageRectangle = ceil((($i*($hauteur-60))/$max_os));
	ImageLine ($im, 10, $hauteur-$hauteurImageRectangle-40, $largeur-1, $hauteur-$hauteurImageRectangle-40, $noir);
	imagestringup($im, 0, 0,$hauteur-$hauteurImageRectangle-40, $i, $noir);
	$i+=100;
}

// on dessine un trait vertical pour représenter le nombre de pages vues
ImageLine ($im, 10, 30, 10, $hauteur-40, $noir);

// on affiche les legendes sur les deux axes ainsi que différents textes (note : pour que le script trouve la police verdana, vous devrez placer la police verdana dans un repertoire /fonts/)
imagestring($im, 4, $largeur-70, $hauteur-20, "Heure", $noir);
imagestring($im, 4, 10, $hauteur-20, "Maximum: $max_os", $noir);
imagestring($im, 4, 10, 0, utf8_decode("Nombre de connectés ces dernieres 24 heures."), $noir);

 
$i=0;
foreach ($os as $nom => $nombre) {
		$i++;
		// on calcule la hauteur du baton
		$hauteurImageRectangle = ceil((($nombre*($hauteur-60))/$max_os));
		ImageFilledRectangle ($im, 10+$i*($largeur-10)/(count($os)+1), $hauteur-$hauteurImageRectangle+2-40, 10+($i+1)*($largeur-10)/(count($os)+1), $hauteur-41-1, $bleu_clair);
		Imagerectangle ($im, 10+$i*($largeur-10)/(count($os)+1), $hauteur-$hauteurImageRectangle+2-40, 10+($i+1)*($largeur-10)/(count($os)+1), $hauteur-41, $noir);
}

$i=0;
foreach ($os as $nom => $nombre) {
	$i++;
	if(substr($nom,12,2)=="00"){
		$hauteurImageRectangle = ceil((($nombre*($hauteur-60))/$max_os));
		ImageString ($im, 2, 10+$i*($largeur-10)/(count($os)+1), $hauteur-38, substr($nom,9,2)."h", $noir);
		//imagestring($im, 2, 10+$i*($largeur-10)/(count($os)+1),min($hauteur-$hauteurImageRectangle-60,$hauteur-61), $nombre, $noir);
	}
}
// on dessine le tout
imagecolortransparent ($im,$blanc);
Imagepng ($im);
cache_sauver($cache_id);
}
?>
