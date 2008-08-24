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

require_once "../include/global.inc.php";
require_once "../include/transferts.inc.php";
require_once "../include/wiki.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
if(!(verifie_permission('admin')||verifie_permission('xshare')||verifie_permission('web')))
	acces_interdit();

// Génération de la page
//===============
require_once BASE_FRANKIZ."htdocs/include/page_header.inc.php";

?>
<page id="xshare" titre="Frankiz : Xshare">
<h1>Xshare</h1>

<?php



foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

	// Supprimer le répertoire
	
	if ($temp[0]=='rmdir') {
		$DB_web->query("SELECT lien FROM xshare WHERE id='{$temp[1]}' ");
		list($dir) = $DB_web->next_row();
		$dir = BASE_DATA."xshare/".$dir;
		deldir($dir);
		$DB_web->query("DELETE FROM xshare WHERE id='{$temp[1]}'");
		$DB_web->query("DELETE FROM xshare WHERE id_parent='{$temp[1]}'");
		echo "<warning>Repertoire Supprimé</warning>";
	}
	
	if (($temp[0]=='adddir') && isset($_REQUEST['nom']) && ($_REQUEST['nom']!='')) {
		$nom = $_REQUEST['nom'];
		$DB_web->query("SELECT lien FROM xshare WHERE id='{$temp[1]}' ");
		list($dir) = $DB_web->next_row();
		$dir=$dir."/".strtolower(str_replace(" ","",$nom));
		$tofind = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
		$replac = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
		$dir = strtr($dir,$tofind,$replac);
		$DB_web-> query("INSERT INTO xshare SET id_parent='{$temp[1]}',nom='{$nom}',lien='{$dir}'");
		mkdir(BASE_DATA."xshare/".$dir);
		echo "<commentaire>Repertoire crée</commentaire>";
	}
	
	if (($temp[0]=='ajout') && isset($_REQUEST['nom']) && ($_REQUEST['nom']!='') && (isset($_FILES['file']))&&($_FILES['file']['name']!='')) {
		$nom = $_REQUEST['nom'];
		$DB_web->query("SELECT lien FROM xshare WHERE id='{$temp[1]}' ");
		list($dir) = $DB_web->next_row();
		$filename = $dir."/".$_FILES['file']['name'];
		move_uploaded_file($_FILES['file']['tmp_name'], BASE_DATA."xshare/".$filename);
		echo "<commentaire>".BASE_DATA."xshare/".$filename."</commentaire>";
		if(isset($_REQUEST['version'])) $version = ", version='{$_REQUEST['version']}'"; else $version = '';
		if(isset($_REQUEST['importance'])) $importance =  ", importance='{$_REQUEST['importance']}'"; else $importance = '';
		if(isset($_REQUEST['site'])) $site =  ", site='{$_REQUEST['site']}'"; else $site = '';
		if(isset($_REQUEST['licence'])) $licence = ", licence='{$_REQUEST['licence']}'"; else $licence = '';
		if(isset($_REQUEST['descript'])) $descript = ", descript='{$_REQUEST['descript']}'"; else $descript = '';
		
		$DB_web-> query("INSERT INTO xshare SET id_parent='{$temp[1]}' , nom='{$nom}' , lien='{$filename}' $version $importance $site $licence $descript");
	
		echo "<commentaire>Fichier ajouté</commentaire>";
	}
	
	if (($temp[0]=='modif') && isset($_REQUEST['nom']) && ($_REQUEST['nom']!='')) {
		if((isset($_FILES['file']))&&($_FILES['file']['name']!='')){
			$DB_web->query("SELECT lien FROM xshare WHERE id='{$temp[1]}' ");
			list($dir) = $DB_web->next_row();
			unlink(BASE_DATA."xshare/".$dir);
			$filename = dirname($dir)."/".$_FILES['file']['name'];
			move_uploaded_file($_FILES['file']['tmp_name'], BASE_DATA."xshare/".$filename);
			$lien = ", lien='{$filename}'"; 
			echo "<commentaire>".BASE_DATA."xshare/".$filename."</commentaire>";
		}
		else $lien = '';
		$nom = $_REQUEST['nom'];
		if(isset($_REQUEST['version'])) $version = ", version='{$_REQUEST['version']}'"; else $version = ", version=''";
		if(isset($_REQUEST['importance'])) $importance =  ", importance='{$_REQUEST['importance']}'"; else $importance = ", importance=''";
		if(isset($_REQUEST['site'])) $site =  ", site='{$_REQUEST['site']}'"; else $site = ", site=''";
		if(isset($_REQUEST['licence'])) $licence = ", licence='{$_REQUEST['licence']}'"; else $licence = ", licence=''";
		if(isset($_REQUEST['descript'])) $descript = ", descript='{$_REQUEST['descript']}'"; else $descript =", descript= ''";
		$DB_web-> query("UPDATE xshare SET nom='{$nom}' $lien $version $importance $site $licence $descript WHERE id={$temp[1]}");
	
		echo "<commentaire>Fichier modifié</commentaire>";
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

		$DB_web->query("SELECT id,nom FROM xshare WHERE descript='' AND id_parent='{$id_parent}' ORDER BY nom") ;
		while(list($id,$nom) = $DB_web->next_row()) {
				echo "<noeud  id='".$id."' titre='".$nom."' lien='admin/xshare.php?dir_id=".$id."&amp;affich_elt=".base64_encode(all_elt_affich($id)) ;
			if ($a_marquer != "") echo "&amp;a_marquer=".base64_encode($a_marquer);
			echo "'>\n\r" ;
			if (isset($_REQUEST['dir_id']) && ($id == $_REQUEST['dir_id'])) {
				echo "<p id='selected'>[séléctionné]</p>\n\r" ;
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
			echo "\n\r<feuille  id='".$id."'  titre='".$nom."' lien='admin/xshare.php?dir_id=".$id."&amp;affich_elt=".base64_encode(all_elt_affich($id))."&amp;idpopup=".$id;
			if ($a_marquer != "") echo "&amp;a_marquer=".base64_encode($a_marquer) ;
			echo "'>\n\r" ;
			if (isset($_REQUEST['dir_id']) && ($id == $_REQUEST['dir_id'])) {
				echo "<p id='selected'>[séléctionné]</p>\n\r" ;
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
  
<p align="left">&#160;</p>

      

<p><strong>Visualisation des différents téléchargements : </strong> </p>
<?php

//
// on affiche  l'arbre 
// simple maintenant
//----------------------
echo "<arbre>";
rech_fils(0) ;
echo "</arbre>";

?>



<?php

//
// Corps du Documents pour les réponses
//---------------------------------------------------

  	if(isset($_REQUEST['idpopup'])) $id = $_REQUEST['idpopup']; else $id = "";
  	if ($id != "") {
		$DB_web->query("SELECT * FROM xshare WHERE id='{$id}'") ;
		if (list($id,$id_parent,$nom,$licence,$lien,$importance,$date,$descript,$version,$site) = $DB_web->next_row()) {
	?>
	<formulaire id="xshare_<?php echo $id ?>" titre="Le logiciel" action="admin/xshare.php">
	<champ id="nom" titre="Nom du logiciel" valeur="<?php echo $nom ?>" />
	<champ id="version" titre="Version" valeur="<?php echo $version ?>" />
	<choix titre="Importance" id="importance" type="combo" valeur="<?php echo $importance ?>">
				<option id="0" titre="Normal"/>
				<option id="1" titre="Important"/>
				<option id="2" titre="Indispensable"/>
	</choix>
	<champ id="site" titre="Site de l'éditeur" valeur="<?php echo $site ?>" />
	<champ id="licence" titre="Licence" valeur="<?php echo $licence ?>" />
	<zonetext id="descript" titre="Description"><?php echo $descript; ?></zonetext>
	<fichier id="file" titre="Nouveau fichier" taille="1000000000"/>
	<bouton id='modif_<?php echo $id ?>' titre="Modifier"/>
	<bouton id='suppr_<?php echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer ce logiciel ?!!!!!')"/>
	</formulaire>
	<?php 
			affiche_syntaxe_wiki();
		} else {
	?>
	<warning>Erreur : Impossible de trouver cette description </warning>
	<?php
		}
	}
	else
	{
	?>
	<!-- Supprimer le dossier en cours -->
	<?php if(isset($_REQUEST['dir_id'])) $dir_id = $_REQUEST['dir_id']; else $dir_id="0"; ?>
	<formulaire id="xshare_<?php echo $dir_id ?>" titre="Supprimer ce dossier" action="admin/xshare.php">
	<?php foreach ($_REQUEST AS $keys => $val){
		if(!strstr($keys,"rmdir")) echo "<hidden id=\"".$keys."\" valeur=\"".$val."\" />";
		}
	?>
	<bouton id='rmdir_<?php echo  $dir_id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer ce répertoire et tous ses fichiers ?!!!!!')"/>
	</formulaire>
	
	<!-- Ajouter un sous-dossier -->
	<formulaire id="xshare_<?php echo $dir_id ?>" titre="Ajouter un sous-dossier" action="admin/xshare.php">
	<champ id="nom" titre="Nom du sous-dossier" valeur="" />
	<?php foreach ($_REQUEST AS $keys => $val){
		if(!strstr($keys,"adddir")&&!strstr($keys,"nom")) echo "<hidden id=\"".$keys."\" valeur=\"".$val."\" />";
		}
	?>
	<bouton id='adddir_<?php echo $dir_id ?>' titre='Ajouter' onClick="return window.confirm('!!!!!!Créer ce répertoire ?!!!!!')"/>
	</formulaire>
	
	<!-- Ajouter un fichier -->
	<formulaire id="xshare_<?php echo $dir_id ?>" titre="Ajouter un logiciel" action="admin/xshare.php">
	<champ id="nom" titre="Nom du logiciel" valeur="" />
	<champ id="version" titre="Version" valeur="" />
	<choix titre="Importance" id="importance" type="combo" valeur="0">
				<option id="0" titre="Normal"/>
				<option id="1" titre="Important"/>
				<option id="2" titre="Indispensable"/>
	</choix>
	<champ id="site" titre="Site de l'éditeur" />
	<champ id="licence" titre="Licence" />
	<zonetext id="descript" titre="Description"></zonetext>
	<?php foreach ($_REQUEST AS $keys => $val){
		if(!strstr($keys,"ajout")&&!strstr($keys,"nom")&&!strstr($keys,"version")&&!strstr($keys,"importance")&&!strstr($keys,"site")&&!strstr($keys,"licence")&&!strstr($keys,"descript")&&!strstr($keys,"file")) echo "<hidden id=\"".$keys."\" valeur=\"".$val."\" />";
	}
	?>
	<fichier id="file" titre="Fichier" taille="1000000000"/>
	<bouton id='ajout_<?php echo $dir_id ?>' titre="Ajouter" onClick="return window.confirm('!!!!!!Ajouter ce fichier ?!!!!!')"/>
	</formulaire>
	
<?php
	}
//
// Pied de page ...
//---------------------------------------------------
?>

</page>
<?php

require_once BASE_FRANKIZ."htdocs/include/page_footer.inc.php";
?>