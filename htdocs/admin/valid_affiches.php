<?php
/*
	Page qui permet aux admins de valider une activité
	
	$Log$
	Revision 1.1  2004/09/20 22:19:27  kikx
	test


	
*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);



$temp = explode("admin",$_SERVER['SCRIPT_FILENAME']) ;
$racine = $temp[0] ;
$uploaddir  =  $racine."/proposition/image_temp/" ;


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="valid_activité" titre="Frankiz : Valide une activité">
<h1>Validation des activités</h1>

<?
// On traite les différents cas de figure d'enrigistrement et validation d'affiche :)

// Enregistrer ...

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;


	if (($temp[0]=='modif')||($temp[0]=='valid')) {
		$DB_valid->query("UPDATE valid_affiches SET date='{$_POST['date']}', titre='{$_POST['titre']}' WHERE affiche_id='{$temp[1]}'");	
	?>
		<commentaire><p>Modif effectuée</p></commentaire>
	<?	
	}
	
	if ($temp[0]=='valid') {
		$DB_valid->query("SELECT eleve_id FROM valid_affiches WHERE affiche_id='{$temp[1]}'");
		list($eleve_id) = $DB_valid->next_row() ;
		// envoi du mail
		$contenu = "Merci de ta participation \n\n".
			"Très BR-ement\n" .
			"L'automate :)\n"  ;
		couriel($eleve_id,"[Frankiz] Ton activité a été validé par le BR",$contenu);

		$DB_web->query("INSERT affiches SELECT 0 as affiche_id, NOW() as stamp,date, titre,url,eleve_id FROM a_valider.valid_affiches WHERE affiche_id='{$temp[1]}'");
		
		
		// On déplace l'image si elle existe dans le répertoire prevu à cette effet
		$index = mysql_insert_id() ;
		if (file_exists($uploaddir."/{$temp[1]}_affiche")){
			rename($uploaddir."/{$temp[1]}_affiche",$racine.UPLOAD_WEB_DIR."affiche_$index") ;
		}
		$DB_valid->query("DELETE FROM valid_affiches WHERE affiche_id='{$temp[1]}'") ;
	?>
		<commentaire><p>Validation effectuée</p></commentaire>
	<?	

	}
	if ($temp[0]=='suppr') {
		$DB_valid->query("SELECT eleve_id FROM valid_affiches WHERE affiche_id='{$temp[1]}'");
		list($eleve_id) = $DB_valid->next_row() ;
		// envoi du mail
		$contenu = "Désolé \n\n".
			"Très BR-ement\n" .
			"L'automate :)\n"  ;
		couriel($eleve_id,"[Frankiz] Ton affiche n'a pas été validé par le BR",$contenu);

		$DB_valid->query("DELETE FROM valid_affiches WHERE affiche_id='{$temp[1]}'") ;
		//On supprime aussi l'image si elle existe ...
		
		$supp_image = "" ;
		if (file_exists($uploaddir."/{$temp[1]}_affiche")){
			unlink($uploaddir."/{$temp[1]}_affiche") ;
			$supp_image = " et de son image associée" ;
		}
		

	?>
		<warning><p>Suppression d'une affiche<? echo $supp_image?></p></warning>
	<?
	}
	
	
}


//===============================

	$DB_valid->query("SELECT v.affiche_id,v.date, v.titre, v.url, e.nom, e.prenom, e.surnom, e.promo, e.mail, e.login FROM valid_affiches as v INNER JOIN trombino.eleves as e USING(eleve_id)");
	while(list($id,$date,$titre,$url,$nom, $prenom, $surnom, $promo,$mail,$login) = $DB_valid->next_row()) {
		echo "<module id=\"activites\" titre=\"Activités\">\n";
?>
		<annonce titre="<?php  echo $titre ?>" 
				categorie=""
				date="<? echo $date?>">
				<?
				if (file_exists($uploaddir."/{$id}_affiche")){
				?>
					<a href="<?php echo $url ?>">
					<image source="<? echo "proposition/image_temp/{$id}_affiche" ; ?>"/>
					</a>
				<?
				}
				?>
				<p><?php echo $titre ?></p>
				<eleve nom="<?=$nom?>" prenom="<?=$prenom?>" promo="<?=$promo?>" surnom="<?=$surnom?>" mail="<?=$mail?>"/>
		</annonce>
<?
		echo "</module>\n" ;
// Zone de saisie de l'affiche
?>

		<formulaire id="affiche_<? echo $id ?>" titre="L'activité" action="admin/valid_affiches.php">
			<champ id="titre" titre="Le titre" valeur="<?  echo $titre ;?>"/>
			<champ id="url" titre="URL du lien" valeur="<? echo $url ;?>"/>
		
			<textsimple valeur="La signature sera automatiquement généré"/>
			<champ id="date" titre="Date de péremption" valeur="<? echo $date ;?>"/>
			
			<bouton id='modif_<? echo $id ?>' titre="Modifier"/>
			<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Valider cette affiche ?')"/>
			<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer cette affiche ?!!!!!')"/>
		</formulaire>
<?
	}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
