<? 

require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_MINIMUM);


// G�n�ration de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="faq" titre="Frankiz : FAQ">
<h1>Xshare</h1>

<?



foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

	// Supprimer le r�pertoire
	
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
		echo "<warning>Repertoire Supprim�</warning>";
	}
	
	if (($temp[0]=='adddir') && isset($_REQUEST['nom']) && ($_REQUEST['nom']!='')) {
		$nom = $_REQUEST['nom'];
		if(isset($_REQUEST['desc'])) $desc = $_REQUEST['desc']; else $desc = $nom;
		$DB_web->query("SELECT reponse FROM faq WHERE faq_id='{$temp[1]}' ");
		list($dir) = $DB_web->next_row();
		$dir=$dir."/".strtolower(str_replace(" ","",$nom));
		$DB_web-> query("INSERT INTO faq SET parent='{$temp[1]}',question='{$desc}',reponse='{$dir}'");
		mkdir(BASE_DATA."faq/".$dir);
		echo "<commentaire>Repertoire cr�e</commentaire>";
	}
	
	if (($temp[0]=='ajout') && isset($_REQUEST['question']) && ($_REQUEST['question']!='') && (isset($_FILES['file']))&&($_FILES['file']['size']!=0)) {
		$question = $_REQUEST['question'];
		$DB_web->query("SELECT reponse FROM faq WHERE faq_id='{$temp[1]}' ");
		list($dir) = $DB_web->next_row();
		$filename = $dir."/index.php";
		move_uploaded_file($_FILES['file']['tmp_name'], BASE_DATA."faq/".$filename);
		
		$DB_web-> query("INSERT INTO faq SET parent='{$temp[1]}' , question='{$question}' , reponse='{$filename}'");
	
		echo "<commentaire>Fichier ajout�</commentaire>";
	}
}


//
// Corps du Documents
//---------------------------------------------------

if(isset($_REQUEST['affich_elt'])) define("AFFICH_ELT",base64_decode($_REQUEST['affich_elt'])) ; else define("AFFICH_ELT",'');
if(isset($_REQUEST['a_marquer'])) define("A_MARQUER",base64_decode($_REQUEST['a_marquer'])) ; else define("A_MARQUER","");



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
				echo "<feuille class='foldheader1'>\n\r";		// folder ferm�
			}
			echo "<a name=\"".$id."\"/>" ;
			echo "<lien titre='".htmlspecialchars($question,ENT_QUOTES)."' url='admin/faq.php?affich_elt=".base64_encode(all_elt_affich($id)) ;
			if (A_MARQUER != "") echo "&amp;a_marquer=".base64_encode(A_MARQUER) ;
			echo "&amp;dir_id=".$id."#".$id."' />" ;
			if (eregi("/".$id."/",A_MARQUER)) {
				echo "<image source='./faq_fleche_folder.gif'/>" ;
			}
			echo "\n\r</feuille>\n\r " ;
			rech_fils($id) ;
		}
		
		// affichage des vrais fichiers !
		//------------------------------------
		
		$DB_web->query("SELECT faq_id,question FROM faq WHERE parent='{$parent}' AND reponse LIKE '%index.php'" ) ;
		while(list($id,$question) = $DB_web->next_row()) {
			echo "\n\r<feuille class='question'>\n\r" ;
			echo "<lien titre='".htmlspecialchars($question,ENT_QUOTES)."' url='admin/faq.php?affich_elt=".base64_encode(all_elt_affich($id))."&amp;idpopup=".$id;
			if (A_MARQUER != "") echo "&amp;a_marquer=".base64_encode(A_MARQUER) ;
			echo "#descript'/>" ;
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
//Petit programme qui cr�e
// la liste des folder � 
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
		global $DB_web;
		$liste = $id."/" ;
		while ($id != 0) {
			$DB_web->query("SELECT parent FROM faq WHERE faq_id='{$id}'") ;
			$id = $DB_web->next_row() ;
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

      

<p><strong>Visualisation des diff&eacute;rents t�l�chargements : </strong> </p>
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
// Corps du Documents pour les r�ponses
//---------------------------------------------------

  	if(isset($_REQUEST['idpopup'])) $id = $_REQUEST['idpopup'] ;
  	if ($id != "") {
		$DB_web->query("SELECT * FROM faq WHERE faq_id='{$id}'") ;
		if (list($id,$parent,$question,$reponse) = $DB_web->next_row()) {
	?>
	<a name='descript' />
	<formulaire id="faq_<? echo $id ?>" titre="La r�ponse" action="admin/faq.php">
	<champ id="question" titre="Question" valeur="<? echo $question ?>" />
	<bouton id='modif_<? echo $id ?>' titre="Modifier"/>
	<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer ce logiciel ?!!!!!')"/>
	</formulaire>
	<? 
	
		} else {
	?>
	<p><strong>Erreur</strong> : Impossible de trouver cette description </p>
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
	<bouton id='rmdir_<? echo  $dir_id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer ce r�pertoire et tous ses fichiers ?!!!!!')"/>
	</formulaire>
	
	<!-- Ajouter un sous-dossier -->
	<formulaire id="faq_<? echo $dir_id ?>" titre="Ajouter un sous-dossier" action="admin/faq.php">
	<champ id="nom" titre="Nom du sous-dossier" valeur="" />
	<champ id="desc" titre="Description du sous-dossier" valeur="" />
	<? foreach ($_GET AS $keys => $val){
		if(!strstr($keys,"adddir")) echo "<hidden id=\"".$keys."\" valeur=\"".$val."\" />";
		}
	?>
	<bouton id='adddir_<? echo $dir_id ?>' titre='Ajouter' onClick="return window.confirm('!!!!!!Cr�er ce r�pertoire ?!!!!!')"/>
	</formulaire>
	
	<!-- Ajouter un fichier -->
	<formulaire id="faq_<? echo $dir_id ?>" titre="Le logiciel" action="admin/faq.php">
	<champ id="question" titre="Question" valeur="" />
	<? foreach ($_GET AS $keys => $val){
		if(!strstr($keys,"ajout")) echo "<hidden id=\"".$keys."\" valeur=\"".$val."\" />";
	}
	?>
	<champ id="file" titre="Fichier r�ponse" valeur=""/>
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