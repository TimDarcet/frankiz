<module id="liste_css" visible="false">
<?php
//Liste des css disponibles
$dir=opendir(BASE_LOCAL."/css");
while($file = readdir($dir)) {
	list($nom,$extension) = explode(".",$file);
	if($extension!="css") continue;
	echo "<lien titre='$nom' url='".BASE_URL."/css/$nom.css'/>\n";
}
closedir($dir);
?>
</module>
