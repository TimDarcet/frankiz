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
	Affichage de flux rss externes.

	$Log$
	Revision 1.15  2004/11/24 15:55:33  pico
	Code pour gérer les liens perso + les rss au lancement de la session

	Revision 1.14  2004/11/24 15:37:37  pico
	Lis et sauvegarde les infos de session depuis la sql
	
	Revision 1.13  2004/11/24 15:18:19  pico
	Mise en place des liens sur une base sql
	
	Revision 1.12  2004/11/24 13:45:24  pico
	Modifs skins pour le wiki et l'id de la page d'annonces
	
	Revision 1.11  2004/11/24 13:31:42  pico
	Modifs pages liens rss
	
	Revision 1.10  2004/11/23 21:17:41  pico
	Ne charge qu'au login ou à l'établissemnt de la session (ce code va buger, je fais juste un travail préparatoire)
	
	
*/


require_once "include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MINIMUM);


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";
require_once BASE_LOCAL."/include/rss_func.inc.php";
?>
<page id="rss" titre="Frankiz : News Externes">

<?

$DB_web->query("SELECT url,description FROM liens_rss");
while(list($value,$description)=$DB_web->next_row())
	$array[$value] = $description;
	
	
if(!empty($_REQUEST['OK_param'])) {
	if(!empty($_REQUEST['vis']))
		foreach($_REQUEST['vis'] as $temp => $null){
			list($mode,$value) = split("_",$temp,2);
			if(!isset($rss[$value]) || $rss[$value] != 'complet') $rss[$value] = $mode;
		}
	$_SESSION['rss'] = $rss;
	$rss = serialize($rss);
	$DB_web->query("UPDATE compte_frankiz SET liens_rss='$rss' WHERE eleve_id='{$_SESSION['user']->uid}'");
	
}

?>
	<formulaire id="form_param_rss" titre="Choix des RSS" action="rss.php">
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
		} 
?>
		<bouton titre="Appliquer" id="OK_param" />
	</formulaire>
<?



$liens = $_SESSION['rss'];
foreach($liens as $value => $mode){
	rss_xml($value,$mode);
}

?>
</page>
<?
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>