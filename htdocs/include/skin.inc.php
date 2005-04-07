<?php
/*
	Copyright (C) 2004 Binet Réseau
	http://www.polytechnique.fr/eleves/binets/br/
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/
/*
	Fonction servant pour la gestion des skins.
	
	$Log$
	Revision 1.17  2005/04/07 21:34:01  fruneau
	Idem pour skin.inc.php

	Revision 1.15  2004/12/14 17:14:53  schmurtz
	modification de la gestion des annonces lues :
	- toutes les annonces sont envoyees dans le XML
	- annonces lues avec l'attribut visible="non"
	- suppression de la page affichant toutes les annonces
	
	Revision 1.14  2004/12/06 00:01:42  kikx
	Passage de la skin par défaut en parametre du site et non pas stocké en dur
	
	Revision 1.13  2004/11/24 23:38:38  schmurtz
	Gestion des skins perso + corrections dans la skin default
	
	Revision 1.12  2004/11/24 20:26:38  schmurtz
	Reorganisation des skins (affichage melange skin/css + depacement des css)
	
*/

require_once "xml.inc.php";

/*
	Lit le contenu d'un fichier de description d'une skin.
	Renvoi un arbre ayant la structure suivante :
	array (
		[nom] => «nom de la skin»
		[description] => «sa description»
		[chemin] => «chemin d'accès au dossier xsl»
		[parametres] => array (				//liste des paramètres de la skin
			[«id du premier paramètre»] => array (
				[id] => «id du premier paramètre»
				[description] => «description du paramètre»
				[valeurs] => array (		// liste des valeurs que peut prendre le paramètre
					[«id de la première valeur»] => [«nom de la première valeur»]
					[«id de la deuxième valeur»] => [«nom de la deuxième valeur»]
				)
			)
			[«id du deuxième paramêtre»] => array (
				...
			)
		)
	)
	
	Le code XML étant :
	<skin>
		<nom>«nom de la skin»</nom>
		<descrition>«sa description»</description>
		<chemin>«chemin d'accès au dossier xsl: "xsl" ou "." en général»</chemin>
		<parametre id="«id du premier paramètre»">
			<description>«description du paramètre»</description>
			<valeur id="«id de la première valeur»">«nom de la première valeur»</valeur>
			<valeur id="«id de la deuxième valeur»">«nom de la deuxième valeur»</valeur>
		</parametre>
		<parametre id="«id du deuxième paramètre»">
			...
		</parametre>
	</skin>
*/

function lire_description_skin($fichier) {
	$fichier = "$fichier/description.xml";
	// Parsage du code XML
	if(!file_exists($fichier)) return array();
	$parsed_xml = xml_get_tree($fichier);
	
	// Vérification de la structure de l'arbre et stockage des données qui nous servent
	// sous la forme d'un arbre.
	$desc = array('description'=>"", 'chemin'=>".", 'parametres'=>array());
	if( $parsed_xml[0]['tag'] == 'skin' ) {
		// pour chaque élément de <skin>
		$element_list = $parsed_xml[0]['children'];
		foreach($element_list as $element) {
			switch($element['tag']) {
				case 'nom':
				case 'description':
				case 'chemin':
					$desc[$element['tag']] = $element['value'];
					break;
					
				case 'parametre':
					$param = array();
					$param['valeurs'] = array();

					$param['id'] = $element['attributes']['id'];
					if(empty($param['id'])) break;  // le nom est obligatoire

					// pour chaque élément de <parametre>
					$param_element_list = $element['children'];
					foreach($param_element_list as $param_element) {
						switch($param_element['tag']) {
							case 'description':
								$param['description'] = $param_element['value'];
								break;
								
							case 'valeur':
								$id = !empty($param_element['attributes']) && !empty($param_element['attributes']['id']) ?
										$param_element['attributes']['id'] :
										$param_element['value'];
								$param['valeurs'][$id] = $param_element['value'];
								break;
						}
					}
					
					// enregistrement du paramètre avec si besoin une valeur par défaut de la
					// description
					if( empty($param['description']) )
						$param['description'] = "Paramètre «".$param['id']."»";
					$desc['parametres'][$param['id']] = $param;
					break;
			}
		}
	}
	
	return $desc;
}

/*
	Lit la description d'une feuille de style css
	$css_dir est le dossier contenant les fichiers de la css
*/
function lire_description_css($css_dir) {
	$description="";
	if(file_exists("$css_dir/description.txt")) {
		// Lecture du fichier de description et suppression des éventuelles balises html
		$fd = fopen("$css_dir/description.txt","r");
		$description=fread($fd,filesize("$css_dir/description.txt"));
		$description=htmlspecialchars($description, ENT_QUOTES);
		fclose($fd);
	}
	return $description;
}

/*
	Renvoi les paramètres de la skin par défaut
*/
function skin_defaut() {
	global $DB_web ;
	
	$DB_web->query("SELECT valeur FROM parametres WHERE nom='skin_default'") ; 
	list($skin) = $DB_web->next_row() ;
	$DB_web->query("SELECT valeur FROM parametres WHERE nom='css_default'") ; 
	list($css) = $DB_web->next_row() ;
	return array (
			"skin_nom" => "$skin",
			"skin_css" => "$css",
			"skin_parametres" => array(),
			"skin_visible" => array(),
	);
}

/*
	Vérifie la validité des paramètres d'une skin. Corrige ce qui peut l'être et
	au pire utilise la skin par defaut.
*/
function skin_valider() {
	// Test de l'existence de la skin et de la CSS
	if( !isset($_SESSION['skin']) || empty($_SESSION['skin']['skin_nom']) || !is_dir(BASE_LOCAL."/skins/{$_SESSION['skin']['skin_nom']}") ||
		!empty($_SESSION['skin']['skin_css']) && !is_dir(BASE_LOCAL."/skins/{$_SESSION['skin']['skin_nom']}/{$_SESSION['skin']['skin_css']}") )
		$_SESSION['skin'] = skin_defaut();
	
	// Vérification de l'existance de de skin_visible et skin_parametres
	if(!isset($_SESSION['skin']['skin_visible']))		$_SESSION['skin']['skin_visible'] = array();
	if(!isset($_SESSION['skin']['skin_parametres']))	$_SESSION['skin']['skin_parametres'] = array();
	
	// Calcul d'éléments utiles
	$description = lire_description_skin(BASE_LOCAL."/skins/{$_SESSION['skin']['skin_nom']}");
	if(!empty($description)) {
		$_SESSION['skin']['skin_xsl_chemin'] = BASE_LOCAL."/skins/{$_SESSION['skin']['skin_nom']}/{$description['chemin']}/skin.xsl";
		if(isset($_SESSION['skin']['skin_css_perso']))
			$_SESSION['skin']['skin_css_url'] = $_SESSION['skin']['skin_css_perso'];
		else
			$_SESSION['skin']['skin_css_url'] = BASE_URL."/skins/{$_SESSION['skin']['skin_nom']}/{$_SESSION['skin']['skin_css']}/style.css";
	} else {
		ajouter_debug_log("Erreur de lecture de la skin {$_SESSION['skin']['skin_nom']}");
		$_SESSION['skin'] = skin_defaut();
		skin_valider();
	}
}

function skin_parse($skin_str) {
	$_SESSION['skin'] = unserialize($skin_str);
	skin_valider();
}