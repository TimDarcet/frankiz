<module id="liste_css" visible="false">
<?php
//Liste des css disponibles
$dir=opendir(BASE_LOCAL."/css");
while($file = readdir($dir)) {
	list($nom_skin,$extension) = explode(".",$file);
	if($extension!="css") continue;
	echo "<lien titre='$nom_skin' url='".BASE_URL."/css/$nom_skin.css'/>\n";
}
closedir($dir);
?>
</module>
