<?php
/*
	Administration d'un binet. Le webmestre peut modifier les informations liees au site web (url,
	description), le prez peut en plus modifier les membres de son binet et leur commentaire dans
	le trombino.
	
	L'ID du binet � administrer est passer dans le param�tre GET 'binet'.
	
	$Log$
	Revision 1.8  2004/10/19 14:58:42  schmurtz
	Creation d'un champ de formulaire specifique pour les fichiers (sans passer
	l'element champ, qui actuellement est un peu acrobatique).

	Revision 1.7  2004/10/18 23:07:43  kikx
	Finalisation de la page d'administration des binets par le prez ou le webmestre de ce dit binet
	
	Revision 1.6  2004/10/18 20:29:44  kikx
	Enorme modification pour la fusion des bases des binets (Merci Schmurtz)
	
	Revision 1.5  2004/10/17 23:30:44  kikx
	Juste un petit bug si on supprime zero entr�es
	
	Revision 1.4  2004/10/17 23:17:18  kikx
	Maintenant le prez peut supprimer les personnes qui sont dans son binet et modifier leur commentaires
	
	Revision 1.3  2004/10/17 20:27:35  kikx
	Permet juste au prez des binets de consulter les perosnne adherant aux binet ainsi que leur commentaires
	
	Revision 1.2  2004/10/17 17:31:32  kikx
	Micro modif avant que j'oublie
	
	Revision 1.1  2004/10/17 15:22:05  kikx
	Mise en place d'un repertoire de gestion qui se diff�rencie de admin car ce n'est pas l'admin :)
	En gros il servira a tout les modification des prez des webmestres , des pages persos, ...
	
	Revision 1.4  2004/09/15 23:20:18  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.3  2004/09/15 21:42:27  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
	
// En-tetes
require_once "../include/global.inc.php";

// R�cup�ration d'une image
if(isset($_REQUEST['image'])){
	$DB_valid->query("SELECT image,format FROM valid_binet WHERE binet_id='{$_GET['id']}'");
	list($image,$format) = $DB_valid->next_row() ;
	header("content-type: $format");
	echo $image;
	exit;
}

// V�rification des droits
demande_authentification(AUTH_FORT);
if ((empty($_GET['binet'])) || ((!verifie_permission_webmestre($_GET['binet'])) && (!verifie_permission_prez($_GET['binet']))))
	rediriger_vers("/admin/");
	
$DB_trombino->query("SELECT nom FROM binets WHERE binet_id=".$_GET['binet']);
list($nom_binet) = $DB_trombino->next_row() ;
$message ="" ;
$message2 ="" ;


//=================================
// G�n�ration de la page
//=================================

require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="admin_binet" titre="Frankiz : administration binet">
<?
//==============================================================
//
// Si le mec est PREZIDENT
//
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

if(verifie_permission_prez($_GET['binet'])){

	//=================================
	// Suppression d'une personne
	//=================================
	$ids ="" ;
	if(isset($_POST['suppr'])) {
		if(isset($_POST['elements'])) {
	
			foreach($_POST['elements'] as $id => $on) {
				$ids .= (empty($ids) ? "" : ",") . "'$id'";
			}
			$DB_trombino->query("DELETE FROM membres  WHERE eleve_id IN ($ids)");
			$message .= "<warning>".count($_POST['elements'])." personnes viennent d'�tre supprim�es.</warning>\n";
		}
	}
	//=================================
	// Modification des commentaires des differents membres
	//=================================
	$ids ="" ;
	if(isset($_POST['modif'])) {
		foreach($_POST['description'] as $id => $on) {
			$DB_trombino->query("UPDATE  membres SET remarque='$on' WHERE eleve_id=$id");
		}
		$message .= "<commentaire> Sauvegardes des commentaires des diff�rents membres du binet</commentaire>\n";
	}

	$DB_trombino->query("SELECT promo FROM eleves WHERE eleve_id='".$_SESSION['user']->uid."'");
	list($promo_prez) = $DB_trombino->next_row() ;
	?>
	<h1>Administration par le </h1>
	<h1>pr�z du binet <?=$nom_binet?></h1>
	<?
	echo $message ;
	?>
	<h2>Liste des membres</h2>
	<?
	$DB_trombino->query("SELECT m.eleve_id,remarque,nom,prenom,surnom,promo FROM membres as m INNER JOIN eleves USING(eleve_id) WHERE binet_id=".$_GET['binet']." AND promo>=$promo_prez ORDER BY promo ASC,nom ASC");
	?>
	<liste id="liste_binets" selectionnable="oui" action="gestion/binet.php?binet=<?=$_GET['binet']?>">
		<entete id="nom" titre="Nom"/>
		<entete id="description" titre="Description"/>
	<?
	while(list($id,$remarque,$nom,$prenom,$surnom,$promo) = $DB_trombino->next_row()) {
			echo "\t\t<element id=\"$id\">\n";
			$surnom = (empty($surnom) ? "" : " (".$surnom.")") ; 
			echo "\t\t\t<colonne id=\"nom\">X$promo : $prenom $nom $surnom</colonne>\n";
			echo "\t\t\t<colonne id=\"description\"><champ id=\"description[$id]\" valeur=\"$remarque\"/></colonne>\n";
			echo "\t\t</element>\n";
	}
?>
		
		<bouton titre="Supprimer" id="suppr" onClick="return window.confirm('Supprimer cette personne de mon binet ?')"/>
		<bouton titre="Modifier tous les commentaires" id="modif"/>
	</liste>
<?
}
//==============================================================
//
// Si le mec est WEBMESTRE
//
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

if(verifie_permission_webmestre($_GET['binet'])){

	// On demande la validation du changement
	//==========================

	if (isset($_POST['modif2'])) {
		$texte_image = "" ;
		$DB_trombino->query("SELECT format,exterieur,nom,image FROM binets as b WHERE binet_id=".$_GET['binet']);
		list($format,$exterieur,$nom,$image) = $DB_trombino->next_row() ;

		// On verifie d'abord que le binet n'a pas une autre entr�e dans la table de validation
		//------------------------------------
	
		$DB_valid->query("SELECT binet_id FROM valid_binet WHERE binet_id={$_POST['id']}");
		if ($DB_valid->num_rows()!=0) {
			$message2 .= "<warning>Vous aviez d�j� demand� une modification, seule la demande que vous venez de poster sera prise en compte</warning>" ;
			$DB_valid->query("DELETE FROM valid_binet WHERE binet_id={$_POST['id']}");
		}
		
		$DB_valid->query("INSERT INTO  valid_binet SET binet_id={$_POST['id']}, nom='$nom', http='{$_POST['http']}', description='{$_POST['descript']}', catego_id='{$_POST['catego']}' , exterieur=$exterieur, image=\"".addslashes($image)."\", format='$format'");
		
		$index = mysql_insert_id() ;

		
			
		// si on demande la modification de l'image
		//--------------------------------------------------------

		if ($_FILES['file']['tmp_name']!='none') {
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
			echo $dim[0]."x".$dim[1] ;
			if ((in_array (strtolower ($type_img), $image_types))&&($dim[0]<=100)&&($dim[1]<=100)) {
				$DB_valid->query("UPDATE valid_binet SET image=\"$data\", format='$type_img' WHERE  binet_id={$_POST['id']}") ;
				$texte_image = " et de son image " ;
			} else {
				$message2 .= "<warning>Ton image n'est pas au bon format (taille ou extension... $type_img / $dim[0]x$dim[1] pxl)</warning>" ;
			}
		}
		$message2 .= "<commentaire>La demande de modification du binet ' $nom'  $texte_image a �t� effectu�e</commentaire>" ;

	}
	
//============================================

	$liste_catego ="" ;
	$DB_trombino->query("SELECT catego_id,categorie FROM binets_categorie ORDER BY categorie ASC");
	while( list($catego_id,$catego_nom) = $DB_trombino->next_row() )
		$liste_catego .= "\t\t\t<option titre=\"$catego_nom\" id=\"$catego_id\"/>\n";

		
	$DB_valid->query("SELECT binet_id FROM valid_binet WHERE binet_id=".$_GET['binet']);
	if ($DB_valid->num_rows()!=0) {
		$DB_valid->query("SELECT binet_id,nom,description,http,catego_id,exterieur FROM valid_binet WHERE binet_id=".$_GET['binet']);
		list($id,$nom,$descript,$http,$cat_id,$exterieur) = $DB_valid->next_row() ;
		$message2 .= "<commentaire>L'aper�u que vous avez maintenant n'a pas encore �t� valid� par le BR. Il faut encore attendre pour que celui ci soit pris en compte</commentaire>" ;
		$image_link = "<image source=\"gestion/binet.php?image=1&amp;id=$id\"/>" ;
	} else {
		$DB_trombino->query("SELECT binet_id,nom,description,http,catego_id,exterieur FROM binets WHERE binet_id=".$_GET['binet']);
		list($id,$nom,$descript,$http,$cat_id,$exterieur) = $DB_trombino->next_row() ;
		$image_link = "<image source=\"binets/?image=1&amp;id=$id\"/>" ;
	}


?>
	<h1>Administration par le </h1>
	<h1>webmestre du binet <?=$nom_binet?></h1>
	<?
	echo $message2 ;
	?>
		<formulaire id="binet_web" titre="<? echo $nom?>" action="gestion/binet.php?binet=<?=$_GET['binet']?>">
			<hidden id="id" titre="ID" valeur="<? echo $id?>"/>
			<choix titre="Cat�gorie" id="catego" type="combo" valeur="<?=$cat_id?>">
<?php
				echo $liste_catego ;
?>
			</choix>
			<champ id="http" titre="Http" valeur="<? echo $http?>"/>
			<zonetext id="descript" titre="Description" valeur="<? echo stripslashes($descript)?>"/>
			<?=$image_link?>
			<fichier id="file" titre="Ton image de 100x100 px" taille="100000"/>
			<choix titre="Exterieur" id="exterieur" type="checkbox" valeur="<? if ($exterieur==1) echo 'ext' ;?>" >
				<option id="ext" titre="" modifiable="non"/>
			</choix>

			<bouton id='modif2' titre="Modifier" onClick="return window.confirm('Souhaitez vous valider les changements')"/>
		</formulaire>
<?php
}
?>
</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
