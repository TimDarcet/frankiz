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
	Page qui permet aux admins de vider la bdd des activités périmées
	
	$Id$

*/
	
require_once "../include/global.inc.php";


// Vérification des droits
demande_authentification(AUTH_MDP);
if(!verifie_permission('admin')&&!verifie_permission('web'))
	acces_interdit();



// Génération de la page
//===============
require_once BASE_FRANKIZ."include/page_header.inc.php";

?>
<page id="nettoyage" titre="Frankiz : Nettoyage des bases de données du site.">
<h1>Modification d'annonces</h1>

<?php
// On traite les différents cas de figure d'enrigistrement et validation d'annonce :)

// Enregistrer ...

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;


	if ($temp[0]=='annonces') {
		
		$DB_web->query("SELECT annonce_id FROM annonces WHERE perime<".date("Ymd000000",time()- 5 * 24 * 3600)."") ;
		//On supprime aussi l'image si elle existe ...
		$compteur = 0;
		while(list($id)=$DB_web->next_row()) {
			$compteur++;
			$DB_web->query("DELETE FROM annonces_lues WHERE annonces_id='".$id."'") ;
			if (file_exists(DATA_DIR_LOCAL."annonces/$id")){
				unlink(DATA_DIR_LOCAL."annonces/$id") ;
			}
		}
		$DB_web->query("DELETE FROM annonces WHERE perime<".date("Ymd000000",time()- 5 * 24 * 3600)."") ;

	?>
		<warning>Suppression de <?php echo $compteur?> annonces périmées</warning>
	<?php
	}
	
	if ($temp[0]=='affiches') {
		$DB_web->query("SELECT affiche_id FROM affiches WHERE date<'".date("Y-m-d 00:00",time()- 5 * 24 * 3600)."' ") ;
		$compteur = $DB_web->num_rows();
		$DB_web->query("DELETE FROM affiches WHERE date<'".date("Y-m-d 00:00",time()- 5 * 24 * 3600)."' ") ;
	?>
		<warning>Suppression de <?php echo $compteur?> affiches périmées</warning>
	<?php
	}
	
	if ($temp[0]=='qdj') {
		$DB_web->query("SELECT qdj_id FROM qdj WHERE date<'".date("Y-m-d", time() - 365 * 24 * 3600)."' AND date>'0000-00-00'");
		$compteur = $DB_web->num_rows();
		$DB_web->query("DELETE FROM qdj WHERE date<'".date("Y-m-d", time() - 365 * 24 * 3600)."' AND date>'0000-00-00'");
	?>
		<warning>Suppression de <?php echo $compteur?> qdj périmées</warning>
	<?php
	}
	
	if ($temp[0] == "sondage") {
		$DB_web->query("SELECT sondage_id FROM sondage_question WHERE TO_DAYS(perime) - TO_DAYS(NOW()) <-60");
		$compteur = $DB_web->num_rows();
		while(list($id)=$DB_web->next_row()) {
			$DB_web->query("DELETE FROM sondage_votants WHERE sondage_id=$id");
		}
		$DB_web->query("DELETE FROM sondage_question WHERE TO_DAYS(perime) - TO_DAYS(NOW()) <-60");
	?>
		<warning>Suppression de <?php echo $compteur?> sondages périmés et de leurs résultats</warning>
	<?php
	}
}

?>
	<formulaire id="nett_fkz" titre="Nettoyer les bdd Fkz" action="admin/nettoyage.php">
		<choix titre="Eléments à vider" id="element" type="checkbox">
			<option  titre="Supprimer les annonces périmées depuis plus de 5 jours" id="annonces"/>
			<option  titre="Supprimer les affiches périmées depuis plus de 5 jours" id="affiches"/>
			<option  titre="Supprimer les qdj périmées depuis plus de 365 jours" id="qdj"/>
			<option  titre="Supprimer les sondages périmés depuis plus de 60 jours" id="sondage"/>
		</choix>
		<bouton id='mod_compte_fkz' titre='Changer'/>
	</formulaire>

</page>

<?php
require_once BASE_FRANKIZ."include/page_footer.inc.php";
?>
