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
	Page qui permet aux utilisateurs de demander le rajout d'une annonce
	
	$Log$
	Revision 1.33  2004/12/14 07:40:33  pico
	/me boulet

	Revision 1.32  2004/12/14 07:26:33  pico
	Correction du module random
	La politique est de na pas rajouter des balises si elles ne sont pas utiles ailleurs, là, je pense que l'on peut s'en passer et avoir tout de même l'effet recherché.
	
	Revision 1.31  2004/12/14 00:27:40  kikx
	Pour que le FROM des mails de validation soit au nom du mec qui demande la validation... (qu'est ce que je ferai pas pour les TOS :))
	
	Revision 1.30  2004/11/27 20:16:55  pico
	Eviter le formatage dans les balises <note> <commentaire> et <warning> lorsque ce n'est pas necessaire
	
	Revision 1.29  2004/11/25 11:52:10  pico
	Correction des liens mysql_id
	
	Revision 1.28  2004/11/24 22:56:18  schmurtz
	Inclusion de wiki.inc.php par les fichiers concerne uniquement et non de facon
	globale pour tous les fichiers.
	
	Revision 1.27  2004/11/24 13:32:23  kikx
	Passage des annonces en wiki !
	
	Revision 1.26  2004/11/24 12:51:02  kikx
	Pour commencer la compatibilité wiki
	
	Revision 1.25  2004/11/23 23:30:20  schmurtz
	Modification de la balise textarea pour corriger un bug
	(return fantomes)
	
	Revision 1.24  2004/11/22 23:38:42  kikx
	Ajout de <note></note> un peu partout pour plus de compréhension !
	
	Revision 1.23  2004/11/07 22:43:10  pico
	correction faute d'orthograffe
	
	Revision 1.22  2004/10/29 14:40:48  kikx
	Erreur mineur dans le lien
	
	Revision 1.21  2004/10/29 14:38:37  kikx
	Mise en format HTML des mails pour les validation de la qdj, des mails promos, et des annonces
	
	Revision 1.20  2004/10/21 22:19:38  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.19  2004/10/20 23:21:39  schmurtz
	Creation d'un element <html> qui permet d'afficher du html brute sans verification
	C'est ce qui est maintenant utilise dans les annonces/cadres
	
	Revision 1.18  2004/10/19 14:58:43  schmurtz
	Creation d'un champ de formulaire specifique pour les fichiers (sans passer
	l'element champ, qui actuellement est un peu acrobatique).
	
	Revision 1.17  2004/10/13 19:37:13  kikx
	oubli
	
	Revision 1.16  2004/10/11 11:01:38  kikx
	Correction des pages de proposition et de validation des annonces pour permettre
	- de stocker les image au bon endroit
	- de mettre les annonces su l'esterieur
	
	Revision 1.15  2004/10/04 21:48:54  kikx
	Modification du champs fichier pour uploader des fichiers
	
	Revision 1.14  2004/09/20 22:19:28  kikx
	test
	
	Revision 1.13  2004/09/20 07:14:41  kikx
	Permet de supprimer l'image qd on va valider l'annonce !!!
	C'est chaint si on peut pas la suppimer
	
	Revision 1.12  2004/09/18 16:22:26  kikx
	micro bug fix
	
	Revision 1.11  2004/09/18 16:04:52  kikx
	Beaucoup de modifications ...
	Amélioration des pages qui gèrent les annonces pour les rendre compatible avec la nouvelle norme de formatage xml -> balise web et balise image qui permette d'afficher une image et la signature d'une personne
	
	Revision 1.8  2004/09/17 16:14:43  kikx
	Pffffff ...
	Je sais plus trop ce que j'ai fait donc allez voir le code parce que la ca me fait chié de refléchir
	
	Revision 1.7  2004/09/17 14:19:58  kikx
	Page de demande d'annonce terminé
	Ajout d'une page de validations d'annonces
	
*/
	
require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";

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
//--// # annonce_{$id_eleves} qd l'annonce n'a pas été soumise à validation
//--// # {$id_annonce_a_valider}_annonce qd l'annonce est soumise à validation
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
	if (($larg>=400)||($haut>=300)) {
		$erreur_upload =1 ;
	} else if (($filetype=="image/jpg")||($filetype=="image/jpeg")||($filetype=="image/pjpg")||($filetype=="image/gif")||($filetype=="image/png")) {
		$filename = "temp_$eleve_id" ;
		move_uploaded_file($_FILES['file']['tmp_name'], DATA_DIR_LOCAL ."annonces/". $filename) ;
	} else {
		$erreur_upload = 1 ;
	}

}
//================================================
// On Supprime l'image qui a été uploadé (si elle existe bien sur :))
//================================================

if (isset($_POST['suppr_img'])) {

	if (file_exists(DATA_DIR_LOCAL."annonces/temp_$eleve_id")) {
		unlink(DATA_DIR_LOCAL."annonces/temp_$eleve_id") ; 
	}
}
//================================================
// On valide l'annonce et en envoie un mail aux webmestres pour les prévenir 
//================================================

if (isset($_POST['valid'])) {

	$tempo = explode("proposition",$_SERVER['REQUEST_URI']) ;

	if (isset($_REQUEST['ext']))
		$temp_ext = '1'  ;
	else 
		$temp_ext = '0' ;

	$DB_valid->query("INSERT INTO valid_annonces SET perime=FROM_UNIXTIME({$_POST['date']}), eleve_id='".$_SESSION['user']->uid."', titre='".$_POST['titre']."',contenu='".$_POST['text']."', exterieur=$temp_ext");
	
	// on modifie le nom du fichier qui a été téléchargé si celui ci existe
	// selon la norme de nommage ci-dessus
	//----------------------------------------------------------------------------------------------
	
	if (file_exists(DATA_DIR_LOCAL."annonces/temp_$eleve_id")) {
		$index = mysql_insert_id($DB_valid->link) ;
		rename(DATA_DIR_LOCAL."annonces/temp_$eleve_id",DATA_DIR_LOCAL."annonces/a_valider_{$index}") ; 
	}
	$contenu = "<strong>Bonjour,</strong><br><br>".
			"$prenom $nom a demandé la validation d'une annonce : <br>".
			$_POST['titre']."<br><br>".
			"Pour valider ou non cette demande va sur la page suivante<br>".
			"<div align='center'><a href='http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_annonces.php'>".
			"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_annonces.php</a></div><br><br>" .
			"Très BR-ement<br>" .
			"L'automate :)<br>"  ;
				
	couriel(WEBMESTRE_ID,"[Frankiz] Validation d'une annonce",$contenu,$eleve_id);
}
//=================
//===============
// Génération de la page
//===============
//=================

require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="propoz_annonce" titre="Frankiz : Propose une annonce">
<h1>Proposition d'annonce</h1>

 <?
if ($erreur_upload==1) {
?>
	<warning>Ton fichier n'a pas été téléchargé car il ne respecte pas une des conditions spécifiées ci dessous<br/>
		Dimension : <? echo $larg."x".$haut ;?><br/>
		Taille : <? echo $_FILES['file']['size'] ;?> octets<br/>
		Type : <? echo $filetype ;?><br/>
	</warning>
<?

}
//=========================================
// PREVISUALISATION :
// On teste l'affichage de l'annonce pour voir à quoi ça ressemble
//=========================================

if (!isset($_POST['text'])) $_POST['text']="" ;
if (!isset($_POST['titre']))  $_POST['titre']="Titre" ;

?>
	<annonce titre="<?php echo $_POST['titre'] ; ?>" 
			categorie=""
			date="<? echo date("d/m/y") ?>">
			<? 
			echo wikiVersXML($_POST['text']) ;
			if ((!isset($_POST['valid']))&&(file_exists(DATA_DIR_LOCAL."annonces/temp_$eleve_id"))) {
			?>
				<image source="<?echo DATA_DIR_URL."annonces/temp_".$eleve_id ; ?>" texte=""/>
			<? 
			} else if ((isset($index))&&(file_exists(DATA_DIR_LOCAL."annonces/a_valider_{$index}"))){
			?>
				<image source="<?echo DATA_DIR_URL."annonces/a_valider_".$index ; ?>" texte=""/>
			<?
			}
			?>
			<eleve nom="<?=$nom?>" prenom="<?=$prenom?>" promo="<?=$promo?>" surnom="<?=$surnom?>" mail="<?=$mail?>"/>
	</annonce>
<?

//=========================
// On met le commentaire qui va bien 
//=========================

if (isset($_POST['valid'])) {
?>
	<commentaire>
		Tu as demandé à un webmestre de valider ton annonce<br/>
		Il faut compter 24h pour que ton annonce soit prise en compte par notre système<br/>
		<br/>
		Nous te remercions d'avoir soumis une annonce et nous essayerons d'y répondre le plus rapidement possible<br/>
	</commentaire>
<?	
} else {
//====================
// Zone de saisie de l'annonce
//====================
?>
<note>La syntaxe est la syntaxe wiki... si tu ne sais pas taper correctement du texte wiki, va sur notre page <lien titre="d'aide WIKI" url="helpwiki.php"/></note>

	<formulaire id="propoz_annonce" titre="Ton annonce" action="proposition/annonce.php">
		<champ id="titre" titre="Le titre" valeur="<? if (isset($_POST['titre'])) echo $_POST['titre'] ;?>"/>
		<zonetext id="text" titre="Le texte"><? if (isset($_POST['text'])) echo $_POST['text'];?></zonetext>
		<note>Ton image doit être un fichier gif, png ou jpg, ne doit pas dépasser 400x300 pixels et 250ko car sinon elle ne sera pas téléchargée</note>
		<fichier id="file" titre="Ton image" taille="250000"/>
		<bouton id='suppr_img' titre="Supprimer l'image"/>

		<note>Ton annonce disparaitra le lendemain de la date de péremption</note>
		<choix titre="Date de péremption" id="date" type="combo" valeur="<? if (isset($_REQUEST['date'])) echo $_REQUEST['date'] ;?>">
<?		for ($i=0 ; $i<MAX_PEREMPTION ; $i++) {
			$date_id = mktime(0, 0, 0, date("m") , date("d") + $i, date("Y")) ;
			$date_value = date("d/m/y" , $date_id);
?>
			<option titre="<? echo $date_value?>" id="<? echo $date_id?>" />
<?
		}
?>
		</choix>
		
		<note>Si tu veux que ton annonce soit visible de l'exterieur, clique ici</note>
		<choix titre="Exterieur" id="exterieur" type="checkbox" valeur="<? if (isset($_REQUEST['ext'])) echo 'ext' ;?>">
			<option id="ext" titre=""/>
		</choix>
		<note>Ta signature sera automatiquement générée</note>

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
