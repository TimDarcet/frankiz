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
	affichage d'un sondage

	$Log$
	Revision 1.1  2004/11/17 23:46:21  kikx
	Prepa pour le votes des sondages


	
*/

require_once "include/global.inc.php";

demande_authentification(AUTH_MINIMUM);

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="sondage" titre="Frankiz : Sondage">
<?
$DB_web->query("SELECT v.perime, v.sondage_id,v.questions,v.titre,v.eleve_id, e.nom, e.prenom, e.promo FROM sondage_question as v INNER JOIN trombino.eleves as e USING(eleve_id) WHERE sondage_id='{$_GET['id']}'");
if ($DB_web->num_rows()==1) {
	list($date,$id,$questions,$titre,$eleve_id,$nom, $prenom, $promo) = $DB_web->next_row() ;
?>
	<formulaire id="form" titre="<?=$titre?> (<?=date("d/m",strtotime($date))?>)">
	<?
	decode_sondage($questions) ;
	?>
	</formulaire>
<?
} else {
?>
<warning>Le sondage que tu demandes n'existes plus ou n'a jamais existé</warning>
<?
}
?>
</page>
<?php require "include/page_footer.inc.php" ?>
