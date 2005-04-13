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
	Page qui permet aux admins de valider un sondage
	
	$Log$
	Revision 1.22  2005/04/13 17:09:58  pico
	Passage de tous les fichiers en utf8.

	Revision 1.21  2005/03/04 23:11:33  pico
	Restriction des sondages par promo/section/binet
	
	Revision 1.20  2005/03/02 07:24:49  pico
	corrige une petite erreur d'url
	
	Revision 1.19  2005/02/15 19:45:14  pico
	Pour modifier les sondages lors de la validation
	
	Revision 1.18  2005/02/15 19:30:40  kikx
	Mise en place de log pour surveiller l'admin :)
	
	Revision 1.17  2005/01/21 17:01:31  pico
	Fonction pour savoir si interne
	
	Revision 1.16  2005/01/20 20:09:03  pico
	Changement de "Très BRment, l'automate"
	
	Revision 1.15  2005/01/14 09:19:31  pico
	Corrections bug mail
	+
	Sondages maintenant public ou privé (ne s'affichant pas dans le cadre)
	Ceci sert pour les sondages section par exemple
	
	Revision 1.14  2005/01/13 17:10:58  pico
	Mails de validations From le validateur qui va plus ou moins bien
	
	Revision 1.13  2005/01/05 21:59:48  pico
	Envoit de commentaire dans le mail de validation d'annonce
	
	Revision 1.12  2004/12/17 17:25:08  schmurtz
	Ajout d'une belle page d'erreur.
	
	Revision 1.11  2004/12/16 13:00:41  pico
	INNER en LEFT
	
	Revision 1.10  2004/12/14 22:17:32  kikx
	Permet now au utilisateur de modifier les Faqqqqqqqqqqqqqqqq :)
	
	Revision 1.9  2004/12/14 13:39:20  pico
	Y'avait de la merde au niveau des locks, ça ça marche, ce serait bien si tu pouvais y jeter un coup d'oeil, kikx
	
	Revision 1.8  2004/12/13 16:40:46  kikx
	Protection de la validation des sondages
	
	Revision 1.7  2004/11/27 20:16:55  pico
	Eviter le formatage dans les balises <note> <commentaire> et <warning> lorsque ce n'est pas necessaire
	
	Revision 1.6  2004/11/27 15:29:22  pico
	Mise en place des droits web (validation d'annonces + sondages)
	
	Revision 1.5  2004/11/27 15:02:17  pico
	Droit xshare et faq + redirection vers /gestion et non /admin en cas de pbs de droits
	
	Revision 1.4  2004/11/23 23:30:20  schmurtz
	Modification de la balise textarea pour corriger un bug
	(return fantomes)
	
	Revision 1.3  2004/11/17 22:19:15  kikx
	Pour avoir un module sondage
	
	Revision 1.2  2004/11/17 21:17:21  kikx
	Validation d'un sondage par l'admin
	
	Revision 1.1  2004/11/17 13:49:49  kikx
	Preparation de la page de validation des sondages
	

*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')&&!verifie_permission('web'))
	acces_interdit();

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="valid_sondage" titre="Frankiz : Valide un sondage">

<h1>Validation des sondages</h1>

<?
// On traite les différents cas de figure d'enrigistrement et validation de qdj :)

// Enregistrer ...
$DB_valid->query("LOCK TABLES valid_sondages AS v WRITE,valid_sondages WRITE, trombino.eleves AS e READ");
$DB_valid->query("SET AUTOCOMMIT=0");

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;
	
	// On refuse le sondage
	//==========================
	if ($temp[0] == "suppr") {
		$DB_valid->query("SELECT 0 FROM valid_sondages WHERE sondage_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {

			
			$DB_valid->query("SELECT titre FROM valid_sondages WHERE sondage_id={$temp[1]}");
			list($titre) = $DB_valid->next_row() ;
			//Log l'action de l'admin
			log_admin($_SESSION['user']->uid," refusé le sondage '$titre'") ;
			
			$DB_valid->query("DELETE FROM valid_sondages WHERE sondage_id='{$temp[1]}'");
			
			$bla = "explication_".$temp[1] ;
			$contenu = "<strong>Bonjour</strong>, <br><br>".
						"Nous sommes désolé mais ton sondage n'a pas été validé par le BR pour la raison suivante : <br>".
						$_POST[$bla]."<br>".
						"<br>" .
						"Très Cordialement<br>" .
						"Le Webmestre de Frankiz<br>"  ;
		
			couriel($temp[2],"[Frankiz] Ton sondage a été refusé ",$contenu,WEBMESTRE_ID);
			echo "<warning>Envoie d'un mail <br/>Le prévient que sa demande n'est pas acceptée</warning>" ;
		} else {
			echo "<warning>Requête deja traitée par un autre administrateur</warning>";
		}
	}
	// On accepte le sondage
	//==========================
	if ($temp[0] == "valid") {
		cache_supprimer('sondages') ;// On supprime le cache pour reloader
		
		$DB_valid->query("SELECT v.perime,v.questions,v.titre,v.eleve_id,v.restriction, e.nom, e.prenom, e.promo FROM valid_sondages as v LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE sondage_id={$temp[1]}");
		if ($DB_valid->num_rows()!=0) {
		
			list($date,$questions,$titre,$eleve_id,$restriction,$nom, $prenom, $promo) = $DB_valid->next_row() ;
			
			//Log l'action de l'admin
			log_admin($_SESSION['user']->uid," validé le sondage '$titre'") ;

			$DB_web->query("INSERT INTO sondage_question SET eleve_id=$eleve_id, questions='$questions', titre='$titre', perime='$date', restriction='$restriction'") ;
			$index = mysql_insert_id($DB_web->link) ;
			$DB_valid->query("DELETE FROM valid_sondages WHERE sondage_id='{$temp[1]}'");
			
			$bla = "explication_".$temp[1] ;
			$contenu = "<strong>Bonjour</strong>, <br><br>".
						"Ton sondage vient d'être mis en ligne par le BR <br>";
			$contenu .= "Il est accessible à l'adresse suivante: ".BASE_URL."/sondage.php?id=".$index."<br>";
			$contenu .= $_POST[$bla]."<br>".
						"<br>" .
						"Très Cordialement<br>" .
						"Le Webmestre de Frankiz<br>"  ;
		
			couriel($temp[2],"[Frankiz] Ton sondage a été validé ",$contenu,WEBMESTRE_ID);
			echo "<commentaire>Envoie d'un mail <br/>Prévient $prenom $nom que sa demande est acceptée</commentaire>" ;
		} else {
			echo "<warning>Requête deja traitée par un autre administrateur</warning>";
		}
	}
	if ($temp[0] == "modif") {
		$DB_valid->query("SELECT 0 FROM valid_sondages WHERE sondage_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {
			$DB_valid->query("UPDATE valid_sondages SET questions='{$_POST['contenu_form']}', titre='{$_POST['titre_sondage']}', perime=FROM_UNIXTIME({$_POST['date']}) WHERE sondage_id={$temp[1]}");
			echo "<commentaire>Modification effectuée</commentaire>" ;
		 } else {
			echo "<warning>Requête deja traitée par un autre administrateur</warning>";
		}
	}

}

$DB_valid->query("COMMIT");
$DB_valid->query("UNLOCK TABLES");
$DB_trombino->query("UNLOCK TABLES");

//===============================

	$DB_valid->query("SELECT v.perime, v.sondage_id,v.questions,v.titre,v.eleve_id,v.restriction, e.nom, e.prenom, e.promo FROM valid_sondages as v LEFT JOIN trombino.eleves as e USING(eleve_id)");
	while(list($date,$id,$questions,$titre,$eleve_id,$restriction,$nom, $prenom, $promo) = $DB_valid->next_row()) {
	?>
		<formulaire id="form" titre="<?=$titre?> (<?=date("d/m",strtotime($date))?>)">	
	<?
		decode_sondage($questions) ;
	?>
		</formulaire>

		<formulaire id="sond_<? echo $id ?>" titre="Validation de '<?=$titre?>'" action="admin/valid_sondages.php">
			<note>Sondage proposé par <?=$prenom?> <?=$nom?> (<?=$promo?>)</note>
			<zonetext titre="La raison du choix du modérateur (Surtout si refus)" id="explication_<? echo $id ;?>"></zonetext>
			<textsimple id='restriction' valeur='Restriction demandée: <?=$restriction?>'/><br/>
			<choix titre="Sondage jusqu'à " id="date" type="combo" valeur="<? echo $date ;?>">
			<?	for ($i=1 ; $i<=MAX_PEREMPTION ; $i++) {
				$date_id = mktime(0, 0, 0, date("m") , date("d") + $i, date("Y")) ;
				$date_value = date("d/m/y" , $date_id);
				?>
				<option titre="<? echo $date_value?>" id="<? echo $date_id?>" />
				<?
			}
			?>
			</choix>
			<note>
				La syntaxe est la suivante:<br/>
				Pour une explication: ###expli///Mon texte<br/>
				Pour un champ: ###champ///Le nom du champ<br/>
				Pour un texte: ###text///Ma question<br/>
				Pour un radio: ###radio///ma question///option1///option2///option3<br/>
				Pour une boite déroulante: ###combo///ma question///option1///option2///option3<br/>
				Pour une checkbox: ###check///ma question///option1///option2///option3<br/>
			</note>
			<champ id="titre_sondage" titre="Titre" valeur="<?=$titre?>"/>
			<zonetext id="contenu_form" titre="Zone d'édition avancée" type="grand"><?=$questions?></zonetext>
			<bouton titre="Mettre à jour le sondage" id="modif_<? echo $id ?>_<? echo $eleve_id ?>" />
			<bouton id='valid_<? echo $id ?>_<? echo $eleve_id ?>' titre='Valider' onClick="return window.confirm('Valider ce sondage ?')"/>
			<bouton id='suppr_<? echo $id ?>_<? echo $eleve_id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer ce sondage ?!!!!!')"/>
		</formulaire>
	<?
	}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>