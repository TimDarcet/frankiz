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
	Gestion de la liste des binets.

	$Log$
	Revision 1.12  2004/11/08 15:26:44  kikx
	bla

	Revision 1.11  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.10  2004/10/20 21:00:04  kikx
	C'est qd meme plus beau
	
	Revision 1.9  2004/09/15 23:20:18  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.8  2004/09/15 21:42:27  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

// En-tetes
require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");

$message = "";

// Gestion de la suppression
if(isset($_POST['supprimer'])) {
	if(isset($_POST['elements'])) {
		$ids = "";
		foreach($_POST['elements'] as $id => $on)
			if($on='on') $ids .= (empty($ids) ? "" : ",") . "'$id'";
		
		$DB_trombino->query("DELETE FROM binets WHERE binet_id IN ($ids)");
		$DB_trombino->query("DELETE FROM membres WHERE binet_id IN ($ids)");
		
		$message = "<p>".count($_POST['elements'])." binets viennent d'�tre supprim�s avec succ�s.</p>\n";
	} else {
		ajoute_erreur(ERR_SELECTION_VIDE);
	}
}

// Gestion de la cr�ation
if(isset($_POST['nouveau'])) {
	if(!empty($_POST['nom'])) {
		$DB_trombino->query("INSERT binets SET nom='".$_POST['nom']."'");
		$message = "<p>Le binet ".$_POST['nom']." vient d'�tre cr��.</p>\n";
	} else {
		ajoute_erreur(ERR_TROP_COURT);
	}
}

// G�n�ration de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="admin_binets" titre="Frankiz : liste des binets">
	<h2>Liste des binets</h2>
<?php
	if(!empty($message))
		echo "<p>$message</p>\n";
	if(a_erreur(ERR_SELECTION_VIDE))
		echo "<p>Aucun binet n'est s�lectionn�.</p>\n";
	if(a_erreur(ERR_TROP_COURT))
		echo "<p>Le nom choisi est trop court.</p>\n";
?>
	<liste id="liste_binets" selectionnable="oui" action="admin/binets.php">
		<entete id="nom" titre="Nom"/>
		<entete id="description" titre="Description"/>
<?php
		$DB_trombino->query("SELECT nom,description,binet_id FROM binets ORDER BY nom ASC");
		while(list($nom,$desc,$id) = $DB_trombino->next_row()) {
			echo "\t\t<element id=\"$id\">\n";
			echo "\t\t\t<colonne id=\"nom\">$nom</colonne>\n";
			echo "\t\t\t<colonne id=\"description\">".stripslashes($desc)."</colonne>\n";
			echo "\t\t</element>\n";
		}
?>
		<bouton titre="Supprimer" id="supprimer"/>
	</liste>
	<notice>Pour ajouter un binet, allez dans la partie binet web</notice>
<!--	<formulaire id="nouveau_binet" titre="Nouveau Binet" action="admin/binets.php">
		<champ titre="Nom du binet" id="nom" valeur="" />
		<bouton titre="Nouveau" id="nouveau"/>
	</formulaire>-->
</page>
<?php


require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
