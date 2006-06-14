<?php
require_once "../include/global.inc.php";
demande_authentification(AUTH_MINIMUM);
// on spécifie le type d'image que l'on va créer, ici ce sera une image au format PNG
header ("Content-type: image/png");  

$cache_id="xnet_stats_clients";

/** Retourne la taille en pixel à utiliser pour le cas donné
 * @param INT nb nombre d'éléments
 * @param INT max nombre référence indiquant lenombre maximum d'élément
 * @param INT size taille en pixel de la zone à utiliser
 */
function height($nb, $max, $size)
{
	return ceil((($nb * ($size - 80)) / $max));
}

/** Retourne la position du nieme objet
 * @param INT nb numéro de l'objet
 * @param INT max nombre d'objet total
 * @param INT size taille de la zone d'affichage
 */
function width($nb, $max, $size)
{
	 return $nb * $size / ($max + 1);
}

if(!cache_recuperer($cache_id, time()-600)) {

$DB_xnet->query('SELECT p1.name AS clients, COUNT(p2.jone) AS users, SUM(p2.rouje) AS roujes, SUM(p2.jone) AS jones, (COUNT(p2.jone) - SUM(p2.rouje) - SUM(p2.jone)) AS oranjes
                   FROM software AS p1
				        RIGHT JOIN
						    (SELECT if(((options >> 9) & 3) = 2, 1, 0) AS rouje, if(((options >> 9) & 3) = 3, 1, 0) AS jone, version
							   FROM clients) 
							AS p2 USING(version)
			   GROUP BY p1.version');

while(list($nom, $nb, $roujes, $jones, $oranjes) = $DB_xnet->next_row()){
	if($nom != '') {
		$os[$nom] = $nb;
		$promo[$nom] = Array('jones' => $jones, 'roujes' => $roujes, 'oranjes' => $oranjes);
	}
}

// on calcule le nombre de pages vues sur l'année
$max_os   = max($os);
$count_os = count($os);

// on définit la largeur et la hauteur de notre image
$largeur = 550;
$hauteur = 590;

// on crée une ressource pour notre image qui aura comme largeur $largeur et $hauteur comme hauteur (on place également un or die si la création se passait mal afin d'avoir un petit message d'alerte)
$im = @ImageCreate ($largeur, $hauteur) or die ("Erreur lors de la création de l'image");

// on place tout d'abord la couleur blanche dans notre table des couleurs (je vous rappelle donc que le blanc sera notre couleur de fond pour cette image).
$blanc = ImageColorAllocate ($im, 255, 255, 255);  

// on place aussi le noir dans notre palette, ainsi qu'un bleu foncé et un bleu clair
$noir = ImageColorAllocate ($im, 0, 0, 0);  
$bleu_fonce = ImageColorAllocate ($im, 75, 130, 195);
$bleu_clair = ImageColorAllocate ($im, 95, 160, 240);
$jaune = ImageColorAllocate($im, 255, 220, 0);
$rouge = ImageColorAllocate($im, 255, 0, 0);
$orange = ImageColorAllocate($im, 255, 128, 0);


// on dessine un trait horizontal pour représenter l'axe du temps     
ImageLine ($im, 20, $hauteur-50, $largeur-15, $hauteur-50, $noir);

// on affiche le numéro des 12 mois
$i=0;
foreach ($os as $nom => $nombre) {
	$i++;
	if($i%2 == 0) {
        ImageString ($im, 2, width($i, $count_os, $largeur) - 25, $hauteur-35, $nom, $noir);
	} else {
		ImageString ($im, 2, width($i, $count_os, $largeur) - 25, $hauteur-48, $nom, $noir);
	}
}

// on dessine un trait vertical pour représenter le nombre de pages vues
ImageLine ($im, 20, 30, 20, $hauteur-50, $noir);

// on affiche les legendes sur les deux axes ainsi que différents textes (note : pour que le script trouve la police verdana, vous devrez placer la police verdana dans un repertoire /fonts/)
imagestring($im, 4, $largeur-70, $hauteur-20, "Client", $noir);
imagestring($im, 4, 10, 0, utf8_decode("Répartition par clients"), $noir);
	
$i=0;
foreach ($os as $nom => $nombre) {
		$promos = $promo[$nom];
		$i++;
		
		// on calcule la hauteur du baton
		$base = $hauteur - 51;
		$jone    = height($promos['jones'],  $max_os, $hauteur);
		$rouje   = height($promos['roujes'], $max_os, $hauteur);
		$oranje  = height($promos['oranjes'], $max_os, $hauteur);
		$taille  = $jone + $rouje + $oranje;

		$x = width($i, $count_os, $largeur);
		if($jone != 0) {
			ImageFilledRectangle ($im, $x, $base - $jone + 1,   $x + 14, $base, $jaune);
			$base = $base - $jone;
		}
		if($rouje != 0) {
			ImageFilledRectangle ($im, $x, $base - $rouje + 1,  $x + 14, $base, $rouge);
			$base = $base - $rouje;
		}
		if($oranje != 0)
			ImageFilledRectangle ($im, $x, $base - $oranje + 1, $x + 14, $base, $orange);
        imagestring($im, 2, $x, min($hauteur - $taille - 80, $hauteur - 21), $nombre, $noir);
}

// on dessine le tout
imagecolortransparent($im, $blanc);
Imagepng($im);

cache_sauver($cache_id);
}
?>
