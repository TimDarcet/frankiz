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
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");

$message = "";
$texte_image ="" ;
// =====================================
// Modification d'un binet
// =====================================

	// On crée un binet
	//==========================

	if (isset($_POST['ajout'])) {
		$DB_trombino->query("INSERT INTO  binets SET nom='{$_POST['nom']}', http='{$_POST['http']}', description='{$_POST['descript']}', catego_id='{$_POST['catego']} ' ");
		$index = mysql_insert_id() ;
		$message .= "<commentaire>Création du binet ' {$_POST['nom']}' effectuée</commentaire>" ;
		
		// Trick pour ne pas avoir a recopier le code d'integration de l'image à la base de donnée
		$_POST['modif'] = 1 ;
		$_POST['id'] = $index  ;
		if (($_FILES['file']['tmp_name']!='none')&&($_FILES['file']['tmp_name']!=''))
			$_POST['suppr_img'] = 1 ;
	}
	
	// On modifie un binet
	//==========================
	
	if (isset($_POST['modif'])) {
		if ($_POST['ext']=='on') 
			$ext = 1;
		else
			$ext = 0;
			
		// si on demande la modification de l'image
		//--------------------------------------------------------
		if (($_FILES['file']['tmp_name']!='none')&&($_FILES['file']['tmp_name']!='')) {
			$img = $_FILES['file']['tmp_name'] ;
			$image_types = Array ("image/bmp","image/jpeg","image/pjpeg","image/gif","image/x-png","image/png");
		
				//récupere les données de l'images
				//--------------------------------------
				
			$type_img =  $_FILES["file"]["type"];
			
			$fp = fopen($img,"rb"); // (b est pour lui dire que c'est bineaire !)
			$size = filesize($img) ;
			$dim = getimagesize($img) ;
			$data = fread($fp,$size);
			fclose($fp);
			$data = addslashes($data);
		
				//
				// On verifie que le truc télécharger est une image ...
				//--------------------------------------
			
			if ((in_array (strtolower ($type_img), $image_types))&&($dim[0]<=100)&&($dim[1]<=100)) {
				$DB_trombino->query("UPDATE binets SET image=\"$data\", format='$type_img' WHERE  binet_id={$_POST['id']}") ;
				$texte_image = " et de son image " ;
			} else {
				$message .= "<warning>Ton image n'est pas au bon format (taille ou extension... $type_img / $dim[0]x$dim[1] pxl)</warning>" ;
			}
		}
		
		//---------------------------------------------------------
			
		$DB_trombino->query("UPDATE binets SET nom='{$_POST['nom']}', folder='{$_POST['folder']}', http='{$_POST['http']}', description='{$_POST['descript']}', catego_id='{$_POST['catego']}' , exterieur=$ext WHERE binet_id={$_POST['id']}");
		$message .= "<commentaire>Modification de {$_POST['nom']} $texte_image effectuée</commentaire>" ;
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
				// On verifie que le truc télécharger est une image ...
				//--------------------------------------
			$DB_trombino->query("UPDATE binets SET image=\"$data\", format='$type_img' WHERE  binet_id={$_POST['id']}") ;
			$message .= "<warning> Suppression de l'image du binet {$_POST['nom']}</warning>" ;
	}
	// On supprime un binet
	//==========================
	
	if (isset($_POST['suppr'])) {
		$DB_trombino->query("DELETE FROM binets WHERE binet_id={$_POST['id']}");
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

<h1>Modification des binets ayant un site web</h1>

	<?
	$categorie_precedente = -1;
	$DB_trombino->query("SELECT binet_id,nom,description,http,b.catego_id,categorie,exterieur,folder FROM binets as b LEFT JOIN binets_categorie as c USING(catego_id) WHERE http IS NOT NULL ORDER BY nom ASC");
	while(list($id,$nom,$descript,$http,$cat_id,$catego,$exterieur,$folder) = $DB_trombino->next_row()) {
?>
		<formulaire id="binet_web_<? echo $id?>" titre="<? echo $nom?>" action="admin/binets_web.php">
			<hidden id="id" titre="ID" valeur="<? echo $id?>"/>
			<champ id="nom" titre="Nom" valeur="<? echo $nom?>"/>
			<choix titre="Catégorie" id="catego" type="combo" valeur="<?=$cat_id?>">
<?php
				echo $liste_catego ;
?>
			</choix>
			<champ id="http" titre="Http" valeur="<? echo $http?>"/>
			<champ id="folder" titre="Folder de stockage" valeur="<? echo $folder?>"/>
			<zonetext id="descript" titre="Description" valeur="<? echo stripslashes($descript)?>"/>
			<image source="binets/?image=1&amp;id=<?=$id?>"/>
			<fichier id="file" titre="Ton image de 100x100 px" taille="50000"/>
			<choix titre="Exterieur" id="exterieur" type="checkbox" valeur="<? if ($exterieur==1) echo 'ext' ;?>">
				<option id="ext" titre=""/>
			</choix>

			<bouton id='modif' titre="Modifier"/>
			<bouton id='suppr' titre="Supprimer" onClick="return window.confirm('Voulez vous vraiment supprimer ce binet ?')"/>
			<bouton id='suppr_img' titre="Supprimer l'image" onClick="return window.confirm('Voulez vous vraiment supprimer l'image de ce binet ?')"/>
		</formulaire>
<?php
	}
?>

<h1>Modification des binets n'ayant pas de site web</h1>

	<?
	$categorie_precedente = -1;
	$DB_trombino->query("SELECT binet_id,nom,description,http,b.catego_id,categorie,exterieur,folder FROM binets as b LEFT JOIN binets_categorie as c USING(catego_id) WHERE http IS NULL ORDER BY nom ASC");
	while(list($id,$nom,$descript,$http,$cat_id,$catego,$exterieur,$folder) = $DB_trombino->next_row()) {
?>
		<formulaire id="binet_web_<? echo $id?>" titre="<? echo $nom?>" action="admin/binets_web.php">
			<hidden id="id" titre="ID" valeur="<? echo $id?>"/>
			<champ id="nom" titre="Nom" valeur="<? echo $nom?>"/>
			<choix titre="Catégorie" id="catego" type="combo" valeur="<?=$cat_id?>">
<?php
				echo $liste_catego ;
?>
			</choix>
			<champ id="http" titre="Http" valeur="<? echo $http?>"/>
			<champ id="folder" titre="Folder de stockage" valeur="<? echo $folder?>"/>
			<zonetext id="descript" titre="Description" valeur="<? echo stripslashes($descript)?>"/>
			<image source="binets/?image=1&amp;id=<?=$id?>"/>
			<fichier id="file" titre="Ton image de 100x100 px" taille="50000"/>
			<choix titre="Exterieur" id="exterieur" type="checkbox" valeur="<? if ($exterieur==1) echo 'ext' ;?>">
				<option id="ext" titre=""/>
			</choix>

			<bouton id='modif' titre="Modifier"/>
			<bouton id='suppr' titre="Supprimer" onClick="return window.confirm('Voulez vous vraiment supprimer ce binet ?')"/>
			<bouton id='suppr_img' titre="Supprimer l'image" onClick="return window.confirm('Voulez vous vraiment supprimer l'image de ce binet ?')"/>
		</formulaire>
<?php
	}
?>


<h1>Création d'un site Web de binet</h1>

		<formulaire id="binet_web" titre="Nouveau Binet" action="admin/binets_web.php">
			<hidden id="id" titre="ID" valeur=""/>
			<champ id="nom" titre="Nom" valeur=""/>
			<choix titre="Catégorie" id="catego" type="combo" valeur="">
<?php
				echo $liste_catego ;
?>
			</choix>
			<champ id="http" titre="Http" valeur=""/>
			<champ id="folder" titre="Folder de stockage" valeur=""/>
			<zonetext id="descript" titre="Description" valeur=""/>
			<fichier id="file" titre="Ton image de 100x100 px" taille="50000"/>
			<choix titre="Exterieur" id="exterieur" type="checkbox" valeur="">
				<option id="ext" titre=""/>
			</choix>

			<bouton id='ajout' titre="Ajouter"/>
		</formulaire>
		

</page>
<?php


require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
