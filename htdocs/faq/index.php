<? 
/*
		$Log$
		Revision 1.6  2004/10/18 22:17:45  pico
		Ajout des logs dans le fichier

*/
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MINIMUM);


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="faq" titre="Frankiz : FAQ">
<h1>FAQ</h1>

<?
//
// Corps du Documents
//---------------------------------------------------

if(isset($_REQUEST['affich_elt'])) define("AFFICH_ELT",base64_decode($_REQUEST['affich_elt'])) ; else define("AFFICH_ELT",'');
if(isset($_REQUEST['a_marquer'])) define("A_MARQUER",base64_decode($_REQUEST['a_marquer'])) ; else define("A_MARQUER","");
if(isset($_REQUEST['mots'])) define("MOTS",$_REQUEST['mots']) ; else define("MOTS",'');

//
//Petit programme recursif
// pour parcourir l'arbre
// vers le bas
//------------------------------

function rech_fils($parent) {
	global $DB_web ; 

	if (affiche_element_faq($parent)) {			// on continue l'affichage ssi on demande l'affichage

		if ($parent!=0) {
			echo "<noeud class='foldinglist'>\n\r" ;
		} else {
			echo "<noeud>\n\r" ;
		}
	
		// affichage des folders et recherche de leurs fils 
		//----------------------------------
		$DB_web->query("SELECT faq_id,question FROM faq WHERE parent='{$parent}' AND reponse NOT LIKE '%index.php' ") ;
		while(list($id,$question) = $DB_web->next_row()) {
			if (affiche_element_faq($id)) {
				echo "<feuille class='foldheader2'>\n\r";		// folder open
			} else {
				echo "<feuille class='foldheader1'>\n\r";		// folder fermé
			}
			echo "<a  name=\"".$id."\"/>" ;
			echo "<lien  url='faq/index.php?affich_elt=".base64_encode(all_elt_affich($id)) ;
			if (A_MARQUER != "") echo "&a_marquer=".base64_encode(A_MARQUER) ;
			echo "#".$id."' titre='".htmlspecialchars($question,ENT_QUOTES)."'/>" ;
			if (eregi("/".$id."/",A_MARQUER)) {
				echo "<image source='./faq_fleche_folder.gif'/>" ;
			}
			echo "\n\r</feuille>\n\r " ;
			rech_fils($id) ;
		}
		
		// affichage des vrais questions !
		//------------------------------------
		
		$DB_web->query("SELECT faq_id,question FROM faq WHERE parent='{$parent}' AND reponse LIKE '%index.php'" ) ;
		while(list($id,$question) = $DB_web->next_row()) {
			echo "\n\r<feuille>\n\r" ;
			echo "<lien  url='faq/index.php?affich_elt=".base64_encode(all_elt_affich($id))."&amp;idpopup=".$id ;
			if (A_MARQUER != "") echo "&a_marquer=".base64_encode(A_MARQUER) ;
			echo "#reponse' titre='".htmlspecialchars($question,ENT_QUOTES)."'/>" ;
			if (eregi("/".$id."/",A_MARQUER)) {
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
			$DB_web->query("SELECT parent FROM faq WHERE faq_id='{$id}'") ;
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

if ((MOTS!="")||(A_MARQUER!="")) {
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

if (MOTS!="") {
	$DB_web->query("SELECT faq_id,question,reponse FROM faq") ;
	$recherche = 0 ;
	${A_MARQUER} = "/" ;			// liste des elements qui contiendront les mots
	$a_afficher = "0/" ;		// liste des elements à afficher
	while(list($id,$question,$reponse) = $DB_web->next_row()) {
		$result = explode(" ",MOTS) ;
		$n = count($result) ;
		for ($i=0 ; $i<$n ; $i++){ 			// on regarde dans chaque FAQ si il y a les mots ...
			if ((eregi($result[$i],$reponse))||(eregi($result[$i],$question))) {
				$a_afficher .= rech_parent($id) ;
				${A_MARQUER} .= $id."/" ;
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
		${AFFICH_ELT} = $a_afficher ;
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
            <champ id="mots" titre="Mots-clefs" valeur="<? echo MOTS ;?>"/>
            <bouton id="Submit" titre="Valide"/>
            <bouton id="reset" titre="Reset"/>
        </formulaire>
        <p><em>(Tous les mots seront dans la description 
          / Séparez les par un blanc) </em>
        </p>
<?

//
// Corps du Documents pour les réponses
//---------------------------------------------------

  	if(isset($_REQUEST['idpopup'])) $id = $_REQUEST['idpopup'] ;
  	if ($id != "") {
		$DB_web->query("SELECT question,reponse FROM faq WHERE faq_id='{$id}'") ;
		if (list($question,$reponse) = $DB_web->next_row()) {
	?>
	<a name='reponse' />
	<? 
	echo "<h2>Q: ".$question."</h2>" ;
	echo "<br/>";
	$repfaq = "../../data/faq/".$reponse."";
	echo "<cadre>";
	include($repfaq);
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