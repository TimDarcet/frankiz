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
	
	$Id$

*/

require_once "../include/global.inc.php";

demande_authentification(AUTH_MINIMUM);

// récupération des modifications de l'utilisateur
$new_skin = array();

if(!empty($_REQUEST['OK_skin'])) {
	list($new_skin['skin_nom'],$new_skin['skin_css']) = split("/",$_REQUEST['newskin']."/");
	$new_skin['skin_parametres'] = array();
	$new_skin['skin_visible'] = $_SESSION['skin']['skin_visible'];

} else if(!empty($_REQUEST['OK_param'])) {
	// recopie des infos sur la skin
	$new_skin['skin_nom'] = $_SESSION['skin']['skin_nom'] ;
	$new_skin['skin_css'] = $_SESSION['skin']['skin_css'];


	// CSS perso
	if(!empty($_REQUEST['newcss_perso']))
		$new_skin['skin_css_perso'] = urldecode($_REQUEST['newcss_perso']);
	
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

// Si la skin a été modifié, on rajoute un cookie de validité 3 ans et on stocke la skin dans nos bases.
if(!empty($new_skin)) {
	$cookie = serialize($new_skin);
	SetCookie("skin",base64_encode($cookie),time()+3*365*24*3600,"/");
	skin_parse($cookie);
	$DB_web->query("UPDATE compte_frankiz SET skin='$cookie' WHERE eleve_id='{$_SESSION['user']->uid}'");
}

// Récupération du contenu de la page (en XML)
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="choix_skin" titre="Frankiz : choix skin">
	<h1>Personnalisation de Frankiz II</h1>
	
	<formulaire id="form_choix_skin" titre="Choix de la skin" action="profil/skin.php">
		<choix titre="Skin" id="newskin" type="radio" valeur="<?php echo $_SESSION['skin']['skin_nom']."/".$_SESSION['skin']['skin_css'] ?>">
<?php
			//NB total d'utilisateurs
			$DB_web->query("SELECT COUNT(*) FROM compte_frankiz WHERE skin!=''");
			list($nbutilisateurtotal) = $DB_web->next_row();
			
			
			
			// Parcourt des skins XSL
			$dir_xsl=opendir(BASE_LOCAL."/skins");
			while($file_xsl = readdir($dir_xsl)) {
				// uniquement pour les dossiers non particuliers
				if(!is_dir(BASE_LOCAL."/skins/$file_xsl") || $file_xsl == "." || $file_xsl == ".." ||
					$file_xsl == "CVS" || $file_xsl{0} == "#") continue;
				
				$description = lire_description_skin(BASE_LOCAL."/skins/$file_xsl");
				if(empty($description)) {
					ajouter_debug_log("Erreur de lecture de la description de la skin xsl $file_xsl");
					continue;
				}
				
				// Si c'est une skin sans CSS
				if($description['chemin'] == ".") {
					$DB_web->query("SELECT COUNT(*) FROM compte_frankiz WHERE skin LIKE '%$file_css%$file_xsl%'");
					list($nbutilisateur) = $DB_web->next_row();
					echo "<option titre=\"{$description['nom']}: {$description['description']} (".round(100*$nbutilisateur/$nbutilisateurtotal)."%)\" id=\"$file_xsl/\"/>";
					continue;
				}
				
				// Parcourt des feuilles de style css
				$dir_css=opendir(BASE_LOCAL."/skins/$file_xsl");
				while($file_css = readdir($dir_css)) {
					// uniquement pour les dossiers non particuliers
					if(!is_dir(BASE_LOCAL."/skins/$file_xsl/$file_css") || $file_css == "." || $file_css == ".." ||
						$file_css == "CVS" || $file_css{0} == "#" || $file_css == $description['chemin']) continue;
					
					$description_css = lire_description_css(BASE_LOCAL."/skins/$file_xsl/$file_css");
					if($description_css!=""){
						$DB_web->query("SELECT COUNT(*) FROM compte_frankiz WHERE skin LIKE '%$file_css%$file_xsl%'");
						list($nbutilisateur) = $DB_web->next_row();
						if($file_css!="default")
							echo "<option titre=\"$file_css: $description_css (".round(100*$nbutilisateur/$nbutilisateurtotal)."%)\" id=\"$file_xsl/$file_css\"/>";
						else
							echo "<option titre=\"$file_xsl: $description_css (".round(100*$nbutilisateur/$nbutilisateurtotal)."%)\" id=\"$file_xsl/$file_css\"/>";
					}
				}
				closedir($dir_css);
			}
			closedir($dir_xsl);
?>
		</choix>
		<bouton titre="Appliquer" id="OK_skin" />
	</formulaire>
	
	<formulaire id="form_param_skin" titre="Paramètres de la skin <? echo $_SESSION['skin']['skin_nom'] ?>" action="profil/skin.php">
		<note>Tu peux choisir des paramètres spéciaux pour la skin courante.</note>
<?php
		// Paramètres spécifique à la skin
		$description = lire_description_skin(BASE_LOCAL."/skins/".$_SESSION['skin']['skin_nom']);
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
		<note>Tu peux aussi ne pas faire apparaître tous les éléments de la skin. Tu gagneras ainsi de la
			  place. Choisis donc les éléments que tu veux afficher.</note>
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

		<note>Si tu souhaites personnaliser ta skin plus en profondeur, tu peux créer ta propre feuille de style CSS
			(ceci s'adresse aux experts).</note>
		<champ titre="CSS perso" id="newcss_perso" valeur="<?php if(isset($_SESSION['skin']['skin_css_perso'])) echo $_SESSION['skin']['skin_css_perso'];?>"/>
		<bouton titre="Appliquer" id="OK_param" />
	</formulaire>
</page>
<?php

// Applique les transformations
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
