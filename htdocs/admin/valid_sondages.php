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
	Page qui permet aux admins de valider un sondage
	
	$Log$
	Revision 1.1  2004/11/17 13:49:49  kikx
	Preparation de la page de validation des sondages


*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="valid_sondage" titre="Frankiz : Valide un sondage">

<h1>Validation des sondages</h1>

<?
// On traite les différents cas de figure d'enrigistrement et validation de qdj :)

// Enregistrer ...

foreach ($_POST AS $keys => $val){

}

//===============================

	$DB_valid->query("SELECT v.sondage_id,v.questions,v.titre,v.eleve_id, e.nom, e.prenom, e.surnom, e.promo FROM valid_sondages as v INNER JOIN trombino.eleves as e USING(eleve_id)");
	while(list($id,$questions,$titre,$eleve_id,$nom, $prenom, $surnom, $promo) = $DB_valid->next_row()) {
	?>
		<formulaire id="form" titre="<?=$titre?>">	
	<?
		decode_sondage($questions) ;
	?>
		</formulaire>
		
		<formulaire id="sond_<? echo $id ?>" titre="Validation de '<?=$titre?>'" action="admin/valid_sondages.php">
			<zonetext titre="Raison du Refus si refus" id="refus_<? echo $eleve_id ;?>" valeur=""/>
			
			<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Valider ce sondage ?')"/>
			<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer ce sondage ?!!!!!')"/>
		</formulaire>
	<?
	}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
