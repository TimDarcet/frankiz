<?php
require_once "include/global.inc.php";
demande_authentification(AUTH_MINIMUM);

function get_categorie($en_haut,$stamp,$perime) {
	if($en_haut==1) return "important";
	elseif($stamp > date("YmdHis",time()-12*3600)) return "nouveau";
	elseif($perime < date("YmdHis",time()+24*3600)) return "vieux";
	else return "reste";
}

// génération de la page
require "include/page_header.inc.php";
echo "<page id='accueil' titre='Frankiz : accueil'>\n";

connecter_mysql_frankiz();

$result = mysql_query("SELECT annonce_id,stamp,perime,titre,contenu,en_haut,nom,prenom "
					 ."FROM annonces LEFT JOIN eleves USING(eleve_id) "
					 ."WHERE (perime>=".date("Ymd000000",time())." AND valide=1) ORDER BY perime DESC");
while(list($id,$stamp,$perime,$titre,$contenu,$en_haut,$nom,$prenom)=mysql_fetch_row($result)) { ?>
	<annonce titre="<?php echo $titre ?>" 
			categorie="<?php echo get_categorie($en_haut, $stamp, $perime) ?>"
			auteur="<?php echo "$prenom $nom" ?>"
			date="<?php echo substr($stamp,6,2)."/".substr($stamp,4,2)."/".substr($stamp,0,4) ?>">
		<?php echo afficher_identifiant($contenu) ?>
	</annonce>
<?php }
mysql_free_result($result);

deconnecter_mysql_frankiz();

echo "</page>\n";
require_once "include/page_footer.inc.php";
?>