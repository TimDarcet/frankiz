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
	affichage d'un sondage

	$Log$
	Revision 1.3  2004/11/29 17:27:32  schmurtz
	Modifications esthetiques.
	Nettoyage de vielles balises qui trainaient.

	Revision 1.2  2004/11/19 17:14:31  kikx
	Gestion complete et enfin FINIIIIIIIIIIIIIIII des sondages !!! bon ok c'est assez moche l'affichage des resultats mais .... j'en ai marrrrrrrrrrre
	
	Revision 1.1  2004/11/17 23:46:21  kikx
	Prepa pour le votes des sondages
*/

require_once "include/global.inc.php";

demande_authentification(AUTH_MINIMUM);


//---------------------------------------------------------------------------------
// Fonction de décodage du sondage (résultat !)
//---------------------------------------------------------------------------------
function resultat_sondage($string,$sondage_id) {
	global $DB_web ;
	
	$DB_web->query("SELECT sondage_id FROM sondage_votants WHERE sondage_id='{$_GET['id']}'");
	$nombre_votants = $DB_web->num_rows() ;
	echo "<p>==========================================================</p>" ;
	echo "<p>== $nombre_votants personnes ont répondu à ce sondage</p>" ;
	echo "<p>==========================================================</p>" ;
	$string = explode("###",$string) ;
	for ($i=1 ; $i<count($string) ; $i++) {
		$temp = explode("///",$string[$i]) ;
		if ($temp[0]=="expli") {
			echo "<note>$temp[1]</note>\n" ;
		}
		if (($temp[0]=="champ")||($temp[0]=="text")) {
			$DB_web->query("SELECT reponse FROM sondage_reponse WHERE question_num='$i' AND sondage_id='$sondage_id' AND reponse !=''") ;
			echo "<p>$temp[1] (".($DB_web->num_rows())." réponses)</p>\n" ;
			while (list($rep) = $DB_web->next_row()) {
				echo "<p>$rep</p>" ;
			}
			echo "<p>=====================</p>" ;
			
		}

		if (($temp[0]=="radio")||($temp[0]=="combo")||($temp[0]=="check")) {
			echo "<p>$temp[1]</p>\n" ;
			for ($j=2 ; $j<count($temp) ; $j++) {
				$DB_web->query("SELECT reponse FROM sondage_reponse WHERE question_num='$i' AND sondage_id='$sondage_id' AND reponse='".($j-1)."'") ;
				echo "<p>$temp[$j] (".($DB_web->num_rows()).")</p>\n" ;
			}	
			echo "<p>=====================</p>" ;
		}
	}
}
//======================================================================================================

$message = "" ;


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="sondage" titre="Frankiz : Sondage">
<?
$a_vote="non" ;
$DB_web->query("SELECT sondage_id FROM sondage_votants WHERE sondage_id='{$_GET['id']}' AND eleve_id='".$_SESSION['user']->uid."'");
if ($DB_web->num_rows()>=1) {
	$a_vote = "oui" ;
?>
	<warning>Tu as déjà voté pour ce sondage...</warning>
<?
}
// Si la personne a valider on stock son vote ...
//=================================================================

// La personne a t'elle cliqué sur le vouton de validation ?
if (isset($_POST['valid'])) {
	$DB_web->query("SELECT (TO_DAYS(perime) - TO_DAYS(NOW())) FROM sondage_question WHERE sondage_id='{$_GET['id']}'");
	// Y a t'il yn sondage qui existe sous cette id ?
	if ($DB_web->num_rows()==1) {
		list($delta) = $DB_web->next_row() ;
		
		// La date permet elle encore de voter ?
		if ($delta >= 0) {
			// Verifie que le mec a pas déja voté !
			$DB_web->query("SELECT sondage_id FROM sondage_votants WHERE sondage_id='{$_GET['id']}' AND eleve_id='".$_SESSION['user']->uid."'");
			if ($DB_web->num_rows()==0) {
				// Il a donc pas voté
				//on le marque donc comme ayant voté
				$DB_web->query("INSERT INTO sondage_votants SET sondage_id='{$_GET['id']}', eleve_id='".$_SESSION['user']->uid."'");
				$a_vote = "oui" ;
				// On va lire les variables du sondage
				foreach($_POST as $keys => $val) {
					if ($keys != 'valid') {
						// Il y a 2 cas car nous avons le cas des checkbox a traiter ...
						if (count(explode("_",$keys))==2) {
							$temp = explode("_",$keys) ;
							$qnum = $temp[0] ;
							$reponse = $temp[1] ;
						} else {
							$qnum = $keys ;
							$reponse = $val ;
						}
						$DB_web->query("INSERT INTO sondage_reponse SET sondage_id='{$_GET['id']}', question_num='".$qnum."', reponse='$reponse' ");
					}
				}
			}
		}
	}
}
// Dévut du formulaire !
//======================================================
echo $message ;

$DB_web->query("SELECT v.perime,(TO_DAYS(perime) - TO_DAYS(NOW())), v.sondage_id,v.questions,v.titre,v.eleve_id, e.nom, e.prenom, e.promo FROM sondage_question as v INNER JOIN trombino.eleves as e USING(eleve_id) WHERE sondage_id='{$_GET['id']}'");
if ($DB_web->num_rows()==1) {
	list($date,$delta,$id,$questions,$titre,$eleve_id,$nom, $prenom, $promo) = $DB_web->next_row() ;
	
	// Le Formulaire pour repondre ...
?>
	<note>Sondage proposé par <? echo "$prenom $nom ($promo)" ?></note> 
<?
	if ($delta>=0) {
?>
		<formulaire id="form" titre="<?=$titre?> (<?=date("d/m",strtotime($date))?>)" action="sondage.php?id=<?=$_GET['id']?>">
		<?
		decode_sondage($questions) ;
		if ($a_vote=="non") {
		?>
			<bouton id='valid' titre='Valider' onClick="return window.confirm('Voulez vous vraiment valider le vote pour ce sondage?')"/>	
	<?
		}
		?>
		</formulaire>
		<?
	} else {
		// Les résultats du sondage !
		?>
		<cadre id="form" titre="<?=$titre?> (<?=date("d/m",strtotime($date))?>)">
		<?
		resultat_sondage($questions,$_GET['id']) ;
		?>
		</cadre>
		<?
	}
} else {
?>
<warning>Le sondage que tu demandes n'existes plus ou n'a jamais existé</warning>
<?
}
?>
</page>
<?php require "include/page_footer.inc.php" ?>
