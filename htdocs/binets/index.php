<?php
/*
	Affichage de la liste des binets ayant un site web.

	$Log$
	Revision 1.8  2004/10/18 20:29:44  kikx
	Enorme modification pour la fusion des bases des binets (Merci Schmurtz)

	Revision 1.7  2004/09/16 13:56:32  kikx
	Modification de skins (détails)
	
	Revision 1.4  2004/09/15 23:20:39  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.3  2004/09/15 21:42:36  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
require_once "../include/global.inc.php";

// demande_authentification(AUTH_MINIMUM);

// Récupération d'une image
if(isset($_REQUEST['image'])){
	$DB_trombino->query("SELECT image,format FROM binets WHERE binet_id='{$_GET['id']}'");
	list($image,$format) = $DB_trombino->next_row() ;
	header("content-type: $format");
	echo $image;
	exit;
}


// Affichage de la liste des binets
require BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="binets" titre="Frankiz : Binets">
<?php
	$categorie_precedente = -1;
	$DB_trombino->query("SELECT binet_id,nom,description,http,b.catego_id,categorie FROM binets as b LEFT JOIN binets_categorie as c USING(catego_id) WHERE http IS NOT NULL");
	while(list($id,$nom,$description,$http,$cat_id,$categorie) = $DB_trombino->next_row()) {

?>
		<binet id="<?=$id?>" categorie="<?=$categorie?>" nom="<?=$nom?>">
			<image source="binets/?image=1&amp;id=<?=$id?>"/>
			<description><?=stripslashes($description)?></description>
			<url><?=$http?></url>
		</binet>
<?php
	}
?>
</page>
<?php require "../include/page_footer.inc.php" ?>
