<? 
/*
		$Log$
		Revision 1.14  2004/10/20 22:10:06  pico
		BugFix (devrait marcher)

		Revision 1.13  2004/10/20 22:05:55  pico
		Changements Noeuds/Feuilles
		
		Revision 1.12  2004/10/20 19:58:46  pico
		BugFix: génération des balises plus conforme
		
		Revision 1.11  2004/10/19 14:58:42  schmurtz
		Creation d'un champ de formulaire specifique pour les fichiers (sans passer
		l'element champ, qui actuellement est un peu acrobatique).
		
		Revision 1.10  2004/10/19 07:56:56  pico
		Corrections diverses
		
		Revision 1.9  2004/10/18 22:18:11  pico
		Ajout des logs
		
*/
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MINIMUM);


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="xshare" titre="Frankiz : Xshare">
<h1>Xshare</h1>

<?



foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

	// Supprimer le répertoire
	
	if ($temp[0]=='rmdir') {
		$DB_web->query("SELECT lien FROM xshare WHERE id='{$temp[1]}' ");
		list($dir) = $DB_web->next_row();
		$dir = BASE_DATA."xshare/".$dir;
		foreach(glob($dir."/*") as $fn) {
			unlink($fn);
		} 
		rmdir($dir);
		$DB_web->query("DELETE FROM xshare WHERE id='{$temp[1]}'");
		$DB_web->query("DELETE FROM xshare WHERE id_parent='{$temp[1]}'");
		echo "<warning>Repertoire Supprimé</warning>";
	}
	
	if (($temp[0]=='adddir') && isset($_REQUEST['nom']) && ($_REQUEST['nom']!='')) {
		$nom = $_REQUEST['nom'];
		$DB_web->query("SELECT lien FROM xshare WHERE id='{$temp[1]}' ");
		list($dir) = $DB_web->next_row();
		$dir=$dir."/".strtolower(str_replace(" ","",$nom));
		$DB_web-> query("INSERT INTO xshare SET id_parent='{$temp[1]}',nom='{$nom}',lien='{$dir}'");
		mkdir(BASE_DATA."xshare/".$dir);
		echo "<commentaire>Repertoire crée</commentaire>";
	}
	
	if (($temp[0]=='ajout') && isset($_REQUEST['nom']) && ($_REQUEST['nom']!='') && (isset($_FILES['file']))&&($_FILES['file']['size']!=0)) {
		$nom = $_REQUEST['nom'];
		$DB_web->query("SELECT lien FROM xshare WHERE id='{$temp[1]}' ");
		list($dir) = $DB_web->next_row();
		$filename = $dir."/".$_FILES['file']['name'];
		move_uploaded_file($_FILES['file']['tmp_name'], BASE_DATA."xshare/".$filename);
		if(isset($_REQUEST['version'])) $version = ", version='{$_REQUEST['version']}'"; else $version = '';
		if(isset($_REQUEST['importance'])) $importance =  ", importance='{$_REQUEST['importance']}'"; else $importance = '';
		if(isset($_REQUEST['site'])) $site =  ", site='{$_REQUEST['site']}'"; else $site = '';
		if(isset($_REQUEST['licence'])) $licence = ", licence='{$_REQUEST['licence']}'"; else $licence = '';
		if(isset($_REQUEST['descript'])) $descript = ", descript='{$_REQUEST['descript']}'"; else $descript = '';
		
		$DB_web-> query("INSERT INTO xshare SET id_parent='{$temp[1]}' , nom='{$nom}' , lien='{$filename}' $version $importance $site $licence $descript");
	
		echo "<commentaire>Fichier ajouté</commentaire>";
	}
	if ($temp[0]=='suppr') {
		$DB_web->query("SELECT lien FROM xshare WHERE id='{$temp[1]}' ");
		list($dir) = $DB_web->next_row();
		unlink(BASE_DATA."xshare/".$dir);
		$DB_web->query("DELETE FROM xshare WHERE id='{$temp[1]}'");
		echo "<warning>Fichier Supprimé</warning>";
	}
}


//
// Corps du Documents
//---------------------------------------------------

if(isset($_REQUEST['affich_elt'])) define("AFFICH_ELT",base64_decode($_REQUEST['affich_elt'])) ; else define("AFFICH_ELT",'');

//
//Petit programme recursif
// pour parcourir l'arbre
// vers le bas
//------------------------------
function rech_fils($id_parent) {
	global $DB_web, $a_marquer ; 

	if (affiche_element_xshare($id_parent)) {			// on continue l'affichage ssi on demande l'affichage

		
	
		// affichage des folders et recherche de leurs fils 
		//----------------------------------

		$DB_web->query("SELECT id,nom FROM xshare WHERE descript='' AND id_parent='{$id_parent}'") ;
		while(list($id,$nom) = $DB_web->next_row()) {
				echo "<noeud  id='".$id."' titre='".htmlspecialchars($nom,ENT_QUOTES)."' lien='xshare/index.php?affich_elt=".base64_encode(all_elt_affich($id)) ;
			if ($a_marquer != "") echo "&amp;a_marquer=".base64_encode($a_marquer);
			echo "' >\n\r" ;
			if (eregi("/".$id."/",$a_marquer)) {
				echo "<image source='skins/".$_SESSION['skin']['skin_nom']."/fleche_folder.gif'/>\n\r" ;
			}
			rech_fils($id) ;
			echo "\n\r</noeud>\n\r " ;
		}
		
		// affichage des vrais fichiers !
		//------------------------------------
		
		$DB_web->query("SELECT id,nom FROM xshare WHERE descript!='' AND id_parent='{$id_parent}'" ) ;
		while(list($id,$nom) = $DB_web->next_row()) {
			echo "\n\r<feuille  id='".$id."'  titre='".htmlspecialchars($nom,ENT_QUOTES)."' lien='xshare/index.php?affich_elt=".base64_encode(all_elt_affich($id))."&amp;idpopup=".$id;
			if ($a_marquer != "") echo "&amp;a_marquer=".base64_encode($a_marquer) ;
			echo "#descript'>\n\r" ;
			if (eregi("/".$id."/",$a_marquer)) {
				echo "<image source='skins/".$_SESSION['skin']['skin_nom']."/fleche.gif'/>\n\r" ;
			}
			echo "</feuille>\n\r" ;
		}
	}
}


//
//Petit programme qui verifie
// si on affiche ou non le
// du folder !!!
//------------------------------

function affiche_element_xshare($idfold){
	
	if ($idfold == 0) return 1 ;			// on affiche toujours la racine !!
	$ids = explode("/",AFFICH_ELT) ;
	for ($i=0 ; $i<count($ids) ; $i++) {
		if (intval($ids[$i]) == $idfold) return 1 ;
	}
	return 0 ;
}

//
//Petit programme qui crée
// la liste des folder à 
// afficher !
//------------------------------

function all_elt_affich($idfold){

	$ids = explode("/",AFFICH_ELT) ;
	$str = "0" ;
	$retire = 0 ;
	for ($i=0 ; $i<count($ids) ; $i++) {		// on parcours tous les element et on les re-rajoute avce condition ...
		if ((intval($ids[$i]) != $idfold)) {
			if (intval($ids[$i]) != 0) $str .= "/".$ids[$i] ;
		} else {
			$retire = 1 ;			// si on reclique sur le lien c'est kon veu le supprimer
		}
		
	}
	if (!$retire) $str .= "/".$idfold ;		// si je ne l'ai pas supprimé c'est k'il faut le rajouter justement !
	
	return $str ;
}

//////////////////////////////
//
// Recherche
//
////////////////////////////////////////////////


//
//Petit programme 
// pour parcourir l'arbre
// vers le haut
//------------------------------

function rech_parent($id) {
		global $DB_web;
		$liste = $id."/" ;
		while ($id != 0) {
			$DB_web->query("SELECT id_parent FROM xshare WHERE id='{$id}'") ;
			$id = $DB_web->next_row() ;
			if (($id != "")&&($id != 0)){ // on rajoute ssi c'est pas le racine
				$liste .= $id."/";		  // car on la deja rajouté !
			}
		}
		return $liste ; 
}
////////////////////////////////////////////////////////////////////////////////
//
// Corps du document
//
/////////////////////////////////////////////////////////////////////////////////
?>
  
<p align="left">&nbsp;</p>

      

<p><strong>Visualisation des différents téléchargements : </strong> </p>
<?

//
// on affiche  l'arbre 
// simple maintenant
//----------------------
echo "<arbre>";
rech_fils(0) ;
echo "</arbre>";
echo "<br/>" ;

?>



<?

//
// Corps du Documents pour les réponses
//---------------------------------------------------

  	if(isset($_REQUEST['idpopup'])) $id = $_REQUEST['idpopup'] ;
  	if ($id != "") {
		$DB_web->query("SELECT * FROM xshare WHERE id='{$id}'") ;
		if (list($id,$id_parent,$nom,$licence,$lien,$importance,$date,$descript,$version,$site) = $DB_web->next_row()) {
	?>
	<a name='descript' />
	<formulaire id="xshare_<? echo $id ?>" titre="Le logiciel" action="admin/xshare.php">
	<champ id="nom" titre="Nom du logiciel" valeur="<? echo $nom ?>" />
	<champ id="version" titre="Version" valeur="<? echo $version ?>" />
	<champ id="lien" titre="Lien de téléchargement" valeur="<? echo $lien ?>" />
	<choix titre="Importance" id="importance" type="combo" valeur="<? echo $importance ?>">
				<option id="0" titre="Normal"/>
				<option id="1" titre="Important"/>
				<option id="2" titre="Indispensable"/>
	</choix>
	<champ id="site" titre="Site de l'éditeur" valeur="<? echo $site ?>" />
	<champ id="licence" titre="Licence" valeur="<? echo $licence ?>" />
	<zonetext id="descript" titre="Description" valeur="<? echo $descript ;?>"/>
	<bouton id='modif_<? echo $id ?>' titre="Modifier"/>
	<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer ce logiciel ?!!!!!')"/>
	</formulaire>
	<? 
	
		} else {
	?>
	<p><strong>Erreur</strong> : Impossible de trouver cette description </p>
	<?
		}
		echo "<br/><br/>" ;
	}
	else
	{
	?>
	<!-- Supprimer le dossier en cours -->
	<? if(isset($_REQUEST['dir_id'])) $dir_id = $_REQUEST['dir_id']; else $dir_id="0"; ?>
	<formulaire id="xshare_<? echo $dir_id ?>" titre="Supprimer ce dossier" action="admin/xshare.php">
	<? foreach ($_GET AS $keys => $val){
		if((!strstr($keys,"rmdir"))&&(!strstr($keys,"rmdir"))) echo "<hidden id=\"".$keys."\" valeur=\"".$val."\" />";
		}
	?>
	<bouton id='rmdir_<? echo  $dir_id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer ce répertoire et tous ses fichiers ?!!!!!')"/>
	</formulaire>
	
	<!-- Ajouter un sous-dossier -->
	<formulaire id="xshare_<? echo $dir_id ?>" titre="Ajouter un sous-dossier" action="admin/xshare.php">
	<champ id="nom" titre="Nom du sous-dossier" valeur="" />
	<? foreach ($_GET AS $keys => $val){
		if(!strstr($keys,"adddir")) echo "<hidden id=\"".$keys."\" valeur=\"".$val."\" />";
		}
	?>
	<bouton id='adddir_<? echo $dir_id ?>' titre='Ajouter' onClick="return window.confirm('!!!!!!Créer ce répertoire ?!!!!!')"/>
	</formulaire>
	
	<!-- Ajouter un fichier -->
	<formulaire id="xshare_<? echo $dir_id ?>" titre="Le logiciel" action="admin/xshare.php">
	<champ id="nom" titre="Nom du logiciel" valeur="" />
	<champ id="version" titre="Version" valeur="" />
	<choix titre="Importance" id="importance" type="combo" valeur="0">
				<option id="0" titre="Normal"/>
				<option id="1" titre="Important"/>
				<option id="2" titre="Indispensable"/>
	</choix>
	<champ id="site" titre="Site de l'éditeur" />
	<champ id="licence" titre="Licence" />
	<zonetext id="descript" titre="Description" />
	<? foreach ($_GET AS $keys => $val){
		if(!strstr($keys,"ajout")) echo "<hidden id=\"".$keys."\" valeur=\"".$val."\" />";
	}
	?>
	<fichier id="file" titre="Fichier" taille="1000000"/>
	<bouton id='ajout_<? echo $dir_id ?>' titre="Ajouter" onClick="return window.confirm('!!!!!!Ajouter ce fichier ?!!!!!')"/>
	</formulaire>
	
<?
	}
//
// Pied de page ...
//---------------------------------------------------
?>

</page>
<?php

require_once BASE_LOCAL."/include/page_footer.inc.php";
?>