<?php
/*
	Copyright (C) 2004 Binet R�seau
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
		Revision 1.22  2004/10/21 22:19:37  schmurtz
		GPLisation des fichiers du site

		Revision 1.21  2004/10/21 17:46:05  pico
		Corrections diverses
		Affiche l'host correspondant aux ip dans la page du profil
		D�but de gestion du password xnet
		
		Revision 1.20  2004/10/21 12:23:17  pico
		Un doublon qui servait pas
		
		Revision 1.19  2004/10/21 12:18:52  pico
		Gestion des recherches
		
		Revision 1.18  2004/10/21 08:33:07  pico
		Chgts divers pour matcher avec la balise <html>
		
		Revision 1.17  2004/10/20 23:04:06  pico
		Affichage de l'arbre mieux respect�
		
		Revision 1.16  2004/10/20 22:41:01  pico
		Encore un ptit truc
		
		Revision 1.15  2004/10/20 22:10:06  pico
		BugFix (devrait marcher)
		
		Revision 1.14  2004/10/20 22:05:55  pico
		Changements Noeuds/Feuilles
		
		Revision 1.13  2004/10/20 21:59:13  pico
		BugFix
		
		Revision 1.12  2004/10/20 21:52:31  pico
		Chgt noeuds
		
		Revision 1.11  2004/10/20 20:00:37  pico
		G�n�ration des balises plus conforme
		
		Revision 1.10  2004/10/19 22:00:50  pico
		Pas d'authentification
		Fixe un warning
		
		Revision 1.9  2004/10/19 21:01:25  pico
		Le texte de la qdj s'affiche !!
		
		Revision 1.8  2004/10/19 07:56:56  pico
		Corrections diverses
		
		Revision 1.7  2004/10/18 23:37:03  pico
		BugFix Recherche (pas 2 requetes sql en m�me temps !)
		
		Revision 1.6  2004/10/18 22:17:45  pico
		Ajout des logs dans le fichier
		
*/
require_once "../include/global.inc.php";

// V�rification des droits
//demande_authentification(AUTH_MINIMUM);


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

if(isset($_REQUEST['mots'])) $mots=$_REQUEST['mots'] ; else $mots='';
if(isset($_REQUEST['affich_elt'])) $affich_elt = base64_decode($_REQUEST['affich_elt']); else $affich_elt ="";
if(isset($_REQUEST['a_marquer'])) $a_marquer = base64_decode($_REQUEST['a_marquer']) ; else $a_marquer ="";
//
//Petit programme recursif
// pour parcourir l'arbre
// vers le bas
//------------------------------

function rech_fils($parent) {
	global $DB_web,$a_marquer ; 

	if (affiche_element_faq($parent)) {			// on continue l'affichage ssi on demande l'affichage
	
		// affichage des folders et recherche de leurs fils 
		//----------------------------------
		$DB_web->query("SELECT faq_id,question FROM faq WHERE parent='{$parent}' AND reponse NOT LIKE '%index.php' ") ;
		while(list($id,$question) = $DB_web->next_row()) {
			echo "<noeud id='".$id."' ";
			$DB_web->push_result();
			echo "lien='faq/index.php?affich_elt=".base64_encode(all_elt_affich($id)) ;
			$DB_web->pop_result();
			if ($a_marquer != "") echo "&amp;a_marquer=".base64_encode($a_marquer) ;
			echo "' titre='".htmlspecialchars($question,ENT_QUOTES)."'>" ;
			if (eregi("/".$id."/",$a_marquer)) {
				echo "<image source='skins/".$_SESSION['skin']['skin_nom']."/fleche_folder.gif'/>" ;
			}
			$DB_web->push_result();
			rech_fils($id) ;
			$DB_web->pop_result();
			echo "\n\r</noeud>\n\r " ;
		}
		
		// affichage des vrais questions !
		//------------------------------------
		
		$DB_web->query("SELECT faq_id,question FROM faq WHERE parent='{$parent}' AND reponse LIKE '%index.php'" ) ;
		while(list($id,$question) = $DB_web->next_row()) {
			$DB_web->push_result();
			echo "\n\r<feuille lien='faq/index.php?affich_elt=".base64_encode(all_elt_affich($id))."&amp;idpopup=".$id ;
			$DB_web->pop_result();
			if ($a_marquer != "") echo "&amp;a_marquer=".base64_encode($a_marquer) ;
			echo "#reponse' titre='".htmlspecialchars($question,ENT_QUOTES)."'>" ;
			if (eregi("/".$id."/",$a_marquer)) {
				echo "<image source='skins/".$_SESSION['skin']['skin_nom']."/fleche.gif'/>" ;
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
//Petit programme qui cr�e
// la liste des folder � 
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
		global $DB_web,$affich_elt;
		$liste="";
		while ($id != 0) {
			$DB_web->query("SELECT parent FROM faq WHERE faq_id='{$id}'") ;
			while(list($id) = $DB_web->next_row()){
				if (($id != "")&&($id != 0)){ // on rajoute ssi c'est pas le racine
					$liste .= "/".$id;		  // car on la deja rajout� !
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
<p><strong>Visualisation des diff�rentes FAQ : </strong> </p>
<?
}

//////////////////////////////
//
// Recherche
//
////////////////////////////////////////////////

if ($mots!="") {
	$DB_web->query("SELECT faq_id,question,reponse FROM faq") ;
	$recherche = 0 ;
	$a_marquer = "/" ;			// liste des elements qui contiendront les mots
	$affich_elt = "1" ;		// liste des elements � afficher
	$result = explode(" ",$mots) ;
	$n = count($result) ;
	while(list($id,$question,$reponse) = $DB_web->next_row()) {
		for ($i=0 ; $i<$n ; $i++){ 			// on regarde dans chaque FAQ si il y a les mots ...
			if ((eregi($result[$i],$reponse))||(eregi($result[$i],$question))) {
				$DB_web->push_result();
				$affich_elt = $affich_elt.rech_parent($id);
				$DB_web->pop_result();
				$a_marquer = $a_marquer.$id."/" ;
				$recherche = 1 ;
			}
		}
	}
	if (!$recherche) {
?>
  <font color="#000066">Recherche infructueuse ...</font>
essayer avec d'autres crit�res 
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

echo "<br/>" ;

?>


        <formulaire id="form" action="faq/index.php">
            <champ id="mots" titre="Mots-clefs" valeur="<? echo $mots ;?>"/>
            <bouton id="Submit" titre="Valide"/>
            <bouton id="reset" titre="Reset"/>
        </formulaire>
    <commentaire>(Tous les mots seront dans la description / S�parez les par un blanc) </commentaire>
<?

//
// Corps du Documents pour les r�ponses
//---------------------------------------------------

  	if(isset($_REQUEST['idpopup'])) $id = $_REQUEST['idpopup'] ; else $id = "";
  	if ($id != "") {
		$DB_web->query("SELECT question,reponse FROM faq WHERE faq_id='{$id}'") ;
		if (list($question,$reponse) = $DB_web->next_row()) {
	?>
	<a name='reponse' />
	<? 
	$repfaq = "../../data/faq/".$reponse;
	echo "<cadre titre=\"Q: ".$question."\">\n";
	echo "<html>";
	if(file_exists($repfaq)){
 		if($texte = fopen($repfaq,"r")){
 	 		while(!feof($texte))
   			{
   	 			$ligne = fgets($texte,255);
   	 			print(htmlspecialchars($ligne,ENT_QUOTES));
   			}
 	 		fclose($texte);
		}
	echo "</html>\n</cadre>";
	} else {
	?>
	<warning>Erreur : Impossible de trouver cette question </warning>
	<?
	}
	
	
	} else {
	?>
	<warning>Erreur : Impossible de trouver cette question </warning>
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