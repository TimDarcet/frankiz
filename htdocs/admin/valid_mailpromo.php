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
	Page qui permet aux admins de valider un mail promo
	
	$Id$
	
*/

require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
if(!verifie_permission('admin')&&!verifie_permission('kes'))
	acces_interdit();
	
$message ="" ;

// Génération de la page
//===============

if ((isset($_POST['promo']))&&($_POST['promo'] == "")) {
	$titre_mail = "Mail Bi-Promo :" ;
} else {
	$titre_mail = "Mail Promo :" ;
}
		
// On traite les différents cas de figure d'enrigistrement et validation d'affiche :)

// Enregistrer ...
$DB_valid->query("LOCK TABLE valid_mailpromo WRITE");
$DB_valid->query("SET AUTOCOMMIT=0");

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;


	if (($temp[0]=='modif')||($temp[0]=='valid')) {
		$DB_valid->query("SELECT 0 FROM valid_mailpromo WHERE mail_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {

			$DB_valid->query("UPDATE valid_mailpromo SET titre='".mysql_addslashes($_POST['titre'],true)."', mail='".mysql_addslashes($_POST['mail'],true)."', promo='".intval($_POST['promo'])."' WHERE mail_id='{$temp[1]}'");	
			if ($temp[0]!='valid') {
				$message .= "<commentaire>Modification effectuée</commentaire>" ;
			}
		} else {
			$message .= "<warning>Requête deja traitée par un autre administrateur</warning>" ;		
		}	
	}
	
	if ($temp[0]=='valid') {
		$DB_valid->query("SELECT eleve_id FROM valid_mailpromo WHERE mail_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {

			list($eleve_id) = $DB_valid->next_row() ;
			
			//Log l'action de l'admin
			log_admin($_SESSION['uid']," accepté le mail promo '".gpc_stripslashes($_POST['titre'])."' ") ;
			
			// envoi du mail
			$contenu = 	"Ton mail promo a été validé par la Kès<br><br>".
						"Merci de ta participation<br><br>".
						"Cordialement,<br>" .
						"La Kès<br>"  ;
			couriel($eleve_id,"[Frankiz] Ton mail promo a été validé par la Kès",$contenu,MAILPROMO_ID);
			
			if ((isset($_POST['promo']))&&($_POST['promo'] == "")) {
				$promo = '' ;
			} else {
				$promo = $_POST['promo'] ;
			}
			if (!isset($_POST["from_name_{$temp[1]}"]))
				$sendername = "$prenom $nom" ;
			else
				$sendername = base64_encode(gpc_stripslashes($_POST["from_name_{$temp[1]}"])) ;
			
			if (!isset($_POST["from_mail_{$temp[1]}"]))
				$sendermail = "$mail" ;
			else
				$sendermail = base64_encode(gpc_stripslashes($_POST["from_mail_{$temp[1]}"])) ;
			rediriger_vers("/admin/valid_mailpromo_envoi.php?id={$temp[1]}&promo=$promo&sendername=$sendername&sendermail=$sendermail") ;

		}
	}
	if ($temp[0]=='suppr') {
		$DB_valid->query("SELECT eleve_id FROM valid_mailpromo WHERE mail_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {

			list($eleve_id) = $DB_valid->next_row() ;
			//Log l'action de l'admin
			log_admin($_SESSION['uid']," refusé le mail promo '".gpc_stripslashes($_POST['titre'])."' ") ;
			// envoi du mail
			$contenu = 	"Ton mail promo n'a pas été validé par la Kès pour la raison suivante<br>".
						$_POST['refus']."<br><br>".
						"Désolé <br>".
						"Cordialement,\n" .
						"la Kès\n"  ;
			couriel($eleve_id,"[Frankiz] Ton mail promo n'a pas été validé par la Kès",$contenu,MAILPROMO_ID);
	
			$DB_valid->query("DELETE FROM valid_mailpromo WHERE mail_id='{$temp[1]}'") ;
	
		
			$message .= "<warning>Suppression d'un mail promo</warning>" ;
		
		} else {
			$message .= "<warning>Requête deja traitée par un autre administrateur</warning>" ;		
		}	
	}
}
$DB_valid->query("COMMIT");
$DB_valid->query("UNLOCK TABLES");

//===============================

require_once BASE_FRANKIZ."htdocs/include/page_header.inc.php";

?>
<page id="valid_mailpromo" titre="Frankiz : Valide un mail promo">
<h1>Validation des mails promos</h1>

<?php
echo $message ;

$DB_valid->query("SELECT v.mail_id,DATE_FORMAT(v.stamp,'%d/%m/%Y %H:%i:%s'), v.titre,v.promo, v.mail, e.nom, e.prenom, e.surnom, e.promo, e.mail, e.login FROM valid_mailpromo as v LEFT JOIN trombino.eleves as e USING(eleve_id)");
while(list($id,$date,$titre,$promo_mail,$mailpromo,$nom, $prenom, $surnom, $promo,$mail,$login) = extdata_stripslashes($DB_valid->next_row())) {
	if (empty($mail)) $mail="$login@poly" ;
?>
	<commentaire>
		<em>FROM</em>: <?php  echo "$prenom $nom &lt;$mail&gt; " ?><br/>
		Posté le 
			<?php  echo $date; ?>
	</commentaire>
	<cadre titre="<?php  echo $titre_mail." ".$titre ?>">
			<?php echo wikiVersXML($mailpromo) ?>
	</cadre>
<?php

// Zone de saisie de l'affiche
?>

	<formulaire id="mailpromo_<?php echo $id ?>" titre="Mail Promo" action="admin/valid_mailpromo.php">
		<champ id="titre" titre="Sujet " valeur="<?php  echo $titre ;?>"/>
		<?php
			if ((!isset($_POST["from_name_$id"]))||((isset($temp))&&($temp[1]!=$id)))
				$_POST["from_name_$id"] = "$prenom $nom" ;
			else $_POST["from_name_$id"] = gpc_stripslashes($_POST["from_name_$id"]);
			if ((!isset($_POST["from_mail_$id"]))||((isset($temp))&&($temp[1]!=$id)))
				$_POST["from_mail_$id"] = "$mail" ;
			else $_POST["from_mail_$id"] = gpc_stripslashes($_POST["from_mail_$id"]);
		?>
		<champ titre="From Nom" id="from_name_<?php echo $id; ?>"  valeur="<?php echo  $_POST["from_name_$id"] ?>"/>
		<champ titre="From Mail" id="from_mail_<?php echo $id; ?>"  valeur="<?php echo  $_POST["from_mail_$id"] ?>"/>
		<zonetext id="mail" titre="Mail"><?php echo $mailpromo; ?></zonetext>
		<choix titre="Promo" id="promo" type="combo" valeur="<?php echo $promo_mail ;?>">
		<?php
			$DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus'");
			list($promo_temp) = $DB_web->next_row() ;
			$promo1 = $promo_temp ;
			$promo2 = $promo_temp-1 ;
				echo "<option titre='$promo1 et $promo2' id='' />" ;
				echo "<option titre='$promo1' id='$promo1' />" ;
				echo "<option titre='$promo2' id='$promo2' />" ;
		?>
		</choix>
		<zonetext id="refus" titre="La raison du refus si refus"></zonetext>

		<bouton id='modif_<?php echo $id ?>' titre="Modifier"/>
		<bouton id='valid_<?php echo $id ?>' titre='Valider' onClick="return window.confirm('Envoyer ce mail promo ?')"/>
		<bouton id='suppr_<?php echo $id ?>' titre='Supprimer' onClick="return window.confirm('Supprimer ce mail promo et ne pas l'envoyer ?')"/>
	</formulaire>
<?php
	affiche_syntaxe_wiki();
}

?>
</page>

<?php
require_once BASE_FRANKIZ."htdocs/include/page_footer.inc.php";
?>
