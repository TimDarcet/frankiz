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
	Page de gestion "automatique" de l'état du bob

	$Id: etat_bob.php 1788 2006-11-19 23:32:53Z alakazam $
	
*/

require_once "../../include/global.inc.php";
// fait on une verification par ip ?
$verifIP = true;

// si demande d'information concernant l'état du bob
if(isset($_GET['estOuvert'])){
	$DB_web->query("SELECT valeur FROM parametres WHERE nom='bob'");
	list($val) = $DB_web->next_row();
	$valeur = intval($val);
	if ($valeur && (time()-$valeur > 36000)) {
		$DB_web->query("UPDATE parametres SET valeur='0' WHERE nom='bob';");
		$valeur = 0;
	}
	if($valeur){
		echo "1"; 
		//echo "Le message a afficher";
	}
	else{
		echo "0"; 
		//echo "Le message a afficher";
	}
}
else{ //sinon c'est normalement une demande de modification
	if(isset($_REQUEST["nouvel_etat"])&&((!$verifIP)||($_SERVER['REMOTE_ADDR']== IP_DU_BOB))&& isset($_REQUEST['mdp_bob'])&&(md5($_REQUEST['mdp_bob'])== MDP_DU_BOB)){
		if ($_REQUEST["nouvel_etat"] == "1"){
			$DB_web->query("UPDATE parametres SET valeur='".time()."' WHERE nom='bob'");
		}
		else{
			$DB_web->query("UPDATE parametres SET valeur='0' WHERE nom='bob'");
		}
	}
}
//surtout pas de saut de ligne apres la balise !
?>
