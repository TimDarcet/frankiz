<?php
/*
	$Id$
	
	Gestion des skins : lecture du cookie contenant les préférences d'affichage
*/

// Valeurs par défaut
$_SESSION['skin'] = array (
	"skin_nom" => "basic",
	"skin_css" => BASE_URL."/css/basic.css",
	
	"skin_parametres" => array (),
	"skin_visible" => array()
);

// Relit les informations de skin et d'affichage
function skin_parse($skin_str) {	
	// Lecture du cookie
	$_SESSION['skin'] = unserialize($skin_str);
	
	// Test de l'existence de la skin et de la CSS
	if( empty($_SESSION['skin']['skin_nom']) || !is_dir(BASE_LOCAL."/skins/".$_SESSION['skin']['skin_nom']) )
		$_SESSION['skin']['skin_nom'] = "basic";
	
	if( empty($_SESSION['skin']['skin_css']) )
		$_SESSION['skin']['skin_css'] = BASE_URL."/css/basic.css" /*BASE_LOCAL."/skins/".$_SESSION['skin']['skin_nom']."/style.css"*/;
	
	// Vérification de l'existance de de skin_visible et skin_parametres
	if( !array_key_exists('skin_visible',$_SESSION['skin']) )
		$_SESSION['skin']['skin_visible'] = array(
			"activites" => TRUE, // visible par défaut
			"qdj" => TRUE,
			"qdj_hier" => TRUE,
			"anniversaires" => TRUE,
			"liens_contacts" => TRUE,
			"liens_ecole" => TRUE,
			"tour_kawa" => TRUE,
			"stats" => TRUE
		);
		
	if( !array_key_exists('skin_parametres',$_SESSION['skin']) )
		$_SESSION['skin']['skin_parametres'] = array();
}

// Indique si une section doit être affichée
function skin_visible($section) {
	return ((!array_key_exists($section, $_SESSION['skin']['skin_visible']) || !$_SESSION['skin']['skin_visible'][$section]) ? "false" : "true" );
}

// Retrouve les donnees skin
skin_parse(base64_decode($_COOKIE['skin']));
?>
