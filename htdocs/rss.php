<?
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
	'http://www.liberation.fr/rss.php'=>'complet',
	'http://linuxfr.org/backend/news/rss20.rss'=>'sommaire',
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
	'http://www.washingtonpost.com/wp-srv/world/rssheadlines.xml'=>'sommaire',
	'http://mozillazine.org/contents.rdf'=>'sommaire',
	);

$_SESSION['rss'] = base64_encode(serialize($array));

$liens = unserialize(base64_decode($_SESSION['rss']));
foreach($liens as $value => $mode){
	rss_xml($value,$mode);
}

?>
</page>
<?
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>