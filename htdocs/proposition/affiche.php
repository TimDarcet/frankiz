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
	
	$Log$
	Revision 1.7  2004/10/29 14:09:10  kikx
	Envoie des mail en HTML pour la validation des affiche

	Revision 1.6  2004/10/21 22:19:38  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.5  2004/10/19 14:58:43  schmurtz
	Creation d'un champ de formulaire specifique pour les fichiers (sans passer
	l'element champ, qui actuellement est un peu acrobatique).
	
	Revision 1.4  2004/10/10 21:40:49  kikx
	Pour permettre aux eleves de demander à mettre une activité visible de l'exterieur
	
	Revision 1.3  2004/10/07 22:52:20  kikx
	Correction de la page des activites (modules + proposition + administration)
		rajout de variables globales : DATA_DIR_LOCAL
						DATA_DIR_URL
	
	Comme ca si ca change, on est safe :)
	
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

$msg="" ;
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
	$original_size = getimagesize($_FILES['file']['tmp_name']);
	$filetype = $_FILES['file']['type'] ;
	
	$larg = $original_size[0];
	$haut = $original_size[1];
	if (($larg>=200)||($haut>=300)) {
		$erreur_upload =1 ;
	} else if (($filetype=="image/jpg")||($filetype=="image/jpeg")||($filetype=="image/pjpg")||($filetype=="image/gif")||($filetype=="image/png")) {
		$filename = "temp_$eleve_id" ;
		move_uploaded_file($_FILES['file']['tmp_name'], DATA_DIR_LOCAL.'affiches/'.$filename) ;
	} else {
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
	
		$DB_valid->query("INSERT INTO valid_affiches SET date=FROM_UNIXTIME({$_POST['date']}), eleve_id='".$_SESSION['user']->uid."', titre='".$_POST['titre']."',url='".$_POST['url']."', exterieur=".$temp_ext);
		
		// on modifie le nom du fichier qui a été téléchargé si celui ci existe
		// selon la norme de nommage ci-dessus
		//----------------------------------------------------------------------------------------------
		
		$index = mysql_insert_id() ;
		rename(DATA_DIR_LOCAL."affiches/temp_$eleve_id",DATA_DIR_LOCAL."affiches/a_valider_{$index}") ; 
	
		$contenu = "<strong>Bonjour,</strong><br><br>".
					"$prenom $nom a demandé la validation d'une activité : <br>".
					$_POST['titre']."<br><br>".
					"Pour valider ou non cette demande va sur la page suivante<br>".
					"<div align='center'><a href='http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_affiches.php'>".
					"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_affiches.php</a></div><br><br>" .
					"Très BR-ement<br>" .
					"L'automate :)<br>"  ;
					
		couriel(WEBMESTRE_ID,"[Frankiz] Validation d'une activité",$contenu,"Frankiz <br@frankiz>");
	} else {
		
		$msg .= "<warning> Il faut soumettre une image pour les activités </warning>" ;
		
	}

}
//=================
//===============
// Génération de la page
//===============
//=================

require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="propoz_affiche" titre="Frankiz : Propose une activité">
<h1>Proposition d'activité</h1>

 <?
 echo $msg ;
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
	<annonce date="">
		<commentaire>NB : Cette activité sera affichée le <?php echo date("d/m/y",$_POST['date']) ;?></commentaire>
		<a href="<?php echo $_POST['url'] ;?>">
		<?
		if ((!isset($_POST['valid']))&&(file_exists(DATA_DIR_LOCAL."affiches/temp_$eleve_id"))) {
		?>
		<image source="<?echo DATA_DIR_URL."affiches/temp_".$eleve_id ; ?>" texte="Affiche"/>
		<? 
		} else if ((isset($index))&&(file_exists(DATA_DIR_LOCAL."affiches/a_valider_{$index}"))){
		?>
		<image source="<? echo DATA_DIR_URL."affiches/a_valider_{$index}" ; ?>" texte="Affiche"/>
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

if ((isset($_POST['valid']))&&(isset($index))&&file_exists(DATA_DIR_LOCAL."affiches/a_valider_{$index}")) {
?>
	<commentaire>
		<p>Tu as demandé à un webmestre de valider ton activité</p>
		<p>Il faut compter 24h pour que ton activité soit prise en compte par notre système</p>		
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
		<fichier id="file" titre="Ton image" taille="100000"/>
		<textsimple valeur="Si tu veux que ton activité soit visible de l'exterieur, clique ici"/>
		<choix titre="Exterieur" id="exterieur" type="checkbox" valeur="<? if (isset($_REQUEST['ext'])) echo 'ext' ;?>">
			<option id="ext" titre=""/>
		</choix>
		<bouton id='suppr_img' titre="Supprimer l'image"/>

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
