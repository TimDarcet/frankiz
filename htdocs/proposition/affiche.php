<?php
/*
	Page qui permet aux utilisateurs de demander le rajout d'une activité
	
	$Log$
	Revision 1.2  2004/10/04 21:48:54  kikx
	Modification du champs fichier pour uploader des fichiers

	Revision 1.1  2004/09/20 22:31:28  kikx
	oubli
	

	
*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MINIMUM);

$DB_trombino->query("SELECT eleve_id,nom,prenom,surnom,mail,login,promo FROM eleves WHERE eleve_id='".$_SESSION['user']->uid."'");
list($eleve_id,$nom,$prenom,$surnom,$mail,$login,$promo) = $DB_trombino->next_row();

//---------------------------------------------------------------------------------
// On traite l'image qui vient d'etre uploader si elle existe
//---------------------------------------------------------------------------------

//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//
//--//
//--// Norme de nommage des images que nous uploadons
//--// dans le rep prévu a cette effet : 
//--// # affiche_{$id_eleves} qd l'annonce n'a pas été soumise à validation
//--// # {$id_affiche}_affiche qd l'annonce est soumise à validation
//--//
//--// Ceci permet de faire la différence entre les fichiers tempo et les fichiers a valider
//--//
//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//

$temp = explode("affiche",$_SERVER['SCRIPT_FILENAME']) ;
$temp = $temp[0] ;
$uploaddir  =  $temp."/image_temp/" ;

$erreur_upload = 0 ;
if ((isset($_FILES['file']))&&($_FILES['file']['size']!=0))  {
	$original_size = getimagesize($_FILES['file']['tmp_name']);
	$filetype = $_FILES['file']['type'] ;
	
	$larg = $original_size[0];
	$haut = $original_size[1];
	if (($larg>=200)||($haut>=300)) {
		$erreur_upload =1 ;
	} else if (($filetype=="image/jpg")||($filetype=="image/jpeg")||($filetype=="image/pjpg")||($filetype=="image/gif")||($filetype=="image/png")) {
		$filename = "affiche_$eleve_id" ;
		move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir . $filename) ;
	} else {
		$erreur_upload = 1 ;
	}

}
//================================================
// On Supprime l'image qui a été uploadé (si elle existe bien sur :))
//================================================

if (isset($_POST['suppr_img'])) {

	if (file_exists($uploaddir."/affiche_$eleve_id")) {
		unlink($uploaddir."/affiche_$eleve_id") ; 
	}
}
//================================================
// On valide l'annonce et en envoie un mail aux webmestres pour les prévenir 
//================================================

if (isset($_POST['valid'])) {

	$tempo = explode("proposition",$_SERVER['REQUEST_URI']) ;

	$DB_valid->query("INSERT INTO valid_affiches SET date=FROM_UNIXTIME({$_POST['date']}), eleve_id='".$_SESSION['user']->uid."', titre='".$_POST['titre']."',url='".$_POST['url']."'");
	
	// on modifie le nom du fichier qui a été téléchargé si celui ci existe
	// selon la norme de nommage ci-dessus
	//----------------------------------------------------------------------------------------------
	
	if (file_exists($uploaddir."/affiche_$eleve_id")) {
		$index = mysql_insert_id() ;
		rename($uploaddir."/affiche_$eleve_id",$uploaddir."/{$index}_affiche") ; 
	}
	$contenu = "$prenom $nom a demandé la validation d'une activité : \n".
				$_POST['titre']."\n\n".
				"Pour valider ou non cette demande va sur la page suivante : \n".
				"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_affiches.php\n\n" .
				"Très BR-ement\n" .
				"L'automate :)\n"  ;
				
	mail("Admin Frankiz <gruson@poly.polytechnique.fr>","[Frankiz] Validation d'une activité",$contenu);

}
//=================
//===============
// Génération de la page
//===============
//=================

require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="propoz_affiche" titre="Frankiz : Propose une activité">
<h1>Proposition d'annonce</h1>

 <?
if ($erreur_upload==1) {
?>
	<warning><p>Ton fichier n'a pas été téléchargé car il ne respecte pas une des conditions spécifiées ci dessous</p>
		<p>Dimension : <? echo $larg."x".$haut ;?></p>
		<p>Taille : <? echo $_FILES['file']['size'] ;?> octets</p>
		<p>Type : <? echo $filetype ;?></p>
	</warning>
<?

}
//=========================================
// PREVISUALISATION :
// On teste l'affichage de l'annonce pour voir à quoi ça ressemble
//=========================================

if (!isset($_POST['titre']))  $_POST['titre']="Titre" ;
if (!isset($_POST['url']))  $_POST['url']="http://" ;
if (!isset($_POST['date']))  $_POST['date']=time() ;

	echo "<module id=\"activites\" titre=\"Activités\">\n";

?>
	<annonce date="<?php echo date("d/m/y",$_POST['date']) ;?>">
		<a href="<?php echo $_POST['url'] ;?>">
		<?
		if ((!isset($_POST['valid']))&&(file_exists($uploaddir."/affiche_$eleve_id"))) {
		?>
		<image source="<?echo "proposition/image_temp/affiche_".$eleve_id ; ?>" texte="Affiche"/>
		<? 
		} else if ((isset($index))&&(file_exists($uploaddir."/{$index}_affiche"))){
		?>
		<image source="<? echo "proposition/image_temp/{$index}_affiche" ; ?>" texte="Affiche"/>
		<?
		}
		?>
		</a>
		<p><?php echo $_POST['titre']?></p>
		<eleve nom="<?=$nom?>" prenom="<?=$prenom?>" promo="<?=$promo?>" surnom="<?=$surnom?>" mail="<?=$mail?>"/>
	</annonce>
		
<?
	echo "</module>\n" ;

//=========================
// On met le commentaire qui va bien 
//=========================

if (isset($_POST['valid'])) {
?>
	<commentaire>
		<p>Tu as demandé à un webmestre de valider ton activité</p>
		<p>Il faut compter 24h pour que ton annonce soit prise en compte par notre système</p>		
		<p>&nbsp;</p>		
		<p>Nous te remercions d'avoir soumis une activité et nous essayerons d'y répondre le plus rapidement possible</p>		
	</commentaire>
<?	
} else {
//====================
// Zone de saisie de l'annonce
//====================
?>

	<formulaire id="propoz_activite" titre="Ton activité" action="proposition/affiche.php">
		<champ id="titre" titre="Le titre" valeur="<? if (isset($_POST['titre'])) echo $_POST['titre'] ;?>"/>
		<champ id="url" titre="URL du lien" valeur="<? if (isset($_POST['url'])) echo $_POST['url'] ;?>"/>
		<textsimple valeur="Ton image doit être un fichier gif, png ou jpg, ne doit pas dépasser 200x300 pixels et 100ko car sinon elle ne sera pas téléchargée"/>
		<champ id="file" titre="Ton image" valeur="" taille="100000"/>
		<bouton id='suppr_img' titre="Supprimer l'image"/>

		<textsimple valeur="Ta signature sera automatiquement généré"/>
		<choix titre="Date de l'activité" id="date" type="combo" valeur="<? if (isset($_REQUEST['date'])) echo $_REQUEST['date'] ;?>">
<?		for ($i=0 ; $i<MAX_PEREMPTION ; $i++) {
			$date_id = mktime(0, 0, 0, date("m") , date("d") + $i, date("Y")) ;
			$date_value = date("d/m/y" , $date_id);
?>
			<option titre="<? echo $date_value?>" id="<? echo $date_id?>" />
<?
		}
?>
		</choix>
		<bouton id='test' titre="Tester"/>
		<bouton id='valid' titre='Valider' onClick="return window.confirm('Voulez vous vraiment valider votre annonce ?')"/>
	</formulaire>
<?
}
?>

</page>
<?php

require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
