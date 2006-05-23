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
	Page qui permet de modifier le contenu du module postit, pour les personnes a qui
	il est confie pour le moment present
*/
	
require_once "../include/global.inc.php";
require_once "../include/global_func.inc.php";
require_once "../include/wiki.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')&&!verifie_permission('web')&&!verifie_permission('postit'))
	acces_interdit();



// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";





$postit_dir = BASE_DATA."postit/";

?>
<page id="modif_annonce" titre="Frankiz : Module Post-it">
<h1>Edition du module Post-it</h1>

<?
// On traite les différents cas de figure d'enrigistrement et validation d'annonce :)



// éventuelles modifications de l'image
	// nettoyage prealable (si on etait parti sans valider, une image a pu rester dans imagetemp)
	if (!isset($_POST["modif"]) && !isset($_POST["test"]) && file_exists($postit_dir."imagetemp")) {
		unlink($postit_dir."imagetemp");
	}

	$erreur_upload = 0;
	if (isset($_POST["modif"]) && $_POST["modif"]) {

		// remplacement de l'image
		$imgfilename = $postit_dir."image";
		if (!isset($_POST["supprimgtemp"])&&(isset($_FILES['file']))&&($_FILES['file']['size']!=0)) {
			if($original_size = getimagesize($_FILES['file']['tmp_name'])) {
				$larg = $original_size[0];
				$haut = $original_size[1];
				if (($larg>400)||($haut>300)) {
					$erreur_upload = 1;
				} else {
					if (file_exists($imgfilename)) {
						unlink($imgfilename);
					}
					move_uploaded_file($_FILES['file']['tmp_name'],$imgfilename);
				}
			} else {
				$erreur_upload = 1;
			}
		}
		// sinon mais s'il y a une image temporaire (et si on n'a pas demande de retablir l'ancienne), on remplace l'image par la temporaire
		elseif (!isset($_POST["supprimgtemp"]) && file_exists($imgfilename."temp")) {
			if (file_exists($imgfilename)) {
				unlink($imgfilename);
			}
			rename($imgfilename."temp",$imgfilename);
		}

		// suppression de l'image si demandée
		if (isset($_POST['supprimg'])) {
			if (file_exists($imgfilename)) {
				unlink($imgfilename);
			}
			$erreur_upload = 0;
		}

		// suppression de l'image temporaire (eventuelle) dans tous les cas
		if (file_exists($imgfilename."temp")) {
			unlink($imgfilename."temp");
		}

		// affichage de l'erreur éventuelle (à l'upload de l'image)
		if ($erreur_upload == 1) {
			echo "<warning>L'image n'est pas au bon format, ou est trop grande.</warning>";
		}

	}

	elseif (isset($_POST["test"]) && $_POST["test"]) {
		
		// remplacement de l'image
		$imgfilename = $postit_dir."imagetemp";
		if ((isset($_FILES['file']))&&($_FILES['file']['size']!=0)) {
			if($original_size = getimagesize($_FILES['file']['tmp_name'])) {
				$larg = $original_size[0];
				$haut = $original_size[1];
				if (($larg>400)||($haut>300)) {
					$erreur_upload = 1;
				} else {
					if (file_exists($imgfilename)) {
						unlink($imgfilename);
					}
					move_uploaded_file($_FILES['file']['tmp_name'],$imgfilename);
				}
			} else {
				$erreur_upload = 1;
			}
		}

		// suppression de l'image si demandée
		if (isset($_POST['supprimgtemp']) || isset($_POST['supprimg'])) {
			if (file_exists($imgfilename)) {
				unlink($imgfilename);
			}
			$erreur_upload = 0;
		}

		// affichage de l'erreur éventuelle (à l'upload de l'image)
		if ($erreur_upload == 1) {
			echo "<warning>L'image n'est pas au bon format, ou est trop grande.</warning>";
		}

	}


// Cas de l'edition du module
	if (isset($_POST["modif"]) && $_POST["modif"]) {
		if (file_exists($postit_dir."info.txt")) {
			unlink($postit_dir."info.txt");
		}
		$eleve_id = $_SESSION['user']->uid;
		file_put_contents($postit_dir."info.txt",$_POST["titre"]."\n".$eleve_id);
		
		if (file_exists($postit_dir."contenu.txt")) {
			unlink($postit_dir."contenu.txt");
		}
		file_put_contents($postit_dir."contenu.txt",$_POST["contenu"]);

		$DB_web->query("DELETE FROM annonces_lues WHERE annonce_id=1;");

		
		$DB_trombino->query("SELECT nom,prenom,surnom,promo FROM eleves WHERE eleve_id='".$eleve_id."';");
		list($nom,$prenom,$surnom,$promo) = extdata_stripslashes($DB_trombino->next_row());
		$mailcontenu = "<strong>Bonjour,</strong><br/><br/>".
			"$prenom $nom a modifié le module Post-it : <br/>".
			$_POST['titre']."<br/><br/>".
			"Cette modification entraînant la réapparition du module Post-it chez tous les élèves qui l'auraient effacés, ".
			"veuillez vous assurer qu'il s'agit bien d'une modification légitime et non d'un abus ".
			"auquel cas un retrait du droit attribué à $prenom $nom peut être envisagé.<br/><br/>".
			"Cordialement,<br/>".
			"Le Webmestre de Frankiz<br>";
		couriel(WEBMESTRE_ID,"[Frankiz] Modification du module Post-it",$mailcontenu,$eleve_id);

	?>
		<commentaire>Edition effectuée</commentaire>
	<?	
	}

// Cas de la suppression du module
	if (isset($_POST["suppr"]) && $_POST["suppr"]) {
		if (file_exists($postit_dir."info.txt")) {
			unlink($postit_dir."info.txt");
		}
		if (file_exists($postit_dir."contenu.txt")) {
			unlink($postit_dir."contenu.txt");
		}
		
		//On supprime aussi l'image si elle existe ...
		$supp_image = "" ;
		if (file_exists($postit_dir."image")){
			unlink($postit_dir."image") ;
			$supp_image = " et de son image associée" ;
		}
	?>
		<warning>Suppression du module Post-it<? echo $supp_image?></warning>
	<?
	}

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
		<annonce titre="Post-it : <?php  echo $titre ?>" 
				categorie="">
				<?php
				if (file_exists($postit_dir."imagetemp")){
				?>
					<image source="<?php echo URL_DATA."postit/imagetemp" ; ?>" texte="image"/>
				<?php
				}
				elseif (!isset($_POST["supprimg"]) && file_exists($postit_dir."image")) {
				?>
					<image source="<?php echo URL_DATA."postit/image" ; ?>" texte="image"/>
				<?php
				}
				echo wikiVersXML($contenu) ;
				?>
				<eleve nom="<?=$nom?>" prenom="<?=$prenom?>" promo="<?=$promo?>" surnom="<?=$surnom?>"/>
		</annonce>
<?php
	} else {
?>
		<annonce titre="Post-it" categorie="">
			Module désactivé
		</annonce>
<?php
	}
	
// Zone de saisie de l'annonce
?>

		<formulaire id="postit" titre="Edition du post-it" action="gestion/module_postit.php">
			<note>
				Le texte du post-it utilise le format wiki rappelé en bas de la page et décrit dans l'<lien url="helpwiki.php" titre="aide wiki"/><br/>
			</note>
			<champ id="titre" titre="Le titre" valeur="<?php echo $titre; ?>"/>
			<zonetext id="contenu" titre="Le texte"><?php echo $contenu; ?></zonetext>
			
			<note>L'image doit être un fichier gif, png ou jpeg ne dépassant pas 400x300 pixels et 250Ko.</note>
			<fichier id="file" titre="Modifier l'image" taille="250000"/>
<?php
		if (isset($_POST["test"]) && isset($_POST["supprimg"])) {
			$supprimg = " valeur='supprimg'";
		} else {
			$supprimg = "";
		}
?>
			<choix titre="Pas d'image" id="supprimg" type="checkbox"<?php echo $supprimg; ?>>
				<option id="supprimg" titre="" />
			</choix>
			<choix titre="Rétablir l'ancienne image" id="supprimgtemp" type="checkbox">
				<option id="supprimgtemp" titre="" />
			</choix>
			
			<bouton id='test' titre="Tester"/>
			<bouton id='modif' titre="<?php echo ($vide)? 'Créer' : 'Valider'; ?>"/>
<?php
	if (!$vide) {
?>
			<bouton id='suppr' titre='Supprimer le module Post-it' onClick="return window.confirm('Le module post-it disparaîtra, toutes les données entrées seront perdues. Etes-vous sûr de vouloir continuer ?')"/>
<?php
	}
?>
		</formulaire>
<?
		affiche_syntaxe_wiki();
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
