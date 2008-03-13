<?php
/*
	Copyright (C) 2007 Binet Réseau
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
        Page permettant aux admins de modifier les caractéristiques d'un sondage validé.


*/

require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
if(!verifie_permission('admin')&&!verifie_permission('web'))
	acces_interdit();

// Génération de la page
//===============
require_once BASE_FRANKIZ."include/page_header.inc.php";

?>
<page id="modif_sondage" titre="Frankiz : Modifie un sondage">
<h1>Modification de sondages</h1>

<?php

// Enregistrement
$DB_valid->query("LOCK TABLE valid_sondages WRITE");
$DB_valid->query("SET AUTOCOMMIT=0");

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

	if (($temp[0]=='modif')||($temp[0]=='valid')) {
		cache_supprimer('sondages') ;
		if (($_REQUEST['restriction']!='aucune')&&($_REQUEST[$_REQUEST['restriction']])!='')
                	$restriction = $_REQUEST['restriction']."_".$_REQUEST[$_REQUEST['restriction']];
		else $restriction = "aucune";

		$DB_web->query("UPDATE sondage_question SET perime='{$_POST['date']}', titre='{$_POST['titre']}',restriction='$restriction'  WHERE sondage_id='{$temp[1]}'");
	?>
		<commentaire>Modif effectuée</commentaire>
	<?php	
	}
	
	if ($temp[0]=='suppr') {
		cache_supprimer('sondages') ;
		$DB_web->query("DELETE FROM sondage_question WHERE sondage_id='{$temp[1]}'") ;
		$DB_web->query("DELETE FROM sondage_votants WHERE sondage_id='{$temp[1]}'") ;
		$DB_web->query("DELETE FROM sondage_reponse WHERE sondage_id='{$temp[1]}'") ;
	?>
		<warning>Suppression d'un sondage</warning>
	<?php
	}
}
$DB_valid->query("COMMIT");
$DB_valid->query("UNLOCK TABLES");

//===============================

	$DB_web->query("SELECT sondage_id, titre, perime, restriction FROM sondage_question WHERE TO_DAYS(perime) - TO_DAYS(NOW()) >=0 ORDER BY perime DESC");

	while(list($id, $titre, $date, $restriction) = $DB_web->next_row()) {

// Zone de saisie des éléments du sondage
?>

		<formulaire id="sondage_<?php echo $id ?>" titre="Le sondage" action="admin/modif_sondages.php">
			<champ id="titre" titre="Titre" valeur="<?php echo $titre ;?>"/>
			<champ id="date" titre="Date de péremption" valeur="<?php echo $date ;?>"/>

				<?php
				if ($restriction != "") $restr = explode("_",$restriction);
				else $restr = array("aucune","");
				?>

			<choix titre="Restriction" id="restriction" type="radio" valeur="<?php echo $restr[0]; ?>">
				<option id="aucune" titre="Aucune"/>
				<option id="promo" titre="A une promo"/>
				<option id="section" titre="A une section"/>
				<option id="binet" titre="A un binet"/>
			</choix>
		
			<choix titre="Promo" id="promo" type="combo" valeur="<?php if ($restr[0]=="promo") echo $restr[1];?>">
				<option titre="Toutes" id="" />
				<?php
				$DB_trombino->query("SELECT DISTINCT promo FROM eleves ORDER BY promo DESC");
				while( list($promo) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$promo\" id=\"$promo\"/>\n";
				?>
			</choix>

			<choix titre="Section" id="section" type="combo" valeur="<?php if ($restr[0]=="section") echo $restr[1];?>">
				<option titre="Toutes" id=""/>
				<?php
				$DB_trombino->query("SELECT section_id,nom FROM sections ORDER BY nom ASC");
				while( list($section_id,$section_nom) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$section_nom\" id=\"$section_id\"/>\n";
				?>
			</choix>

			<choix titre="Binet" id="binet" type="combo" valeur="<?php if ($restr[0]=="binet") echo $restr[1];?>">
				<option titre="Tous" id=""/>
				<?php
				$DB_trombino->query("SELECT binet_id,nom FROM binets ORDER BY nom ASC");
				while( list($binet_id,$binet_nom) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$binet_nom\" id=\"$binet_id\"/>\n";
				?>
			</choix>

			<bouton id='modif_<?php echo $id ?>' titre="Modifier"/>
			<bouton id='suppr_<?php echo $id ?>' titre='Supprimer' onClick="return window.confirm('Si vous supprimez ce sondage, celui-ci sera supprimé de façon definitive ... Voulez-vous vraiment le supprimer ?')"/>
		</formulaire>
<?php
	}
?>
</page>

<?php
require_once BASE_FRANKIZ."include/page_footer.inc.php";
?>
