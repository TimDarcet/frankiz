<?
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

	$Log$
	Revision 1.4  2004/11/24 20:07:12  pico
	Ajout des liens persos

	Revision 1.3  2004/11/24 19:02:37  pico
	Applique les changements tout de suite
	
	Revision 1.2  2004/11/24 18:48:01  pico
	Encore un warning
	
	Revision 1.1  2004/11/24 16:24:09  pico
	Passage du formulaire de choix des rss à afficher dans une page spéciale
	

	
	
*/


require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MINIMUM);



$DB_web->query("SELECT url,description FROM liens_rss");
while(list($value,$description)=$DB_web->next_row())
	$array[$value] = $description;
	
	
if(!empty($_REQUEST['OK_rss'])) {
	$rss = array();
	if(!empty($_REQUEST['vis']))
		foreach($_REQUEST['vis'] as $temp => $null){
			list($mode,$value) = split("_",$temp,2);
			if(!isset($rss[$value]) || $rss[$value] != 'complet') $rss[$value] = $mode;
		}
	$_SESSION['rss'] = $rss;
	$rss = serialize($rss);
	$DB_web->query("UPDATE compte_frankiz SET liens_rss='$rss' WHERE eleve_id='{$_SESSION['user']->uid}'");	
}


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
	$DB_web->query("UPDATE compte_frankiz SET liens_perso='$liens' WHERE eleve_id='{$_SESSION['user']->uid}'");
}

if(!empty($_REQUEST['del_lien'])) {
	$liens = array();
	$url_suppr = base64_decode($_REQUEST['del_lien']);
	if(!empty($_SESSION['liens_perso'])) {
		foreach($_SESSION['liens_perso'] as $titre => $url)
			if($url != $url_suppr) $liens[$titre] = $url;
	}
	$_SESSION['liens_perso'] = $liens;
	$liens = serialize($liens);
	$DB_web->query("UPDATE compte_frankiz SET liens_perso='$liens' WHERE eleve_id='{$_SESSION['user']->uid}'");
}
	
// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="profil_liens_ext" titre="Frankiz : Gestion des liens externes">

	<formulaire id="form_liens" titre="Choix des Liens perso" action="profil/liens_ext.php">
<?
	if(isset($_SESSION['liens_perso']) && !empty($_SESSION['liens_perso']) && count($_SESSION['liens_perso'])>0)
		foreach($_SESSION['liens_perso'] as $titre => $url)
			echo "<note>$titre: $url <lien titre=\"supprimer\" url=\"profil/liens_ext.php?del_lien=".base64_encode($url)."\"/></note>";
	echo "\t\t\t<champ id=\"newlien[titre]\" titre=\"Titre\"/>\n\t\t\t<champ id=\"newlien[url]\" titre=\"Url\"/>";
?>
		<bouton titre="Ajouter" id="OK_liens" />
</formulaire>


	<formulaire id="form_rss" titre="Choix des RSS" action="profil/liens_ext.php">
		<note>Choisis quelles infos tu veux avoir sur ta page de news externes</note>
<?
 		foreach(array('sommaire','complet') as $mode){ 
				echo "<choix titre=\"Affichage $mode\" id=\"newrss\" type=\"checkbox\" valeur=\"";
					foreach($array as $value => $description)
							if($value != "" && (isset($_SESSION['rss'][$value])) && ($_SESSION['rss'][$value] == $mode))
								echo "vis[".$mode."_".$value."]";
						echo"\">";
						foreach($array as $value => $description)
							if($value != "")
								echo "\t\t\t<option titre=\"$description\" id=\"vis[".$mode."_".$value."]\"/>\n";
								
				echo "</choix>";
				echo "\t\t\t<champ id=\"rss_perso_".$mode."\" titre=\"Nouvelle rss perso\"/><br/><br/>";
		} 
?>
		<note>Choisis quelles infos tu veux avoir sur toutes tes pages Frankiz</note>
<?
 		foreach(array('module') as $mode){ 
				echo "<choix titre=\"Affichage sommaire en module\" id=\"newrss\" type=\"checkbox\" valeur=\"";
					foreach($array as $value => $description)
							if($value != "" && (isset($_SESSION['rss']['m_'.$value])) && ($_SESSION['rss']['m_'.$value] == $mode))
								echo "vis[".$mode."_m_".$value."]";
						echo"\">";
						foreach($array as $value => $description)
							if($value != "")
								echo "\t\t\t<option titre=\"$description\" id=\"vis[".$mode."_m_".$value."]\"/>\n";
				echo "</choix>";
				echo "\t\t\t<champ id=\"rss_perso_module\" titre=\"Nouvelle rss perso\"/>";
		} 
?>
		<bouton titre="Appliquer" id="OK_rss" />
	</formulaire>
</page>
<?
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>