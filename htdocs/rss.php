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
rss_xml('http://www.liberation.fr/rss.php','sommaire');
rss_xml('http://linuxfr.org/backend/news/rss20.rss','sommaire');
rss_xml('http://linuxfr.org/backend/news-homepage/rss20.rss','sommaire');
rss_xml('http://www.framasoft.net/backend.php3','sommaire');
rss_xml('http://www.infos-du-net.com/backend.php','sommaire');
rss_xml('http://www.clubic.com/c/xml.php?type=news','sommaire');
?>
</page>
<?
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>