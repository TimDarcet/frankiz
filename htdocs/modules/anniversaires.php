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
	connecter_mysql_frankiz();
	$resultat = mysql_query("SELECT nom,prenom,surnom,section,cie,promo,login,mail FROM eleves "
						   ."WHERE DAYOFYEAR(date_nais)=DAYOFYEAR(NOW()) AND (promo='2002' OR promo='2003')");
	$contenu = "";
	while(list($nom,$prenom,$surnom,$section,$cie,$promo,$login,$mail) = mysql_fetch_row($resultat)) {
		$contenu .= "<eleve nom='$nom' prenom='$prenom' surnom='$surnom' section='$section' cie='$cie'"
				   ." promo='$promo' login='$login' mail='$mail'/>\n";
	}
	mysql_free_result($resultat);
	deconnecter_mysql_frankiz();
	
	$file = fopen($fichier_cache, 'w');
	fwrite($file, $contenu);
	fclose($file);                 

	echo $contenu;
}

?>
</module>