<?php
/*
	$Id$
	
	Gestion de la liste des sections.
	
	Il est volontairement interdit de supprimer une section. Cela pose en effet
	des problèmes de cohérence de la base de donnée : pour supprimer une section,
	il faut d'abord qu'il n'y ait personne de rattaché à la section à supprimer.
	Il est cependant possible de marquer un binet comme disparu.
	
*/

// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')) {
	header("Location: ".BASE_URL."/admin/");
	exit;
}

$message = "";

// Gestion de la "suppression"
if(isset($_POST['existe']) || isset($_POST['existeplus'])) {
	$section_existe = isset($_POST['existe']) ? '1' : '0';
	if(isset($_POST['elements'])) {
		$ids = "";
		foreach($_POST['elements'] as $id => $on)
			if($on='on') $ids .= (empty($ids) ? "" : ",") . "'$id'";
		
		$DB_web->query("UPDATE sections SET existe='$section_existe' WHERE section_id IN ($ids)");
		
		$message = "<p>".count($_POST['elements'])." sections viennent d'être marquées comme disparues avec succés.</p>\n";
	} else {
		ajoute_erreur(ERR_SELECTION_VIDE);
	}
}

// Gestion de la création
if(isset($_POST['nouvelle'])) {
	if(!empty($_POST['nom'])) {
		$DB_web->query("INSERT sections SET nom='".$_POST['nom']."'");
		$message = "<p>La section ".$_POST['nom']." vient d'être créée.</p>\n";
	} else {
		ajoute_erreur(ERR_TROP_COURT);
	}
}

// Modification du nom
if(isset($_POST['maj'])) {
	foreach($_POST['maj'] as $id => $val)
		$DB_web->query("UPDATE sections SET nom='{$_POST['nom'][$id]}' WHERE section_id='$id'");
	
	$message = "<p>Le nom de la section a été modifié avec succés.</p>\n";
}

// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="admin_sections" titre="Frankiz : liste des sections">
	<h2>Liste des sections</h2>
<?php
	if(!empty($message))
		echo "<p>$message</p>\n";
	if(a_erreur(ERR_SELECTION_VIDE))
		echo "<p>Aucune section n'est sélectionnée.</p>\n";
	if(a_erreur(ERR_TROP_COURT))
		echo "<p>Le nom choisi est trop court.</p>\n";
?>
	<liste id="liste_sections" selectionnable="oui" action="admin/sections.php">
		<entete id="nom" titre="Nom"/>
		<entete id="existe" titre="État"/>
<?php
		$DB_web->query("SELECT nom,existe,section_id FROM sections ORDER BY nom ASC, existe DESC");
		while(list($nom,$existe,$id) = $DB_web->next_row()) {
			echo "\t\t<element id=\"$id\">\n";
			echo "\t\t\t<colonne id=\"nom\"><champ id=\"nom[$id]\" valeur=\"$nom\"/><bouton titre=\"Mise à jour\" id=\"maj[$id]\"/></colonne>\n";
			echo "\t\t\t<colonne id=\"existe\">".($existe?"existante":"disparue")."</colonne>\n";
			echo "\t\t</element>\n";
		}
?>
		<bouton titre="Marquer comme disparue" id="existeplus"/>
		<bouton titre="Ne pas marquer comme disparue" id="existe"/>
	</liste>
	<formulaire id="nouvelle_section" titre="Nouvelle section" action="admin/sections.php">
		<champ titre="Nom de la section" id="nom" valeur="" />
		<bouton titre="Nouvelle" id="nouvelle"/>
	</formulaire>
</page>
<?php


require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
