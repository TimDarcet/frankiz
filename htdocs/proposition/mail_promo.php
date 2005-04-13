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
	
	$Log$
	Revision 1.21  2005/04/13 17:10:00  pico
	Passage de tous les fichiers en utf8.

	Revision 1.20  2005/01/20 20:09:03  pico
	Changement de "Très BRment, l'automate"
	
	Revision 1.19  2005/01/18 19:50:31  pico
	Ce sont les kessiers et dei qui reçoivent les notifications de mail promo
	
	Revision 1.18  2005/01/04 21:44:40  pico
	Remise en place du lien vers l'helpwiki parce que le résumé en bas de page est incomprehensible
	
	Revision 1.17  2004/12/15 03:37:42  kikx
	Photo d'ortho
	
	Revision 1.16  2004/12/15 00:05:04  schmurtz
	Plus beau
	
	Revision 1.15  2004/12/14 14:18:12  schmurtz
	Suppression de la page de doc wiki : doc directement dans les pages concernees.
	
	Revision 1.14  2004/12/14 00:27:40  kikx
	Pour que le FROM des mails de validation soit au nom du mec qui demande la validation... (qu'est ce que je ferai pas pour les TOS :))
	
	Revision 1.13  2004/11/29 17:27:33  schmurtz
	Modifications esthetiques.
	Nettoyage de vielles balises qui trainaient.
	
	Revision 1.12  2004/11/27 20:16:55  pico
	Eviter le formatage dans les balises <note> <commentaire> et <warning> lorsque ce n'est pas necessaire
	
	Revision 1.11  2004/11/25 00:17:12  kikx
	Passage des mails promo en wiki
	
	Revision 1.10  2004/11/23 23:30:20  schmurtz
	Modification de la balise textarea pour corriger un bug
	(return fantomes)
	
	Revision 1.9  2004/10/29 14:38:37  kikx
	Mise en format HTML des mails pour les validation de la qdj, des mails promos, et des annonces
	
	Revision 1.8  2004/10/21 22:19:38  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.7  2004/10/20 23:21:39  schmurtz
	Creation d'un element <html> qui permet d'afficher du html brute sans verification
	C'est ce qui est maintenant utilise dans les annonces/cadres
	
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
		<zonetext titre="Mail" id="mail" type="grand"><? if (isset($_REQUEST['mail'])) echo $_REQUEST['mail']?></zonetext>
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
		<cadre  titre="Mail Promo : <? if (isset($_REQUEST['sujet'])) echo $_REQUEST['sujet']?>" >
			<? echo wikiVersXML($_REQUEST['mail']) ; ?>
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
		Merci d'avoir proposé un mail promo. Le responsable au BR essayera de te le valider le plus tôt possible.
	</commentaire>
<?
	// Stockage dans la base SQL
	$DB_valid->query("INSERT INTO valid_mailpromo SET mail='{$_REQUEST['mail']}', titre='{$_REQUEST['sujet']}',eleve_id={$_SESSION['user']->uid}, promo='{$_REQUEST['promo']}'") ;

	//Envoie du mail à l'admin pour la validation
	$tempo = explode("proposition",$_SERVER['REQUEST_URI']) ;
	
	
	$contenu = "<strong>Bonjour,</strong><br><br>".
			"$prenom $nom a demandé la validation d'un mail promo : <br>".
			$_POST['sujet']."<br><br>".
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
