<?php
/*
	Copyright (C) 2004 Binet Réseau
	http://www.polytechnique.fr/eleves/binets/br/
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/
/*
	Page qui permet aux utilisateurs de demander le rajout d'une activité
	
	$Id$
	
*/

require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";

// Vérification des droits
demande_authentification(AUTH_COOKIE);

$DB_trombino->query("SELECT eleve_id,nom,prenom,surnom,mail,login,promo FROM eleves WHERE eleve_id='".$_SESSION['uid']."'");
list($eleve_id,$nom,$prenom,$surnom,$mail,$login,$promo) = $DB_trombino->next_row();

$msg="" ;

if (!isset($_POST['titre']))  $_POST['titre']="Titre" ;
if (!isset($_POST['url']))  $_POST['url']="" ;
if (!isset($_POST['date']))  $_POST['date']=time() ;
if (!isset($_POST['time']))  $_POST['time']=time() ;
if (!isset($_POST['heure']))  $_POST['heure']="00:00";

$date_complete = $_POST['date'];

// Vérifie si le format d'heure est bon avant de le mettre dans l'affichage
if (ereg("(((^[0-9]{1})|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9])", $_POST['heure'], $regs)) {
	list ($H, $i)  = explode(':', $_POST['heure']);
	$date_complete = $date_complete + $H * 3600 + $i * 60;
}
else $_POST['heure']="00:00";
//---------------------------------------------------------------------------------
// On traite l'image qui vient d'etre uploader si elle existe
//---------------------------------------------------------------------------------

//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//
//--//
//--// Norme de nommage des images que nous uploadons
//--// dans le rep prévu a cette effet : 
//--// # temp_{$id_eleves} qd l'annonce n'a pas été soumise à validation
//--// # a_valider_{$id_affiche} qd l'annonce est soumise à validation
//--//
//--// Ceci permet de faire la différence entre les fichiers tempo et les fichiers a valider
//--//
//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//

$erreur_upload = 0 ;
if ((isset($_FILES['file']))&&($_FILES['file']['size']!=0))  {
	if($original_size = getimagesize($_FILES['file']['tmp_name'])) {
		$larg = $original_size[0];
		$haut = $original_size[1];
		if (($larg>=200)||($haut>=300)) {
			$erreur_upload =1 ;
		} else {
			$filename = "temp_$eleve_id" ;
			move_uploaded_file($_FILES['file']['tmp_name'], DATA_DIR_LOCAL.'affiches/'.$filename) ;
		} 
	}else {
		$erreur_upload = 1 ;
	}

}
//================================================
// On Supprime l'image qui a été uploadé (si elle existe bien sur :))
//================================================

if (isset($_POST['suppr_img'])) {

	if (file_exists(DATA_DIR_LOCAL."affiches/temp_$eleve_id")) {
		unlink(DATA_DIR_LOCAL."affiches/temp_$eleve_id") ; 
	}
}
//================================================
// On valide l'annonce et en envoie un mail aux webmestres pour les prévenir 
//================================================

if (isset($_POST['valid'])) {

	if (file_exists(DATA_DIR_LOCAL."affiches/temp_$eleve_id")) {

		$tempo = explode("proposition",$_SERVER['REQUEST_URI']) ;
		
		if (isset($_REQUEST['ext']))
			$temp_ext = '1'  ;
		else 
			$temp_ext = '0' ;

		$DB_valid->query("INSERT INTO valid_affiches SET date=FROM_UNIXTIME({$date_complete}), eleve_id='".$_SESSION['uid']."', titre='".$_POST['titre']."',url='".$_POST['url']."', description='".$_POST['text']."',exterieur=".$temp_ext);
		
		// on modifie le nom du fichier qui a été téléchargé si celui ci existe
		// selon la norme de nommage ci-dessus
		//----------------------------------------------------------------------------------------------
		
		$index = mysql_insert_id($DB_valid->link) ;
		rename(DATA_DIR_LOCAL."affiches/temp_$eleve_id",DATA_DIR_LOCAL."affiches/a_valider_{$index}") ; 
	
		$contenu = "<strong>Bonjour,</strong><br><br>".
					"$prenom $nom a demandé la validation d'une activité : <br>".
					$_POST['titre']."<br><br>".
					"Pour valider ou non cette demande va sur la page suivante<br>".
					"<div align='center'><a href='http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_affiches.php'>".
					"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_affiches.php</a></div><br><br>" .
					"Cordialement,<br>" .
					"Le Webmestre de Frankiz<br>"  ;
					
		couriel(WEBMESTRE_ID,"[Frankiz] Validation d'une activité",$contenu,$eleve_id);
	} else {
		
		$msg .= "<warning> Il faut soumettre une image pour les activités </warning>" ;
		
	}

}
//=================
//===============
// Génération de la page
//===============
//=================

require_once BASE_FRANKIZ."include/page_header.inc.php";

?>
<page id="propoz_affiche" titre="Frankiz : Propose une activité">
<h1>Proposition d'activité</h1>

 <?php
 echo $msg ;
if ($erreur_upload==1)
	echo "<warning>Ton image n'est pas au bon format, ou est trop grande.</warning>\n";


//=========================================
// PREVISUALISATION :
// On teste l'affichage de l'annonce pour voir à quoi ça ressemble
//=========================================

	echo "<module id=\"activites\" titre=\"Activités\">\n";

?>
	<annonce date="<?php echo date('Y-m-d H:i:s',$date_complete)  ?>">
		<note>NB : Cette activité sera affichée le <?php echo date("d/m/y",$_POST['date']) ;?></note>
		<lien url="<?php echo ($_POST['url']!="")?$_POST['url']:"affiches.php" ;?>">
		<?php
		if ((!isset($_POST['valid']))&&(file_exists(DATA_DIR_LOCAL."affiches/temp_$eleve_id"))) {
		?>
		<image source="<?php echo DATA_DIR_URL."affiches/temp_".$eleve_id ; ?>" texte="Affiche" legende="<?php echo  $_POST['titre']?>"/>
		<?php 
		} else if ((isset($index))&&(file_exists(DATA_DIR_LOCAL."affiches/a_valider_{$index}"))){
		?>
		<image source="<?php echo DATA_DIR_URL."affiches/a_valider_{$index}" ; ?>" texte="Affiche" legende="<?php echo $_POST['titre']?>"/>
		<?php
		}
		?>
		</lien>
		<?php  if (isset($_POST['text'])) echo wikiVersXML($_POST['text']); ?>
		<eleve nom="<?php echo $nom; ?>" prenom="<?php echo $prenom; ?>" promo="<?php echo $promo; ?>" surnom="<?php echo $surnom; ?>" mail="<?php echo $mail; ?>"/>
		
	</annonce>
		
<?php
	echo "</module>\n" ;

//=========================
// On met le commentaire qui va bien 
//=========================

if ((isset($_POST['valid']))&&(isset($index))&&file_exists(DATA_DIR_LOCAL."affiches/a_valider_{$index}")) {
?>
	<commentaire>Ta nouvelle annonce a été prise en compte et sera validée dans les meilleurs délais.</commentaire>
<?php	
} else {
//====================
// Zone de saisie de l'annonce
//====================
?>

	<formulaire id="propoz_activite" titre="Ton activité" action="proposition/affiche.php">
		<champ id="titre" titre="Le titre" valeur="<?php if (isset($_POST['titre'])) echo $_POST['titre'] ;?>"/>
		<champ id="url" titre="Lien vers une page décrivant l'activité" valeur="<?php if (isset($_POST['url'])) echo $_POST['url'] ;?>"/>
		<zonetext id="text" titre="Description plus détaillée"><?php if (isset($_POST['text'])) echo $_POST['text'];?></zonetext>
		<note>L'image doit être un fichier gif, png ou jpeg ne dépassant pas 200x300 pixels et 250Ko.</note>
		<fichier id="file" titre="Ton image" taille="100000"/>

		<note>Si tu souhaites que ton activité soit visible de l'extérieur, clique ici.</note>
		<choix titre="Extérieur" id="exterieur" type="checkbox" valeur="<?php if (isset($_REQUEST['ext'])) echo 'ext' ;?>">
			<option id="ext" titre=""/>
		</choix>
		
		<note>Ton activité ne sera affichée qu'un seul jour. Choisis donc la date de ton événement.</note>

		<choix titre="Date de l'activité" id="date" type="combo" valeur="<?php if (isset($_REQUEST['date'])) echo $_REQUEST['date'] ;?>">
<?php		for ($i=0 ; $i<MAX_PEREMPTION ; $i++) {
			$date_id = mktime(0, 0, 0, date("m") , date("d") + $i, date("Y")) ;
			$date_value = date("d/m/y" , $date_id);
?>
			<option titre="<?php echo $date_value?>" id="<?php echo $date_id?>" />
<?php
		}
?>
		</choix>
		<champ id="heure" titre="Heure de l'activité" valeur="<?php if (isset($_POST['heure'])) echo $_POST['heure'] ;?>"/>

		<bouton id='suppr_img' titre="Supprimer l'image"/>
		<bouton id='test' titre="Tester"/>
		<bouton id='valid' titre='Valider' onClick="return window.confirm('Voulez vous vraiment valider votre annonce ?')"/>
	</formulaire>
<?php
}
?>

</page>
<?php

require_once BASE_FRANKIZ."include/page_footer.inc.php";
?>
