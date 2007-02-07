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
	
	$Id$

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
<?php
if(isset($_POST['envoie'])){
?>
	<commentaire>
		L'état de la Kès vient d'être changé
	</commentaire>
<?php
	$DB_web->query("UPDATE parametres SET valeur='".$_REQUEST['etat']."' WHERE nom='kes'");
}

if (isset($_POST['envoie_lienik'])) {
	
	$file = rawurldecode($_POST['ik']);
	$file_encode = rawurlencode($file);
	$path = escapeshellarg(BASE_LOCAL."/binets/ik/".$file);
	$path2 = escapeshellarg(BASE_LOCAL."/../data/ik_thumbnails/".$file.".png");

	$ret = exec ("convert ".$path."[0] -resize '150x212' ".$path2."; echo $?");

	if ($ret != 0) {
?>
		<warning>
			Echec de la mise à jour du lien vers l'IK électronique.
		</warning>
<?
	} else {

		$DB_web->query("UPDATE parametres SET valeur = '".$file_encode."' WHERE nom='lienik'");

		cache_supprimer ('lienik');
?>
		<commentaire>
			Le lien vers l'IK électronique vient d'être changé		
		</commentaire>

<?
	}
}

$DB_web->query("SELECT valeur FROM parametres WHERE nom='kes'");
list($valeur) = $DB_web->next_row();

?>
	<formulaire id="kes" titre="Ouverture de la kès" action="gestion/etat_kes.php">
		<choix titre="La Kès est:" id="etat" type="radio" valeur="<?php echo $valeur; ?>">
				<option titre="Fermée" id="0"/>
				<option titre="ouverte" id="1"/>
		</choix>
		<bouton titre="Valider" id="envoie" onClick="return window.confirm('Voulez vous vraiment changer cette valeur ?')"/>
	</formulaire>

<?
$DB_web->query("SELECT valeur FROM parametres WHERE nom='lienik'");
list($lienik) = $DB_web->next_row();

$iks = glob(BASE_LOCAL."/binets/ik/*.pdf");

$choix_possibles = array();

foreach ($iks as $ik)
{
	if (is_file($ik)) 
	{
		$key = filemtime($ik);
		while (isset($choix_possibles[$key])) {
			$key++;
		}
		$choix_possibles[$key] = basename($ik);
	}
}

krsort($choix_possibles);

?>

	<formulaire id="lienik" titre="Lien vers l'IK electronique" action="gestion/etat_kes.php">
		<choix titre="Selectionnez le nouvel IK" type='radio' id="ik" valeur="<? echo $lienik; ?>">
<?
		if (!isset($_REQUEST['voirtout'])) {
			$nbr_ik = 5;
		} else {
			$nbr_ik = 0;
		}

		$count = 0;
		foreach ($choix_possibles as $ik) {
			echo "hello";

			echo "<option titre=\"$ik\" id=\"".rawurlencode($ik)."\" />";

			$count++;
			if ($count == $nbr_ik) {
				break;
			}
		}

?>
		</choix>

		<bouton titre="Voir tous les IK" id="voirtout" />
		<bouton titre="Modifier" id="envoie_lienik" />
	</formulaire>
</page>

<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
