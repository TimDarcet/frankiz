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
// demande_authentification(AUTH_COOKIE);


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="xshare" titre="Frankiz : Xshare">
<h1>Xshare</h1>

<?php
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

		$DB_web->query("SELECT id,nom FROM xshare WHERE descript='' AND id_parent='{$id_parent}' ORDER BY nom") ;
		while(list($id,$nom) = $DB_web->next_row()) {
				echo "<noeud  id='".$id."' titre='".$nom."' lien='xshare.php?affich_elt=".base64_encode(all_elt_affich($id)) ;
			if ($a_marquer != "") echo "&amp;a_marquer=".base64_encode($a_marquer);
			echo "'>\n\r" ;
			if (eregi("/".$id."/",$a_marquer)) {
				echo "<image source='images/fleche_folder.gif' texte=\"marqué\"/>\n\r" ;
			}
			$DB_web->push_result();
			rech_fils($id) ;
			$DB_web->pop_result();
			echo "\n\r</noeud>\n\r " ;
		}
		
		// affichage des vrais fichiers !
		//------------------------------------
		
		$DB_web->query("SELECT id,nom FROM xshare WHERE descript!='' AND id_parent='{$id_parent}' ORDER BY nom" ) ;
		while(list($id,$nom) = $DB_web->next_row()) {
			echo "\n\r<feuille  id='".$id."'  titre='".$nom."'>\n\r" ;
			if (eregi("/".$id."/",$a_marquer)) {
				echo "<image source='images/fleche.gif' texte=\"marqué\"/>\n\r" ;
			}
			$DB_web->push_result();
			$DB_web->query("SELECT id,id_parent,nom,licence,lien,importance,DATE_FORMAT(date,'%d/%m/%Y'),descript,version,site FROM xshare WHERE id='{$id}'") ;
			if (list($id,$id_parent,$nom,$licence,$lien,$importance,$date,$descript,$version,$site) = $DB_web->next_row()) { 
				echo "<lien titre='Site de l&apos;éditeur' url='".$site."'/> | <lien titre='Télécharger ici' url='data/xshare/".$lien."'/><br/><br/>";
				if($importance == 1) echo "Logiciel important<br/>";
				if($importance == 2) echo "<strong>Logiciel indispensable</strong><br/>";
				echo "Dernière modification le $date<br/><br/>" ;
				if($version != '') echo "Version: ".$version."<br/>";
				if($licence != '') echo "Licence: ".$licence."<br/>";
				echo "Description: ".wikiVersXML($descript)."<br/>";
	
		
			} else {
				?>
				<warning>Erreur : Impossible de trouver cette description </warning>
				<?php
			}
			$DB_web->pop_result();
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

function rech_parent($id2) {
		global $DB_web;
		$liste = "" ;
		$id = $id2;
		while ($id != 0) {
			$DB_web->query("SELECT id_parent FROM xshare WHERE id='{$id}'") ;
			while(list($id3) = $DB_web->next_row()){
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
      

<?php
// Entete !
//-------------------------

if (($mots!="")||($a_marquer!="")) {
?>
<p><strong>Résultats de la recherche</strong></p>
<?php
} else {
?>
<p><strong>Logiciels disponibles en téléchargement :</strong></p>
<?php
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
	$affich_elt = "0/" ;		// liste des elements à afficher
	while(list($id,$nom,$descript) = $DB_web->next_row()) {
		$result = explode(" ",$mots) ;
		$n = count($result) ;
		for ($i=0 ; $i<$n ; $i++){ 			// on regarde dans chaque dl si il y a les mots ...
			if ((eregi($result[$i],$descript))||(eregi($result[$i],$nom))) {
				$DB_web->push_result();
				$affich_elt = $affich_elt.rech_parent($id) ;
				$DB_web->pop_result();
				$a_marquer = $a_marquer.$id."/" ;
				$recherche = 1 ;
			}
		}
	}
	if (!$recherche) {
?>
  <warning>Recherche infructueuse. Essayer avec d'autres critères.</warning>
<?php
	} 
}
//
// on affiche  l'arbre 
// simple maintenant
//----------------------
echo "<arbre>";
rech_fils(0) ;
echo "</arbre>";

?>

	<formulaire id="form" action="xshare.php">
		<note>Tous les mots seront dans la description. Sépare les par un blanc.</note>
		<champ id="mots" titre="Mots-clefs" valeur="<?php echo $mots ;?>"/>
		<bouton id="Submit" titre="Chercher"/>
	</formulaire>
<?php

//
// Pied de page ...
//---------------------------------------------------
?>

</page>
<?php

require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
