<?php
require_once "../include/global.inc.php";


$DB_xnet->query("select sum(isconnected) from clients");
list($nb_connect)=$DB_xnet->next_row();

$DB_xnet->query("select count(*) as 'Serveurs Samba' from clients where (options & 0x1) group by (options & 0x1)");
list($os["Samba"])=$DB_xnet->next_row();
$DB_xnet->query("select count(*) as 'Serveurs FTP' from clients where (options & 0x2) group by (options & 0x2)");
list($os["FTP"])=$DB_xnet->next_row();
$DB_xnet->query("select count(*) as 'Serveurs HTTP' from clients where (options & 0x8) group by (options & 0x8)");
list($os["HTTP"])=$DB_xnet->next_row();
$DB_xnet->query("select count(*) as 'Serveurs News' from clients where (options & 0x10) group by (options & 0x10)");
list($os["News"])=$DB_xnet->next_row();

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

// on affiche le num�ro des 12 mois
$i=0;
foreach ($os as $nom => $nombre) {
	$i++;
        ImageString ($im, 2, $i*$largeur/(count($os)+1), $hauteur-38, $nom, $noir);
}

// on dessine un trait vertical pour repr�senter le nombre de pages vues
ImageLine ($im, 20, 30, 20, $hauteur-40, $noir);

// on affiche les legendes sur les deux axes ainsi que diff�rents textes (note : pour que le script trouve la police verdana, vous devrez placer la police verdana dans un repertoire /fonts/)
imagestring($im, 4, $largeur-70, $hauteur-20, "Serveur", $noir);
imagestring($im, 4, 10, 0, "Nombre de serveurs", $noir);

 
$i=0;
foreach ($os as $nom => $nombre) {
		$i++;
		// on calcule la hauteur du baton
		$hauteurImageRectangle = ceil((($nombre*($hauteur-50))/$max_os));
		imagestring($im, 2, $i*$largeur/(count($os)+1),min($hauteur-$hauteurImageRectangle-20,$hauteur-61), $nombre, $noir);
		ImageFilledRectangle ($im, $i*$largeur/(count($os)+1), $hauteur-$hauteurImageRectangle, $i*$largeur/(count($os)+1)+14, $hauteur-41, $noir);
		ImageFilledRectangle ($im, $i*$largeur/(count($os)+1)+2, $hauteur-$hauteurImageRectangle+2, $i*$largeur/(count($os)+1)+12, $hauteur-41-1, $bleu_fonce);
		ImageFilledRectangle ($im, $i*$largeur/(count($os)+1)+6, $hauteur-$hauteurImageRectangle+2, $i*$largeur/(count($os)+1)+8, $hauteur-41-1, $bleu_clair);
}

// on dessine le tout
Imagepng ($im);
?>
