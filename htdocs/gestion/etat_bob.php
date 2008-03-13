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
	Cette page permet de déterminer si le Bôb est ouvert ou non.
	
	$Id$
	
*/

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
if(!(verifie_permission('admin')||verifie_permission('bob')))
	acces_interdit();

// Génération de la page
//===============
require_once BASE_FRANKIZ."include/page_header.inc.php";

?>
<page id="etat_bob" titre="Frankiz : Etat du Bôb">
<?php
if(isset($_POST['envoie'])){
?>
	<commentaire>
		L'état du bôb vient d'être changé
	</commentaire>
<?php
	if ($_REQUEST['etat'] == "1") {
		ouvrirBob();
	} else {
		fermerBob();
	}
}

if(isset($_POST['ajout_kawa']) &&(strtotime($_REQUEST['date']) >(time()))&&($_REQUEST['date']!="0000-00-00")){
	$DB_web->query("INSERT INTO kawa SET date='".$_POST['date']."', section_id='".$_POST['section']."' ");
	echo"<commentaire>Tour kawa ajouté</commentaire>";
}

if(isset($_GET['del'])){
	$DB_web->query("DELETE FROM kawa WHERE date='".$_GET['del']."'");
	echo"<commentaire>Tour kawa supprimé</commentaire>";
}

$valeur = getEtatBob();


?>
	<formulaire id="bob" titre="Ouverture du bôb" action="gestion/etat_bob.php">
		<choix titre="Le bôb est:" id="etat" type="radio" valeur="<?php echo $valeur; ?>">
				<option titre="Fermé" id="0"/>
				<option titre="ouvert" id="1"/>
		</choix>
		<bouton titre="Valider" id="envoie" onClick="return window.confirm('Voulez vous vraiment changer cette valeur ?')"/>
	</formulaire>

	
	<formulaire id="kawa" titre="Ajouter un tour kawa" action="gestion/etat_bob.php">
		<choix titre="Section" id="section" type="combo" valeur="">
<?php
			$DB_trombino->query("SELECT section_id,nom FROM sections ORDER BY nom ASC");
			while( list($section_id,$section_nom) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$section_nom\" id=\"$section_id\"/>\n";
?>
		</choix>
		<champ id="date" titre="Date (année-mois-jour)" valeur="0000-00-00"/>
		<bouton titre="Valider" id="ajout_kawa" onClick="return window.confirm('Voulez vous vraiment ajouter ce tour ?')"/>
	</formulaire>
	
<?php
	// Génération des tours kawa
	$DB_web->query("SELECT kawa.date,sections.nom FROM kawa LEFT JOIN trombino.sections ON kawa.section_id=sections.section_id WHERE (kawa.date>=\"".date("Y-m-d",time())."\")");
	$i = 0;
	 echo "<liste id=\"tour_kawa\" titre=\"Liste des tours kawa prévus\" selectionnable=\"non\">\n";
	 echo "<entete id=\"jour\" titre=\"Date\"/>";
	 echo "<entete id=\"kawa\" titre=\"Section\"/>";
	while(list($date,$groupe)=$DB_web->next_row()){
		if(strcasecmp("personne", $groupe) != 0 && $groupe != "") {
			// si c'est le premier tour kawa, on ouvre la liste
			echo "<element id=\"$i\">";
			echo "<colonne id=\"jour\">$date</colonne>";
			echo "<colonne id=\"kawa\">$groupe</colonne>";
			echo "<colonne id=\"supprimer\"><lien url='gestion/etat_bob.php?del=$date' titre='Supprimer'/></colonne>";
			echo "</element>\n";
			$i++;
		}
	}
	echo "</liste>\n";
?>
</page>

<?php require_once BASE_FRANKIZ."include/page_footer.inc.php" ?>
