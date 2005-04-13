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
	
	$Log$
	Revision 1.7  2005/04/13 17:09:59  pico
	Passage de tous les fichiers en utf8.

	Revision 1.6  2005/03/08 11:58:06  pico
	Correction bug, permet d'effacer des tours kawa
	
	Revision 1.5  2005/01/06 23:31:31  pico
	La QDJ change à 0h00 (ce n'est plus la question du jour plus un petit peu)
	
	Revision 1.4  2005/01/04 13:30:13  pico
	Ajout possibilité de virer des tours kawa
	
	Revision 1.3  2004/12/17 17:25:08  schmurtz
	Ajout d'une belle page d'erreur.
	
	Revision 1.2  2004/12/16 13:00:41  pico
	INNER en LEFT
	
	Revision 1.1  2004/12/15 01:44:15  schmurtz
	deplacement de la page d'admin du bob de admin vers gestion
	
	Revision 1.3  2004/12/07 21:54:09  pico
	Interface d'ajout des tours kawa pour le bob
	
	Revision 1.2  2004/11/27 20:16:55  pico
	Eviter le formatage dans les balises <note> <commentaire> et <warning> lorsque ce n'est pas necessaire
	
	Revision 1.1  2004/11/27 18:23:53  pico
	Ajout de l'annonce: 'le bob est ouvert' dans les activités + page de gestion du bob
	

	
	
*/

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!(verifie_permission('admin')||verifie_permission('bob')))
	acces_interdit();

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="etat_bob" titre="Frankiz : Etat du Bôb">
<?
if(isset($_POST['envoie'])){
?>
	<commentaire>
		L'état du bôb vient d'être changé
	</commentaire>
<?
	$DB_web->query("UPDATE parametres SET valeur='".$_REQUEST['etat']."' WHERE nom='bob'");
}

if(isset($_POST['ajout_kawa']) &&(strtotime($_REQUEST['date']) >(time()))&&($_REQUEST['date']!="0000-00-00")){
	$DB_web->query("INSERT INTO kawa SET date='".$_POST['date']."', section_id='".$_POST['section']."' ");
	echo"<commentaire>Tour kawa ajouté</commentaire>";
}

if(isset($_GET['del'])){
	$DB_web->query("DELETE FROM kawa WHERE date='".$_GET['del']."'");
	echo"<commentaire>Tour kawa supprimé</commentaire>";
}

$DB_web->query("SELECT valeur FROM parametres WHERE nom='bob'");
list($valeur) = $DB_web->next_row();

?>
	<formulaire id="bob" titre="Ouverture du bôb" action="gestion/etat_bob.php">
		<choix titre="Le bôb est:" id="etat" type="radio" valeur="<?= $valeur ?>">
				<option titre="Fermé" id="0"/>
				<option titre="ouvert" id="1"/>
		</choix>
		<bouton titre="Valider" id="envoie" onClick="return window.confirm('Voulez vous vraiment changer cette valeur ?')"/>
	</formulaire>

	
	<formulaire id="kawa" titre="Ajouter un tour kawa" action="gestion/etat_bob.php">
		<choix titre="Section" id="section" type="combo" valeur="">
<?
			$DB_trombino->query("SELECT section_id,nom FROM sections ORDER BY nom ASC");
			while( list($section_id,$section_nom) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$section_nom\" id=\"$section_id\"/>\n";
?>
		</choix>
		<champ id="date" titre="Date (année-mois-jour)" valeur="0000-00-00"/>
		<bouton titre="Valider" id="ajout_kawa" onClick="return window.confirm('Voulez vous vraiment ajouter ce tour ?')"/>
	</formulaire>
	
<?
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

<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
