<?php
/*
	Page qui permet aux admins de valider une annonce
	
	$Log$
	Revision 1.8  2004/10/08 06:51:43  pico
	Premier (re)commit:

	Pour la validation des annonces, il manquait un champ -> erreur sql.

	Il faudrait pas rajouter les options pour définir l'annonce en haut ou visible de l'extérieur ??

	Revision 1.7  2004/10/06 14:12:27  kikx
	Page de mail promo quasiment en place ...
	envoie en HTML ...
	Page pas tout a fait fonctionnel pour l'instant
	
	Revision 1.6  2004/09/18 16:04:52  kikx
	Beaucoup de modifications ...
	Amélioration des pages qui gèrent les annonces pour les rendre compatible avec la nouvelle norme de formatage xml -> balise web et balise image qui permette d'afficher une image et la signature d'une personne
	
	Revision 1.5  2004/09/17 22:49:29  kikx
	Rajout de ce qui faut pour pouvoir faire des telechargeement de fichiers via des formulaires (ie des champs 'file' des champ 'hidden') de plus maintenant le formulaire sont en enctype="multipart/form-data" car sinon il parait que ca marche pas !
	
	
*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");


$temp = explode("admin",$_SERVER['SCRIPT_FILENAME']) ;
$racine = $temp[0] ;
$uploaddir  =  $racine."/proposition/image_temp/" ;


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
		$DB_valid->query("SELECT eleve_id FROM valid_annonces WHERE annonce_id='{$temp[1]}'");
		list($eleve_id) = $DB_valid->next_row() ;
		// envoi du mail
		$contenu = "Merci de ta participation \n\n".
			"Très BR-ement\n" .
			"L'automate :)\n"  ;
		couriel($eleve_id,"[Frankiz] Ton annonce a été validé par le BR",$contenu);

		$DB_web->query("INSERT annonces SELECT 0 as annonce_id, NOW() as stamp,perime, titre,contenu,eleve_id,0 as en_haut,0 as exterieur FROM a_valider.valid_annonces WHERE annonce_id='{$temp[1]}'");
		
		
		// On déplace l'image si elle existe dans le répertoire prevu à cette effet
		$index = mysql_insert_id() ;
		if (file_exists($uploaddir."/{$temp[1]}_annonce")){
			rename($uploaddir."/{$temp[1]}_annonce",$racine.UPLOAD_WEB_DIR."annonce_$index") ;
		}
		$DB_valid->query("DELETE FROM valid_annonces WHERE annonce_id='{$temp[1]}'") ;
	?>
		<commentaire><p>Validation effectuée</p></commentaire>
	<?	

	}
	if ($temp[0]=='suppr') {
		$DB_valid->query("SELECT eleve_id FROM valid_annonces WHERE annonce_id='{$temp[1]}'");
		list($eleve_id) = $DB_valid->next_row() ;
		// envoi du mail
		$contenu = "Désolé \n\n".
			"Très BR-ement\n" .
			"L'automate :)\n"  ;
		couriel($eleve_id,"[Frankiz] Ton annonce n'a pas été validé par le BR",$contenu);

		$DB_valid->query("DELETE FROM valid_annonces WHERE annonce_id='{$temp[1]}'") ;
		//On supprime aussi l'image si elle existe ...
		
		$supp_image = "" ;
		if (file_exists($uploaddir."/{$temp[1]}_annonce")){
			unlink($uploaddir."/{$temp[1]}_annonce") ;
			$supp_image = " et de son image associée" ;
		}
		

	?>
		<warning><p>Suppression d'une annonce<? echo $supp_image?></p></warning>
	<?
	}
	
	
}


//===============================

	$DB_valid->query("SELECT v.annonce_id,v.perime, v.titre, v.contenu, e.nom, e.prenom, e.surnom, e.promo, e.mail, e.login FROM valid_annonces as v INNER JOIN trombino.eleves as e USING(eleve_id)");
	while(list($id,$date,$titre,$contenu,$nom, $prenom, $surnom, $promo,$mail,$login) = $DB_valid->next_row()) {
?>
		<annonce titre="<?php  echo $titre ?>" 
				categorie=""
				auteur="<?php echo empty($surnom) ? $prenom.' '.$nom : $surnom .' (X'.$promo.')'?>"
				date="<? echo $date?>">
				<? echo $contenu ;
				if (file_exists($uploaddir."/{$id}_annonce")){
				?>
					<image source="<? echo "proposition/image_temp/{$id}_annonce" ; ?>"/>
				<?
				}
				?>
				<eleve nom="<?=$nom?>" prenom="<?=$prenom?>" promo="<?=$promo?>" surnom="<?=$surnom?>" mail="<?=$mail?>"/>
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
