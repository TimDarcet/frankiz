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

	$Id$
	
	
*/

// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
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

// ruse pour supprimer les droits avant de supprimer le binet
if (isset($_POST['suppr'])) {
	$_POST['prez'] = "";
	$_POST['webmestre'] = "";
}
	
// On modifie un binet
//==========================

// d'abord juste les droits, que l'on supprime aussi dans le cas de la suppression du binet
if (isset($_POST['modif']) || isset($_POST['suppr'])) {

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
				$message .= "<commentaire>$prez_login n'a plus ses droits de prez du binet $nomdubinet</commentaire>\n";
			}
		}
		// on donne les droits au nouveau prez
		if ($_POST['prez']!="") {
			$DB_web->query("SELECT perms,e.eleve_id FROM compte_frankiz LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE login='".$_POST['prez']."' AND (promo='$promo_temp' OR promo='$promo_temp2')" );
			if ($DB_web->num_rows()==0) 
				$message .= "<warning>Ce login n'existe pas ou ne s'est jamais connecté à Frankiz</warning>" ;
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
				$message .= "<commentaire>$web_login n'a plus ses droits de webmestre du binet $nomdubinet</commentaire>\n";
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
		

}

// autres modifs (qu'il est inutile de faire dans le cas de la suppression d'un binet
if (isset($_POST['modif'])) {


	if ((isset($_POST['ext']))&&($_POST['ext']=='on')) {
		$ext = 1;
		if(!file_exists(BASE_BINETS_EXT.$_POST['folder'])) symlink (BASE_BINETS.$_POST['folder'],BASE_BINETS_EXT.$_POST['folder']);
	}else{
		$ext = 0;
		if($_POST['folder']!='' && (file_exists(BASE_BINETS_EXT.$_POST['folder']) || is_link(BASE_BINETS_EXT.$_POST['folder'])))
		{
			unlink(BASE_BINETS_EXT.$_POST['folder']);
		}
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
<?php echo $message ;?>
<?php
	$liste_catego ="" ;
	$DB_trombino->query("SELECT catego_id,categorie FROM binets_categorie ORDER BY categorie ASC");
	while( list($catego_id,$catego_nom) = $DB_trombino->next_row() )
		$liste_catego .= "\t\t\t<option titre=\"$catego_nom\" id=\"$catego_id\"/>\n";
?>


<h1>Modification du binet</h1>

	<?php
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
	<formulaire id="binet_web_<?php echo $binet_id?>" titre="<?php echo $nom_binet?>" action="admin/binets.php?id=<?php echo $_REQUEST['id']; ?>">
		<champ id="nom" titre="Nom" valeur="<?php echo $nom_binet?>"/>
		<choix titre="Catégorie" id="catego" type="combo" valeur="<?php echo $cat_id; ?>">
<?php
			echo $liste_catego ;
?>
		</choix>
		<champ id="http" titre="Http" valeur="<?php echo $http?>"/>
		<champ id="folder" titre="Folder de stockage" valeur="<?php echo $folder?>"/>
		<zonetext id="descript" titre="Description"><?php echo $descript; ?></zonetext>
		<image source="binets/?image=1&amp;id=<?php echo $binet_id; ?>" texte="<?php echo $nom_binet; ?>"/>
		<fichier id="file" titre="Ton image de 100x100 px" taille="50000"/>
		<choix titre="Exterieur" id="exterieur" type="checkbox" valeur="<?php if ($exterieur==1) echo 'ext' ;?>">
			<option id="ext" titre=""/>
		</choix>
		<note>Vous ne pouvez pas mettre plusieurs personnes par poste (il faut mettre les logins dans les champs suivants)</note>
		<champ id="prez" titre="President" valeur="<?php echo $prez_login; ?>"/>
		<champ id="webmestre" titre="Webmestre" valeur="<?php echo $web_login; ?>"/>

		<bouton id='modif' titre="Modifier"/>
		<bouton id='suppr' titre="Supprimer" onClick="return window.confirm('Voulez vous vraiment supprimer ce binet ?')"/>
		<bouton id='suppr_img' titre="Supprimer l'image" onClick="return window.confirm('Voulez vous vraiment supprimer l'image de ce binet ?')"/>
	</formulaire>

</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
