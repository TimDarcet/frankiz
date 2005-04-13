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
		<title>Frankiz : Annonces</title>
		<link><? echo BASE_URL ?></link>
		<description>Frankiz : Le serveur des élèves de l'école polytechnique.</description>
		<ttl>10</ttl>
		<image>
			<url><? echo BASE_URL ?>/skins/pico/images/home_logo.png</url>
			<title>Frankiz : Annonces</title>
			<link><? echo BASE_URL ?></link>
		</image>
			
<?
$DB_web->query("SELECT annonce_id,stamp,perime,titre,contenu,en_haut,exterieur,nom,prenom,surnom,promo,"
					 ."IFNULL(mail,CONCAT(login,'@poly.polytechnique.fr')) as mail "
					 ."FROM annonces LEFT JOIN trombino.eleves USING(eleve_id) "
					 ."WHERE (perime>=".date("Ymd000000",time()).") ORDER BY perime DESC");
while(list($id,$stamp,$perime,$titre,$contenu,$en_haut,$exterieur,$nom,$prenom,$surnom,$promo,$mail)=$DB_web->next_row()) {
	if(!$exterieur && !est_authentifie(AUTH_INTERNE)) continue;
?>
	<item>
		<title><?php echo $titre ?></title>
		<link><? echo BASE_URL."?nonlu=$id#annonce_$id" ?></link>
		<category><?php echo get_categorie($en_haut, $stamp, $perime) ?></category>
		<pubDate><?php echo $stamp ?></pubDate>
		<description>
<?php
		echo wikiVersXML($contenu);

			if (file_exists(DATA_DIR_LOCAL."annonces/$id")) {
			?>
				<img src="<?echo DATA_DIR_URL."annonces/$id" ; ?>" alt=" "/>
			<? 
			}
?>		</description>
		<author><?=$mail?> (<?=$nom?> <?=$prenom?>)</author>
	</item>
<?php } ?>
	</channel>
</rss>