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
		$Id$

*/
require_once "include/global.inc.php";
require_once "include/wiki.inc.php";
// Vérification des droits
//demande_authentification(AUTH_MINIMUM);


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

if(isset($_REQUEST['mots'])) $mots=$_REQUEST['mots'] ; else $mots='';
if(isset($_REQUEST['affich_elt'])) $affich_elt = base64_decode($_REQUEST['affich_elt']); else $affich_elt ="";
if(isset($_REQUEST['a_marquer'])) $a_marquer = base64_decode($_REQUEST['a_marquer']) ; else $a_marquer ="";
//
//Petit programme recursif
// pour parcourir l'arbre
// vers le bas
//------------------------------

function rech_fils($parent) {
	global $DB_faq,$a_marquer ; 

	if (affiche_element_faq($parent)) {			// on continue l'affichage ssi on demande l'affichage
	
		// affichage des folders et recherche de leurs fils 
		//----------------------------------
		$DB_faq->query("SELECT faq_id,question FROM faq WHERE (parent='{$parent}' AND NOT  (reponse LIKE '%index.php' OR reponse LIKE '%index.html')) ORDER BY question") ;
		while(list($id,$question) = $DB_faq->next_row()) {
			echo "<noeud id='".$id."' ";
			$DB_faq->push_result();
			echo "lien='faq.php?affich_elt=".base64_encode(all_elt_affich($id)) ;
			$DB_faq->pop_result();
			if ($a_marquer != "") echo "&amp;a_marquer=".base64_encode($a_marquer) ;
			echo "' titre='".$question."'>" ;
			if (eregi("/".$id."/",$a_marquer)) {
				echo "<image source='images/fleche_folder.gif' texte=\"marqué\"/>" ;
			}
			$DB_faq->push_result();
			rech_fils($id) ;
			$DB_faq->pop_result();
			echo "\n\r</noeud>\n\r " ;
		}
		
		// affichage des vrais questions !
		//------------------------------------
		
		$DB_faq->query("SELECT faq_id,question FROM faq WHERE ((parent='{$parent}') AND (reponse LIKE '%index.php' OR reponse LIKE '%index.html')) ORDER BY question" ) ;
		while(list($id,$question) = $DB_faq->next_row()) {
			$DB_faq->push_result();
			echo "\n\r<feuille lien='faq.php?affich_elt=".base64_encode(all_elt_affich($id))."&amp;idpopup=".$id ;
			$DB_faq->pop_result();
			if ($a_marquer != "") echo "&amp;a_marquer=".base64_encode($a_marquer) ;
			echo "#reponse' titre='".$question."'>" ;
			if (eregi("/".$id."/",$a_marquer)) {
				echo "<image source='images/fleche.gif' texte=\"marqué\"/>" ;
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

function affiche_element_faq($idfold){
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

function rech_parent($id2) {
		global $DB_faq;
		$liste="";
		$id = $id2;
		while ($id != 0) {
			$DB_faq->query("SELECT parent FROM faq WHERE faq_id='{$id}'") ;
			while(list($id3) = $DB_faq->next_row()){
				$id =$id3;
				if (($id != "")&&($id != 0)){ // on rajoute ssi c'est pas le racine
					$liste = $liste."/".$id;		  // car on la deja rajouté !
				}
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
  
<?
// Entete !
//-------------------------

if (($mots!="")||($a_marquer!="")) {
?>
<p><strong>Recherche</strong></p>
<?
} else {
?>
<p><strong>Liste des différentes questions de la FAQ :</strong></p>
<?
}

//////////////////////////////
//
// Recherche
//
////////////////////////////////////////////////

if ($mots!="") {
	$DB_faq->query("SELECT faq_id,question,reponse FROM faq") ;
	$recherche = 0 ;
	$a_marquer = "/" ;			// liste des elements qui contiendront les mots
	$affich_elt = "0" ;		// liste des elements à afficher
	$result = explode(" ",$mots) ;
	$n = count($result) ;
	while(list($id,$question,$reponse) = $DB_faq->next_row()) {
		for ($i=0 ; $i<$n ; $i++){ 			// on regarde dans chaque FAQ si il y a les mots ...
			if ((eregi($result[$i],$reponse))||(eregi($result[$i],$question))) {
				$DB_faq->push_result();
				$affich_elt = $affich_elt.rech_parent($id);
				$DB_faq->pop_result();
				$a_marquer = $a_marquer.$id."/" ;
				$recherche = 1 ;
			}
		}
	}
	if (!$recherche)
		echo "<warning>Recherche infructueuse. Essayer avec d'autres critères.</warning>\n";
}

//
// on affiche  l'arbre 
// simple maintenant
//----------------------
echo "<arbre>";
rech_fils(0) ;
echo "</arbre>";

?>


<formulaire id="form" action="faq.php">
	<note>Tous les mots seront dans la description. Sépare les par un blanc.</note>
	<champ id="mots" titre="Mots-clefs" valeur="<? echo $mots ;?>"/>
	<bouton id="Submit" titre="Chercher"/>
</formulaire>
<?

//
// Corps du Documents pour les réponses
//---------------------------------------------------

if(isset($_REQUEST['idpopup'])) $id = $_REQUEST['idpopup'] ; else $id = "";
if ($id != "") {
	$DB_faq->query("SELECT question,reponse FROM faq WHERE faq_id='{$id}'") ;
	if (list($question,$reponse) = $DB_faq->next_row()) {
		$repfaq = BASE_DATA."/faq/".$reponse;
		echo "<cadre titre=\"Q: ".$question."\" id=\"reponse\">\n";
		if(file_exists($repfaq)){
			if($texte = fopen($repfaq,"r")){
				$wiki = '';
				while(!feof($texte))
				{
					$ligne = fgets($texte,2000);
					// Remplace les liens locaux pour les images et les liens, car sinon conflit avec le BASE_HREF
					$patterns[0] ='(\[(?!http://)(?!ftp://)(?!#))';
					$patterns[1] ='(\[#)';
					$replacements[1] = '['.dirname(URL_DATA."faq/$reponse")."/";
					$replacements[0] = '['.getenv('SCRIPT_NAME')."?".getenv('QUERY_STRING')."#";
					$ligne = str_replace(array('&','<','>','\'','"','\\'),array('&amp;','&lt;','&gt;','&apos;','&quot;',''),$ligne);
					$ligne = preg_replace($patterns,$replacements, $ligne);
					$wiki.= $ligne;
				}
				print(wikiVersXML($wiki));
				fclose($texte);
				if (est_authentifie(AUTH_MINIMUM)) {
					?>
					<lien url="proposition/faq_modif.php?id=<?=$id?>" titre="Éditer"/>
					<?
				}
			}
		} else {
		?>
			<warning>Erreur : Impossible de trouver cette question </warning>
		<?
		}
		echo "\n</cadre>";	
	} else {
	?>
		<warning>Erreur : Impossible de trouver cette question </warning>
	<?
	}
}


//
// Pied de page ...
//---------------------------------------------------
?>
<note>Pour tout commentaire, contactez les <lien url="mailto:<?= MAIL_FAQMESTRE ?>" titre="FaqMestres"/></note>
</page>
<?php

require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
