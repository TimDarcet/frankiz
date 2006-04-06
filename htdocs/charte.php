<?php
/*
	Copyright (C) 2006 Binet Réseau
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
	Page permettant de signer numériquement la charte ainsi que de la consulter ultérieurement.

*/

require "include/global.inc.php";
demande_authentification(AUTH_MDP,false);


//analyse du formulaire
if(isset($_REQUEST['valid']) && isset($_REQUEST['approuve'])){
	if ($_REQUEST['approuve'] == "on"){
		$DB_web->query("UPDATE  compte_frankiz SET charte = 1 WHERE eleve_id='{$_SESSION['user']->uid}'");
		$_SESSION['user']->charte = true;
	}
}

// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="charte" titre="Frankiz : Charte du BR">
	<h2>Charte du BR</h2>
	<p>Je reconnais donner tous les droits sur mon pauvre PC aux admins@windows sans jamais les blatter.</p>
	<p>J'accepte de verser la somme minime de 50 (cinquante) euros par mois au BR.</p>
	<p>J'accepte de dire "tiens un prez à la rue!" lorsque je verrai alakazam</p>
<?
if(!$_SESSION['user']->charte)
{
?>
	<formulaire id="charte" titre="Acceptaion de la charte" action=
<?php
		echo '"'.htmlentities($_SERVER['REQUEST_URI']).'"';
?>
	>
		<choix id="signature" titre="" type="checkbox">
			<option id="approuve" titre="J'accepte et approuve cette charte"/>
		</choix>
		<bouton id="valid" titre="Validation"/>
	</formulaire>
<?
}
?>
</page>
<? require_once BASE_LOCAL."/include/page_footer.inc.php" ?>