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
	Cette page permet de modifier les paramètres globaux mysql du site
	genre : 
	# la dernière promo qui est sur le site
	# la dernière promo qui est dans le trombi (qui normalment devrait être mis a jour automatiquement)
	
	$Id$

*/

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
if(!verifie_permission('admin'))
	acces_interdit();

	
foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys,2) ;
	// On traite les modifications dites STANDARD
	if ($temp[0]=='modif') {
		$tempo = "id_".$temp[1] ;
		$DB_web->query("UPDATE parametres SET valeur='".$_POST[$tempo]."' WHERE nom='".$temp[1]."'");
	}
	// On taite maintenant les modifications non standard
	if ($keys == "update_lastpromo_ontrombino") {
		$DB_trombino->query("SELECT MAX(promo) FROM eleves");
		list($max_promo) = $DB_trombino->next_row() ;
		$DB_web->query("UPDATE parametres SET valeur='$max_promo' WHERE nom='lastpromo_ontrombino'");
	}
}


// Génération de la page
//===============
require_once BASE_FRANKIZ."include/page_header.inc.php";
?>
<page id="admin_parametre" titre="Frankiz : Modifier les paramètres globaux">

<note>
Vous pouvez ici modifier directement la configuration générale du site.
Si vous devez créer des variables dans la table, essayez de faire qu'elles soient suffisamment explicites.
</note>
<h2>Paramètres du site</h2>
	<liste id="liste" selectionnable="non" action="admin/parametre.php">
		<entete id="nom_var" titre="Nom de la variable"/>
		<entete id="valeur" titre="Valeur"/>
<?php
		$DB_web->query("SELECT nom,valeur FROM parametres ORDER by nom");
		while(list($nom,$valeur) = $DB_web->next_row()) {
?>
			<element id="<?php echo $nom ;?>">
				<colonne id="eleve"><?php echo "$nom" ?></colonne>
				<colonne id="valeur">
<?php
				// Cas Particuliers traité à la main
				if ($nom=="lastpromo_ontrombino") {
					echo $valeur." &#160; " ;
					echo "<bouton titre='Update' id='update_lastpromo_ontrombino'/>" ;
				} else {
				// fin des cas particuliers 
?>
					<champ titre="" id="id_<?php echo $nom ;?>" valeur="<?php echo $valeur ;?>"/>
					<bouton titre='Ok' id='modif_<?php echo $nom ;?>'/>
<?php
				}
?>
				</colonne>
			</element>
<?php
		}
?>
	</liste>
	
</page>

<?php require_once BASE_FRANKIZ."include/page_footer.inc.php" ?>
