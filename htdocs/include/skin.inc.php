<?php
/*
	Gestion des skins : lecture du cookie contenant les préférences d'affichage
	
	$Log$
	Revision 1.6  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")

	Revision 1.5  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

// Valeurs par défaut (dans le cas où il n'y a pas de cookie)
$_SESSION['skin'] = array (
	"skin_nom" => "basic",
	"skin_css" => BASE_URL."/css/basic.css",
	
	"skin_parametres" => array(),
	"skin_visible" => array()
);

// Relit les informations de skin et d'affichage
function skin_parse($skin_str) {	
	// Lecture du cookie
	$_SESSION['skin'] = unserialize($skin_str);
	
	// Test de l'existence de la skin et de la CSS
	if( empty($_SESSION['skin']['skin_nom']) || !is_dir(BASE_LOCAL."/skins/".$_SESSION['skin']['skin_nom']) ) {
		$_SESSION['skin']['skin_nom'] = "basic";
		$_SESSION['skin']['skin_parametres'] = array();
	}
	
	if( empty($_SESSION['skin']['skin_css']) )
		$_SESSION['skin']['skin_css'] = BASE_URL."/css/basic.css" /*BASE_LOCAL."/skins/".$_SESSION['skin']['skin_nom']."/style.css"*/;
	
	// Vérification de l'existance de de skin_visible et skin_parametres
	if( !isset($_SESSION['skin']['skin_visible']) )
		$_SESSION['skin']['skin_visible'] = array();
		
	if( !isset($_SESSION['skin']['skin_parametres']) )
		$_SESSION['skin']['skin_parametres'] = array();
}

// Retrouve les données skin
if (isset($_COOKIE['skin'])) 
	skin_parse(base64_decode($_COOKIE['skin']));
?>
