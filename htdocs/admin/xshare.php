<? 

require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_MINIMUM);


// G�n�ration de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="xshare" titre="Frankiz : Xshare">
<h1>Xshare</h1>

<?
foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

	// Supprimer le r�pertoire
	
	if ($temp[0]=='rmdir') {
		//echo "<p>temp: ".$temp[1]."</p>";
		$DB_web->query("SELECT lien FROM xshare WHERE id='{$temp[1]}' ");
		list($dir) = $DB_web->next_row();
		$dir = BASE_DATA."xshare/".$dir;
		//echo "<p>dir: ".$dir."</p>";
		foreach(glob($dir."/*") as $fn) {
			unlink($fn);
		//echo "<p>File: $fn</p>";
		} 
		rmdir($dir);
		$DB_web->query("DELETE FROM xshare WHERE id='{$temp[1]}'");
		$DB_web->query("DELETE FROM xshare WHERE id_parent='{$temp[1]}'");
		echo "<warning>Repertoire Supprim�</warning>";
	}
}


//
// Corps du Documents
//---------------------------------------------------

if(isset($_REQUEST['affich_elt'])) $affich_elt = base64_decode($_REQUEST['affich_elt']) ; else $affich_elt = '';
if(isset($_REQUEST['a_marquer'])) $a_marquer = base64_decode($_REQUEST['a_marquer']) ; else $a_marquer = '';

if(isset($_REQUEST['mots'])) $mots = $_REQUEST['mots'] ; else $mots = '';


//
//Petit programme recursif
// pour parcourir l'arbre
// vers le bas
//------------------------------

function rech_fils($id_parent) {
	global $a_marquer,$DB_web ; 

	if (affiche_element_xshare($id_parent)) {			// on continue l'affichage ssi on demande l'affichage

		if ($id_parent!=0) {
			echo "<ul class='foldinglist'>\n\r" ;
		} else {
			echo "<ul>\n\r" ;
		}
	
		// affichage des folders et recherche de leurs fils 
		//----------------------------------

		$DB_web->query("SELECT id,nom FROM xshare WHERE descript='' AND id_parent='{$id_parent}'") ;
		while(list($id,$nom) = $DB_web->next_row()) {
			if (affiche_element_xshare($id)) {
				echo "<li class='foldheader2'>\n\r";		// folder open
			} else {
				echo "<li class='foldheader1'>\n\r";		// folder ferm�
			}
			echo "<a name=\"".$id."\"/>" ;
			echo "<lien titre='".$nom."' url='admin/xshare.php?affich_elt=".base64_encode(all_elt_affich($id)) ;
			if ($a_marquer != "") echo "&amp;a_marquer=".base64_encode($a_marquer) ;
			echo "&amp;dir_id=".$id."#".$id."' />" ;
			if (eregi("/".$id."/",$a_marquer)) {
				echo "<image source='./xshare_fleche_folder.gif'/>" ;
			}
			echo "\n\r</li>\n\r " ;
			rech_fils($id) ;
		}
		
		// affichage des vrais fichiers !
		//------------------------------------
		
		$DB_web->query("SELECT id,nom FROM xshare WHERE descript!='' AND id_parent='{$id_parent}'" ) ;
		while(list($id,$nom) = $DB_web->next_row()) {
			echo "\n\r<li class='question'>\n\r" ;
			echo "<lien titre='".htmlentities($nom,ENT_QUOTES)."' url='admin/xshare.php?affich_elt=".base64_encode(all_elt_affich($id))."&amp;idpopup=".$id;
			if ($a_marquer != "") echo "&amp;a_marquer=".base64_encode($a_marquer) ;
			echo "#descript'/>" ;
			if (eregi("/".$id."/",$a_marquer)) {
				echo "<image source='./xshare_fleche.gif'/>" ;
			}
			echo "</li>\n\r" ;
		}
		echo "</ul>\n\r" ;
	}
}

//
//Petit programme qui verifie
// si on affiche ou non le
// du folder !!!
//------------------------------

function affiche_element_xshare($idfold){
	global $affich_elt  ;
	
	if ($idfold == 0) return 1 ;			// on affiche toujours la racine !!
	$ids = explode("/",$affich_elt) ;
	for ($i=0 ; $i<count($ids) ; $i++) {
		if (intval($ids[$i]) == $idfold) return 1 ;
	}
	return 0 ;
}

//
//Petit programme qui cr�e
// la liste des folder � 
// afficher !
//------------------------------

function all_elt_affich($idfold){
	global $affich_elt ;

	$ids = explode("/",$affich_elt) ;
	$str = "0" ;
	$retire = 0 ;
	for ($i=0 ; $i<count($ids) ; $i++) {		// on parcours tous les element et on les re-rajoute avce condition ...
		if ((intval($ids[$i]) != $idfold)) {
			if (intval($ids[$i]) != 0) $str .= "/".$ids[$i] ;
		} else {
			$retire = 1 ;			// si on reclique sur le lien c'est kon veu le supprimer
		}
		
	}
	if (!$retire) $str .= "/".$idfold ;		// si je ne l'ai pas supprim� c'est k'il faut le rajouter justement !
	
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
				$liste .= $id."/";		  // car on la deja rajout� !
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

      

<p><strong>Visualisation des diff&eacute;rents t�l�chargements : </strong> </p>
<?

//
// on affiche  l'arbre 
// simple maintenant
//----------------------

rech_fils(0) ;

echo "<br/>" ;

?>



<?

//
// Corps du Documents pour les r�ponses
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
	<champ id="lien" titre="Lien de t�l�chargement" valeur="<? echo $lien ?>" />
	<choix titre="Importance" id="importance" type="combo" valeur="<? echo $importance ?>">
				<option id="0" titre="Normal"/>
				<option id="1" titre="Important"/>
				<option id="2" titre="Indispensable"/>
	</choix>
	<champ id="site" titre="Site de l'�diteur" valeur="<? echo $site ?>" />
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
	<formulaire id="xshare_<? echo $_REQUEST['dir_id'] ?>" titre="Supprimer ce dossier" action="admin/xshare.php">
	<? foreach ($_GET AS $keys => $val){
		if((!strstr($keys,"adddir"))&&(!strstr($keys,"rmdir"))) echo "<hidden id=\"".$keys."\" valeur=\"".$val."\" />";
		}
	?>
	<bouton id='rmdir_<? echo  $_REQUEST['dir_id'] ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer ce r�pertoire et tous ses fichiers ?!!!!!')"/>
	</formulaire>
	
	<!-- Ajouter un sous-dossier -->
	<formulaire id="xshare_<? echo $_REQUEST['dir_id'] ?>" titre="Ajouter un sous-dossier" action="admin/xshare.php">
	<champ id="nom" titre="Nom du sous-dossier" valeur="" />
	<? foreach ($_GET AS $keys => $val){
		if(!strstr($keys,"adddir")) echo "<hidden id=\"".$keys."\" valeur=\"".$val."\" />";
		}
	?>
	<bouton id='adddir_<? echo $_REQUEST['dir_id'] ?>' titre='Ajouter' onClick="return window.confirm('!!!!!!Cr�er ce r�pertoire ?!!!!!')"/>
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