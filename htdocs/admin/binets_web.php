<?php
/*
	Gestion de la liste des binets.

	$Log$
	Revision 1.1  2004/10/15 22:03:07  kikx
	Mise en place d'une page pour la gestion des sites des binets

	Revision 1.9  2004/09/15 23:20:18  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.8  2004/09/15 21:42:27  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");

$message = "";

// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
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
<?php


require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
