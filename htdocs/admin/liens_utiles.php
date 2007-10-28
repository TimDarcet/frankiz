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
	Modif des liens utiles

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
	if ($temp[0]=='titre') {
		$DB_web->query("UPDATE liens SET titre='".$val."' WHERE lien_id='".$temp[1]."'");
	}
	if ($temp[0]=='url') {
		$DB_web->query("UPDATE liens SET url='".$val."' WHERE lien_id='".$temp[1]."'");
	}
	if ($temp[0]=='descr') {
		$DB_web->query("UPDATE liens SET description='".$val."'  WHERE lien_id='".$temp[1]."'");
	}
	if ($temp[0]=='visible') {
		$DB_web->query("UPDATE liens SET visible_ext='".$val."'  WHERE lien_id='".$temp[1]."'");
	}
	if ($temp[0]=='suppr') {
		$DB_web->query("DELETE FROM liens WHERE lien_id='".$temp[1]."'");
	}
	if ($keys=='rajoutlien') {
		$DB_web->query("INSERT INTO liens SET description='".$_REQUEST['explilien']."', titre='".$_REQUEST['titrelien']."', url='".$_REQUEST['urllien']."', visible_ext='".$_REQUEST['visiblelien']."'");
	}


}


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="admin_liens" titre="Frankiz : Modifier le Vocabulaire">

<h2>Modification des liens utiles</h2>
	<liste id="liste_lien" selectionnable="non" action="admin/liens_utiles.php">
		<entete id="titre" titre="Titre"/>
		<entete id="url" titre="url - visible de l'extérieur"/>
		<entete id="description" titre="Description"/>

<?php
		$DB_web->query("SELECT lien_id,titre,url,description,visible_ext FROM liens ORDER BY titre") ;
		while(list($lien_id,$titre,$url,$description,$visible_ext) = $DB_web->next_row()) {
			echo "\t\t<element id=\"$lien_id\">\n";
				echo "\t\t\t<colonne id=\"titre\"><champ titre=\"\" id='titre_$lien_id' valeur=\"$titre\"/></colonne>\n";
				echo "\t\t\t<colonne id=\"url\"><champ titre=\"\" id='url_$lien_id' valeur=\"$url\"/></colonne>\n";
				echo "\t\t\t<colonne id=\"ext\"><choix type=\"radio\" titre=\"Visible\" id='visible_$lien_id' valeur=\"$visible_ext\">
					<option titre=\"non\" id=\"0\"/><option titre=\"oui\" id=\"1\"/></choix></colonne>\n";
				echo "\t\t\t<colonne id=\"descr\"><zonetext titre=\"\" id='descr_$lien_id'>$description</zonetext><bouton titre='Suppr' id='suppr_$lien_id'/></colonne>\n";
			echo "\t\t</element>\n";
		}
?>
		<bouton titre='MaJ' id='modif'/>
	</liste>
	<formulaire id="ajout_lien" titre="Ajout d'un lien utile" action="admin/liens_utiles.php">
		<champ id="titrelien" titre="Titre" valeur=""/>
		<champ id="urllien" titre="url" valeur=""/>
		<zonetext id="explilien" titre="description"></zonetext>
		<choix titre="Lien visible de l'extérieur" id="visiblelien" type="radio">
						<option titre="non" id="0"/>
						<option titre="oui" id="1"/>
		</choix>
		<bouton id="rajoutlien" titre="Ajouter"/>
	</formulaire>

</page>

<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
