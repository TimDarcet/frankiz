<?php
/*
	$Id$
	
	Liste des CSS existante compatible avec la skin XSL courante.
	
	$Log$
	Revision 1.6  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"

*/
?>
<module id="liste_css" visible="false">
<?php
	//Liste des css disponibles
	$dir=opendir(BASE_LOCAL."/css");
	while($file = readdir($dir)) {
		if(ereg("^(.*)\.css$",$file,$regs))
			echo "<lien titre='{$regs[1]}' url='".BASE_URL."/css/{$regs[1]}.css'/>\n";
	}
	closedir($dir);
?>
</module>
