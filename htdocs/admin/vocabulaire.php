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
	Modif du vocabulaire
	
	$Id$

*/

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
if (!verifie_permission('admin')&&!verifie_permission('web'))
	acces_interdit();

	
foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys,2) ;
	// On traite les modifications dites STANDARD
	if ($temp[0]=='mot') {
		$DB_web->query("UPDATE vocabulaire SET mot='".$val."' WHERE vocab_id='".$temp[1]."'");
	}
	if ($temp[0]=='descr') {
		$DB_web->query("UPDATE vocabulaire SET explication='".$val."' WHERE vocab_id='".$temp[1]."'");
	}
	if ($temp[0]=='suppr') {
		$DB_web->query("DELETE FROM vocabulaire WHERE vocab_id='".$temp[1]."'");
	}
	if ($keys=='rajoutvoc') {
		$DB_web->query("INSERT INTO vocabulaire SET explication='".$_REQUEST['explivocab']."', mot='".$_REQUEST['motvocab']."'");
	}

	
}


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="admin_vocabulaire" titre="Frankiz : Modifier le Vocabulaire">

<h2>Modification du vocabulaire</h2>
	<liste id="liste_voc" selectionnable="non" action="admin/vocabulaire.php">
		<entete id="mot" titre="Expression"/>
		<entete id="descr" titre="Description"/>
<?php
		$DB_web->query("SELECT vocab_id,mot,explication FROM vocabulaire ORDER BY mot") ;
		while(list($vocab_id,$mot,$explication) = $DB_web->next_row()) {
			echo "\t\t<element id=\"$vocab_id\">\n";
				echo "\t\t\t<colonne id=\"mot\"><champ titre=\"\" id='mot_$vocab_id' valeur=\"$mot\"/></colonne>\n";
				echo "\t\t\t<colonne id=\"descr\"><zonetext titre=\"\" id='descr_$vocab_id'>$explication</zonetext><bouton titre='Suppr' id='suppr_$vocab_id'/></colonne>\n";
			echo "\t\t</element>\n";
		}
?>
		<bouton titre='MaJ' id='modif'/>
	</liste>
	<formulaire id="ajout_vocab" titre="Ajout d'un mot de Vocabulaire" action="admin/vocabulaire.php">
		<champ id="motvocab" titre="Mot" valeur=""/>
		<zonetext id="explivocab" titre="contenu"></zonetext>
		<bouton id="rajoutvoc" titre="Ajouter"/>
	</formulaire>

</page>

<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
