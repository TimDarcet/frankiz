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
	$Log$
	Revision 1.13  2005/04/13 17:09:58  pico
	Passage de tous les fichiers en utf8.

	Revision 1.12  2005/02/09 16:54:41  pico
	Correction pour la recherche dans le xshare
	
	Revision 1.11  2005/01/22 17:58:38  pico
	Modif des images
	
	Revision 1.10  2005/01/21 18:21:44  pico
	Hum...
	
	Revision 1.9  2005/01/11 16:47:09  pico
	Bug #26
	
	Revision 1.8  2005/01/10 13:31:34  pico
	Affichage des logiciels à l'intérieur de l'arborescence
	
	Revision 1.7  2005/01/10 10:24:33  pico
	Bug #16
	
	Revision 1.6  2004/12/15 05:19:13  falco
	cohérence
	
	Revision 1.5  2004/12/09 20:53:27  pico
	Faute d'orthographe
	
	Revision 1.4  2004/11/29 17:27:32  schmurtz
	Modifications esthetiques.
	Nettoyage de vielles balises qui trainaient.
	
	Revision 1.3  2004/11/27 23:30:34  pico
	Passage des xshare et faq en wiki
	Ajout des images dans l'aide du wiki
	
	Revision 1.2  2004/11/25 12:45:36  pico
	Duble emploi de htmlspecialchar vu que les entrées dans la bdd sont déjà transformées
	
	Revision 1.1  2004/11/25 00:10:30  schmurtz
	Suppression des dossiers ne contenant qu'un unique fichier index.php
	
	Revision 1.23  2004/11/22 20:40:00  pico
	Patch fonction tar
	
	Revision 1.22  2004/11/06 20:03:06  kikx
	Suppression de liens inutiles
	
	Revision 1.21  2004/10/21 22:19:38  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.20  2004/10/21 12:23:17  pico
	Un doublon qui servait pas
	
	Revision 1.19  2004/10/21 12:18:52  pico
	Gestion des recherches
	
	Revision 1.18  2004/10/20 23:18:49  pico
	Derniers fixes, ça marche !!
	
	Revision 1.17  2004/10/20 23:04:06  pico
	Affichage de l'arbre mieux respecté
	
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
require_once "include/global.inc.php";
require_once "include/wiki.inc.php";
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
			$DB_web->query("SELECT * FROM xshare WHERE id='{$id}'") ;
			if (list($id,$id_parent,$nom,$licence,$lien,$importance,$date,$descript,$version,$site) = $DB_web->next_row()) { 
				echo "<lien titre='Site de l&apos;éditeur' url='".$site."'/> | <lien titre='Télécharger ici' url='data/xshare/".$lien."'/><br/><br/>";
				if($importance == 1) echo "Logiciel important<br/>";
				if($importance == 2) echo "<strong>Logiciel indispensable</strong><br/>";
				echo "Dernière modification le ".substr($date, 6, 2)."/".substr($date, 4, 2)."/".substr($date, 0, 4)."<br/><br/>" ;
				if($version != '') echo "Version: ".$version."<br/>";
				if($licence != '') echo "Licence: ".$licence."<br/>";
				echo "Description: ".wikiVersXML($descript)."<br/>";
	
		
			} else {
				?>
				<warning>Erreur : Impossible de trouver cette description </warning>
				<?
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
      

<?
// Entete !
//-------------------------

if (($mots!="")||($a_marquer!="")) {
?>
<p><strong>Résultats de la recherche</strong></p>
<?
} else {
?>
<p><strong>Logiciels disponibles en téléchargement :</strong></p>
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
<?
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
		<champ id="mots" titre="Mots-clefs" valeur="<? echo $mots ;?>"/>
		<bouton id="Submit" titre="Chercher"/>
	</formulaire>
<?

//
// Pied de page ...
//---------------------------------------------------
?>

</page>
<?php

require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
