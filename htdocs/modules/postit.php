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
if (est_interne() && est_authentifie(AUTH_MINIMUM)) {



$postit_dir = BASE_DATA."postit/";

//===============================
// Recuperation des donnees eventuelles
	$vide = true;
	$titre = "";
	$contenu = "";
	if (isset($_POST["test"]) && $_POST["test"]) {
		$vide = false;
		$titre = $_POST["titre"];
		$contenu = $_POST["contenu"];
		$eleve_id = $_SESSION["user"]->uid;
	}
	elseif (file_exists($postit_dir."info.txt")&&file_exists($postit_dir."contenu.txt")) {
		$vide = false;
		$postit = file($postit_dir."info.txt");
		$titre = rtrim($postit[0]);
		$eleve_id = rtrim($postit[1]);
		$contenu = file_get_contents($postit_dir."contenu.txt");
	}
	if (!$vide) {
		$DB_trombino->query("SELECT nom,prenom,surnom,promo FROM eleves WHERE eleve_id='".$eleve_id."';");
		list($nom,$prenom,$surnom,$promo) = extdata_stripslashes($DB_trombino->next_row());
?>
		<module id="postit" titre="<?php  echo $titre ?>">
				<?php
				if (!isset($_POST["supprimg"]) && file_exists($postit_dir."image")) {
				?>
					<image source="<?php echo URL_DATA."postit/image" ; ?>" texte="image"/>
				<?php
				}
				echo wikiVersXML($contenu) ;
				?>
				<eleve nom="<?=$nom?>" prenom="<?=$prenom?>" promo="<?=$promo?>" surnom="<?=$surnom?>"/>
		</module>
<?php
	}
}

?>
