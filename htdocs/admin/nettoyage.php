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
	
	$Log$
	Revision 1.8  2005/01/06 23:31:31  pico
	La QDJ change à 0h00 (ce n'est plus la question du jour plus un petit peu)

	Revision 1.7  2004/12/17 19:55:44  pico
	Ajout d'une page pour voir l'historique des qdj
	
	Revision 1.6  2004/12/17 17:25:08  schmurtz
	Ajout d'une belle page d'erreur.
	
	Revision 1.5  2004/12/10 20:23:50  kikx
	Pour supprimer les entrées des annonces non lues si celle ci n'existe plus ... evite d'exploser les tables
	
	Revision 1.4  2004/12/07 19:53:05  pico
	Remise en place des paramètres de skin
	Mise à jour css classique
	
	Revision 1.3  2004/12/07 13:10:56  pico
	Passage du nettoyage en formulaire
	
	Revision 1.2  2004/12/07 08:45:13  pico
	Nettoyage des qdj
	
	Revision 1.1  2004/12/07 08:36:39  pico
	Ajout d'une page pour pouvoir vider un peu les bases de données (genre pas garder les news qui datent de vieux)
		
*/
	
require_once "../include/global.inc.php";


// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')&&!verifie_permission('web'))
	acces_interdit();



// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="nettoyage" titre="Frankiz : Nettoyage des bases de données du site.">
<h1>Modification d'annonces</h1>

<?
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
		<warning>Suppression de <? echo $compteur?> annonces périmées</warning>
	<?
	}
	
	if ($temp[0]=='affiches') {
		$DB_web->query("SELECT affiche_id FROM affiches WHERE date<'".date("Y-m-d 00:00",time()- 5 * 24 * 3600)."' ") ;
		$compteur = $DB_web->num_rows();
		$DB_web->query("DELETE FROM affiches WHERE date<'".date("Y-m-d 00:00",time()- 5 * 24 * 3600)."' ") ;
	?>
		<warning>Suppression de <? echo $compteur?> affiches périmées</warning>
	<?
	}
	
	if ($temp[0]=='qdj') {
		$DB_web->query("SELECT qdj_id FROM qdj WHERE date<'".date("Y-m-d", time() - 365 * 24 * 3600)."' AND date>'0000-00-00'");
		$compteur = $DB_web->num_rows();
		$DB_web->query("DELETE FROM qdj WHERE date<'".date("Y-m-d", time() - 365 * 24 * 3600)."' AND date>'0000-00-00'");
	?>
		<warning>Suppression de <? echo $compteur?> qdj périmées</warning>
	<?
	}
	
	if ($temp[0] == "sondage") {
		$DB_web->query("SELECT sondage_id FROM sondage_question WHERE TO_DAYS(perime) - TO_DAYS(NOW()) <-60");
		$compteur = $DB_web->num_rows();
		while(list($id)=$DB_web->next_row()) {
			$DB_web->query("DELETE FROM sondage_votants WHERE sondage_id=$id");
		}
		$DB_web->query("DELETE FROM sondage_question WHERE TO_DAYS(perime) - TO_DAYS(NOW()) <-60");
	?>
		<warning>Suppression de <? echo $compteur?> sondages périmés et de leurs résultats</warning>
	<?
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
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
