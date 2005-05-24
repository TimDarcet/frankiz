<?php
require_once "../include/global.inc.php";
demande_authentification(AUTH_MINIMUM);
// on spécifie le type d'image que l'on va créer, ici ce sera une image au format PNG
header ("Content-type: image/png");  

$cache_id="xnet_stats_os";

if(!cache_recuperer($cache_id,time()-600)) {

$DB_xnet->query("select sum(isconnected) from clients");
list($nb_connect)=$DB_xnet->next_row();

$DB_xnet->query("Select if( ((options & 0x1c0) >> 6) = 1, 'Windows 9x', if( ((options & 0x1c0) >> 6)= 2, 'Windows XP', if( ((options & 0x1c0) >> 6) = 3, 'Linux', if( ((options & 0x1c0) >> 6)= 4, 'MacOS', if( ((options & 0x1c0) >> 6), 'MacOS X', '!!!'))))), count(*)  from clients where (NOT status) group by (options & 0x1c0)");

while(list($nom,$nb)=$DB_xnet->next_row()){
	if($nom!='!!!') $os[$nom] = $nb;
}

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
ImageLine ($im, 20, $hauteur-40, $largeur-15, $hauteur-40, $noir);

// on affiche le numéro des 12 mois
$i=0;
foreach ($os as $nom => $nombre) {
	$i++;
        ImageString ($im, 2, $i*$largeur/(count($os)+1), $hauteur-38, $nom, $noir);
}

// on dessine un trait vertical pour représenter le nombre de pages vues
ImageLine ($im, 20, 30, 20, $hauteur-40, $noir);

// on affiche les legendes sur les deux axes ainsi que différents textes (note : pour que le script trouve la police verdana, vous devrez placer la police verdana dans un repertoire /fonts/)
imagestring($im, 4, $largeur-70, $hauteur-20, "OS", $noir);
imagestring($im, 4, 10, 0, utf8_decode("Répartition par OS"), $noir);
 
$i=0;
foreach ($os as $nom => $nombre) {
		$i++;
		// on calcule la hauteur du baton
		$hauteurImageRectangle = ceil((($nombre*($hauteur-60))/$max_os));
		imagestring($im, 2, $i*$largeur/(count($os)+1),min($hauteur-$hauteurImageRectangle-60,$hauteur-61), $nombre, $noir);
		ImageFilledRectangle ($im, $i*$largeur/(count($os)+1), $hauteur-$hauteurImageRectangle-40, $i*$largeur/(count($os)+1)+14, $hauteur-41, $noir);
		ImageFilledRectangle ($im, $i*$largeur/(count($os)+1)+2, $hauteur-$hauteurImageRectangle+2-40, $i*$largeur/(count($os)+1)+12, $hauteur-41-1, $bleu_fonce);
		ImageFilledRectangle ($im, $i*$largeur/(count($os)+1)+6, $hauteur-$hauteurImageRectangle+2-40, $i*$largeur/(count($os)+1)+8, $hauteur-41-1, $bleu_clair);
}

// on dessine le tout
imagecolortransparent ($im,$blanc);
Imagepng ($im);
cache_sauver($cache_id);
}
?>
