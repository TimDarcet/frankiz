<?php
/*
	Page de validation d'une modification d'un binet
	
	$Log$
	Revision 1.2  2004/10/19 18:16:24  kikx
	hum


*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="valid_binet" titre="Frankiz : Valide les modifications des binets">
<h1>Validation des modifications des binets</h1>

<?
// On traite les différents cas de figure d'enrigistrement et validation :)

// Enregistrer ...

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

	if ($temp[0]=='valid') {
	}
	if ($temp[0]=='suppr') {
	}
}


//===============================
	$DB_valid->query("SELECT binet_id,nom,description,http,categorie,exterieur FROM valid_binet LEFT JOIN trombino.binets_categorie USING(catego_id)");
	while(list($binet_id,$nom,$description,$http,$categorie,$exterieur) = $DB_valid->next_row()) {
?>
		<formulaire id="binet_web" titre="<? echo $nom?>" action="admin/valid_binets.php">
			<hidden id="id" titre="ID" valeur="<? echo $id?>"/>
			<textsimple  titre="Catégorie" valeur="<? echo $categorie?>"/>
			<lien  url="<? echo $http?>" titre="<? echo $http?>"/>
			<textsimple  titre="Description" valeur="<? echo stripslashes($description)?>"/>

			<choix titre="Exterieur" id="exterieur" type="checkbox" valeur="<? if ($exterieur==1) echo 'ext' ;?>" >
				<option id="ext" titre="" modifiable="non"/>
			</choix>
			<bouton id='valid' titre="Valider" onClick="return window.confirm('Souhaitez vous valider les changements')"/>
		</formulaire>
	<?
	}
	?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
