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
i	Affichage du module "Post-it"
*/
	
require_once "include/wiki.inc.php";

// Vérification des droits
if (est_authentifie(AUTH_MINIMUM)) {
	$DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus';");
	list($lastpromo) = extdata_stripslashes($DB_web->next_row());
	$DB_trombino->query("SELECT promo FROM eleves WHERE eleve_id=".$_SESSION['user']->uid.";");
	list($promo) = extdata_stripslashes($DB_trombino->next_row());
	if ($promo == $lastpromo || $promo == $lastpromo-1) {


	$postit_dir = BASE_DATA."postit/";

//===============================
// Recuperation des donnees eventuelles
		$vide = true;
		$postit_titre = "";
		$postit_contenu = "";
		if (isset($_POST["test"]) && $_POST["test"]) {
			$vide = false;
			$postit_titre = $_POST["titre"];
			$postit_contenu = $_POST["contenu"];
			$eleve_id = $_SESSION["user"]->uid;
		}
		elseif (file_exists($postit_dir."info.txt")&&file_exists($postit_dir."contenu.txt")) {
			$vide = false;
			$postit = file($postit_dir."info.txt");
			$postit_titre = rtrim($postit[0]);
			$eleve_id = rtrim($postit[1]);
			$postit_contenu = file_get_contents($postit_dir."contenu.txt");
		}
		if (!$vide) {
			$DB_trombino->query("SELECT nom,prenom,surnom,promo FROM eleves WHERE eleve_id='".$eleve_id."';");
			list($nom,$prenom,$surnom,$promo) = extdata_stripslashes($DB_trombino->next_row());
			$DB_web->query("SELECT 0 FROM annonces_lues WHERE annonce_id=1 AND eleve_id=".$_SESSION["user"]->uid.";");
			$postit_visible = $DB_web->num_rows() == 0;
?>
	<annonce id="1" titre="Post-it : <?php echo $postit_titre ?>" visible="<?=$postit_visible?"oui":"non" ?>" categorie="important">
<?php
			if (file_exists($postit_dir."image")) {
?>
		<image source="<?php echo URL_DATA."postit/image" ; ?>" texte="image"/>
<?php
			}
			echo wikiVersXML($postit_contenu) ;
?>
		<eleve nom="<?=$nom?>" prenom="<?=$prenom?>" promo="<?=$promo?>" surnom="<?=$surnom?>"/>
		<lien url="?lu=1#annonce_1" titre="Faire disparaître" id="annonces_lues"/>
		<br/>
	</annonce>
<?php
		}
	}
}

?>
