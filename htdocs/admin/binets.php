<?php
/*
	$Id$
	
	Gestion de la liste des binets.
*/

// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')) {
	header("Location: ".BASE_URL."/admin/");
	exit;
}

connecter_mysql_frankiz();
$message = "";

// Gestion de la suppression
if(isset($_POST['supprimer'])) {
	if(isset($_POST['elements'])) {
		$ids = "";
		foreach($_POST['elements'] as $id => $on)
			if($on='on') $ids .= (empty($ids) ? "" : ",") . "'$id'";
		
		mysql_query("DELETE FROM binets WHERE binet_id IN ($ids)");
		mysql_query("DELETE FROM membres WHERE binet_id IN ($ids)");
		
		$message = "<p>".count($_POST['elements'])." binets viennent d'être supprimés avec succés.</p>\n";
	} else {
		ajoute_erreur(ERR_SELECTION_VIDE);
	}
}

// Gestion de la création
if(isset($_POST['nouveau'])) {
	if(!empty($_POST['nom'])) {
		mysql_query("INSERT binets SET nom='".$_POST['nom']."'");
		$message = "<p>Le binet ".$_POST['nom']." vient d'être créé.</p>\n";
	} else {
		ajoute_erreur(ERR_TROP_COURT);
	}
}

// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="admin_binets" titre="Frankiz : liste des binets">
	<h1>Liste des binets</h1>
<?php
	if(!empty($message))
		echo "<p>$message</p>\n";
	if(a_erreur(ERR_SELECTION_VIDE))
		echo "<p>Aucun binet n'est sélectionné.</p>\n";
	if(a_erreur(ERR_TROP_COURT))
		echo "<p>Le nom choisi est trop court.</p>\n";
?>
	<liste id="liste_binets" selectionnable="oui" action="admin/binets.php">
		<entete id="nom" titre="Nom"/>
		<entete id="description" titre="Description"/>
<?php
		$result = mysql_query("SELECT nom,description,binet_id FROM binets");
		while(list($nom,$desc,$id) = mysql_fetch_row($result)) {
			echo "\t\t<element id=\"$id\">\n";
			echo "\t\t\t<colonne id=\"nom\">$nom</colonne>\n";
			echo "\t\t\t<colonne id=\"description\">$description</colonne>\n";
			echo "\t\t</element>\n";
		}
		mysql_free_result($result);
?>
		<bouton titre="Supprimer" id="supprimer"/>
	</liste>
	<formulaire id="nouveau_binet" titre="Nouveau Binet" action="admin/binets.php">
		<champ titre="Nom du binet" id="nom" valeur="" />
		<bouton titre="Nouveau" id="nouveau"/>
	</formulaire>
</page>
<?php

deconnecter_mysql_frankiz();

require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
