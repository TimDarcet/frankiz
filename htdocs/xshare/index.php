<? 
/*
		$Log$
		Revision 1.16  2004/10/20 22:28:27  pico
		Encore des corrections

		Revision 1.15  2004/10/20 22:10:06  pico
		BugFix (devrait marcher)
		
		Revision 1.14  2004/10/20 22:05:55  pico
		Changements Noeuds/Feuilles
		
		Revision 1.13  2004/10/20 20:00:37  pico
		Génération des balises plus conforme
		
		Revision 1.12  2004/10/19 22:04:23  pico
		Pas d'aut
		+ Fix
		
		Revision 1.11  2004/10/19 07:56:56  pico
		Corrections diverses
		
		Revision 1.10  2004/10/18 23:37:03  pico
		BugFix Recherche (pas 2 requetes sql en même temps !)
		
		Revision 1.9  2004/10/18 22:17:45  pico
		Ajout des logs dans le fichier
		
*/
require_once "../include/global.inc.php";

// Vérification des droits
// demande_authentification(AUTH_MINIMUM);


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="xshare" titre="Frankiz : Xshare">
<h1>Xshare</h1>

<?
//
// Corps du Documents
//---------------------------------------------------
if(isset($_REQUEST['mots'])) $mots=$_REQUEST['mots'] ; else $mots='';
if(isset($_REQUEST['affich_elt'])) $affich_elt = base64_decode($_REQUEST['affich_elt']); else $affich_elt ="";
if(isset($_REQUEST['a_marquer'])) $a_marquer = base64_decode($_REQUEST['a_marquer']) ; else $a_marquer ="";


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
	global $affich_elt;
	if ($idfold == 0) return 1 ;			// on affiche toujours la racine !!
	$ids = explode("/",$affich_elt) ;
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
	global $affich_elt;
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

      

<?
// Entete !
//-------------------------

if (($mots!="")||($a_marquer!="")) {
?>
<u><strong>Recherche</strong></u><br/><br/>
<?
} else {
?>
<p><strong>Visualisation des différents téléchargements : </strong> </p>
<?
}

//////////////////////////////
//
// Recherche
//
////////////////////////////////////////////////

if ($mots!="") {
	$DB_web->query("SELECT id,nom,descript FROM xshare") ;
	$recherche = 0 ;
	$a_marquer = "/" ;			// liste des elements qui contiendront les mots
	$a_afficher = "0/" ;		// liste des elements à afficher
	while(list($id,$nom,$descript) = $DB_web->next_row()) {
		$result = explode(" ",$mots) ;
		$n = count($result) ;
		for ($i=0 ; $i<$n ; $i++){ 			// on regarde dans chaque dl si il y a les mots ...
			if ((eregi($result[$i],$descript))||(eregi($result[$i],$nom))) {
				$DB_web->push_result();
				$a_afficher = $a_afficher.rech_parent($id) ;
				$DB_web->pop_result();
				$a_marquer = $a_marquer.$id."/" ;
				$recherche = 1 ;
			}
		}
	}
	if (!$recherche) {
?>
  <font color="#000066">Recherche infructueuse ...</font>
essayer avec d'autres crit&egrave;res 
  <?
		define("$affich_elt" , "") ;
		define ("$a_marquer","");
	} else {
		define ("$affich_elt", $a_afficher) ;
		define ("$a_marquer",$a_marquer);
		rech_fils(0) ; 
	}
} else {

//
// on affiche  l'arbre 
// simple maintenant
//----------------------
echo "<arbre>";
rech_fils(0) ;
echo "</arbre>";
}
echo "<br/>" ;

?>


        <formulaire id="form" action="xshare/index.php">
            <champ id="mots" titre="Mots-clefs" valeur="<? echo $mots ;?>"/>
            <bouton id="Submit" titre="Valide"/>
            <bouton id="reset" titre="Reset"/>
        </formulaire>
        <commentaire>(Tous les mots seront dans la description / Séparez les par un blanc) </commentaire>
        </p>
<?

//
// Corps du Documents pour les réponses
//---------------------------------------------------

  	if(isset($_REQUEST['idpopup'])) $id = $_REQUEST['idpopup'] ; else $id ="";
  	if ($id != "") {
		$DB_web->query("SELECT * FROM xshare WHERE id='{$id}'") ;
		if (list($id,$id_parent,$nom,$licence,$lien,$importance,$date,$descript,$version,$site) = $DB_web->next_row()) {
	?>
	<a name='descript' />
	<? 
	echo "<h2><lien titre='".$nom."' url='../data/xshare/".$lien."'/></h2>";
	if($importance == 1) echo "<p>Important</p>";
	if($importance == 2) echo "<p><strong>Indispensable</strong></p>";
	echo "<em><lien titre='site de l&apos;éditeur' url='".$site."'/></em>";
	echo "<p>Dernière modification le ".substr($date, 6, 2)."/".substr($date, 4, 2)."/".substr($date, 0, 4)."</p>" ;
	if($version != '') echo "<p>Version: ".$version."</p>";
	if($licence != '') echo "<p>Licence: ".$licence."</p>";
	echo "<p>Description: ".$descript."</p>";

	
		} else {
	?>
	<warning>Erreur : Impossible de trouver cette description </warning>
	<?
		}
		echo "<br/><br/>" ;
	}


//
// Pied de page ...
//---------------------------------------------------
?>

</page>
<?php

require_once BASE_LOCAL."/include/page_footer.inc.php";
?>