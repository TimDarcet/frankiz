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

	$Id$

*/

require_once "include/global.inc.php";

demande_authentification(AUTH_FORT);


//---------------------------------------------------------------------------------
// Fonction de décodage du sondage (résultat !)
//---------------------------------------------------------------------------------
function resultat_sondage($string, $sondage_id) {
	global $DB_web ;
	
	$DB_web->query("SELECT sondage_id FROM sondage_votants WHERE sondage_id='{$_REQUEST['id']}'");
	$nombre_votants = $DB_web->num_rows() ;
	echo "<p>==========================================================</p>" ;
	echo "<p>== $nombre_votants personnes ont répondu à ce sondage</p>" ;
	echo "<p>==========================================================</p>" ;
	$stringtab = explode ("###", $string);
	$i = 1;
	foreach ($stringtab as $string_part) {
		if (!$string_part) {
			continue;
		}

		$temp = explode("///",$string_part) ;
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
	
		if (($temp[0]=="radiolntab") || ($temp[0]=="checktab") || ($temp[0]=="radiotab"))
		{
			$tabheaders = explode("%%%", $temp[1]);
			$tablines = explode("%%%", $temp[2]);
			echo "<table>\n<tr><th></th>";

			foreach ($tabheaders as $tabheader)
			{
				echo "<th>$tabheader</th>";
			}

			echo "</tr>\n";

			for ($j = 0; $j < count($tablines); $j++)
			{
				echo "<tr><td>".$tablines[$j]."</td>";

				for ($k = 0; $k < count($tabheaders); $k++)
				{

					$DB_web->query("SELECT reponse FROM sondage_reponse WHERE question_num = '$i' AND sondage_id = '$sondage_id' AND reponse = '{$j}x{$k}'");

					echo "<td>".$DB_web->num_rows()."</td>";
				}

				echo "</tr>\n";
				
				if ($temp[0] == "radiolntab") 
				{
					$i++;
				}
			}

			echo "</table>";
		}

		$i++;
	}
}

//---------------------------------------------------------------------------------
// Génère un answer id pour le sondage
//---------------------------------------------------------------------------------
function answer_id($sondage_id) {
    global $DB_web;

    $DB_web->query("SELECT MAX(answer_id) + 1 AS aid FROM sondage_reponse WHERE sondage_id='$sondage_id'");
    list($aid) = $DB_web->next_row();
    if (!isset($aid)) {
        $aid = 0;
    }
    return $aid;
}

//======================================================================================================

$message = "" ;


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="sondage" titre="Frankiz : Sondage">

<?php
if (!isset($_REQUEST['id'])) {
	?>
	<warning>Le sondage que tu demandes n'existe plus ou n'a jamais existé.</warning>
	<?php
} else {
	$DB_web->query("SELECT restriction,eleve_id FROM sondage_question WHERE sondage_id='{$_REQUEST['id']}'");
	$restrOki=true;
	if ($DB_web->num_rows()==1) {
		list($restriction,$eleve_id) = $DB_web->next_row() ;
		if(($_SESSION['user']->uid!=$eleve_id)&&($restriction!='')){
			$restr = explode("_",$restriction);
			switch($restr[0]){
				case "promo":
					$DB_trombino->query("SELECT promo FROM eleves WHERE promo='{$restr[1]}' AND eleve_id='".$_SESSION['user']->uid."'");
					if($DB_trombino->num_rows()==0) $restrOki=false;
					break;
				case "section":
					$DB_trombino->query("SELECT section_id FROM eleves WHERE section_id='{$restr[1]}' AND eleve_id='".$_SESSION['user']->uid."'");
					if($DB_trombino->num_rows()==0) $restrOki=false;
					break;
				case "binet":
					$DB_trombino->query("SELECT binet_id FROM membres WHERE binet_id='{$restr[1]}' AND eleve_id='".$_SESSION['user']->uid."'");
					if($DB_trombino->num_rows()==0) $restrOki=false;
					break;
			}
		}
	}
	if($restrOki) {
		$a_vote="non" ;
		$DB_web->query("SELECT sondage_id FROM sondage_votants WHERE sondage_id='{$_REQUEST['id']}' AND eleve_id='".$_SESSION['user']->uid."'");
		if ($DB_web->num_rows()>=1) {
			$a_vote = "oui" ;
		?>
			<warning>Tu as déjà voté pour ce sondage...</warning>
		<?php
		$DB_web->query("SELECT sondage_id FROM sondage_votants WHERE sondage_id='{$_REQUEST['id']}'");
		$nombre_votants = $DB_web->num_rows() ;
		echo "<note>$nombre_votants personnes ont répondu à ce sondage.</note>" ;
		}
	
		// Si la personne a validé on stocke son vote ...
		//=================================================================
		
		// La personne a t'elle cliqué sur le bouton de validation ?
		if (isset($_POST['valid'])) {
			$DB_web->query("SELECT (TO_DAYS(perime) - TO_DAYS(NOW())) FROM sondage_question WHERE sondage_id='{$_REQUEST['id']}'");
			// Y a t'il un sondage qui existe sous cette id ?
			if ($DB_web->num_rows()==1) {
				list($delta) = $DB_web->next_row() ;
				
				// La date permet elle encore de voter ?
				if ($delta >= 0) {
					// Verifie que le mec a pas déja voté !
					$DB_web->query("SELECT sondage_id FROM sondage_votants WHERE sondage_id='{$_REQUEST['id']}' AND eleve_id='".$_SESSION['user']->uid."'");
					if ($DB_web->num_rows()==0) {
						// Il a donc pas voté
						// on le marque donc comme ayant voté
						$DB_web->query("INSERT INTO sondage_votants SET sondage_id='{$_REQUEST['id']}', eleve_id='".$_SESSION['user']->uid."'");
						$a_vote = "oui" ;
                        
                        // Lock la table et stocke les résultats
                        $DB_web->query("LOCK TABLE sondage_reponse WRITE");
                        $aid = answer_id($_REQUEST['id']);
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
                                if ($qnum > 0) {
    								$DB_web->query("INSERT INTO sondage_reponse SET sondage_id='{$_REQUEST['id']}', question_num='".$qnum."', reponse='$reponse', answer_id=$aid");
                                }
							}
						}
                        // On rend la table
                        $DB_web->query("UNLOCK TABLES");
					}
				}
			}
			?>
			<commentaire>Merci d'avoir voté</commentaire>
			<?php
		} else {
			// Début du formulaire !
			//======================================================
			echo $message ;
			
			$DB_web->query("SELECT DATE_FORMAT(v.perime,'%d/%m'),(TO_DAYS(perime) - TO_DAYS(NOW())), v.sondage_id,v.questions,v.titre,v.eleve_id, e.nom, e.prenom, e.promo FROM sondage_question as v LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE sondage_id='{$_REQUEST['id']}'");
			if ($DB_web->num_rows()==1) {
				list($date,$delta,$id,$questions,$titre,$eleve_id,$nom, $prenom, $promo) = $DB_web->next_row() ;
				
				// Le Formulaire pour repondre ...
			?>
				<note>Sondage proposé par <?php echo "$prenom $nom ($promo)" ?></note> 
			<?php
				if ($delta>=0) {
			?>
					<formulaire id="form" titre="<?php echo $titre; ?> (<?php echo $date; ?>)" action="sondage.php?id=<?php echo $_REQUEST['id']; ?>">
					<?php
					decode_sondage($questions) ;
					if ($a_vote=="non") {
					?>
						<bouton id='valid' titre='Valider' onClick="return window.confirm('Voulez vous vraiment valider le vote pour ce sondage?')"/>	
				<?php
					}
					?>
					</formulaire>
					<?php
				} else {
					// Les résultats du sondage !
					?>
					<cadre id="form" titre="<?php echo $titre; ?> (<?php echo $date; ?>)">
					<?php
					resultat_sondage($questions,$_REQUEST['id']) ;
					?>
					</cadre>
					<?php
				}
			} else {
				echo "<warning>Le sondage que tu demandes n'existe plus ou n'a jamais existé.</warning>";
			}
		}
	}else{
			echo "<warning>Ce sondage a un accès restreint et tu n'es pas autorisé à y participer.</warning>";
	}
}
	?>
</page>
<?php require "include/page_footer.inc.php" ?>
