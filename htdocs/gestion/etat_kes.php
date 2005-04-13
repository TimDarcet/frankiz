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
	Cette page permet de déterminer si la Kès est ouvert ou non.
	
	$Log$
	Revision 1.2  2005/04/13 17:09:59  pico
	Passage de tous les fichiers en utf8.

	Revision 1.1  2005/01/18 12:11:49  pico
	Etat de la kès + validation des mails promos dans l'interface de la Kès
	
	
*/

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!(verifie_permission('admin')||verifie_permission('kes')))
	acces_interdit();

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="etat_bob" titre="Frankiz : Etat de la kès">
<?
if(isset($_POST['envoie'])){
?>
	<commentaire>
		L'état de la Kès vient d'être changé
	</commentaire>
<?
	$DB_web->query("UPDATE parametres SET valeur='".$_REQUEST['etat']."' WHERE nom='kes'");
}

$DB_web->query("SELECT valeur FROM parametres WHERE nom='kes'");
list($valeur) = $DB_web->next_row();

?>
	<formulaire id="kes" titre="Ouverture de la kès" action="gestion/etat_kes.php">
		<choix titre="La Kès est:" id="etat" type="radio" valeur="<?= $valeur ?>">
				<option titre="Fermée" id="0"/>
				<option titre="ouverte" id="1"/>
		</choix>
		<bouton titre="Valider" id="envoie" onClick="return window.confirm('Voulez vous vraiment changer cette valeur ?')"/>
	</formulaire>

</page>

<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
