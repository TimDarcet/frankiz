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
	Gestion de la liste des sections.
	
	ATTENTION : Il est volontairement interdit de supprimer une section. Cela pose
	en effet des problèmes de cohérence de la base de donnée : pour supprimer une
	section, il faut d'abord qu'il n'y ait personne de rattaché à la section à
	supprimer. Il est cependant possible de marquer un binet comme disparu.
	
	$Id$
	
*/

// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
if(!verifie_permission('admin')&&!verifie_permission('web')&&!verifie_permission('trombino'))
	acces_interdit();

$message = "";

// Gestion de la "suppression"
if(isset($_POST['existe']) || isset($_POST['existeplus'])) {
	$section_existe = isset($_POST['existe']) ? '1' : '0';
	if(isset($_POST['elements'])) {
		$ids = "";
		foreach($_POST['elements'] as $id => $on)
			if($on='on') $ids .= (empty($ids) ? "" : ",") . "'$id'";
		
		$DB_trombino->query("UPDATE sections SET existe='$section_existe' WHERE section_id IN ($ids)");
		
		$message = "<p>".count($_POST['elements'])." sections viennent d'être marquées comme disparues avec succés.</p>\n";
	} else {
		ajoute_erreur(ERR_SELECTION_VIDE);
	}
}

// Gestion de la création
if(isset($_POST['nouvelle'])) {
	if(!empty($_POST['nom'])) {
		$DB_trombino->query("INSERT sections SET nom='".$_POST['nom']."'");
		$message = "<p>La section ".$_POST['nom']." vient d'être créée.</p>\n";
	} else {
		ajoute_erreur(ERR_TROP_COURT);
	}
}

// Modification du nom
if(isset($_POST['maj'])) {
	foreach($_POST['maj'] as $id => $val)
		$DB_trombino->query("UPDATE sections SET nom='{$_POST['nom'][$id]}' WHERE section_id='$id'");
	
	$message = "<p>Le nom de la section a été modifié avec succés.</p>\n";
}

// Génération de la page
require_once BASE_FRANKIZ."include/page_header.inc.php";
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
		$DB_trombino->query("SELECT nom,existe,section_id FROM sections ORDER BY nom ASC, existe DESC");
		while(list($nom,$existe,$id) = $DB_trombino->next_row()) {
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


require_once BASE_FRANKIZ."include/page_footer.inc.php";
?>
