<?php
/*
	$Id$
	
	Page de gestion des skins. Chaque skin est contenu dans un dossier dans $serveur_base/skins/.
	Le fichier skin.xsl contient le code XSLT pour convertir le code XML et le fichier description.xsl
	contient des informations importantes pour l'application de la transformation XSL et la configuration
	de la skin par l'utilisateur.
	
	Toutes les configurations de l'utilisateur sont stockées dans un cookie. Ce cookie est l'encodage
	en base64 de la version sérialisée d'une structure de la forme :
	array (
		[skin_nom]  => «nom de la skin»
		[skin_css]  => «nom du fichier css»
		[skin_parametres] => array (
			[«param 1] => «valeur»
			[«param 2»] => «valeur»
		)
		[skin_visible] => array (
			[«module 1»] => «true/false»
			[«module 2»] => «true/false»
		)
	)
*/

require_once "../include/global.inc.php";
require_once "../include/xml.inc.php";
require_once "../include/skin.inc.php";

demande_authentification(AUTH_MINIMUM);

/*
	Lit le contenu d'un fichier de description d'une skin.
	Renvoi un arbre ayant la structure suivante :
	array (
		[nom] => «nom de la skin»
		[description] => «sa description»
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
	// Parsage du code XML
	if(!file_exists($fichier)) return array();
	$parsed_xml = xml_get_tree($fichier);
	
	// Vérification de la structure de l'arbre et stockage des données qui nous servent
	// sous la forme d'un arbre.
	$desc = array();
	$desc['parametres'] = array();
	if( $parsed_xml[0]['tag'] == 'skin' ) {
		// pour chaque élément de <skin>
		$element_list = $parsed_xml[0]['children'];
		foreach($element_list as $element) {
			switch($element['tag']) {
				case 'nom':
					$desc['nom'] = $element['value'];
					break;
					
				case 'description':
					$desc['description'] = $element['value'];
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

function lire_description_css($fichier) {
	$description="";
	if(file_exists($fichier)) {
		// Lecture du fichier de description et suppression des éventuelles balises html
		$fd = fopen($fichier,"r");
		$description=fread($fd,filesize($fichier));
		$description=htmlspecialchars($description, ENT_QUOTES);
		fclose($fd);
	}
	return $description == "" ? "Pas de description" : $description;
}

// récupération des modifications de l'utilisateur
$new_skin = array();

if(!empty($_REQUEST['OK_skin'])) {
	$new_skin['skin_nom'] = $_REQUEST['newskin'];
	$new_skin['skin_css'] = $_SESSION['skin']['skin_css'];
	$new_skin['skin_parametres'] = array();
	$new_skin['skin_visible'] = $_SESSION['skin']['skin_visible'];

} else if(!empty($_REQUEST['OK_param'])) {
	// Skin et CSS
	$new_skin['skin_nom'] = $_SESSION['skin']['skin_nom'];
	$new_skin['skin_css'] = empty($_REQUEST['newcss_perso']) ?
								urldecode($_REQUEST['newcss']) :
								urldecode($_REQUEST['newcss_perso']);
	
	// Paramètres
	$new_skin['skin_parametres'] = array();
	if(!empty($_REQUEST['param']))
		foreach($_REQUEST['param'] as $module => $valeur)
			$new_skin['skin_parametres'][$module] = $valeur;
	
	// Visibilité
	foreach(liste_modules() as $module => $nom)
		if($nom != "")
			$new_skin['skin_visible'][$module] = false;
	
	if(!empty($_REQUEST['vis']))
		foreach($_REQUEST['vis'] as $module => $visible)
			$new_skin['skin_visible'][$module] = true;
}

// Si la skin a été modifié, on rajoute un cookie de validité 3 ans
if( !empty($new_skin) ) {
	$cookie = serialize($new_skin);
	SetCookie("skin",base64_encode($cookie),time()+3*365*24*3600,"/");
	skin_parse($cookie);
}

// Récupération du contenu de la page (en XML)
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="choix_skin" titre="Frankiz : choix skin">
	<h1>Personnalisation de Frankiz II</h1>
	
	<formulaire titre="Choix de la skin" action="profil/skin.php">
		<choix titre="Skin" id="newskin" type="radio" valeur="<?php echo $_SESSION['skin']['skin_nom']?>">
<?php
			// Choix de la feuille de style XSL
			$dir=opendir(BASE_LOCAL."/skins");
			while($file = readdir($dir)) {
				// uniquement pour les dossiers non particulier
				if(!is_dir(BASE_LOCAL."/skins/$file") || $file == "." || $file == ".." ||
					$file == "CVS" || $file{0} == "#") continue;
				$description = lire_description_skin(BASE_LOCAL."/skins/$file/description.xml");
				echo "<option titre=\"".$description['nom']." (".$description['description']
					.")\" id=\"$file\"/>";
			}
			closedir($dir);
?>
		</choix>
		<bouton titre="Appliquer" id="OK_skin" />
	</formulaire>
	
	<formulaire titre="Paramètres de la skin" action="profil/skin.php">
		<choix titre="CSS" id="newcss" type="combo" valeur="<?php echo $_SESSION['skin']['skin_css']?>">
<?php
			// Choix de la feuille de style CSS
			$dir=opendir(BASE_LOCAL."/css");
			while($file = readdir($dir)) {
				// uniquement pour les fichiers .css
				if(!ereg("^(.*)\.css$", $file, $elements)) continue;
				$nom = $elements[1];
				echo "<option titre=\"$nom (".lire_description_css(BASE_LOCAL."/css/$nom.txt")
					.")\" id=\"".BASE_URL."/css/$file\"/>";
			}
			closedir($dir);
?>
		</choix>
		<champ titre="CSS perso" id="newcss_perso" valeur="<?php
			if (dirname($_SESSION['skin']['skin_css']) != BASE_URL."/css")
				echo $_SESSION['skin']['skin_css']; ?>"/>
<?php
		// Paramètres spécifique à la skin
		$description = lire_description_skin(BASE_LOCAL."/skins/".$_SESSION['skin']['skin_nom']."/description.xml");
		foreach($description['parametres'] as $parametre_id => $parametre) {
			if(empty($parametre['valeurs'])) {
				echo "<champ titre=\"".$parametre['description']."\" id=\"param[$parametre_id]\" valeur=\""
						.(isset($_SESSION['skin']['skin_parametres'][$parametre_id]) ? $_SESSION['skin']['skin_parametres'][$parametre_id] : "")."\"/>\n";
			} else {
				echo "<choix titre=\"".$parametre['description']."\" id=\"param[$parametre_id]\" valeur=\""
						.(isset($_SESSION['skin']['skin_parametres'][$parametre_id]) ? $_SESSION['skin']['skin_parametres'][$parametre_id] : "")."\" type=\"combo\">\n";
				foreach($parametre['valeurs'] as $param_id => $param_desc)
					echo "\t<option titre=\"$param_desc\" id=\"$param_id\"/>\n";
				echo "</choix>\n";
			}
		}
?>
		<choix titre="Eléments" id="newskin" type="checkbox" valeur="<?php
			foreach(liste_modules() as $module => $nom)
				if($nom != "" && (!isset($_SESSION['skin']['skin_visible'][$module])
								  || $_SESSION['skin']['skin_visible'][$module]      ) )
					echo "vis[$module] ";?>">
<?php
			foreach(liste_modules() as $module => $nom)
				if($nom != "")
					echo "\t\t\t<option titre=\"$nom\" id=\"vis[$module]\"/>\n";
?>
		</choix>
		<bouton titre="Appliquer" id="OK_param" />
	</formulaire>
</page>
<?php

// Applique les transformations
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
