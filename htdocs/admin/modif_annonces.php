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
	Page qui permet aux admins de modifier une annonce validée
	
	$Id$
	
*/
	
require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
if(!verifie_permission('admin')&&!verifie_permission('web'))
	acces_interdit();



// Génération de la page
//===============
require_once BASE_FRANKIZ."include/page_header.inc.php";

?>
<page id="modif_annonce" titre="Frankiz : Modifie une annonce">
<h1>Modification d'annonces</h1>

<?php
// On traite les différents cas de figure d'enrigistrement et validation d'annonce :)

// Enregistrer ...
$DB_valid->query("LOCK TABLE valid_annonces WRITE");
$DB_valid->query("SET AUTOCOMMIT=0");

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

// éventuelles modifications de l'image
	$erreur_upload = 0;
	if ($temp[0] == 'modif') {

		// remplacement de l'image
		$imgfilename = DATA_DIR_LOCAL."annonces/".$temp[1];
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
		if (isset($_POST['supprimg'])) {
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


	
	if (($temp[0]=='modif')||($temp[0]=='valid')) {
		if (isset($_REQUEST['ext_auth']))
			$temp_ext = '1'  ;
		else 
			$temp_ext = '0' ;

		if (isset($_REQUEST['important']))
			$temp_imp = '1';
		else
			$temp_imp = '0' ;



		$DB_web->query("UPDATE annonces SET perime='{$_POST['date']}', titre='{$_POST['titre']}', contenu='{$_POST['text']}',exterieur=$temp_ext, en_haut=$temp_imp  WHERE annonce_id='{$temp[1]}'");	
	?>
		<commentaire>Modif effectuée</commentaire>
	<?php	
	}
	
	if ($temp[0]=='suppr') {
		$DB_web->query("DELETE FROM annonces WHERE annonce_id='{$temp[1]}'") ;
		//On supprime aussi l'image si elle existe ...
		
		$supp_image = "" ;
		if (file_exists(DATA_DIR_LOCAL."annonces/{$temp[1]}")){
			unlink(DATA_DIR_LOCAL."annonces/{$temp[1]}") ;
			$supp_image = " et de son image associée" ;
		}
	?>
		<warning>Suppression d'une annonce<?php echo $supp_image?></warning>
	<?php
	}
}
$DB_valid->query("COMMIT");
$DB_valid->query("UNLOCK TABLES");

//===============================

	$DB_web->query("SELECT v.exterieur, v.en_haut, v.annonce_id,v.perime, v.titre, v.contenu, v.commentaire, e.nom, e.prenom, e.surnom, e.promo, e.mail, e.login FROM annonces as v LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE (perime>'".date("Y-m-d H:i:s",time()-48*3600)."') ORDER BY perime DESC");
	while(list($ext, $enhaut, $id,$date,$titre,$contenu, $commentaire,$nom, $prenom, $surnom, $promo,$mail,$login) = $DB_web->next_row()) {
?>
		<annonce titre="<?php  echo $titre ?>" 
				categorie=""
				auteur="<?php echo empty($surnom) ? $prenom.' '.$nom : $surnom .' (X'.$promo.')'?>"
				date="<?php echo $date?>">
				<?php echo wikiVersXML($contenu) ;
				if (file_exists(DATA_DIR_LOCAL."annonces/{$id}")){
				?>
					<image source="<?php echo DATA_DIR_URL."annonces/{$id}" ; ?>" texte="image"/>
				<?php
				}
				?>
					<eleve nom="<?php echo $nom; ?>" prenom="<?php echo $prenom; ?>" promo="<?php echo $promo; ?>" surnom="<?php echo $surnom; ?>" mail="<?php echo $mail; ?>" login="<?php echo $login; ?>" lien="oui" />
		</annonce>
<?php
// Zone de saisie de l'annonce
?>

		<formulaire id="annonce_<?php echo $id ?>" titre="L'annonce" action="admin/modif_annonces.php">
			<note>
				Le texte de l'annonce utilise le format wiki rappelé en bas de la page et décrit dans l'<lien url="helpwiki.php" titre="aide wiki"/><br/>
			</note>
			<?php
			if ($commentaire != "") {
				echo "<commentaire>Commentaire : $commentaire</commentaire>";
			}
			?>
			<champ id="titre" titre="Le titre" valeur="<?php echo $titre ;?>"/>
			<zonetext id="text" titre="Le texte"><?php echo $contenu; ?></zonetext>
			
			<note>L'image doit être un fichier gif, png ou jpeg ne dépassant pas 400x300 pixels et 250Ko.</note>
			<fichier id="file" titre="Modifier l'image" taille="250000"/>
			<choix titre="Supprimer l'image" id="supprimg" type="checkbox">
				<option id="supprimg" titre="" />
			</choix>
			
			<champ id="date" titre="Date de péremption" valeur="<?php echo $date ;?>"/>
			<choix titre="Extérieur" id="exterieur" type="checkbox" valeur="<?php if ($ext==1) echo "ext_auth" ?>">
				<option id="ext_auth" titre="Décision du Webmestre"/>
			</choix>
			<choix titre="Important" id="important" type="checkbox" valeur="<?php if ($enhaut==1) echo "important" ?>">
				<option id="important" titre="" />
			</choix>

			<bouton id='modif_<?php echo $id ?>' titre="Modifier"/>
			<bouton id='suppr_<?php echo $id ?>' titre='Supprimer' onClick="return window.confirm('Si vous supprimez cette annonce, celle-ci sera supprimée de façon definitive ... Voulez-vous vraiment la supprimer ?')"/>
		</formulaire>
<?php
		affiche_syntaxe_wiki();
	}
?>
</page>

<?php
require_once BASE_FRANKIZ."include/page_footer.inc.php";
?>
