<?php
/*
	$Id$
	
	Liste des CSS accessibles.
*/
?>
<module id="liste_css" visible="false">
<?php
	//Liste des css disponibles
	$dir=opendir(BASE_LOCAL."/css");
	while($file = readdir($dir)) {
		$regs = array("");
		if(ereg("^(.*)\.css$",$file,&$regs))
			echo "<lien titre='{$regs[1]}' url='".BASE_URL."/css/{$regs[1]}.css'/>\n";
	}
	closedir($dir);
?>
</module>
