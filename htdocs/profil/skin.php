<?php
/*
	Copyright (C) 2004 Binet R�seau
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
	Page de gestion des skins. Chaque skin est contenu dans un dossier dans $serveur_base/skins/.
	Le fichier skin.xsl contient le code XSLT pour convertir le code XML et le fichier description.xsl
	contient des informations importantes pour l'application de la transformation XSL et la configuration
	de la skin par l'utilisateur.
	
	Toutes les configurations de l'utilisateur sont stock�es dans un cookie. Ce cookie est l'encodage
	en base64 de la version s�rialis�e d'une structure de la forme�:
	array (
		[skin_nom]  => �nom de la skin�
		[skin_css]  => �nom du fichier css�
		[skin_parametres] => array (
			[�param 1] => �valeur�
			[�param 2�] => �valeur�
		)
		[skin_visible] => array (
			[�module 1�] => �true/false�
			[�module 2�] => �true/false�
		)
	)
	
	$Log$
	Revision 1.16  2004/11/22 23:38:42  kikx
	Ajout de <note></note> un peu partout pour plus de compr�hension !

	Revision 1.15  2004/11/16 12:17:26  schmurtz
	Deplacement des skins de trombino.eleves vers frankiz.compte_frankiz
	
	Revision 1.14  2004/11/13 00:12:24  schmurtz
	Ajout du su
	
	Revision 1.13  2004/11/11 21:15:52  kikx
	Rajout d'un champs dans le trombino pour stocker la skin du mec ...
	le cookie est prioritaire, mais si il n'existe pas ou qu'il a ppartient a quelqu'un d'autre, alors on va cherhcer dans la BDD
	
	Revision 1.12  2004/11/08 12:45:17  pico
	Rajout d'un id pour pouvoir skinner les titres des formulaires
	
	Revision 1.11  2004/11/06 10:40:46  pico
	Maintenant, �a doit marcher
	
	Revision 1.10  2004/11/06 10:23:15  pico
	BugFix au niveau de l'affichage du choix de css
	
	Lorsqu'on change de skin, la css est la css "style.css" du r�pertoire de la skin.
	Cela permet d'�viter de garder la css d'une autre skin, sinon �a rend tout pas beau.
	
	Revision 1.9  2004/11/06 10:13:27  pico
	Mise � jour fichier choix skin
	
	Revision 1.8  2004/10/21 22:19:38  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.7  2004/09/15 23:20:07  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.6  2004/09/15 21:42:21  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

require_once "../include/global.inc.php";
require_once "../include/xml.inc.php";
require_once "../include/skin.inc.php";

demande_authentification(AUTH_MINIMUM);

/*
	Lit le contenu d'un fichier de description d'une skin.
	Renvoi un arbre ayant la structure suivante�:
	array (
		[nom] => �nom de la skin�
		[description] => �sa description�
		[parametres] => array (				//liste des param�tres de la skin
			[�id du premier param�tre�] => array (
				[id] => �id du premier param�tre�
				[description] => �description du param�tre�
				[valeurs] => array (		// liste des valeurs que peut prendre le param�tre
					[�id de la premi�re valeur�] => [�nom de la premi�re valeur�]
					[�id de la deuxi�me valeur�] => [�nom de la deuxi�me valeur�]
				)
			)
			[�id du deuxi�me param�tre�] => array (
				...
			)
		)
	)
	
	Le code XML �tant�:
	<skin>
		<nom>�nom de la skin�</nom>
		<descrition>�sa description�</description>
		<parametre id="�id du premier param�tre�">
			<description>�description du param�tre�</description>
			<valeur id="�id de la premi�re valeur�">�nom de la premi�re valeur�</valeur>
			<valeur id="�id de la deuxi�me valeur�">�nom de la deuxi�me valeur�</valeur>
		</parametre>
		<parametre id="�id du deuxi�me param�tre�">
			...
		</parametre>
	</skin>
*/

function lire_description_skin($fichier) {
	// Parsage du code XML
	if(!file_exists($fichier)) return array();
	$parsed_xml = xml_get_tree($fichier);
	
	// V�rification de la structure de l'arbre et stockage des donn�es qui nous servent
	// sous la forme d'un arbre.
	$desc = array();
	$desc['parametres'] = array();
	if( $parsed_xml[0]['tag'] == 'skin' ) {
		// pour chaque �l�ment de <skin>
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

					// pour chaque �l�ment de <parametre>
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
					
					// enregistrement du param�tre avec si besoin une valeur par d�faut de la
					// description
					if( empty($param['description']) )
						$param['description'] = "Param�tre �".$param['id']."�";
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
		// Lecture du fichier de description et suppression des �ventuelles balises html
		$fd = fopen($fichier,"r");
		$description=fread($fd,filesize($fichier));
		$description=htmlspecialchars($description, ENT_QUOTES);
		fclose($fd);
	}
	return $description == "" ? "Pas de description" : $description;
}

// r�cup�ration des modifications de l'utilisateur
$new_skin = array();

if(!empty($_REQUEST['OK_skin'])) {
	$new_skin['skin_nom'] = $_REQUEST['newskin'];
	$new_skin['skin_css'] = BASE_URL."/skins/".$_REQUEST['newskin']."/style.css";
	$new_skin['skin_parametres'] = array();
	$new_skin['skin_visible'] = $_SESSION['skin']['skin_visible'];

} else if(!empty($_REQUEST['OK_param'])) {
	// Skin et CSS
	$new_skin['skin_nom'] = $_SESSION['skin']['skin_nom'];
	$new_skin['skin_css'] = empty($_REQUEST['newcss_perso']) ?
								urldecode($_REQUEST['newcss']) :
								urldecode($_REQUEST['newcss_perso']);
	
	// Param�tres
	$new_skin['skin_parametres'] = array();
	if(!empty($_REQUEST['param']))
		foreach($_REQUEST['param'] as $module => $valeur)
			$new_skin['skin_parametres'][$module] = $valeur;
	
	// Visibilit�
	foreach(liste_modules() as $module => $nom)
		if($nom != "")
			$new_skin['skin_visible'][$module] = false;
	
	if(!empty($_REQUEST['vis']))
		foreach($_REQUEST['vis'] as $module => $visible)
			$new_skin['skin_visible'][$module] = true;
}

// Si la skin a �t� modifi�, on rajoute un cookie de validit� 3 ans et on stocke la skin dans nos bases.
if(!empty($new_skin)) {
	$cookie = serialize($new_skin);
	SetCookie("skin",base64_encode($cookie),time()+3*365*24*3600,"/");
	skin_parse($cookie);
	$DB_web->query("UPDATE compte_frankiz SET skin='$cookie' WHERE eleve_id='{$_SESSION['user']->uid}'");
}

// R�cup�ration du contenu de la page (en XML)
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="choix_skin" titre="Frankiz : choix skin">
	<h1>Personnalisation de Frankiz II</h1>
	
	<formulaire id="form_choix_skin" titre="Choix de la skin" action="profil/skin.php">
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
	
	<formulaire id="form_param_skin" titre="Param�tres de la skin <? echo $_SESSION['skin']['skin_nom'] ?>" action="profil/skin.php">
		<note>Pour chaque skin, il existe plusieurs version disponible gr�ce � des feuilles de style (CSS) : Choisis en une ...</note>
		<choix titre="Version CSS" id="newcss" type="combo" valeur="<?php echo $_SESSION['skin']['skin_css']?>">
<?php
			// Choix de la feuille de style CSS
			if(is_dir(BASE_LOCAL."/css/".$_SESSION['skin']['skin_nom'])) {
				$dir=opendir(BASE_LOCAL."/css/{$_SESSION['skin']['skin_nom']}");
				while($file = readdir($dir)) {
					// uniquement pour les fichiers .css
					if(!ereg("^(.*)\.css$", $file, $elements)) continue;
					$nom = $elements[1];
					echo "<option titre=\"$nom (".lire_description_css(BASE_LOCAL."/css/{$_SESSION['skin']['skin_nom']}/$nom.txt")
						.")\" id=\"".BASE_URL."/css/{$_SESSION['skin']['skin_nom']}/$file\"/>";
				}
				closedir($dir);
			}
			echo "<option titre=\"CSS par d�faut de la skin\" id=\"".BASE_URL."/skins/{$_SESSION['skin']['skin_nom']}/style.css\"/>";
?>
		</choix>
		<note>Si tu veux personnaliser encore plus ta skin, tu peux cr�er ta propre feuille de style. Ceci s'adresse aux experts :)</note>
		<champ titre="CSS perso" id="newcss_perso" valeur="<?php
			if ((dirname($_SESSION['skin']['skin_css']) != BASE_URL."/css/".$_SESSION['skin']['skin_nom'])&&(dirname($_SESSION['skin']['skin_css']) != BASE_URL."/skins/".$_SESSION['skin']['skin_nom'])){
				echo $_SESSION['skin']['skin_css']; }?>"/>
<?php
		// Param�tres sp�cifique � la skin
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
		<note>Tu peux aussi ne pas faire appara�tre tous les �l�ment de la skin. Tu gagneras ainsi de la place :) Choisis donc les �l�ments que tu veux afficher</note>

		<choix titre="El�ments" id="newskin" type="checkbox" valeur="<?php
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
