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
	
	$Log$
	Revision 1.28  2005/01/13 17:10:58  pico
	Mails de validations From le validateur qui va plus ou moins bien

	Revision 1.27  2005/01/11 17:07:40  pico
	Correction bug #25
	
	Revision 1.26  2004/12/17 17:25:08  schmurtz
	Ajout d'une belle page d'erreur.
	
	Revision 1.25  2004/12/16 13:00:41  pico
	INNER en LEFT
	
	Revision 1.24  2004/12/15 20:07:01  kikx
	Correction
	
	Revision 1.23  2004/12/15 19:26:09  kikx
	Les mails promo devrait fonctionner now ...
	
	Revision 1.22  2004/12/14 14:18:12  schmurtz
	Suppression de la page de doc wiki : doc directement dans les pages concernees.
	
	Revision 1.21  2004/12/08 13:11:42  kikx
	Protection de la validation des mailpromo
	
	Revision 1.20  2004/11/27 20:16:55  pico
	Eviter le formatage dans les balises <note> <commentaire> et <warning> lorsque ce n'est pas necessaire
	
	Revision 1.19  2004/11/27 15:02:17  pico
	Droit xshare et faq + redirection vers /gestion et non /admin en cas de pbs de droits
	
	Revision 1.18  2004/11/25 00:53:03  kikx
	Voilà... la validation des mails promo est faite en wiki
	j'en ai profité pour cooriger un oubli ?... de pourvoir changer l'expediter du mai par exemple au om de son binet
	
	Revision 1.17  2004/11/23 23:30:20  schmurtz
	Modification de la balise textarea pour corriger un bug
	(return fantomes)
	
	Revision 1.16  2004/11/16 18:32:34  schmurtz
	Petits problemes d'interpretation de <note> et <commentaire>
	
	Revision 1.15  2004/10/31 21:30:48  kikx
	Oups ca fonctionnera miuex comme ca ...
	
	Revision 1.14  2004/10/31 21:29:56  kikx
	Mise a jour du mail promo grace a la librairie de Schmurtz
	
	Revision 1.13  2004/10/29 15:54:28  kikx
	Mail en HTLM et raison du refus si refus
	
	Revision 1.12  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.11  2004/10/20 23:21:39  schmurtz
	Creation d'un element <html> qui permet d'afficher du html brute sans verification
	C'est ce qui est maintenant utilise dans les annonces/cadres
	
	Revision 1.10  2004/10/13 21:21:14  kikx
	Le rend opérationnel
	
	Revision 1.9  2004/10/13 21:19:52  kikx
	Rajout de bug fix
	
	Revision 1.8  2004/10/13 20:45:16  kikx
	Mises en place de log pour l'envoie des mail promos
	
	Revision 1.7  2004/10/13 19:36:44  kikx
	Correction de la page mail promo pour le text plain
	
	Revision 1.6  2004/10/12 18:23:15  kikx
	On retire le petit logo de merde dans les mails promo
	
	Revision 1.5  2004/10/10 20:13:18  kikx
	Bug fix pour les envoie de mail promo
	-> n'envoie plus en texte brut les truc comme &apo; &nbps; ...
	
	Revision 1.4  2004/10/10 20:10:05  kikx
	Ne sert plus a rien
	
	Revision 1.3  2004/10/06 21:29:29  kikx
	Mail promo != mail bi-promo
	
	Revision 1.2  2004/10/06 19:29:53  kikx
	La page d'envoi de mail promo est terminéééééééééééééééééééééééé
	
	Revision 1.1  2004/10/06 14:12:27  kikx
	Page de mail promo quasiment en place ...
	envoie en HTML ...
	Page pas tout a fait fonctionnel pour l'instant
	
*/

require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
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

			$DB_valid->query("UPDATE valid_mailpromo SET titre='{$_POST['titre']}', mail='{$_POST['mail']}', promo='{$_POST['promo']}' WHERE mail_id='{$temp[1]}'");	
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
			// envoi du mail
			$contenu = 	"Ton mail promo a été validé par le BR<br><br>".
						"Merci de ta participation<br><br>".
						"Très BR-ement<br>" .
						"L'automate :)<br>"  ;
			couriel($eleve_id,"[Frankiz] Ton mail promo a été validé par le BR",$contenu,PREZ_ID);
			
			if ((isset($_POST['promo']))&&($_POST['promo'] == "")) {
				$promo = '' ;
			} else {
				$promo = $_POST['promo'] ;
			}
			if (!isset($_POST["from_{$temp[1]}"]))
				$sender = "$prenom $nom &lt;$mail&gt; " ;
			else
				$sender = base64_encode($_POST["from_{$temp[1]}"]) ;
				
			rediriger_vers("/admin/valid_mailpromo_envoi.php?id={$temp[1]}&promo=$promo&sender=$sender") ;

		}
	}
	if ($temp[0]=='suppr') {
		$DB_valid->query("SELECT eleve_id FROM valid_mailpromo WHERE mail_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {

			list($eleve_id) = $DB_valid->next_row() ;
			// envoi du mail
			$contenu = 	"Ton mail promo n'a pas été validé par le BR pour la raison suivante<br>".
						$_POST['refus']."<br><br>".
						"Désolé <br>".
						"Très BR-ement\n" .
						"L'automate :)\n"  ;
			couriel($eleve_id,"[Frankiz] Ton mail promo n'a pas été validé par le BR",$contenu,PREZ_ID);
	
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

require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="valid_mailpromo" titre="Frankiz : Valide un mail promo">
<h1>Validation des mails promos</h1>

<?
echo $message ;

$DB_valid->query("SELECT v.mail_id,v.stamp, v.titre,v.promo, v.mail, e.nom, e.prenom, e.surnom, e.promo, e.mail, e.login FROM valid_mailpromo as v LEFT JOIN trombino.eleves as e USING(eleve_id)");
while(list($id,$date,$titre,$promo_mail,$mailpromo,$nom, $prenom, $surnom, $promo,$mail,$login) = $DB_valid->next_row()) {
	if (empty($mail)) $mail="$login@poly" ;
?>
	<commentaire>
		<em>FROM</em>: <?php  echo "$prenom $nom &lt;$mail&gt; " ?><br/>
		Posté le 
			<?php  echo substr($date,6,2) ."/".substr($date,4,2) ."/".substr($date,2,2)." à ".substr($date,8,2).":".substr($date,10,2) ?>
	</commentaire>
	<cadre titre="<?php  echo $titre_mail." ".$titre ?>">
			<?php echo wikiVersXML($mailpromo) ?>
	</cadre>
<?

// Zone de saisie de l'affiche
?>

	<formulaire id="mailpromo_<? echo $id ?>" titre="Mail Promo" action="admin/valid_mailpromo.php">
		<champ id="titre" titre="Sujet " valeur="<?  echo $titre ;?>"/>
		<?
			if ((!isset($_POST["from_$id"]))||((isset($temp))&&($temp[1]!=$id)))
				$_POST["from_$id"] = "$prenom $nom &lt;$mail&gt; " ;
		?>
		<champ titre="From " id="from_<?=$id?>"  valeur="<? echo  $_POST["from_$id"] ?>"/>
		<zonetext id="mail" titre="Mail"><?=$mailpromo?></zonetext>
		<choix titre="Promo" id="promo" type="combo" valeur="<?echo $promo_mail ;?>">
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
		<zonetext id="refus" titre="La raison du refus si refus"></zonetext>

		<bouton id='modif_<? echo $id ?>' titre="Modifier"/>
		<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Envoyer ce mail promo ?')"/>
		<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('Supprimer ce mail promo et ne pas l'envoyer ?')"/>
	</formulaire>
<?
	affiche_syntaxe_wiki();
}

?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
