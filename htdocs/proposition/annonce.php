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
	Page qui permet aux utilisateurs de demander le rajout d'une annonce
	
	$Id$
	
*/
	
require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";

// Vérification des droits
demande_authentification(AUTH_COOKIE);

$DB_trombino->query("SELECT eleve_id,nom,prenom,surnom,mail,login,promo FROM eleves WHERE eleve_id='".$_SESSION['uid']."'");
list($eleve_id,$nom,$prenom,$surnom,$mail,$login,$promo) = $DB_trombino->next_row();


//---------------------------------------------------------------------------------
// On traite l'image qui vient d'etre uploader si elle existe
//---------------------------------------------------------------------------------

//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//
//--//
//--// Norme de nommage des images que nous uploadons
//--// dans le rep prévu a cette effet : 
//--// # annonce_{$id_eleves} qd l'annonce n'a pas été soumise à validation
//--// # {$id_annonce_a_valider}_annonce qd l'annonce est soumise à validation
//--//
//--// Ceci permet de faire la différence entre les fichiers tempo et les fichiers a valider
//--//
//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//

$erreur_upload = 0 ;
if ((isset($_FILES['file']))&&($_FILES['file']['size']!=0))  {
	if($original_size = getimagesize($_FILES['file']['tmp_name'])) {
		$larg = $original_size[0];
		$haut = $original_size[1];
		if (($larg>400)||($haut>300)) {
			$erreur_upload =1 ;
		} else {
			$filename = "temp_$eleve_id" ;
			move_uploaded_file($_FILES['file']['tmp_name'], DATA_DIR_LOCAL ."annonces/". $filename) ;
		} 
	}else{
		$erreur_upload = 1 ;
	}
}
//================================================
// On Supprime l'image qui a été uploadé (si elle existe bien sur :))
//================================================

if (isset($_POST['suppr_img'])) {

	if (file_exists(DATA_DIR_LOCAL."annonces/temp_$eleve_id")) {
		unlink(DATA_DIR_LOCAL."annonces/temp_$eleve_id") ; 
	}
}
//================================================
// On valide l'annonce et en envoie un mail aux webmestres pour les prévenir 
//================================================

if (isset($_POST['valid'])) {

	$tempo = explode("proposition",$_SERVER['REQUEST_URI']) ;

	if (isset($_REQUEST['ext']))
		$temp_ext = '1'  ;
	else 
		$temp_ext = '0' ;

	$DB_valid->query("INSERT INTO valid_annonces SET perime=FROM_UNIXTIME({$_POST['date']}), eleve_id='".$_SESSION['uid']."', titre='".$_POST['titre']."',contenu='".$_POST['text']."', exterieur=$temp_ext, commentaire='".$_POST['comment']."'");
	
	// on modifie le nom du fichier qui a été téléchargé si celui ci existe
	// selon la norme de nommage ci-dessus
	//----------------------------------------------------------------------------------------------
	
	if (file_exists(DATA_DIR_LOCAL."annonces/temp_$eleve_id")) {
		$index = mysql_insert_id($DB_valid->link) ;
		rename(DATA_DIR_LOCAL."annonces/temp_$eleve_id",DATA_DIR_LOCAL."annonces/a_valider_{$index}") ; 
	}
	$contenu = "<strong>Bonjour,</strong><br><br>".
			"$prenom $nom a demandé la validation d'une annonce : <br>".
			$_POST['titre']."<br><br>".
			"Pour valider ou non cette demande va sur la page suivante<br>".
			"<div align='center'><a href='http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_annonces.php'>".
			"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_annonces.php</a></div><br><br>" .
			"Cordialement,<br>" .
			"Le Webmestre de Frankiz<br>"  ;
				
	couriel(WEBMESTRE_ID,"[Frankiz] Validation d'une annonce",$contenu,$eleve_id);
}
//=================
//===============
// Génération de la page
//===============
//=================

require_once BASE_FRANKIZ."include/page_header.inc.php";

?>
<page id="propoz_annonce" titre="Frankiz : Propose une annonce">
<h1>Proposition d'annonce</h1>

<?php
if ($erreur_upload==1) {
	echo "<warning>Ton image n'est pas au bon format, ou est trop grande.</warning>\n";

}
//=========================================
// PREVISUALISATION :
// On teste l'affichage de l'annonce pour voir à quoi ça ressemble
//=========================================

if (!isset($_POST['text'])) $_POST['text']="" ;
if (!isset($_POST['titre']))  $_POST['titre']="Titre" ;

?>
	<annonce titre="<?php echo $_POST['titre'] ; ?>" 
			categorie=""
			date="<?php echo date("d/m/y") ?>">
			<?php 
			if (!isset($_POST['valid']) && file_exists(DATA_DIR_LOCAL."annonces/temp_$eleve_id")) {
				echo "<image source=\"".DATA_DIR_URL."annonces/temp_$eleve_id\" texte=\"image\"/>\n";
			} else if (isset($index) && file_exists(DATA_DIR_LOCAL."annonces/a_valider_{$index}")){
				echo "<image source=\"".DATA_DIR_URL."annonces/a_valider_$index\" texte=\"image\"/>\n";
			}
			echo wikiVersXML($_POST['text']) ;
			?>
			<eleve nom="<?php echo $nom; ?>" prenom="<?php echo $prenom; ?>" promo="<?php echo $promo; ?>" surnom="<?php echo $surnom; ?>" mail="<?php echo $mail; ?>" login="<?php echo $login; ?>" lien="oui"/>
	</annonce>
<?php

//=========================
// On met le commentaire qui va bien 
//=========================

if (isset($_POST['valid'])) {
?>
	<commentaire>Ta nouvelle annonce a été prise en compte et sera validée dans les meilleurs délais.</commentaire>
<?php	
} else {
//====================
// Zone de saisie de l'annonce
//====================
?>

	<formulaire id="propoz_annonce" titre="Ton annonce" action="proposition/annonce.php">
		<champ id="titre" titre="Le titre" valeur="<?php if (isset($_POST['titre'])) echo $_POST['titre'] ;?>"/>

		<note>
			Le texte de l'annonce utilise le format wiki rappelé en bas de la page et décrit dans l'<lien url="helpwiki.php" titre="aide wiki"/>.<br/>
			Pour toute remarque particulière, envoyer un mail à <lien url="mailto:web@frankiz.polytechnique.fr" titre="web@frankiz"/>.<br/><br/>
         Il est rappelé qu'une annonce n'est pas une activité et que si l'annonce concerne une activité, nous ne la validerons que si elle est accompagnée d'une proposition d'activité et si l'activité a lieu dans plus de quatre jours (une semaine c'est mieux).
		</note>
		<zonetext id="text" titre="Le texte" type="grand"><?php if (isset($_POST['text'])) echo $_POST['text'];?></zonetext>

		<note>L'image doit être un fichier gif, png ou jpeg ne dépassant pas 400x300 pixels et 250Ko.</note>
		<fichier id="file" titre="Ton image" taille="250000"/>

		<note>Ton annonce disparaîtra le jour de la date de péremption.</note>
		<choix titre="Date de péremption" id="date" type="combo" valeur="<?php if (isset($_REQUEST['date'])) echo $_REQUEST['date'] ;?>">
<?php		for ($i=1 ; $i<=MAX_PEREMPTION ; $i++) {
			$date_id = mktime(0, 0, 0, date("m") , date("d") + $i, date("Y")) ;
			$date_value = date("d/m/y" , $date_id);
?>
			<option titre="<?php echo $date_value?>" id="<?php echo $date_id?>" />
<?php
		}
?>
		</choix>
		
		<note>
			Si tu souhaites que ton annonce soit visible de l'extérieur, clique ici.
		</note>
		<choix titre="Extérieur" id="exterieur" type="checkbox" valeur="<?php if (isset($_REQUEST['ext'])) echo 'ext' ;?>">
			<option id="ext" titre=""/>
		</choix>

		<note>
			Tu peux laisser un commentaire à l'attention des Webmestres (il n'apparaîtra pas dans l'annonce),
			en particulier si tu souhaites que l'annonce soit visible de l'extérieur :
			une justification facilitera la décision des Webmestres.
		</note>
		<zonetext id="comment" titre="Commentaire"><?php if (isset($_REQUEST['comment'])) echo $_REQUEST['comment']; ?></zonetext>

		<bouton id='suppr_img' titre="Supprimer l'image"/>
		<bouton id='test' titre="Tester"/>
		<bouton id='valid' titre='Valider' onClick="return window.confirm('Voulez vous vraiment valider votre annonce ?')"/>
	</formulaire>
<?php
	affiche_syntaxe_wiki();
}
?>

</page>
<?php

require_once BASE_FRANKIZ."include/page_footer.inc.php";
?>
