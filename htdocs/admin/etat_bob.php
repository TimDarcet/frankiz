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
	Cette page permet de déterminer si le Bôb est ouvert ou non.
	
	$Log$
	Revision 1.2  2004/11/27 20:16:55  pico
	Eviter le formatage dans les balises <note> <commentaire> et <warning> lorsque ce n'est pas necessaire

	Revision 1.1  2004/11/27 18:23:53  pico
	Ajout de l'annonce: 'le bob est ouvert' dans les activités + page de gestion du bob
	

	
	
*/

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!(verifie_permission('admin')||verifie_permission('bob')))
	rediriger_vers("/gestion/");

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="etat_bob" titre="Frankiz : Etat du Bôb">
<?
if(isset($_REQUEST['envoie'])){
?>
	<commentaire>
		L'état du bôb vient d'être changé
	</commentaire>
<?
	$DB_web->query("UPDATE parametres SET valeur='".$_REQUEST['etat']."' WHERE nom='bob'");
}

$DB_web->query("SELECT valeur FROM parametres WHERE nom='bob'");
list($valeur) = $DB_web->next_row();

?>
	<formulaire id="bob" titre="Ouverture du bôb" action="admin/etat_bob.php">
		<choix titre="Le bôb est:" id="etat" type="radio" valeur="<?= $valeur ?>">
				<option titre="Fermé" id="0"/>
				<option titre="ouvert" id="1"/>
		</choix>
		<bouton titre="Valider" id="envoie" onClick="return window.confirm('Voulez vous vraiment changer cette valeur ?')"/>
	</formulaire>

</page>

<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
