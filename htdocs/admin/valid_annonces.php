<?php
/*
	Page qui permet aux admins de valider une annonce
	
	$Log$
	Revision 1.4  2004/09/17 17:41:23  kikx
	Bon ct plein de bugs partout et ca ressemblait  a rien mais bon c'est certainement la faute de Schmurtz :))))))

	
*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);




// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="valid_annonce" titre="Frankiz : Valide une annonce">
<h1>Validation d'annonces</h1>

<?
// On traite les différents cas de figure d'enrigistrement et validation d'annonce :)

// Enregistrer ...

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;


	if (($temp[0]=='modif')||($temp[0]=='valid')) {
		$DB_valid->query("UPDATE valid_annonces SET perime='{$_POST['date']}', titre='{$_POST['titre']}', contenu='{$_POST['text']}' WHERE annonce_id='{$temp[1]}'");	
	?>
		<commentaire><p>Modif effectuée</p></commentaire>
	<?	
	}
	
	if ($temp[0]=='valid') {
		$DB_web->query("INSERT annonces SELECT 0 as annonce_id, NOW() as stamp,perime, titre,contenu,eleve_id,0 as en_haut FROM a_valider.valid_annonces");
		$DB_valid->query("DELETE FROM valid_annonces WHERE annonce_id='{$temp[1]}'") ;
	?>
		<commentaire><p>Validation effectuée</p></commentaire>
	<?	

	}
	if ($temp[0]=='suppr') {
		$DB_valid->query("SELECT eleve_id FROM valid_annonces WHERE annonce_id='{$temp[1]}'");
		list($eleve_id) = $DB_valid->next_row() ;
		
		$DB_valid->query("DELETE FROM valid_annonces WHERE annonce_id='{$temp[1]}'") ;
		
		$contenu = "Désolé \n\n".
			"Très BR-ement\n" .
			"L'automate :)\n"  ;
			
		
			
		couriel($eleve_id,"[Frankiz] Ton annonce n'a pas été validé par le BR",$contenu);

	?>
		<warning><p>Suppression d'une annonce</p></warning>
	<?
	}
	
	
}

//===============================

	$DB_valid->query("SELECT v.annonce_id,v.perime, v.titre, v.contenu, e.nom, e.prenom, e.surnom, e.promo FROM valid_annonces as v INNER JOIN trombino.eleves as e USING(eleve_id)");
	while(list($id,$date,$titre,$contenu,$nom, $prenom, $surnom, $promo) = $DB_valid->next_row()) {
?>
		<annonce titre="<?php  echo $titre ?>" 
				categorie=""
				auteur="<?php echo empty($surnom) ? $prenom.' '.$nom : $surnom .' (X'.$promo.')'?>"
				date="<? echo $date?>">
				<? echo $contenu ;?>
		</annonce>
<?
// Zone de saisie de l'annonce
?>

		<formulaire id="annonce_<? echo $id ?>" titre="L'annonce" action="admin/valid_annonces.php">
			<champ id="titre" titre="Le titre" valeur="<? echo $titre ;?>"/>
			<zonetext id="text" titre="Le texte" valeur="<? echo $contenu ;?>"/>
			<textsimple valeur="La signature sera automatiquement généré"/>
			<champ id="date" titre="Date de péremption" valeur="<? echo $date ;?>"/>
			
			<bouton id='modif_<? echo $id ?>' titre="Modifier"/>
			<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Valider cette annonce ?')"/>
			<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer cette annonce ?!!!!!')"/>
		</formulaire>
<?
	}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
