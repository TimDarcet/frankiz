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
		<zonetext titre="Mail" id="mail"><? if (isset($_REQUEST['mail'])) echo $_REQUEST['mail']?></zonetext>
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
			<html><? if (isset($_REQUEST['mail'])) echo $_REQUEST['mail']?></html>
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
	
	
	$contenu = "<strong>Bonjour,</strong><br><br>".
			"$prenom $nom a demandé la validation d'un mail promo : <br>".
			$_POST['sujet']."<br><br>".
			"Pour valider ou non cette demande va sur la page suivante<br>".
			"<div align='center'><a href='http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_mailpromo.php'>".
			"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_mailpromo.php</a></div><br><br>" .
			"Très BR-ement<br>" .
			"L'automate :)<br>"  ;
			
	couriel(PREZ_ID,"[Frankiz] Validation d'un mail promo",$contenu,"Frankiz <br@frankiz>");

}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
