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
if( !isset($_SESSION['rss']) || nouveau_login() ) {
$array = array(
	'http://www.liberation.fr/rss.php'=>'complet',
	/*'http://linuxfr.org/backend/news/rss20.rss'=>'sommaire',
	'http://linuxfr.org/backend/news-homepage/rss20.rss'=>'sommaire',
	'http://www.framasoft.net/backend.php3'=>'sommaire',
	'http://www.infos-du-net.com/backend.php'=>'sommaire',
	'http://www.clubic.com/c/xml.php?type=news'=>'sommaire',
	'http://hyperlinkextractor.free.fr/rssfiles/google_france.xml'=>'sommaire',
	'http://www.humanite.fr/backend_une.php3'=>'sommaire',
	'http://www.lexpress.fr/getfeedrss.asp'=>'sommaire',
	'http://permanent.nouvelobs.com/cgi/rss/permanent_une'=>'sommaire',
	'http://www.vnunet.fr/rssrdf/news.xml '=>'sommaire',
	'http://www.microsite.reuters.com/rss/topNews'=>'sommaire',
	'http://www.washingtonpost.com/wp-srv/world/rssheadlines.xml'=>'sommaire',*/
	'http://mozillazine.org/contents.rdf'=>'sommaire',
	);

	$_SESSION['rss'] = base64_encode(serialize($array));
}

$liens = unserialize(base64_decode($_SESSION['rss']));
foreach($liens as $value => $mode){
	rss_xml($value,$mode);
}

?>
</page>
<?
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>