<?php
/*
	Gestion de la liste des binets.

	$Log$
	Revision 1.2  2004/10/16 00:30:56  kikx
	Permet de modifier des binets déjà existants

	
*/

// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");

$message = "";

// =====================================
// Modification d'un binet
// =====================================
	
	// On modifie un binet
	//==========================
	
	if (isset($_POST['modif'])) {
			$DB_web->query("UPDATE binets SET nom='{$_POST['nom']}', http='{$_POST['http']}', descript='{$_POST['descript']}' WHERE id={$_POST['id']}");
			$message .= "<commentaire>Modification de {$_POST['nom']} effectué</commentaire>" ;
	}


// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="binets_web" titre="Frankiz : Binets Web">
<h1>Modification des pages Web des binets</h1>

<?php
	echo $message ;

	$categorie_precedente = -1;
	$DB_web->query("SELECT b.id,date,nom,descript,http,c.id,c.catego,b.exterieur FROM binets as b INNER JOIN categ_binet as c ON(b.catego=c.id) ORDER BY b.nom ASC");
	while(list($id,$date,$nom,$descript,$http,$cat_id,$catego,$exterieur) = $DB_web->next_row()) {
?>
		<formulaire id="binet_web_<? echo $id?>" titre="<? echo $nom?>" action="admin/binets_web.php">
			<hidden id="id" titre="ID" valeur="<? echo $id?>"/>
			<champ id="nom" titre="Nom" valeur="<? echo $nom?>"/>
			<champ id="catego" titre="Catégorie" valeur="<? echo $catego?>"/>
			<champ id="http" titre="Http" valeur="<? echo $http?>"/>
			<zonetext id="descript" titre="Description" valeur="<? echo stripslashes($descript)?>"/>
			<image source="binets/?image=1&amp;id=<?=$id?>"/>
			<bouton id='modif' titre="Modifier"/>
		</formulaire>
<?php
	}
?>
</page>
<?php


require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
