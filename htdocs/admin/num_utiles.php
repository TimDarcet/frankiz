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
	Numero utiles
	
	$Log$
	Revision 1.1  2004/12/17 13:18:47  kikx
	Rajout des numéros utiles car c'est une demande importante

	Revision 1.2  2004/11/29 17:27:32  schmurtz
	Modifications esthetiques.
	Nettoyage de vielles balises qui trainaient.
	
	Revision 1.1  2004/10/31 22:14:52  kikx
	Oubli
*/

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/gestion/");
	
	
foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys,2) ;
	// On traite les modifications dites STANDARD
	if ($temp[0]=='endroit') {
		$DB_web->query("UPDATE num_utiles SET endroit='$val' WHERE num_id='".$temp[1]."'");
	}
	if ($temp[0]=='poste') {
		$DB_web->query("UPDATE num_utiles SET poste='$val' WHERE num_id='".$temp[1]."'");
	}
	if ($temp[0]=='categorie') {
		$DB_web->query("UPDATE num_utiles SET categorie='$val' WHERE num_id='".$temp[1]."'");
	}
	if ($temp[0]=='suppr') {
		$DB_web->query("DELETE FROM num_utiles WHERE num_id='".$temp[1]."'");
	}
}


if (isset($_REQUEST['ajout'])) {
	$DB_web->query("INSERT INTO num_utiles SET categorie='".$_REQUEST['categorieadd']."' ,endroit='".$_REQUEST['endroitadd']."', poste='".$_REQUEST['posteadd']."'");
}
	
	
// génération de la page
require "../include/page_header.inc.php";
?>
<page id='num_utiles' titre='Frankiz : Numéros utiles'>
<h1>Numeros Utiles</h1>
<?
$DB_web->query("SELECT DISTINCT categorie FROM num_utiles GROUP BY categorie") ;
while(list($categorie) = $DB_web->next_row()) {
?>
	<h2><?=$categorie?></h2>
	<liste id="liste_num" selectionnable="non" action="admin/num_utiles.php">
		<entete id="categorie" titre="Catégorie"/>
		<entete id="endroit" titre="Nom"/>
		<entete id="poste" titre="num. Poste"/>
		<entete id="suppr" titre=""/>
<?		
		$DB_web->push_result() ;
		$DB_web->query("SELECT num_id,endroit,poste FROM num_utiles WHERE categorie='$categorie' ORDER BY endroit") ;
		while(list($id,$endroit,$poste) = $DB_web->next_row()) {
			echo "\t\t<element id=\"$id\">\n";
				echo "\t\t\t<colonne id=\"categorie\"><champ id='categorie_$id' titre='Categorie' valeur='$categorie'/></colonne>\n";
				echo "\t\t\t<colonne id=\"endroit\"><champ id='endroit_$id' titre='Endroit' valeur='$endroit'/></colonne>\n";
				echo "\t\t\t<colonne id=\"poste\"><champ id='poste_$id' titre='Num de Poste' valeur='$poste'/></colonne>\n";
				echo "\t\t\t<colonne id=\"suppr\"><bouton id='suppr_$id' titre='Suppr'/></colonne>\n";
			echo "\t\t</element>\n";
		}
		$DB_web->pop_result() ;
?>
		<bouton id='save' titre='Enregistrer toutes les modifs'/>
	</liste>
<?
}

?>
<h1>Création d'un nouveau numéro</h1>

		<formulaire id="binet_web" titre="Nouveau numéro" action="admin/num_utiles.php">
			<note>La categorie est le titre de la sous section des numéros utiles (respectez la casse si vous voulez qu'elle apparaisse dans la même catégorie qu'un autre numéro</note>
			<champ id="categorieadd" titre="Categorie" valeur=""/>
			<champ id="endroitadd" titre="Endroit" valeur=""/>
			<champ id="posteadd" titre="Poste" valeur=""/>
			<bouton id='ajout' titre="Ajouter"/>
		</formulaire>
		

</page>
<?
require_once "../include/page_footer.inc.php";
?>