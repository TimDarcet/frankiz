<?php
/*
	Gestion de la liste des binets.

	$Log$
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
		$DB_web->query("INSERT INTO  binets SET nom='{$_POST['nom']}', http='{$_POST['http']}', descript='{$_POST['descript']}', catego='{$_POST['catego']}' ");
		$index = mysql_insert_id() ;
		$message .= "<commentaire>Création du binet ' {$_POST['nom']}' effectuée</commentaire>" ;
		
		// Trick pour ne pas avoir a recopier le code d'integration de l'image à la base de donnée
		$_POST['modif'] = 1 ;
		$_POST['id'] = $index  ;
		if ($_FILES['file']['tmp_name']=='none')
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
		if ($_FILES['file']['tmp_name']!='none') {
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
				$DB_web->query("UPDATE binets SET image=\"$data\", format='$type_img' WHERE  id={$_POST['id']}") ;
				$texte_image = " et de son image " ;
			} else {
				$message .= "<warning>Ton image n'est pas au bon format (taille ou extension... $type_img / $dim[0]x$dim[1] pxl)</warning>" ;
			}
		}
		
		//---------------------------------------------------------
			
		$DB_web->query("UPDATE binets SET nom='{$_POST['nom']}', http='{$_POST['http']}', descript='{$_POST['descript']}', catego='{$_POST['catego']}' , exterieur=$ext WHERE id={$_POST['id']}");
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
			$DB_web->query("UPDATE binets SET image=\"$data\", format='$type_img' WHERE  id={$_POST['id']}") ;
			$message .= "<warning> Suppression de l'image du binet {$_POST['nom']}</warning>" ;
	}
	// On supprime un binet
	//==========================
	
	if (isset($_POST['suppr'])) {
		$DB_web->query("DELETE FROM binets WHERE id={$_POST['id']}");
		$message .= "<warning>Suppression de {$_POST['nom']} effectuée</warning>" ;
	}



// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="binets_web" titre="Frankiz : Binets Web">
<? echo $message ;?>
<h1>Création d'un site Web de binet</h1>

<?php
	$liste_catego ="" ;
	$DB_web->query("SELECT id,catego FROM categ_binet ORDER BY catego ASC");
	while( list($catego_id,$catego_nom) = $DB_web->next_row() )
		$liste_catego .= "\t\t\t<option titre=\"$catego_nom\" id=\"$catego_id\"/>\n";
	?>
		<formulaire id="binet_web" titre="Nouveau Binet" action="admin/binets_web.php">
			<hidden id="id" titre="ID" valeur=""/>
			<champ id="nom" titre="Nom" valeur=""/>
			<choix titre="Catégorie" id="catego" type="combo" valeur="">
<?php
				echo $liste_catego ;
?>
			</choix>
			<champ id="http" titre="Http" valeur=""/>
			<zonetext id="descript" titre="Description" valeur=""/>
			<champ id="file" titre="Ton image de 100x100 px" valeur="" taille="50000"/>
			<choix titre="Exterieur" id="exterieur" type="checkbox" valeur="">
				<option id="ext" titre=""/>
			</choix>

			<bouton id='ajout' titre="Ajouter"/>
		</formulaire>
<h1>Modification d'un site Web de binet</h1>

	<?
	$categorie_precedente = -1;
	$DB_web->query("SELECT b.id,date,nom,descript,http,c.id,c.catego,b.exterieur FROM binets as b INNER JOIN categ_binet as c ON(b.catego=c.id) ORDER BY b.nom ASC");
	while(list($id,$date,$nom,$descript,$http,$cat_id,$catego,$exterieur) = $DB_web->next_row()) {
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
			<zonetext id="descript" titre="Description" valeur="<? echo stripslashes($descript)?>"/>
			<image source="binets/?image=1&amp;id=<?=$id?>"/>
			<champ id="file" titre="Ton image de 100x100 px" valeur="" taille="50000"/>
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
</page>
<?php


require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
