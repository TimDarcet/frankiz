<module id="liste_css" visible="false">
<?php

//Liste des css disponibles
$dir=opendir(BASE_LOCAL."/css");

while ($file = readdir($dir)) {
	list($nom,$extension) = explode(".", $file);

	if (($file != ".") && ($file != "..") && (ereg("css",$file))) {
		$description="";
		if(file_exists(base_css."/css/$nom.txt")) {
			$fp=fopen(base_css."/css/$nom.txt","r");
			// Contient la taille du fichiet en octet
			$taille_fichier=filesize(base_css."/css/$nom.txt");
			// Contient le contenu intégral du fichier
			$description=fread($fp,$taille_fichier);
			// Supprimer les éventuelles balises de la desc
			$description=htmlspecialchars($description, ENT_QUOTES);
			fclose($fp);
		}

		if ($description == "")
			$description="Pas de description";
		echo "<element";
		if ($global_css == BASE_LOCAL."/css/".$nom.".css")
			echo ' actif="true"';
		echo " nom=\"$nom\""; 
		echo " url=\"".BASE_URL."/css/".$nom.".css\">"; 
		echo "$description</element>" ;
	}
}

closedir($dir);
   
?>
</module>
