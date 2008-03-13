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
	Administration d'un binet. Le webmestre peut modifier les informations liees au site web (url,
	description), le prez peut en plus modifier les membres de son binet et leur commentaire dans
	le trombino.
	
	L'ID du binet à administrer est passer dans le paramètre GET 'binet'.
	
	$Id$
*/
	
// En-tetes
require_once "../include/global.inc.php";

// Récupération d'une image
if(isset($_REQUEST['image'])){
	$DB_valid->query("SELECT image,format FROM valid_binet WHERE binet_id='{$_REQUEST['id']}'");
	list($image,$format) = $DB_valid->next_row() ;
	header("content-type: $format");
	echo $image;
	exit;
}

// Vérification des droits
demande_authentification(AUTH_MDP);
if ((empty($_REQUEST['binet'])) || ((!verifie_permission_webmestre($_REQUEST['binet'])) && (!verifie_permission_prez($_REQUEST['binet']))))
	acces_interdit();
	
$DB_trombino->query("SELECT nom FROM binets WHERE binet_id=".$_REQUEST['binet']);
list($nom_binet) = $DB_trombino->next_row() ;
$message ="" ;
$message2 ="" ;


//=================================
// Génération de la page
//=================================

require_once BASE_FRANKIZ."include/page_header.inc.php";
?>
<page id="admin_binet" titre="Frankiz : administration binet">
<?php
//==============================================================
//
// Si le mec est PREZIDENT
//
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

if(verifie_permission_prez($_REQUEST['binet'])){

	//=================================
	// Suppression d'une personne
	//=================================
	$ids ="" ;
	if(isset($_POST['suppr'])) {
		if(isset($_POST['elements'])) {
	
			foreach($_POST['elements'] as $id => $on) {
				$ids .= (empty($ids) ? "" : ",") . "'$id'";
			}
			$DB_trombino->query("DELETE FROM membres  WHERE eleve_id IN ($ids) AND binet_id='{$_REQUEST['binet']}'");
			$message .= "<warning>".count($_POST['elements'])." personnes viennent d'être supprimées.</warning>\n";
		}
	}
	//=================================
	// Modification des commentaires des differents membres
	//=================================
	$ids ="" ;
	if(isset($_POST['modif'])) {
		foreach($_POST['description'] as $id => $on) {
			$DB_trombino->query("UPDATE membres SET remarque='$on' WHERE eleve_id='$id' AND binet_id='".$_REQUEST['binet']."'");
		}
		$message .= "<commentaire> Sauvegardes des commentaires des différents membres du binet</commentaire>\n";
	}

	$DB_trombino->query("SELECT promo FROM eleves WHERE eleve_id='".$_SESSION['uid']."'");
	list($promo_prez) = $DB_trombino->next_row() ;
	?>
	<h1>Administration par le </h1>
	<h1>prèz du binet <?php echo $nom_binet; ?></h1>
	<?php
	echo $message ;
	?>
	<h2>Liste des membres</h2>
	<?php
	$DB_trombino->query("SELECT m.eleve_id,remarque,nom,prenom,surnom,promo FROM membres as m LEFT JOIN eleves USING(eleve_id) WHERE binet_id=".$_REQUEST['binet']." AND promo>=$promo_prez ORDER BY promo ASC,nom ASC");
	?>
	<liste id="liste_binets" selectionnable="oui" action="gestion/binet.php?binet=<?php echo $_REQUEST['binet']; ?>">
		<entete id="nom" titre="Nom"/>
		<entete id="description" titre="Description"/>
	<?php
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
<?php
}
//==============================================================
//
// Si le mec est WEBMESTRE
//
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

if(verifie_permission_webmestre($_REQUEST['binet'])){
	
	// On demande la validation du changement
	//==========================

	
	if (isset($_POST['modif2'])) {
		$texte_image = "" ;
		$DB_trombino->query("SELECT format,exterieur,nom,image,folder FROM binets as b WHERE binet_id=".$_REQUEST['binet']);
		list($format,$exterieur,$nom,$image,$folder) = $DB_trombino->next_row() ;

		// On verifie d'abord que le binet n'a pas une autre entrée dans la table de validation
		//------------------------------------
	
		$DB_valid->query("SELECT binet_id FROM valid_binet WHERE binet_id={$_POST['id']}");
		if ($DB_valid->num_rows()!=0) {
			$message2 .= "<warning>Vous aviez déjà demandé une modification, seule la demande que vous venez de poster sera prise en compte</warning>" ;
			$DB_valid->query("DELETE FROM valid_binet WHERE binet_id={$_POST['id']}");
		} else {
			$tempo = explode("gestion",$_SERVER['REQUEST_URI']) ;

			$contenu = "Le webmestre du binet $nom a demandé la modification de l'affichage de son binet <br><br>".
				"Pour valider ou non cette demande va sur la page suivante : <br>".
				"<div align='center'><a href='http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_binets.php'>".
				"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_binets.php</a></div><br><br>" .
				"Cordialement,<br>" .
				"Le Webmestre de Frankiz<br>"  ;
				
			couriel(WEBMESTRE_ID,"[Frankiz] Modification du binet $nom",$contenu);
		
		}
		
		$DB_valid->query("INSERT INTO  valid_binet SET binet_id={$_POST['id']}, nom='$nom', http='{$_POST['http']}', description='{$_POST['descript']}', catego_id='{$_POST['catego']}' , exterieur=$exterieur, folder='$folder', image=\"".addslashes($image)."\", format='$format'");
		
		$index = mysql_insert_id($DB_valid->link) ;

		
			
		// si on demande la modification de l'image
		//--------------------------------------------------------
		if (($_FILES['file']['tmp_name']!='none')&&($_FILES['file']['tmp_name']!='')) {
			$img = $_FILES['file']['tmp_name'] ;
				//récupere les données de l'images
				//--------------------------------------
				
			$type_img =  $_FILES["file"]["type"];
			
			$fp = fopen($img,"rb"); // (b est pour lui dire que c'est bineaire !)
			$size = filesize($img) ;
			$data = fread($fp,$size);
			fclose($fp);
			$data = addslashes($data);
		
				//
				// On verifie que le truc télécharger est une image ...
				//--------------------------------------
			//echo $dim[0]."x".$dim[1] ;
			if (($dim = getimagesize($img))&&($dim[0]<=100)&&($dim[1]<=100)) {
				$DB_valid->query("UPDATE valid_binet SET image=\"$data\", format='$type_img' WHERE  binet_id={$_POST['id']}") ;
				$texte_image = " et de son image " ;
			} else {
				$message2 .= "<warning>Ton image n'est pas au bon format (taille ou extension... $type_img / $dim[0]x$dim[1] pxl)</warning>" ;
			}
		}
		$message2 .= "<commentaire>La demande de modification du binet '$nom'  $texte_image a été effectuée</commentaire>" ;
	}
	
//============================================

	$liste_catego ="" ;
	$DB_trombino->query("SELECT catego_id,categorie FROM binets_categorie ORDER BY categorie ASC");
	while( list($catego_id,$catego_nom) = $DB_trombino->next_row() )
		$liste_catego .= "\t\t\t<option titre=\"$catego_nom\" id=\"$catego_id\"/>\n";

	$DB_valid->query("SELECT binet_id,nom,description,http,catego_id,exterieur,folder FROM valid_binet WHERE binet_id=".$_REQUEST['binet']);
	if ($DB_valid->num_rows()!=0) {
		list($id,$nom,$descript,$http,$cat_id,$exterieur,$folder) = $DB_valid->next_row() ;
		$message2 .= "<commentaire>L'aperçu que vous avez maintenant n'a pas encore été validé par le BR. Il faut encore attendre pour que celui ci soit pris en compte</commentaire>" ;
		$image_link = "<image source=\"gestion/binet.php?image=1&amp;id=$id\" texte=\"image\"/>" ;
	} else {
		$DB_trombino->query("SELECT binet_id,nom,description,http,catego_id,exterieur,folder FROM binets WHERE binet_id=".$_REQUEST['binet']);
		list($id,$nom,$descript,$http,$cat_id,$exterieur,$folder) = $DB_trombino->next_row() ;
		$image_link = "<image source=\"binets/?image=1&amp;id=$id\" texte=\"image\"/>" ;
	}


?>
	<h1>Administration par le </h1>
	<h1>webmestre du binet <?php echo $nom_binet; ?></h1>
	<?php
	echo $message2 ;
	?>
	<note> Si tu ne souhaites pas que ton binet apparaisse dans la liste des binets sur le site, alors supprime le champ Http</note>
		<formulaire id="binet_web" titre="<?php echo $nom?>" action="gestion/binet.php?binet=<?php echo $_REQUEST['binet']; ?>">
			<hidden id="id" titre="" valeur="<?php echo $id?>"/>
			<choix titre="Catégorie" id="catego" type="combo" valeur="<?php echo $cat_id; ?>">
<?php
				echo $liste_catego ;
?>
			</choix>
			<champ id="http" titre="Http" valeur="<?php echo $http?>"/>
			<zonetext id="descript" titre="Description"><?php echo $descript; ?></zonetext>
			<?php echo $image_link; ?>
			<fichier id="file" titre="Ton image de 100x100 px" taille="100000"/>
			<champ id="exterieur" titre="Exterieur" valeur="<?php if ($exterieur==1) echo 'Oui' ; else echo 'Non'?>" modifiable="non"/>

			<bouton id='modif2' titre="Modifier" onClick="return window.confirm('Souhaitez vous valider les changements')"/>
		</formulaire>

<?php /*
	function parcours_arbo1($rep) {
		if( $dir = opendir($rep) ) {
			while( FALSE !== ($fich = readdir($dir)) ) {
				if ($fich != "." && $fich != "..") {
					$chemin = "$rep/$fich";
					$fich = utf8_encode($fich);
					if (is_dir($chemin)) {
						echo "<noeud titre=\"#".htmlentities($fich,ENT_QUOTES)."\">";
							parcours_arbo1($chemin);
						echo "</noeud>" ;
					} else {
						echo "<feuille titre=\"$fich\"></feuille>";
					}
				}
			}
		}
	}
	// si le folder du binet n'existe pas alors on le crée
	if($folder!='' && is_dir(BASE_BINETS.$folder)) {
		
		echo "<h2>Gestion des fichiers du site $nom_binet</h2>";
		
		echo "<arbre>";
		echo "<noeud titre=\"/$folder\">" ;
		
		$arbo = parcours_arbo1(BASE_BINETS.$folder);
		echo "</noeud>" ;
		echo "</arbre>";
	}*/
}

?>
</page>
<?php
require_once BASE_FRANKIZ."include/page_footer.inc.php";
?>
