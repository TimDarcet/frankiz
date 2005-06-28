<?
require_once "include/global.inc.php";
require_once "include/wiki.inc.php";

function get_categorie($en_haut,$stamp,$perime) {
	if($en_haut==1) return "important";
	elseif($stamp > date("Y-m-d H:i:s",time()-12*3600)) return "nouveau";
	elseif($perime < date("Y-m-d H:i:s",time()+24*3600)) return "vieux";
	else return "reste";
}
 
echo"<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

?>

<rss version="2.0">
	<channel>
		<title>Frankiz : Activités</title>
		<link><? echo BASE_URL ?></link>
		<description>Frankiz : Le serveur des élèves de l'école polytechnique.</description>
		<ttl>10</ttl>
		<image>
			<url><? echo BASE_URL ?>/skins/xhtml/default/images/frankiz.png</url>
			<title>Frankiz : Activités</title>
			<link><? echo BASE_URL ?></link>
		</image>
<?
$date_legend = array("Aujourd'hui","Demain","Après-demain","Dans 3 jours","Dans 4 jours","Dans 5 jours","Dans une semaine");
if(!est_authentifie(AUTH_INTERNE)) $exterieur=" AND exterieur='1' ";
for($i= 0; $i<7;$i++){
	$DB_web->query("SELECT affiche_id,titre,url,date,description FROM affiches WHERE TO_DAYS(date)=TO_DAYS(NOW() + INTERVAL $i DAY) $exterieur ORDER BY date");
	while (list($id,$titre,$url,$date,$texte)=$DB_web->next_row()) {
?>
		<item>
			<title><?php echo date("d/m/Y H:m",strtotime($date)).": ".$titre ?></title>
			<link><? echo $url ?></link>
			<pubDate><?php echo $stamp ?></pubDate>
			<description>
<?
				echo htmlspecialchars(wikiVersXML($texte,true),ENT_COMPAT,UTF-8);
				if (file_exists(DATA_DIR_LOCAL."affiches/$id")) {
					echo htmlspecialchars("<p><img src='http://www.frankiz.net/data/affiches/".$id."' alt=''/></p>");
				}
?>
			</description>
		</item>
<?
	}
}
 ?>
	</channel>
</rss>
