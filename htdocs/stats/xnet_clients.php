<?php
require_once "../include/global.inc.php";
demande_authentification(AUTH_MINIMUM);

$DB_xnet->query("select sum(isconnected) from clients");
list($nb_connect)=$DB_xnet->next_row();

$DB_xnet->query("select p1.name as 'Client', count(p2.username) as 'Nombre d\'utilisateurs' from software as p1, clients as p2 where p1.version = p2.version group by p1.version");

while(list($nom,$nb)=$DB_xnet->next_row()){
	if($nom!='') $os[$nom] = $nb;
}

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
imagestring($im, 4, $largeur-70, $hauteur-20, "Client", $noir);
imagestring($im, 4, 10, 0, "R�partition par clients", $noir);
 
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
Imagepng ($im);
?>
