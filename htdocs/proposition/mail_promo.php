<?php
/*
	Mail promo permettant l'envoie de pièce jointes et de formatage HTML
	
	$Log$
	Revision 1.6  2004/10/13 19:36:44  kikx
	Correction de la page mail promo pour le text plain

	Revision 1.5  2004/10/06 21:29:29  kikx
	Mail promo != mail bi-promo
	
	Revision 1.4  2004/10/06 21:07:17  kikx
	Micro correction car on abandonne pour le moment l'idee de piece jointe
	
	Revision 1.3  2004/10/06 14:12:27  kikx
	Page de mail promo quasiment en place ...
	envoie en HTML ...
	Page pas tout a fait fonctionnel pour l'instant
	
	Revision 1.2  2004/10/04 22:55:25  kikx
	Modification pour permettre aux personnes de poster des mails promos
	
	Revision 1.1  2004/10/04 22:51:48  kikx
	Modification de l'endroit de stockage
	
	Revision 1.2  2004/10/04 22:48:54  kikx
	Modification mineur de la page d'envoie de mail promo !
	
	Revision 1.1  2004/10/04 21:21:10  kikx
	oubli desolé
	
	
*/

// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MINIMUM);

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

$DB_trombino->query("SELECT eleve_id,nom,prenom,surnom,mail,login,promo FROM eleves WHERE eleve_id='".$_SESSION['user']->uid."'");
list($eleve_id,$nom,$prenom,$surnom,$mail,$login,$promo) = $DB_trombino->next_row();

?>
<page id="admin_mailpromo" titre="Frankiz : Envoie des mails promos">
<?
if (!isset($_REQUEST['envoie'])) {
?>
	<formulaire id="mail_promo" titre="Mail Promo" action="proposition/mail_promo.php">
		<choix titre="Promo" id="promo" type="combo" valeur="<? if (isset($_POST['promo'])) echo  $_POST['promo'] ;?>">
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

		<champ titre="Sujet" id="sujet" valeur="<? if (isset($_REQUEST['sujet'])) echo $_REQUEST['sujet']?>" />
		<zonetext titre="Mail" id="mail" valeur="<? if (isset($_REQUEST['mail'])) echo $_REQUEST['mail']?>" />
		<bouton titre="Mise à jour" id="upload"/>
		<bouton titre="Valider" id="envoie"  onClick="return window.confirm('Voulez vous vraiment envoyer ce mail ?')"/>
	</formulaire>
<?
//==================================================
//=
//= Permet de visualiser son mail avant de l'envoyer
//=
//==================================================
	if (isset($_REQUEST['upload'])) {
?>
		<cadre  titre="Mail Promo : <? if (isset($_REQUEST['sujet'])) echo $_REQUEST['sujet']?>" >
			<? if (isset($_REQUEST['mail'])) echo $_REQUEST['mail']?>
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
		<p>Merci d'avoir proposé un mail promo</p>
		<p>Le responsable au BR essayera de te le valider le plus tôt possible</p>
	</commentaire>
<?
	// Stockage dans la base SQL
	$DB_valid->query("INSERT INTO valid_mailpromo SET mail='{$_REQUEST['mail']}', titre='{$_REQUEST['sujet']}',eleve_id={$_SESSION['user']->uid}, promo='{$_REQUEST['promo']}'") ;

	//Envoie du mail à l'admin pour la validation
	$tempo = explode("proposition",$_SERVER['REQUEST_URI']) ;
	
	$contenu = "$prenom $nom a demandé la validation d'un mail promo : \n".
				$_POST['sujet']."\n\n".
				"Pour valider ou non cette demande va sur la page suivante : \n".
				"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_mailpromo.php\n\n" .
				"Très BR-ement\n" .
				"L'automate :)\n"  ;
				
	mail(MAIL_WEBMESTRE,"[Frankiz] Validation d'un mail promo",$contenu);
}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
