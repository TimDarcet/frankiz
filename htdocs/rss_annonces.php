<?php
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
		<title>Frankiz : Annonces</title>
		<link><?php echo BASE_URL ?></link>
		<description>Frankiz : Le serveur des élèves de l'école polytechnique.</description>
		<ttl>10</ttl>
		<image>
			<url><?php echo BASE_URL ?>/skins/xhtml/default/images/frankiz.png</url>
			<title>Frankiz : Annonces</title>
			<link><?php echo BASE_URL ?></link>
		</image>
			
<?php
$DB_web->query("SELECT annonce_id,UNIX_TIMESTAMP(stamp),stamp,perime,titre,contenu,en_haut,exterieur,nom,prenom,surnom,promo,"
					 ."IFNULL(mail,CONCAT(login,'@poly.polytechnique.fr')) as mail "
					 ."FROM annonces LEFT JOIN trombino.eleves USING(eleve_id) "
					 ."WHERE (perime>=NOW()) ORDER BY perime DESC");
while(list($id,$date,$stamp,$perime,$titre,$contenu,$en_haut,$exterieur,$nom,$prenom,$surnom,$promo,$mail)=$DB_web->next_row()) {
	if(!$exterieur && !verifie_permission('interne')) continue;
?>
	<item>
		<title><?php echo $titre; ?></title>
		<link><?php echo BASE_URL."?nonlu=$id#annonce_$id"; ?></link>
		<category><?php echo get_categorie($en_haut, $stamp, $perime); ?></category>
		<pubDate><?php echo date(DATE_RFC822,$date); ?></pubDate>
		<description>
<?php
		echo htmlspecialchars(wikiVersXML($contenu,true),ENT_COMPAT,UTF-8);

			if (file_exists(DATA_DIR_LOCAL."annonces/$id")) {
				echo htmlspecialchars("<p><img src='http://www.frankiz.net/data/annonces/".$id."' alt=''/></p>");
			}
?>		</description>
		<guid isPermaLink="false"><?php echo BASE_URL."?nonlu=$id#annonce_$id"; ?></guid>
		<author><?php echo $mail; ?> (<?php echo $nom; ?> <?php echo $prenom; ?>)</author>
	</item>
<?php } ?>
	</channel>
</rss>
