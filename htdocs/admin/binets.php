<?php
/*
	Copyright (C) 2004 Binet R�seau
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
	Permet de modifier des binets d�j� existants
	
	
*/

// En-tetes
require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");

$message = "";
$texte_image ="" ;
// =====================================
// Modification d'un binet
// =====================================
$binet_id = $_GET['id'] ;
$DB_trombino->query("SELECT nom FROM binets WHERE binet_id=".$_GET['id']);
list($nomdubinet) = $DB_trombino->next_row() ;

	
	// On modifie un binet
	//==========================
	
if (isset($_POST['modif'])) {

	// On verifie que les droits des webmestre et des prez n'ont pas chang�
	//==========================================================================
	// Les donn�es du prez du Binet actuel
	$DB_web->query("SELECT login FROM trombino.eleves LEFT JOIN compte_frankiz USING(eleve_id) WHERE perms LIKE '%prez_".$_GET['id'].",%' ORDER BY promo DESC");
	list($prez_login) = $DB_web->next_row() ;
	// On change de prez
	if ($_POST['prez']!= $prez_login) {
		// on supprime les droit de l'ancien web (si il existe bien sur)
		if ($prez_login!="") {
			$DB_web->query("SELECT perms,e.eleve_id FROM compte_frankiz LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE login='$prez_login'" );
			while(list($perms,$eleve_id) = $DB_web->next_row()) {
				$perms = str_replace("prez_".$binet_id.",","",$perms) ;
				$DB_web->query("UPDATE compte_frankiz SET perms='$perms' WHERE eleve_id='$eleve_id'");
				$message .= "<commentaire>$prez_login n'a plus ses droit de prez du binet $nomdubinet</commentaire>\n";
			}
		}
		// on donne les droits au nouveau prez
		if ($_POST['prez']!="") {
			$DB_web->query("SELECT perms,e.eleve_id FROM compte_frankiz LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE login='".$_POST['prez']."' " );
			if ($DB_web->num_rows()==0) 
				$message .= "<warning>Ce login n'existe pas ou ne s'est jamais connect� a Frankiz</warning>" ;
			while(list($perms,$eleve_id) = $DB_web->next_row()) {
				$perms = $perms."prez_".$binet_id."," ;
				$DB_web->query("UPDATE compte_frankiz SET perms='$perms' WHERE eleve_id='$eleve_id'");
				$message .= "<commentaire>".$_POST['prez']." a re�u les droits de prez du binet $nomdubinet</commentaire>\n";
			}
		}
	}

	
	
	
	
	// Les donn�es du webmestre du Binet actuel
	$DB_web->query("SELECT login FROM trombino.eleves LEFT JOIN compte_frankiz USING(eleve_id) WHERE perms LIKE '%webmestre_".$_GET['id'].",%' ORDER BY promo DESC");
	list($web_login) = $DB_web->next_row() ;
	
	if ($_POST['webmestre']!= $web_login) {
		// on supprime les droit de l'ancien web (si il existe bien sur)
		if ($web_login!="") {
			$DB_web->query("SELECT perms,e.eleve_id FROM compte_frankiz LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE login='$web_login'" );
			while(list($perms,$eleve_id) = $DB_web->next_row()) {
				$perms = str_replace("prez_".$binet_id.",","",$perms) ;
				$DB_web->query("UPDATE compte_frankiz SET perms='$perms' WHERE eleve_id='$eleve_id'");
				$message .= "<commentaire>$web_login n'a plus ses droit de webmestre du binet $nomdubinet</commentaire>\n";
			}
		}
		// on donne les droits au nouveau prez
		if ($_POST['webmestre']!="") {
			$DB_web->query("SELECT perms,e.eleve_id FROM compte_frankiz LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE login='".$_POST['webmestre']."' " );
			if ($DB_web->num_rows()==0) 
				$message .= "<warning>Ce login n'existe pas ou ne s'est jamais connect� a Frankiz</warning>" ;
			while(list($perms,$eleve_id) = $DB_web->next_row()) {
				$perms = $perms."prez_".$binet_id."," ;
				$DB_web->query("UPDATE compte_frankiz SET perms='$perms' WHERE eleve_id='$eleve_id'");
				$message .= "<commentaire>".$_POST['webmestre']." a re�u les droits de webmestre du binet $nomdubinet</commentaire>\n";
			}
		}
	}
		




	if ((isset($_POST['ext']))&&($_POST['ext']=='on')) 
		$ext = 1;
	else
		$ext = 0;
		
	// si on demande la modification de l'image
	//--------------------------------------------------------
	if (($_FILES['file']['tmp_name']!='none')&&($_FILES['file']['tmp_name']!='')) {
		$img = $_FILES['file']['tmp_name'] ;
		$image_types = Array ("image/bmp","image/jpeg","image/pjpeg","image/gif","image/x-png","image/png");
	
			//r�cupere les donn�es de l'images
			//--------------------------------------
			
		$type_img =  $_FILES["file"]["type"];
		
		$fp = fopen($img,"rb"); // (b est pour lui dire que c'est bineaire !)
		$size = filesize($img) ;
		$dim = getimagesize($img) ;
		$data = fread($fp,$size);
		fclose($fp);
		$data = addslashes($data);
	
			//
			// On verifie que le truc t�l�charger est une image ...
			//--------------------------------------
		
		if ((in_array (strtolower ($type_img), $image_types))&&($dim[0]<=100)&&($dim[1]<=100)) {
			$DB_trombino->query("UPDATE binets SET image=\"$data\", format='$type_img' WHERE  binet_id={$_GET['id']}") ;
			$texte_image = " et de son image " ;
		} else {
			$message .= "<warning>Ton image n'est pas au bon format (taille ou extension... $type_img / $dim[0]x$dim[1] pxl)</warning>" ;
		}
	}
	
	//---------------------------------------------------------
		
	$DB_trombino->query("UPDATE binets SET nom='{$_POST['nom']}', folder='{$_POST['folder']}', http='{$_POST['http']}', description='{$_POST['descript']}', catego_id='{$_POST['catego']}' , exterieur=$ext WHERE binet_id={$_GET['id']}");
	$message .= "<commentaire>Modification de {$_POST['nom']} $texte_image effectu�e</commentaire>" ;
}

// On supprime l'image d'un binet
//==========================

if (isset($_POST['suppr_img'])) {
		$img = BASE_LOCAL.'/admin/binet_default.gif' ;
			
		$type_img =  'image/gif';
		$fp = fopen($img,"rb"); // (b est pour lui dire que c'est bineaire !)
		$size = filesize($img) ;
		$dim = getimagesize($img) ;
		$data = fread($fp,$size);
		fclose($fp);
		$data = addslashes($data);
	
			//
			// On verifie que le truc t�l�charger est une image ...
			//--------------------------------------
		$DB_trombino->query("UPDATE binets SET image=\"$data\", format='$type_img' WHERE  binet_id={$_GET['id']}") ;
		$message .= "<warning> Suppression de l'image du binet {$_POST['nom']}</warning>" ;
}
// On supprime un binet
//==========================

if (isset($_POST['suppr'])) {
	$DB_trombino->query("DELETE FROM binets WHERE binet_id={$_GET['id']}");
	$message .= "<warning>Suppression de {$_POST['nom']} effectu�e</warning>" ;
}



// G�n�ration de la page
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
	
	// Les infos du Binet en g�n�rale
	$DB_trombino->query("SELECT description, nom,binet_id, http, catego_id, exterieur, folder FROM binets WHERE binet_id=".$_GET['id']);
	list($descript,$nom_binet,$binet_id,$http,$cat_id,$exterieur,$folder) = $DB_trombino->next_row() ;
	
	// Les donn�es du prez du Binet
	$DB_web->query("SELECT login FROM trombino.eleves LEFT JOIN compte_frankiz USING(eleve_id) WHERE perms LIKE '%prez_".$_GET['id'].",%' ORDER BY promo DESC");
	list($prez_login) = $DB_web->next_row() ;
	
	// Les donn�es du webmestre du Binet
	$DB_web->query("SELECT login FROM trombino.eleves LEFT JOIN compte_frankiz USING(eleve_id) WHERE perms LIKE '%webmestre_".$_GET['id'].",%' ORDER BY promo DESC");
	list($web_login) = $DB_web->next_row() ;

?>
	<formulaire id="binet_web_<? echo $binet_id?>" titre="<? echo $nom_binet?>" action="admin/binets.php?id=<?=$_GET['id']?>">
		<champ id="nom" titre="Nom" valeur="<? echo $nom_binet?>"/>
		<choix titre="Cat�gorie" id="catego" type="combo" valeur="<?=$cat_id?>">
<?php
			echo $liste_catego ;
?>
		</choix>
		<champ id="http" titre="Http" valeur="<? echo $http?>"/>
		<champ id="folder" titre="Folder de stockage" valeur="<? echo $folder?>"/>
		<zonetext id="descript" titre="Description" valeur="<? echo stripslashes($descript)?>"/>
		<image source="binets/?image=1&amp;id=<?=$binet_id?>"/>
		<fichier id="file" titre="Ton image de 100x100 px" taille="50000"/>
		<choix titre="Exterieur" id="exterieur" type="checkbox" valeur="<? if ($exterieur==1) echo 'ext' ;?>">
			<option id="ext" titre=""/>
		</choix>
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

