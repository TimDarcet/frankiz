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
rss_xml('http://www.liberation.fr/rss.php');
rss_xml('http://linuxfr.org/backend/news/rss20.rss');
rss_xml('http://linuxfr.org/backend/news-homepage/rss20.rss');
?>
</page>
<?
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>