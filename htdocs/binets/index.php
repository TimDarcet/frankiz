<?php
/*
	Affichage de la liste des binets ayant un site web.

	$Log$
	Revision 1.6  2004/09/16 13:44:40  schmurtz
	AmeÌlioration de l'affichage de la page des binets (avec le classement par categorie mais pas le trie).
	Passage de la sortie de la skin basic en xhtml avec doctype.

	Revision 1.5  2004/09/16 11:09:38  kikx
	C'est les vacances maintenant ...
	Bon bref .. c'est dur aussi
	Bon j'ai un peu arrangé la page des binets
	
	Revision 1.4  2004/09/15 23:20:39  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.3  2004/09/15 21:42:36  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
require_once "../include/global.inc.php";

// demande_authentification(AUTH_MINIMUM);

// Récupération d'une image
if(isset($_REQUEST['image'])){
	$DB_web->query("SELECT image,format FROM binets WHERE id='{$_GET['id']}'");
	list($image,$format) = $DB_web->next_row() ;
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
	$DB_web->query("SELECT b.id,date,nom,descript,http,c.id,c.catego FROM binets as b INNER JOIN categ_binet as c ON(b.catego=c.id) ORDER BY c.id ASC, b.nom ASC");
	while(list($id,$date,$nom,$description,$http,$cat_id,$categorie) = $DB_web->next_row()) {
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
