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
	Gestion de la liste des binets.

	$Log$
	Revision 1.33  2005/01/27 17:24:12  pico
	erreur de parenthèses

	Revision 1.32  2005/01/27 15:23:17  pico
	La boucle locale est considérée comme interne
	Tests de photos normalement plus cools.
	Après le reste.... je sais plus
	
	Revision 1.31  2005/01/26 16:41:02  pico
	Bug
	
	Revision 1.30  2005/01/22 17:58:38  pico
	Modif des images
	
	Revision 1.29  2005/01/18 21:38:39  pico
	Correction de bug #38
	
	Revision 1.28  2005/01/18 13:45:31  pico
	Plus de droits pour les web
	
	Revision 1.27  2005/01/11 14:36:42  pico
	Binets triés ext/int + url auto si binet sur le serveur
	
	Revision 1.26  2004/12/17 17:25:08  schmurtz
	Ajout d'une belle page d'erreur.
	
	Revision 1.25  2004/12/16 12:52:57  pico
	Passage des paramètres lors d'un login
	
	Revision 1.24  2004/12/15 22:25:47  kikx
	Verification que le prez et le webmestre sont d'une promo sur le campus
	
	Revision 1.23  2004/12/01 20:29:47  kikx
	Oubli pour les webmestres

	
	Revision 1.22  2004/11/27 15:02:17  pico
	Droit xshare et faq + redirection vers /gestion et non /admin en cas de pbs de droits
	
	Revision 1.21  2004/11/25 02:10:48  kikx
	la non plus
	
	Revision 1.19  2004/11/25 02:03:29  kikx
	Bug d'administration des binets
	
	Revision 1.18  2004/11/25 00:35:19  schmurtz
	une image de plus dans htdocs/image
	
	Revision 1.17  2004/11/23 23:30:20  schmurtz
	Modification de la balise textarea pour corriger un bug
	(return fantomes)
	
	Revision 1.16  2004/11/11 19:22:52  kikx
	Permet de gerer l'affichage externe interne des binets
	Permet de pas avoir de binet sans catégorie valide
	
	Revision 1.15  2004/11/11 17:39:54  kikx
	Centralisation des pages des binets
	
	Revision 1.14  2004/11/11 16:08:51  kikx
	Centralisation de la gestion des binets
	
	Revision 1.8  2004/11/08 15:46:46  kikx
	Correction pour les telechargement des fichiers (visiblement ca depend de la version de php)
	
	Revision 1.7  2004/11/08 08:47:57  kikx
	Pour la gestion online des sites de binets
	
	Revision 1.6  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.5  2004/10/19 14:58:42  schmurtz
	Creation d'un champ de formulaire specifique pour les fichiers (sans passer
	l'element champ, qui actuellement est un peu acrobatique).
	
	Revision 1.4  2004/10/18 20:29:44  kikx
	Enorme modification pour la fusion des bases des binets (Merci Schmurtz)
	
	Revision 1.3  2004/10/17 14:43:03  kikx
	Finalisation de la page de modification des binets WEB
	
	Revision 1.2  2004/10/16 00:30:56  kikx
	Permet de modifier des binets déjà existants
	
	
*/

// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')&&!verifie_permission('web'))
	acces_interdit();

$message = "";
$texte_image ="" ;
// =====================================
// Modification d'un binet
// =====================================
$binet_id = $_REQUEST['id'] ;
$DB_trombino->query("SELECT nom FROM binets WHERE binet_id=".$_REQUEST['id']);
list($nomdubinet) = $DB_trombino->next_row() ;

	
	// On modifie un binet
	//==========================
	
if (isset($_POST['modif'])) {

	$DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus'");
	list($promo_temp) = $DB_web->next_row() ;
	$promo_temp2=$promo_temp-1 ;
	
	// On verifie que les droits des webmestre et des prez n'ont pas changé
	//==========================================================================
	// Les données du prez du Binet actuel

	$DB_web->query("SELECT login FROM trombino.eleves LEFT JOIN compte_frankiz USING(eleve_id) WHERE perms LIKE '%prez_".$_GET['id'].",%' AND (promo='$promo_temp' OR promo='$promo_temp2') ORDER BY promo DESC");

	list($prez_login) = $DB_web->next_row() ;
	// On change de prez
	if ($_POST['prez']!= $prez_login) {
		// on supprime les droit de l'ancien web (si il existe bien sur)
		if ($prez_login!="") {
			$DB_web->query("SELECT perms,e.eleve_id FROM compte_frankiz LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE login='$prez_login' AND (promo='$promo_temp' OR promo='$promo_temp2')" );
			while(list($perms,$eleve_id) = $DB_web->next_row()) {
				$perms = str_replace("prez_".$binet_id.",","",$perms) ;
				$DB_web->query("UPDATE compte_frankiz SET perms='$perms' WHERE eleve_id='$eleve_id'");
				$message .= "<commentaire>$prez_login n'a plus ses droit de prez du binet $nomdubinet</commentaire>\n";
			}
		}
		// on donne les droits au nouveau prez
		if ($_POST['prez']!="") {
			$DB_web->query("SELECT perms,e.eleve_id FROM compte_frankiz LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE login='".$_POST['prez']."' AND (promo='$promo_temp' OR promo='$promo_temp2')" );
			if ($DB_web->num_rows()==0) 
				$message .= "<warning>Ce login n'existe pas ou ne s'est jamais connecté a Frankiz</warning>" ;
			while(list($perms,$eleve_id) = $DB_web->next_row()) {
				$perms = $perms."prez_".$binet_id."," ;
				$DB_web->query("UPDATE compte_frankiz SET perms='$perms' WHERE eleve_id='$eleve_id'");
				$message .= "<commentaire>".$_POST['prez']." a reçu les droits de prez du binet $nomdubinet</commentaire>\n";
			}
		}
	}

	
	
	
	
	// Les données du webmestre du Binet actuel


	$DB_web->query("SELECT login FROM trombino.eleves LEFT JOIN compte_frankiz USING(eleve_id) WHERE perms LIKE '%webmestre_".$_GET['id'].",%' AND (promo='$promo_temp' OR promo='$promo_temp2') ORDER BY promo DESC");

	list($web_login) = $DB_web->next_row() ;
	
	if ($_POST['webmestre']!= $web_login) {
		// on supprime les droit de l'ancien web (s'il existe bien sur)
		if ($web_login!="") {
			$DB_web->query("SELECT perms,e.eleve_id FROM compte_frankiz LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE login='$web_login' AND (promo='$promo_temp' OR promo='$promo_temp2')" );
			while(list($perms,$eleve_id) = $DB_web->next_row()) {
				$perms = str_replace("webmestre_".$binet_id.",","",$perms) ;
				$DB_web->query("UPDATE compte_frankiz SET perms='$perms' WHERE eleve_id='$eleve_id'");
				$message .= "<commentaire>$web_login n'a plus ses droit de webmestre du binet $nomdubinet</commentaire>\n";
			}
		}
		// on donne les droits au nouveau webmestre
		if ($_POST['webmestre']!="") {
			$DB_web->query("SELECT perms,e.eleve_id FROM compte_frankiz LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE login='".$_POST['webmestre']."' AND (promo='$promo_temp' OR promo='$promo_temp2')" );
			if ($DB_web->num_rows()==0) 
				$message .= "<warning>Ce login n'existe pas ou ne s'est jamais connecté a Frankiz</warning>" ;
			while(list($perms,$eleve_id) = $DB_web->next_row()) {
				$perms = $perms."webmestre_".$binet_id."," ;
				$DB_web->query("UPDATE compte_frankiz SET perms='$perms' WHERE eleve_id='$eleve_id'");
				$message .= "<commentaire>".$_POST['webmestre']." a reçu les droits de webmestre du binet $nomdubinet</commentaire>\n";
			}
		}
	}
		




	if ((isset($_POST['ext']))&&($_POST['ext']=='on')) {
		$ext = 1;
		if(!file_exists(BASE_BINETS_EXT.$_POST['folder'])) symlink (BASE_BINETS.$_POST['folder'],BASE_BINETS_EXT.$_POST['folder']);
	}else{
		$ext = 0;
		if($_POST['folder']!='' && file_exists(BASE_BINETS_EXT.$_POST['folder'])) unlink(BASE_BINETS_EXT.$_POST['folder']);
	}
	// si on demande la modification de l'image
	//--------------------------------------------------------
	if (($_FILES['file']['tmp_name']!='none')&&($_FILES['file']['tmp_name']!='')) {
		$img = $_FILES['file']['tmp_name'] ;
		//récupere les données de l'images
		//--------------------------------------
		
		if(($dim = getimagesize($img))&& (($dim[0]<=100)&&($dim[1]<=100))){
			$fp = fopen($img,"rb"); // (b est pour lui dire que c'est bineaire !)
			$size = filesize($img) ;
			$data = fread($fp,$size);
			fclose($fp);
			$data = addslashes($data);
			$type_img =  $_FILES["file"]["type"];
			//
			// On verifie que le truc télécharger est une image ...
			//--------------------------------------
			$DB_trombino->query("UPDATE binets SET image=\"$data\", format='$type_img' WHERE  binet_id={$_REQUEST['id']}") ;
			$texte_image = " et de son image " ;
		} else {
			$message .= "<warning>Ton image n'est pas au bon format (taille ou extension... $type_img / $dim[0]x$dim[1] pxl)</warning>" ;
		}
	}
	
	//---------------------------------------------------------
		
	$DB_trombino->query("UPDATE binets SET nom='{$_POST['nom']}', folder='{$_POST['folder']}', http='{$_POST['http']}', description='{$_POST['descript']}', catego_id='{$_POST['catego']}' , exterieur=$ext WHERE binet_id={$_REQUEST['id']}");
	$message .= "<commentaire>Modification de {$_POST['nom']} $texte_image effectuée</commentaire>" ;
}

// On supprime l'image d'un binet
//==========================

if (isset($_POST['suppr_img'])) {
		$img = BASE_LOCAL.'/images/binet_default.gif' ;
			
		$type_img =  'image/gif';
		$fp = fopen($img,"rb"); // (b est pour lui dire que c'est bineaire !)
		$size = filesize($img) ;
		$data = fread($fp,$size);
		fclose($fp);
		$data = addslashes($data);
	
			//
			// On verifie que le truc télécharger est une image ...
			//--------------------------------------
		$DB_trombino->query("UPDATE binets SET image=\"$data\", format='$type_img' WHERE  binet_id={$_REQUEST['id']}") ;
		$message .= "<warning> Suppression de l'image du binet {$_POST['nom']}</warning>" ;
}
// On supprime un binet
//==========================

if (isset($_POST['suppr'])) {
	$DB_trombino->query("DELETE FROM binets WHERE binet_id={$_REQUEST['id']}");
	$message .= "<warning>Suppression de {$_POST['nom']} effectuée</warning>" ;
}



// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="binets_web" titre="Frankiz : Binets Web">
<? echo $message ;?>
<?php
	$liste_catego ="" ;
	$DB_trombino->query("SELECT catego_id,categorie FROM binets_categorie ORDER BY categorie ASC");
	while( list($catego_id,$catego_nom) = $DB_trombino->next_row() )
		$liste_catego .= "\t\t\t<option titre=\"$catego_nom\" id=\"$catego_id\"/>\n";
?>


<h1>Modification du binet</h1>

	<?
	$categorie_precedente = -1;
	
	// Les infos du Binet en générale
	$DB_trombino->query("SELECT description, nom,binet_id, http, catego_id, exterieur, folder FROM binets WHERE binet_id='".$_REQUEST['id']."'");
	list($descript,$nom_binet,$binet_id,$http,$cat_id,$exterieur,$folder) = $DB_trombino->next_row() ;
	
	// Les données du prez du Binet
	$DB_web->query("SELECT login FROM trombino.eleves LEFT JOIN compte_frankiz USING(eleve_id) WHERE perms LIKE '%prez_".$_REQUEST['id'].",%' ORDER BY promo DESC");
	list($prez_login) = $DB_web->next_row() ;
	
	// Les données du webmestre du Binet
	$DB_web->query("SELECT login FROM trombino.eleves LEFT JOIN compte_frankiz USING(eleve_id) WHERE perms LIKE '%webmestre_".$_REQUEST['id'].",%' ORDER BY promo DESC");
	list($web_login) = $DB_web->next_row() ;

?>
	<formulaire id="binet_web_<? echo $binet_id?>" titre="<? echo $nom_binet?>" action="admin/binets.php?id=<?=$_REQUEST['id']?>">
		<champ id="nom" titre="Nom" valeur="<? echo $nom_binet?>"/>
		<choix titre="Catégorie" id="catego" type="combo" valeur="<?=$cat_id?>">
<?php
			echo $liste_catego ;
?>
		</choix>
		<champ id="http" titre="Http" valeur="<? echo $http?>"/>
		<champ id="folder" titre="Folder de stockage" valeur="<? echo $folder?>"/>
		<zonetext id="descript" titre="Description"><?=$descript?></zonetext>
		<image source="binets/?image=1&amp;id=<?=$binet_id?>" texte="<?=$nom_binet ?>"/>
		<fichier id="file" titre="Ton image de 100x100 px" taille="50000"/>
		<choix titre="Exterieur" id="exterieur" type="checkbox" valeur="<? if ($exterieur==1) echo 'ext' ;?>">
			<option id="ext" titre=""/>
		</choix>
		<note>Vous ne pouvez pas mettre plusieurs personnes par poste (il faut mettre les login dans les champs suivants)</note>
		<champ id="prez" titre="President" valeur="<?=$prez_login?>"/>
		<champ id="webmestre" titre="Webmestre" valeur="<?=$web_login?>"/>

		<bouton id='modif' titre="Modifier"/>
		<bouton id='suppr' titre="Supprimer" onClick="return window.confirm('Voulez vous vraiment supprimer ce binet ?')"/>
		<bouton id='suppr_img' titre="Supprimer l'image" onClick="return window.confirm('Voulez vous vraiment supprimer l'image de ce binet ?')"/>
	</formulaire>

</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>

