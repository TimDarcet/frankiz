<? 

require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_MINIMUM);


// G�n�ration de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="faq" titre="Frankiz : FAQ">
<h1>FAQ</h1>

<?
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

function rech_fils($idparent) {
	global $a_marquer,$DB_faq ; 

	if (affiche_element_faq($idparent)) {			// on continue l'affichage ssi on demande l'affichage

		if ($idparent!=0) {
			echo "<noeud class='foldinglist'>\n\r" ;
		} else {
			echo "<noeud>\n\r" ;
		}
	
		// affichage des folders et recherche de leurs fils 
		//----------------------------------

		$DB_faq->query("SELECT id,question FROM faq WHERE reponse='' AND idparent='{$idparent}'") ;
		while(list($id,$question) = $DB_faq->next_row()) {
			if (affiche_element_faq($id)) {
				echo "<feuille class='foldheader2'>\n\r";		// folder open
			} else {
				echo "<feuille class='foldheader1'>\n\r";		// folder ferm�
			}
			echo "<a  name=\"".$id."\"/>" ;
			echo "<lien  url='faq/index.php?affich_elt=".base64_encode(all_elt_affich($id)) ;
			if ($a_marquer != "") echo "&a_marquer=".base64_encode($a_marquer) ;
			echo "#".$id."' titre='".htmlentities($question,ENT_QUOTES)."'/>" ;
			if (eregi("/".$id."/",$a_marquer)) {
				echo "<image source='./faq_fleche_folder.gif'/>" ;
			}
			echo "\n\r</feuille>\n\r " ;
			rech_fils($id) ;
		}
		
		// affichage des vrais questions !
		//------------------------------------
		
		$DB_faq->query("SELECT id,question FROM faq WHERE reponse!='' AND idparent='{$idparent}'" ) ;
		while(list($id,$question) = $DB_faq->next_row()) {
			echo "\n\r<feuille>\n\r" ;
			echo "<lien  url='faq/index.php?affich_elt=".base64_encode(all_elt_affich($id))."&amp;idpopup=".$id ;
			if ($a_marquer != "") echo "&a_marquer=".base64_encode($a_marquer) ;
			echo "#reponse' titre='".htmlentities($question,ENT_QUOTES)."'/>" ;
			if (eregi("/".$id."/",$a_marquer)) {
				echo "<image source='./faq_fleche.gif'/>" ;
			}
			echo "</feuille>\n\r" ;
		}
		echo "</noeud>\n\r" ;
	}
}

//
//Petit programme qui verifie
// si on affiche ou non le
// du folder !!!
//------------------------------

function affiche_element_faq($idfold){
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
		global $DB_faq;
		$liste = $id."/" ;
		while ($id != 0) {
			$DB_faq->query("SELECT idparent FROM faq WHERE id='{$id}'") ;
			$id = $DB_faq->next_row() ;
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

      

<?
// Entete !
//-------------------------

if (($mots!="")||($a_marquer!="")) {
?>
<u><strong>Recherche</strong></u><br/><br/>
<?
} else {
?>
<p><strong>Visualisation des diff&eacute;rentes FAQ : </strong> </p>
<?
}

//////////////////////////////
//
// Recherche
//
////////////////////////////////////////////////

if ($mots!="") {
	$DB_faq->query("SELECT id,question,reponse FROM faq") ;
	$recherche = 0 ;
	$a_marquer = "/" ;			// liste des elements qui contiendront les mots
	$a_afficher = "0/" ;		// liste des elements � afficher
	while(list($id,$question,$reponse) = $DB_faq->next_row()) {
		$result = explode(" ",$mots) ;
		$n = count($result) ;
		for ($i=0 ; $i<$n ; $i++){ 			// on regarde dans chaque FAQ si il y a les mots ...
			if ((eregi($result[$i],$reponse))||(eregi($result[$i],$question))) {
				$a_afficher .= rech_parent($id) ;
				$a_marquer .= $id."/" ;
				$recherche = 1 ;
			}
		}
	}
	if (!$recherche) {
?>
  <font color="#000066">Recherche infructueuse ...</font>
essayer avec d'autres crit&egrave;res 
  <?
	} else {
		$affich_elt = $a_afficher ;
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


        <formulaire id="form" action="faq/faq.php">
            <champ id="mots" titre="Mots-clefs" valeur="<? echo $mots ;?>"/>
            <bouton id="Submit" titre="Valide"/>
            <bouton id="reset" titre="Reset"/>
        </formulaire>
        <p><em>(Tous les mots seront dans la description 
          / S�parez les par un blanc) </em>
        </p>
<?

//
// Corps du Documents pour les r�ponses
//---------------------------------------------------

  	if(isset($_REQUEST['idpopup'])) $id = $_REQUEST['idpopup'] ;
  	if ($id != "") {
		$DB_faq->query("SELECT question,reponse FROM faq WHERE id='{$id}'") ;
		if (list($question,$reponse) = $DB_faq->next_row()) {
	?>
	<a name='reponse' />
	<? 
	echo "<h2>Q: ".$question."</h2>" ;
	echo "<br/>";
	$repfaq = "./faq-data/".$reponse."";
	echo "<cadre>";
	include("./faq-data/".$reponse."index.php");
	echo "</cadre>";
	
		} else {
	?>
	<p><strong>Erreur</strong> : Impossible de trouver cette question </p>
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