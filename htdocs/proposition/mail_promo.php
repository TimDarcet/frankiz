<?php
/*
	Mail promo permettant l'envoie de pi�ce jointes et de formatage HTML
	
	$Log$
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
	oubli desol�
	
	
*/

// En-tetes
require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_MINIMUM);

// G�n�ration de la page
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
		<champ titre="Sujet" id="sujet" valeur="<? if (isset($_REQUEST['sujet'])) echo $_REQUEST['sujet']?>" />
		<zonetext titre="Mail" id="mail" valeur="<? if (isset($_REQUEST['mail'])) echo $_REQUEST['mail']?>" />
		<textsimple valeur="Ton fichier ne doit pas d�passer 500ko car sinon elle ne sera pas t�l�charg�e"/>
<!---		<champ id="file" titre="Pi�ce jointe" valeur="" taille="500000"/>-->
		<bouton titre="Mise � jour" id="upload"/>
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
		<p>Merci d'avoir propos� un mail promo</p>
		<p>Le responsable au BR essayera de te le valider le plus t�t possible</p>
	</commentaire>
<?
	// Stockage dans la base SQL
	$DB_valid->query("INSERT INTO valid_mailpromo SET mail='{$_REQUEST['mail']}', titre='{$_REQUEST['sujet']}',eleve_id={$_SESSION['user']->uid} ") ;

	//Envoie du mail � l'admin pour la validation
	$tempo = explode("proposition",$_SERVER['REQUEST_URI']) ;
	
	$contenu = "$prenom $nom a demand� la validation d'un mail promo : \n".
				$_POST['sujet']."\n\n".
				"Pour valider ou non cette demande va sur la page suivante : \n".
				"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_mailpromo.php\n\n" .
				"Tr�s BR-ement\n" .
				"L'automate :)\n"  ;
				
	mail("Admin Frankiz <gruson@poly.polytechnique.fr>","[Frankiz] Validation d'un mail promo",$contenu);

}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
