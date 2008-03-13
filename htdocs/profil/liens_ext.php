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
	Gestions des liens perso / des flux rss.

	$Id$

*/

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_COOKIE);


$DB_web->query("SELECT url,description FROM liens_rss");
while(list($value,$description)=$DB_web->next_row())
	$array[$value] = $description;
	

// Mise à jour / ajout de rss
if(isset($_REQUEST['OK_rss'])) {
	$rss = array();
	// Rss contenues dans la base sql
	if(!empty($_REQUEST['vis']))
		foreach($_REQUEST['vis'] as $temp => $null){
			list($mode,$value) = split("_",$temp,2);
			if(!isset($rss[$value]) || $rss[$value] != 'complet') $rss[$value] = $mode;
		}
	// Rss persos
	foreach(array('complet','sommaire') as $mode)
		if(!empty($_REQUEST['rss_perso_'.$mode]))
			$rss[$_REQUEST['rss_perso_'.$mode]] = $mode;
	foreach(array('module') as $mode)
		if(!empty($_REQUEST['rss_perso_'.$mode]))
			$rss['m_'.$_REQUEST['rss_perso_'.$mode]] = $mode;
	// Mise à jour des infos de session et de la base de données
	$_SESSION['rss'] = $rss;
	$rss2 = serialize($rss);
	$DB_web->query("UPDATE compte_frankiz SET liens_rss='$rss2' WHERE eleve_id='{$_SESSION['uid']}'");	
}

// Supprime une rss perso
if(!empty($_REQUEST['del_rss'])) {
	$rss = array();
	$url_suppr = base64_decode($_REQUEST['del_rss']);
	if(!is_array($_SESSION['rss'])) {
		foreach($_SESSION['rss'] as $url => $mode){
			$urltest = $url;
			if($mode == 'module')
				$urltest = substr($urltest, 2); 
			if($urltest != $url_suppr) 
				$rss[$url] = $mode;
		}
	}
	$_SESSION['rss'] = $rss;
	$rss2 = serialize($rss);
	$DB_web->query("UPDATE compte_frankiz SET liens_rss='$rss2' WHERE eleve_id='{$_SESSION['uid']}'");
}

// Ajoute un lien perso
if(!empty($_REQUEST['OK_liens'])) {
	$liens = array();
	$liens = $_SESSION['liens_perso'];
	if(!empty($_REQUEST['newlien'])) {
		foreach($_REQUEST['newlien'] as $var => $value)
			$$var= $value;
		$liens[$titre] = $url;
	}
	$_SESSION['liens_perso'] = $liens;
	$liens = serialize($liens);
	$DB_web->query("UPDATE compte_frankiz SET liens_perso='$liens' WHERE eleve_id='{$_SESSION['uid']}'");
}

// Supprime un lien perso
if(!empty($_REQUEST['del_lien'])) {
	$liens = array();
	$url_suppr = base64_decode($_REQUEST['del_lien']);
	if(!empty($_SESSION['liens_perso'])) {
		foreach($_SESSION['liens_perso'] as $titre => $url)
			if($url != $url_suppr) $liens[$titre] = $url;
	}
	$_SESSION['liens_perso'] = $liens;
	$liens = serialize($liens);
	$DB_web->query("UPDATE compte_frankiz SET liens_perso='$liens' WHERE eleve_id='{$_SESSION['uid']}'");
}
	

// Génération de la page
//===============
require_once BASE_FRANKIZ."htdocs/include/page_header.inc.php";

?>
<page id="profil_liens_ext" titre="Frankiz : Gestion des liens externes">

	<formulaire id="form_liens" titre="Choix des Liens perso" action="profil/liens_ext.php">
<?php
	if(isset($_SESSION['liens_perso']) && !empty($_SESSION['liens_perso']) && count($_SESSION['liens_perso'])>0)
		foreach($_SESSION['liens_perso'] as $titre => $url)
			echo "<note>$titre: $url <lien titre=\"supprimer\" url=\"profil/liens_ext.php?del_lien=".base64_encode($url)."\"/></note>";
	echo "\t\t\t<champ id=\"newlien[titre]\" titre=\"Titre\"/>\n\t\t\t<champ id=\"newlien[url]\" titre=\"Url\"/>";
?>
		<bouton titre="Ajouter" id="OK_liens" />
</formulaire>

<?php

?>
	<formulaire id="form_rss" titre="Choix des RSS" action="profil/liens_ext.php">
		<note>Choisis quelles infos tu veux avoir sur ta page de news externes</note>
<?php
		$liens = array();
		$liens = $_SESSION['rss'];
 		foreach(array('sommaire','complet') as $mode){ 
				echo "<choix titre=\"Affichage $mode\" id=\"newrss\" type=\"checkbox\" valeur=\"";
					foreach($array as $value => $description)
							if($value != "" && (isset($liens[$value])) && ($liens[$value] == $mode))
								echo "vis[".$mode."_".$value."]";
						echo"\">";
						foreach($array as $value => $description)
							if($value != "")
								echo "\t\t\t<option titre=\"$description\" id=\"vis[".$mode."_".$value."]\"/>\n";
								
				echo "</choix>";
		} 
?>
		<note>Choisis quelles infos tu veux avoir sur toutes tes pages Frankiz</note>
<?php
		
 		foreach(array('module') as $mode){ 
				echo "<choix titre=\"Affichage sommaire en module\" id=\"newrss\" type=\"checkbox\" valeur=\"";
					foreach($array as $value => $description)
							if($value != "" && (isset($liens['m_'.$value])) && ($liens['m_'.$value] == $mode))
								echo "vis[".$mode."_m_".$value."]";
						echo"\">";
						foreach($array as $value => $description)
							if($value != "")
								echo "\t\t\t<option titre=\"$description\" id=\"vis[".$mode."_m_".$value."]\"/>\n";
				echo "</choix>";
		} 
?>
		<note>Liste des flux RSS perso</note>
<?php
		if(is_array($liens)){
			foreach($liens as $url => $mode){
				$urltest=$url;
				if($mode == 'module') $urltest = substr($url, 2);
				if(!array_key_exists($urltest,$array)){
					echo "<hidden id=\"vis[".$mode."_".$url."]\" valeur=\"\" />";
					echo "<note>$urltest ($mode) <lien titre=\"supprimer\" url=\"profil/liens_ext.php?del_rss=".base64_encode($urltest)."\"/></note>";
				}
			}
		}
?>
		<note>Ajouter un flux RSS perso</note>
<?php
		foreach(array('sommaire','complet','module') as $mode){ 
			echo "<champ id=\"rss_perso_".$mode."\" titre=\"$mode\"/>\n";
		}
		
?>
		<bouton titre="Appliquer" id="OK_rss" />
	</formulaire>
<?php

?>
</page>
<?php
require_once BASE_FRANKIZ."htdocs/include/page_footer.inc.php";
?>
