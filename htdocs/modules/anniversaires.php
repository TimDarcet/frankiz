<?php
/*
	Affichage des anniversaires avec gestion d'un cache mis à jour une fois par jour.
	
	$Id$
*/

require_once BASE_LOCAL."/include/trombino.inc.php";
?>

<module id="anniversaires" titre="Anniversaires" visible="<?php echo skin_visible("anniversaires"); ?>">
<?php

$fichier_cache = BASE_LOCAL."/cache/anniversaires";

if(file_exists($fichier_cache) && date("Y-m-d", filemtime($fichier_cache)) == date("Y-m-d",time())) {
	readfile($fichier_cache);
	
} else {
	$contenu = trombi_recherche("","","","",date("d"),date("m"),"","","","","2002","","","","","","");
	$contenu .= trombi_recherche("","","","",date("d"),date("m"),"","","","","2003","","","","","","");

	$file = fopen($fichier_cache, 'w');
	fwrite($file, $contenu);
	fclose($file);                 

	echo $contenu;
}

?>
</module>