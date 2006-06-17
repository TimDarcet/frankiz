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
	Mail promo permettant l'envoie de pièce jointes et de formatage HTML
	
	$Id$
	
*/

// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MINIMUM);

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";
require_once BASE_LOCAL."/include/wiki.inc.php";

$DB_trombino->query("SELECT eleve_id,nom,prenom,surnom,mail,login,promo FROM eleves WHERE eleve_id='".$_SESSION['user']->uid."'");
list($eleve_id,$nom,$prenom,$surnom,$mail,$login,$promo) = $DB_trombino->next_row();

?>
<page id="admin_mailpromo" titre="Frankiz : Envoi des mails promos">
<?
if (!isset($_REQUEST['envoie'])) {
?>
	<formulaire id="mail_promo" titre="Mail Promo" action="proposition/mail_promo.php">
		<note>
			Le texte du mail promo utilise le format wiki rappelé en bas de la page et décrit dans l'<lien url="helpwiki.php" titre="aide wiki"/><br/>
			Pour toute remarque particulière, envoyer un mail à <lien url="mailto:mailpromo@frankiz.polytechnique.fr" titre="mailpromo@frankiz"/>
		</note>
		<choix titre="Promo" id="promo" type="combo" valeur="<? if (isset($_POST['promo'])) echo  gpc_stripslashes($_POST['promo']); ?>">
		<?
			$DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus'");
			list($promo_temp) = $DB_web->next_row() ;
			$promo1 = $promo_temp ;
			$promo2 = $promo_temp-1 ;
				echo "<option titre='$promo1 et $promo2' id='' />" ;
				echo "<option titre='$promo1' id='$promo1' />" ;
				echo "<option titre='$promo2' id='$promo2' />" ;
		?>
		</choix>

		<champ titre="Sujet" id="sujet" valeur="<? if (isset($_REQUEST['sujet'])) echo gpc_stripslashes($_REQUEST['sujet']); ?>" />
		<zonetext titre="Mail" id="mail" type="grand"><? if (isset($_REQUEST['mail'])) echo gpc_stripslashes($_REQUEST['mail']); ?></zonetext>
		<bouton titre="Tester" id="upload"/>
		<bouton titre="Envoyer" id="envoie"  onClick="return window.confirm('Voulez vous vraiment envoyer ce mail ?')"/>
	</formulaire>
	<?php affiche_syntaxe_wiki() ?>
<?
//==================================================
//=
//= Permet de visualiser son mail avant de l'envoyer
//=
//==================================================
	if (isset($_REQUEST['upload'])) {
?>
		<cadre  titre="Mail Promo : <? if (isset($_REQUEST['sujet'])) echo gpc_stripslashes($_REQUEST['sujet']); ?>" >
			<? echo wikiVersXML(gpc_stripslashes($_REQUEST['mail'])) ; ?>
		</cadre>
<?
	}
//==================================================
//=
//= Stockage du mail en attente de validation par un webmestre
//=
//==================================================
} else {
?>
	<commentaire>
		Merci d'avoir proposé un mail promo. Le responsable à la Kès essayera de te le valider le plus tôt possible.
	</commentaire>
<?
	// Stockage dans la base SQL
	$DB_valid->query("INSERT INTO valid_mailpromo SET mail='".mysql_addslashes($_REQUEST['mail'],true)."', titre='".mysql_addslashes($_REQUEST['sujet'],true)."',eleve_id={$_SESSION['user']->uid}, promo='".intval($_REQUEST['promo'])."'") ;

	//Envoie du mail à l'admin pour la validation
	$tempo = explode("proposition",$_SERVER['REQUEST_URI']) ;
	
	
	$contenu = "<strong>Bonjour,</strong><br><br>".
			"$prenom $nom a demandé la validation d'un mail promo : <br>".
			gpc_stripslashes($_POST['sujet'])."<br><br>".
			"Pour valider ou non cette demande va sur la page suivante<br>".
			"<div align='center'><a href='http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_mailpromo.php'>".
			"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_mailpromo.php</a></div><br><br>" .
			"Cordialement,<br>" .
			"Le BR<br>"  ;
			
	couriel(MAILPROMO_ID,"[Frankiz] Validation d'un mail promo",$contenu,$_SESSION['user']->uid);

}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
