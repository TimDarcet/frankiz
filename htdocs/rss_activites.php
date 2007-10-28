<?php
require_once "include/global.inc.php";
require_once "include/wiki.inc.php";

echo"<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

?>

<rss version="2.0">
	<channel>
		<title>Frankiz : Activités</title>
		<link><?php echo BASE_URL ?></link>
		<description>Frankiz : Le serveur des élèves de l'école polytechnique.</description>
		<ttl>10</ttl>
		<image>
			<url><?php echo BASE_URL ?>/skins/xhtml/default/images/frankiz.png</url>
			<title>Frankiz : Activités</title>
			<link><?php echo BASE_URL ?></link>
		</image>
<?php
$date_legend = array("Aujourd'hui","Demain","Après-demain","Dans 3 jours","Dans 4 jours","Dans 5 jours","Dans une semaine");
if(!verifie_permission('interne')) $exterieur=" AND exterieur='1' ";
for($i= 0; $i<7;$i++){
	$DB_web->query("SELECT affiche_id,titre,url,DATE_FORMAT(date,'%d/%m/%Y %H:%i:%s'),description FROM affiches WHERE TO_DAYS(date)=TO_DAYS(NOW() + INTERVAL $i DAY) $exterieur ORDER BY date");
	while (list($id,$titre,$url,$date,$texte)=$DB_web->next_row()) {
?>
		<item>
			<title><?php echo $date.": ".$titre; ?></title>
			<link><?php echo ( ($url == "")? BASE_URL : $url ); ?></link>
			<pubDate><?php echo date(DATE_RFC822,$date); ?></pubDate>
			<description>
<?php
				echo htmlspecialchars(wikiVersXML($texte,true),ENT_COMPAT,UTF-8);
				if (file_exists(DATA_DIR_LOCAL."affiches/$id")) {
					echo htmlspecialchars("<p><img src='http://www.frankiz.net/data/affiches/".$id."' alt=''/></p>");
				}
?>
			</description>
			<guid isPermaLink="false"><?php echo $id; ?></guid>
		</item>
<?php
	}
}
 ?>
	</channel>
</rss>
