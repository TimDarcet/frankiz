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
	Recherche dans le trombino.

	$Log$
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

$array = array(
	'http://www.liberation.fr/rss.php'=>'Libération',
	'http://linuxfr.org/backend/news/rss20.rss'=>'News LinuxFr',
	'http://linuxfr.org/backend/news-homepage/rss20.rss'=>'News 1ère page LinuxFr',
	'http://www.framasoft.net/backend.php3'=>'Framasoft',
	'http://www.infos-du-net.com/backend.php'=>'Infos du Net',
	'http://www.clubic.com/c/xml.php?type=news'=>'Clubic',
	'http://hyperlinkextractor.free.fr/rssfiles/google_france.xml'=>'Google France',
	'http://www.humanite.fr/backend_une.php3'=>'L\'Humanité',
	'http://www.lexpress.fr/getfeedrss.asp'=>'L\'Express',
	'http://permanent.nouvelobs.com/cgi/rss/permanent_une'=>'Le Nouvel Obs',
	'http://www.vnunet.fr/rssrdf/news.xml '=>'SVM',
	'http://www.microsite.reuters.com/rss/topNews'=>'Reuters',
	'http://www.washingtonpost.com/wp-srv/world/rssheadlines.xml'=>'Washington Post',
	'http://mozillazine.org/contents.rdf'=>'MozillaZine',
	);
if(!empty($_REQUEST['OK_param'])) {
	// Visibilité
	foreach($array as $value => $mode)
		if($array != "")
			unset($_SESSION['rss'][$value]);
	
	if(!empty($_REQUEST['vis']))
		foreach($_REQUEST['vis'] as $value => $mode)
			if(!isset($_SESSION['rss'][$value]) || $_SESSION['rss'][$value] != 'complet') $_SESSION['rss'][$value] = $mode;
}

if( !isset($_SESSION['rss']) || nouveau_login() ) {
	$_SESSION['rss'] = $array;
}
?>
	<formulaire id="form_param_rss" titre="Choix des RSS" action="rss.php">
		<note>Choisis quelles infos tu veux avoir sur ta page de news externes</note>
<?
 		foreach(array('sommaire','complet') as $mode){ 
				echo "<choix titre=\"Affichage $mode\" id=\"newrss\" type=\"checkbox\" valeur=\"";
					foreach($array as $value => $description)
							if($value != "" && (isset($_SESSION['rss'][$value])) && ($_SESSION['rss'][$value] == $mode))
								echo "vis[$value]=$mode ";
						echo"\">";
						foreach($array as $value => $description)
							if($value != "")
								echo "\t\t\t<option titre=\"$description\" id=\"vis[$value]=$mode\" valeur='$mode'/>\n";
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