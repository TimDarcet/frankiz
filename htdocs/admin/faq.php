<? 
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
		Revision 1.18  2004/11/07 00:07:47  pico
		Utilisation de la fonction unzip pour dezipper une archive

		Revision 1.17  2004/11/06 17:47:43  pico
		........
		
		Revision 1.16  2004/11/06 15:19:09  pico
		Modifiaction possible du titre des faq
		
		Revision 1.15  2004/11/05 14:08:22  pico
		BugFix
		
		Revision 1.14  2004/11/05 13:50:22  pico
		Admin FAQ:
		On peut maintenant uploader un fichier html, une archive tar.gz (ou .tgz) ou un fichier .zip
		Le fichier est décompressé, on cherche dedans un fichier index, si il n'y en a pas, on refuse et on supprime les fichiers pour pas laisser es traces.
		
		Revision 1.13  2004/10/21 22:19:37  schmurtz
		GPLisation des fichiers du site
		
		Revision 1.12  2004/10/20 23:04:06  pico
		Affichage de l'arbre mieux respecté
		
		Revision 1.11  2004/10/20 22:28:27  pico
		Encore des corrections
		
		Revision 1.10  2004/10/20 22:10:06  pico
		BugFix (devrait marcher)
		
		Revision 1.9  2004/10/20 22:05:55  pico
		Changements Noeuds/Feuilles
		
		Revision 1.8  2004/10/20 19:58:46  pico
		BugFix: génération des balises plus conforme
		
		Revision 1.7  2004/10/19 14:58:42  schmurtz
		Creation d'un champ de formulaire specifique pour les fichiers (sans passer
		l'element champ, qui actuellement est un peu acrobatique).
		
		Revision 1.6  2004/10/19 07:56:56  pico
		Corrections diverses
		
		Revision 1.5  2004/10/18 22:17:45  pico
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



foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

	// Supprimer le répertoire
	if($val == "submit"){
		if ($temp[0]=='rmdir') {
			$DB_web->query("SELECT reponse FROM faq WHERE faq_id='{$temp[1]}' ");
			list($dir) = $DB_web->next_row();
			$dir = BASE_DATA."faq/".$dir;
			foreach(glob($dir."/*") as $fn) {
				unlink($fn);
			} 
			rmdir($dir);
			$DB_web->query("DELETE FROM faq WHERE faq_id='{$temp[1]}'");
			$DB_web->query("DELETE FROM faq WHERE parent='{$temp[1]}'");
			echo "<warning>Repertoire Supprimé</warning>";
		}
		
		if (($temp[0]=='adddir') && isset($_REQUEST['nom']) && ($_REQUEST['nom']!='')) {
			$nom = $_REQUEST['nom'];
			if(isset($_REQUEST['desc'])) $desc = $_REQUEST['desc']; else $desc = $nom;
			$DB_web->query("SELECT reponse FROM faq WHERE faq_id='{$temp[1]}' ");
			list($dir) = $DB_web->next_row();
			$dir=$dir."/".strtolower(str_replace(" ","",$nom));
			$DB_web-> query("INSERT INTO faq SET parent='{$temp[1]}',question='{$desc}',reponse='{$dir}'");
			mkdir(BASE_DATA."faq/".$dir);
			echo "<commentaire>Repertoire crée".BASE_DATA."faq/".$dir."</commentaire>";
		}
		
		if (($temp[0]=='ajout') && isset($_REQUEST['question']) && ($_REQUEST['question']!='') && isset($_REQUEST['nom']) && ($_REQUEST['nom']!='') && (isset($_FILES['file']))) {
			$question = $_REQUEST['question'];
			$DB_web->query("SELECT reponse FROM faq WHERE faq_id='{$temp[1]}' ");
			list($dir) = $DB_web->next_row();
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
				$DB_web-> query("INSERT INTO faq SET parent='{$temp[1]}' , question='{$question}' , reponse='{$filename}'");
				echo "<commentaire>FAQ ajoutée</commentaire>";
			}
			else if(file_exists(BASE_DATA."faq/".$dir."/".$_REQUEST['nom']."/index.html")){
				$filename = $dir."/".$_REQUEST['nom']."/index.html";
				$DB_web-> query("INSERT INTO faq SET parent='{$temp[1]}' , question='{$question}' , reponse='{$filename}'");
				echo "<commentaire>FAQ ajoutée</commentaire>";
			}
			else{
				echo "<warning>Impossible de trouver un fichier index.html ou index.php dans la FAQ soumise<br/> opération annulée</warning>";
				$dir = BASE_DATA."faq/".$dir."/".$_REQUEST['nom'];
				exec("rm -r $dir");
			}
		}
		
		if (($temp[0]=='modif') && isset($_REQUEST['question']) && ($_REQUEST['question']!='')) {
			$question = $_REQUEST['question'];
			$DB_web-> query("UPDATE faq SET question='{$question}' WHERE faq_id='{$temp[1]}' ");
			echo "<commentaire>FAQ modifiée</commentaire>";
		}
		
		if ($temp[0]=='suppr') {
			$DB_web->query("SELECT reponse FROM faq WHERE faq_id='{$temp[1]}' ");
			list($dir) = $DB_web->next_row();
			unlink(BASE_DATA."faq/".$dir);
			rmdir(substr(BASE_DATA."faq/".$dir, 0, -10));
			$DB_web->query("DELETE FROM faq WHERE faq_id='{$temp[1]}' ");
			
			echo "<warning>Fichier supprimé</warning>";
		}
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
	global $DB_web,$a_marquer ; 

	if (affiche_element_faq($parent)) {			// on continue l'affichage ssi on demande l'affichage
	
		// affichage des folders et recherche de leurs fils 
		//----------------------------------
		$DB_web->query("SELECT faq_id,question FROM faq WHERE parent='{$parent}' AND reponse NOT LIKE '%index.php' ") ;
		while(list($id,$question) = $DB_web->next_row()) {
			echo "<noeud id='".$id."' ";
			echo "lien='admin/faq.php?dir_id=".$id."&amp;affich_elt=".base64_encode(all_elt_affich($id)) ;
			if ($a_marquer != "") echo "&amp;a_marquer=".base64_encode($a_marquer) ;
			echo "' titre='".htmlspecialchars($question,ENT_QUOTES)."'>\n\r" ;
			if (isset($_REQUEST['dir_id']) && ($id == $_REQUEST['dir_id'])) {
				echo "<p id='selected'>[séléctionné]</p>\n\r" ;
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
			echo "\n\r<feuille id='".$id."' lien='admin/faq.php?affich_elt=".base64_encode(all_elt_affich($id))."&amp;idpopup=".$id ;
			if ($a_marquer != "") echo "&amp;a_marquer=".base64_encode($a_marquer) ;
			echo "#reponse' titre='".htmlspecialchars($question,ENT_QUOTES)."'>" ;
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

      

<p><strong>Visualisation des différentes FAQ : </strong> </p>
<?

//
// on affiche  l'arbre 
// simple maintenant
//----------------------
echo "<arbre>";
rech_fils(0) ;
echo "</arbre>";
echo "<br/>" ;

?>



<?

//
// Corps du Documents pour les réponses
//---------------------------------------------------

  	if(isset($_REQUEST['idpopup'])) $id = $_REQUEST['idpopup'] ; else $id ="";
  	if ($id != "") {
		$DB_web->query("SELECT * FROM faq WHERE faq_id='{$id}'") ;
		if (list($id,$parent,$question,$reponse) = $DB_web->next_row()) {
	?>
	<formulaire id="faq_<? echo $id ?>" titre="La réponse" action="admin/faq.php">
	<champ id="question" titre="Question" valeur="<? echo $question ?>" />
	<bouton id='modif_<? echo $id ?>' titre="Modifier"/>
	<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer cette FAQ ?!!!!!')"/>
	</formulaire>
	<? 
	
		} else {
	?>
	<warning>Erreur : Impossible de trouver cette description </warning>
	<?
		}
		echo "<br/><br/>" ;
	}
	else
	{
	?>
	<!-- Supprimer le dossier en cours -->
	<? if(isset($_REQUEST['dir_id'])) $dir_id = $_REQUEST['dir_id']; else $dir_id="0"; ?>
	<formulaire id="faq_<? echo $dir_id ?>" titre="Supprimer ce dossier" action="admin/faq.php">
	<? foreach ($_GET AS $keys => $val){
		if((!strstr($keys,"rmdir"))&&(!strstr($keys,"rmdir"))) echo "<hidden id=\"".$keys."\" valeur=\"".$val."\" />";
		}
	?>
	<bouton id='rmdir_<? echo  $dir_id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer ce répertoire et tous ses fichiers ?!!!!!')"/>
	</formulaire>
	
	<!-- Ajouter un sous-dossier -->
	<formulaire id="faq_<? echo $dir_id ?>" titre="Ajouter un sous-dossier" action="admin/faq.php">
	<champ id="nom" titre="Nom du sous-dossier" valeur="" />
	<champ id="desc" titre="Description du sous-dossier" valeur="" />
	<? foreach ($_GET AS $keys => $val){
		if(!strstr($keys,"adddir")) echo "<hidden id=\"".$keys."\" valeur=\"".$val."\" />";
		}
	?>
	<bouton id='adddir_<? echo $dir_id ?>' titre='Ajouter' onClick="return window.confirm('!!!!!!Créer ce répertoire ?!!!!!')"/>
	</formulaire>
	
	<!-- Ajouter un fichier -->
	<formulaire id="faq_<? echo $dir_id ?>" titre="Nouvelle FAQ" action="admin/faq.php">
	<champ id="question" titre="Question" valeur="" />
	<champ id="nom" titre="Nom du sous-dossier de la faq" valeur="" />
	<? foreach ($_GET AS $keys => $val){
		if(!strstr($keys,"ajout")) echo "<hidden id=\"".$keys."\" valeur=\"".$val."\" />";
	}
	?>
	<fichier id="file" titre="Fichier réponse (fichier .html, .zip ou .tar.gz)" taille="1000000000"/>
	<bouton id='ajout_<? echo $dir_id ?>' titre="Ajouter" onClick="return window.confirm('!!!!!!Ajouter ce fichier ?!!!!!')"/>
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