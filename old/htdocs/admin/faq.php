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

// Vérification des droits
if(!(verifie_permission('admin')||verifie_permission('faq')||verifie_permission('web')))
	acces_interdit();

if(isset($_REQUEST['download'])&&isset($_REQUEST['download_type'])){
	download(BASE_DATA."faq/".base64_decode($_REQUEST['download']),$_REQUEST['download_type'],"FAQ-".strtr(base64_decode($_REQUEST['download']),"/","-")."-".time());
	exit();
}

// Génération de la page
//===============
require_once BASE_FRANKIZ."htdocs/include/page_header.inc.php";

?>
<page id="faq" titre="Frankiz : FAQ">
<h1>FAQ</h1>

<?php



foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

// Supprimer le répertoire
	if ($temp[0]=='rmdir') {
		$DB_faq->query("SELECT reponse FROM faq WHERE faq_id='{$temp[1]}' ");
		list($dir) = $DB_faq->next_row();
		$DB_faq->query("SELECT faq_id,question FROM faq WHERE (parent='{$temp[1]}' AND NOT  (reponse LIKE '%index.php' OR reponse LIKE '%index.html')) ORDER BY question") ;
		if($DB_faq->num_rows()==0 && $dir!=''){
			$dir = BASE_DATA."faq/".$dir;
			deldir($dir);
			$DB_faq->query("DELETE FROM faq WHERE faq_id='{$temp[1]}'");
			$DB_faq->query("DELETE FROM faq WHERE parent='{$temp[1]}'");
			echo "<warning>Repertoire Supprimé</warning>";
		}else{
			echo "<warning>Tentative de suppression d'un répertoire racine !!!</warning>";
		}
	}
	
	if (($temp[0]=='adddir') && isset($_REQUEST['nom']) && ($_REQUEST['nom']!='')) {
		$nom = $_REQUEST['nom'];
		if(isset($_REQUEST['desc'])) $desc = $_REQUEST['desc']; else $desc = $nom;
		$DB_faq->query("SELECT reponse FROM faq WHERE faq_id='{$temp[1]}' ");
		list($dir) = $DB_faq->next_row();
		$dir=$dir."/".strtolower(str_replace(" ","",$nom));
		$DB_faq-> query("INSERT INTO faq SET parent='{$temp[1]}',question='{$desc}',reponse='{$dir}'");
		mkdir(BASE_DATA."faq/".$dir);
		echo "<commentaire>Repertoire crée".BASE_DATA."faq/".$dir."</commentaire>";
	}
	
	if (($temp[0]=='ajout') && isset($_REQUEST['question']) && ($_REQUEST['question']!='') && isset($_REQUEST['nom']) && ($_REQUEST['nom']!='') && (isset($_FILES['file'])) &&($_FILES['file']['name']!='') ) {
		$question = $_REQUEST['question'];
		$DB_faq->query("SELECT reponse FROM faq WHERE faq_id='{$temp[1]}' ");
		list($dir) = $DB_faq->next_row();
		mkdir(BASE_DATA."faq/".$dir."/".$_REQUEST['nom']);
		if($_FILES['file']['type'] == "text/html"){
			$filename = $dir."/".$_REQUEST['nom']."/index.php";
			move_uploaded_file($_FILES['file']['tmp_name'], BASE_DATA."faq/".$filename);
		}
		else {
			$filename = $dir."/".$_REQUEST['nom']."/".$_FILES['file']['name'];
			move_uploaded_file($_FILES['file']['tmp_name'], BASE_DATA."faq/".$filename);
			unzip(BASE_DATA."faq/".$filename , BASE_DATA."faq/".$dir."/".$_REQUEST['nom'] , true);
		}
		
		if(file_exists(BASE_DATA."faq/".$dir."/".$_REQUEST['nom']."/index.php")){
			$filename = $dir."/".$_REQUEST['nom']."/index.php";
			$DB_faq-> query("INSERT INTO faq SET parent='{$temp[1]}' , question='{$question}' , reponse='{$filename}'");
			echo "<commentaire>FAQ ajoutée</commentaire>";
		}
		else if(file_exists(BASE_DATA."faq/".$dir."/".$_REQUEST['nom']."/index.html")){
			$filename = $dir."/".$_REQUEST['nom']."/index.html";
			$DB_faq-> query("INSERT INTO faq SET parent='{$temp[1]}' , question='{$question}' , reponse='{$filename}'");
			echo "<commentaire>FAQ ajoutée</commentaire>";
		}
		else{
			echo "<warning>Impossible de trouver un fichier index.html ou index.php dans la FAQ soumise. L'opération a été annulée.</warning>";
			$dir = BASE_DATA."faq/".$dir."/".$_REQUEST['nom'];
			deldir($dir);
		}
		$_REQUEST['dir_id'] = $temp[1];
	}
	
	if (($temp[0]=='modif') && isset($_REQUEST['question']) && ($_REQUEST['question']!='')) {
		$reponse = "";
		if((isset($_FILES['file']))&&($_FILES['file']['name']!='')){
			$DB_faq->query("SELECT reponse FROM faq WHERE faq_id='{$temp[1]}' ");
			list($dir) = $DB_faq->next_row();
			$dir = dirname($dir);
			deldir(BASE_DATA."faq/".$dir);
			mkdir(BASE_DATA."faq/".$dir);
			if($_FILES['file']['type'] == "text/html"){
				$filename = $dir."/index.html";
				move_uploaded_file($_FILES['file']['tmp_name'], BASE_DATA."faq/".$filename);
			}
			else {
				$filename = $dir."/".$_FILES['file']['name'];
				move_uploaded_file($_FILES['file']['tmp_name'], BASE_DATA."faq/".$filename);
				unzip(BASE_DATA."faq/".$filename , BASE_DATA."faq/".$dir , true);
			}
			if(file_exists(BASE_DATA."faq/".$dir."/index.php")){
				$filename = $dir."/index.php";
				$reponse =", reponse='{$filename}'";
			}
			else if(file_exists(BASE_DATA."faq/".$dir."/index.html")){
				$filename = $dir."/index.html";
				$reponse =", reponse='{$filename}'";
			}
			else{
				echo "<warning>Impossible de trouver un fichier index.html ou index.php dans la FAQ soumise. L'opération a été annulée.</warning>";
				deldir($dir);
				$reponse = "";
			}
		}
		$question = $_REQUEST['question'];
		$DB_faq-> query("UPDATE faq SET question='{$question}' {$reponse}  WHERE faq_id='{$temp[1]}' ");
		echo "<commentaire>FAQ modifiée</commentaire>";
	}
	
	if ($temp[0]=='suppr') {
		$DB_faq->query("SELECT reponse FROM faq WHERE faq_id='{$temp[1]}' ");
		list($dir) = $DB_faq->next_row();
		unlink(BASE_DATA."faq/".$dir);
		rmdir(substr(BASE_DATA."faq/".$dir, 0, -10));
		$DB_faq->query("DELETE FROM faq WHERE faq_id='{$temp[1]}' ");
		
		echo "<warning>Fichier supprimé</warning>";
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

function rech_fils($parent) {
	global $DB_faq,$a_marquer ; 

	if (affiche_element_faq($parent)) {			// on continue l'affichage ssi on demande l'affichage
	
		// affichage des folders et recherche de leurs fils 
		//----------------------------------
		$DB_faq->query("SELECT faq_id,question FROM faq WHERE (parent='{$parent}' AND NOT  (reponse LIKE '%index.php' OR reponse LIKE '%index.html'))  ORDER BY question") ;
		while(list($id,$question) = $DB_faq->next_row()) {
			echo "<noeud id='".$id."' ";
			echo "lien='admin/faq.php?dir_id=".$id."&amp;affich_elt=".base64_encode(all_elt_affich($id)) ;
			if ($a_marquer != "") echo "&amp;a_marquer=".base64_encode($a_marquer) ;
			echo "' titre='".$question."'>\n\r" ;
			if (isset($_REQUEST['dir_id']) && ($id == $_REQUEST['dir_id'])) {
				echo "<p id='selected'>[séléctionné]</p>\n\r" ;
			}
			$DB_faq->push_result();
			rech_fils($id) ;
			$DB_faq->pop_result();
			echo "\n\r</noeud>\n\r " ;
		}
		
		// affichage des vrais questions !
		//------------------------------------
		
		$DB_faq->query("SELECT faq_id,question FROM faq WHERE  ((parent='{$parent}') AND (reponse LIKE '%index.php' OR reponse LIKE '%index.html')) ORDER BY question" ) ;
		while(list($id,$question) = $DB_faq->next_row()) {
			echo "\n\r<feuille id='".$id."' lien='admin/faq.php?dir_id=".$id."&amp;affich_elt=".base64_encode(all_elt_affich($id))."&amp;idpopup=".$id ;
			if ($a_marquer != "") echo "&amp;a_marquer=".base64_encode($a_marquer) ;
			echo "#reponse' titre='".$question."'>" ;
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
		global $DB_faq;
		$liste = $id."/" ;
		while ($id != 0) {
			$DB_faq->query("SELECT parent FROM faq WHERE faq_id='{$id}'") ;
			$id = $DB_faq->next_row() ;
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

      

<p><strong>Visualisation des différentes FAQ : </strong> </p>
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

  	if(isset($_REQUEST['idpopup'])) $id = $_REQUEST['idpopup'] ; else $id ="";
  	if ($id != "") {
		$DB_faq->query("SELECT * FROM faq WHERE faq_id='{$id}'") ;
		if (list($id,$parent,$question,$reponse) = $DB_faq->next_row()) {
	?>
	<formulaire id="mod_faq_<?php echo $id ?>" titre="La réponse" action="admin/faq.php">
	<champ id="question" titre="Question" valeur="<?php echo $question ?>" />
	<champ id="nom" titre="Nom du sous-dossier de la faq" valeur="<?php echo dirname($reponse) ?>" />
	<fichier id="file" titre="Changer fichier réponse (fichier .html, .zip ou .tar)" taille="1000000000"/>
	<lien titre="Télécharger la faq en .zip" url="admin/faq.php?download=<?php echo base64_encode(dirname($reponse)) ?>&amp;download_type=zip" /><br/>
	<lien titre="Télécharger la faq en .tar" url="admin/faq.php?download=<?php echo base64_encode(dirname($reponse)) ?>&amp;download_type=tar" /><br/>
	<bouton id='modif_<?php echo $id ?>' titre="Modifier"/>
	<bouton id='suppr_<?php echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer cette FAQ ?!!!!!')"/>
	</formulaire>
	<?php 
	
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
	<?php if(isset($_REQUEST['dir_id'])) $dir_id = $_REQUEST['dir_id']; else $dir_id="1"; ?>
	<formulaire id="del_dir_faq_<?php echo $dir_id ?>" titre="Supprimer ce dossier" action="admin/faq.php">
	<?php foreach ($_REQUEST AS $keys => $val){
		if(!strstr($keys,"rmdir")) echo "<hidden id=\"".$keys."\" valeur=\"".$val."\" />";
		}
	?>
	<bouton id='rmdir_<?php echo  $dir_id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer ce répertoire et tous ses fichiers ?!!!!!')"/>
	</formulaire>
	
	<!-- Ajouter un sous-dossier -->
	<formulaire id="add_sub_faq_<?php echo $dir_id ?>" titre="Ajouter un sous-dossier" action="admin/faq.php">
	<champ id="nom" titre="Nom du sous-dossier" valeur="" />
	<champ id="desc" titre="Description du sous-dossier" valeur="" />
	<?php foreach ($_REQUEST AS $keys => $val){
		if(!strstr($keys,"adddir") && !strstr($keys,"nom") && !strstr($keys,"desc")) echo "<hidden id=\"".$keys."\" valeur=\"".$val."\" />";
		}
	?>
	<bouton id='adddir_<?php echo $dir_id ?>' titre='Ajouter' onClick="return window.confirm('!!!!!!Créer ce répertoire ?!!!!!')"/>
	</formulaire>
	
	<!-- Ajouter un fichier -->
	<formulaire id="add_faq_<?php echo $dir_id ?>" titre="Nouvelle FAQ" action="admin/faq.php">
	<champ id="question" titre="Question" valeur="" />
	<champ id="nom" titre="Nom du sous-dossier de la faq" valeur="" />
	<?php foreach ($_REQUEST AS $keys => $val){
		if(!strstr($keys,"ajout") && !strstr($keys,"question") && !strstr($keys,"nom")&& !strstr($keys,"file")) echo "<hidden id=\"".$keys."\" valeur=\"".$val."\" />";
	}
	?>
	<fichier id="file" titre="Fichier réponse (fichier .html, .zip ou .tar)" taille="1000000000"/>
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
