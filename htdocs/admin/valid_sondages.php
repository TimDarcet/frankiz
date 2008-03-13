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
	
	$Id$

*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
if(!verifie_permission('admin')&&!verifie_permission('web'))
	acces_interdit();

// Génération de la page
//===============
require_once BASE_FRANKIZ."include/page_header.inc.php";

?>
<page id="valid_sondage" titre="Frankiz : Valide un sondage">

<h1>Validation des sondages</h1>

<?php
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
			log_admin($_SESSION['uid']," refusé le sondage '$titre'") ;
			
			$DB_valid->query("DELETE FROM valid_sondages WHERE sondage_id='{$temp[1]}'");
			
			$bla = "explication_".$temp[1] ;
			$contenu = "<strong>Bonjour</strong>, <br><br>".
						"Nous sommes désolés mais ton sondage n'a pas été validé par le BR pour la raison suivante : <br>".
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

	// On modifie le sondage
	//==========================
	if ($temp[0] == "modif" or $temp[0] == "valid") {
		$DB_valid->query("SELECT 0 FROM valid_sondages WHERE sondage_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {
			$DB_valid->query("UPDATE valid_sondages SET questions='{$_POST['contenu_form']}', titre='{$_POST['titre_sondage']}', perime=FROM_UNIXTIME({$_POST['date']}) WHERE sondage_id={$temp[1]}");
			echo "<commentaire>Modification effectuée</commentaire>" ;
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
			log_admin($_SESSION['uid']," validé le sondage '$titre'") ;

			$DB_web->query("INSERT INTO sondage_question SET eleve_id=$eleve_id, questions='$questions', titre='$titre', perime='$date', restriction='$restriction'") ;
			$index = mysql_insert_id($DB_web->link) ;
			$DB_valid->query("DELETE FROM valid_sondages WHERE sondage_id='{$temp[1]}'");
			
			$bla = "explication_".$temp[1] ;
			$contenu = "<strong>Bonjour</strong>, <br><br>".
						"Ton sondage vient d'être mis en ligne par le BR. <br>";
			$contenu .= "Il est accessible à l'adresse suivante : ".BASE_URL."/sondage.php?id=".$index."<br>";
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

}

$DB_valid->query("COMMIT");
$DB_valid->query("UNLOCK TABLES");
$DB_trombino->query("UNLOCK TABLES");

//===============================

	$DB_valid->query("SELECT v.perime, v.sondage_id,v.questions,v.titre,v.eleve_id,v.restriction, e.nom, e.prenom, e.promo FROM valid_sondages as v LEFT JOIN trombino.eleves as e USING(eleve_id)");
	while(list($date,$id,$questions,$titre,$eleve_id,$restriction,$nom, $prenom, $promo) = $DB_valid->next_row()) {
	?>
		<formulaire id="form" titre='<?php echo $titre; ?> (<?php echo date("d/m", strtotime($date)); ?>)'>	
	<?php
		decode_sondage($questions) ;
	?>
		</formulaire>

		<formulaire id="sond_<?php echo $id ?>" titre="Validation de '<?php echo $titre; ?>'" action="admin/valid_sondages.php">
			<note>Sondage proposé par <?php echo $prenom; ?> <?php echo $nom; ?> (<?php echo $promo; ?>)</note>
			<zonetext titre="La raison du choix du modérateur (Surtout si refus)" id="explication_<?php echo $id ;?>"></zonetext>
			<textsimple id='restriction' valeur='Restriction demandée :
				<?php 
				$restriction_nom = "Aucune";
				if ($restriction && $restriction != "aucune") {
					$restr = explode("_",$restriction);
					if ($restr[0]=="promo") $restriction_nom = "Promo ".$restr[1];
					if ($restr[0]=="section") {
						$DB_trombino->query("SELECT nom FROM sections WHERE section_id = $restr[1]");
						list($restriction_nom) = $DB_trombino->next_row();
						$restriction_nom = "Section ".$restriction_nom;
					}
					if ($restr[0]=="binet") {
						$DB_trombino->query("SELECT nom FROM binets WHERE binet_id = $restr[1]");
						list($restriction_nom) = $DB_trombino->next_row();
						$restriction_nom = "Binet ".$restriction_nom;
					}
				}

			echo $restriction_nom;	?>'/><br/>
	
			<choix titre="Sondage jusqu'à " id="date" type="combo" valeur="<?php echo strtotime($date);?>">
			<?php	for ($i=1 ; $i<=MAX_PEREMPTION ; $i++) {
				$date_id = mktime(0, 0, 0, date("m") , date("d") + $i, date("Y")) ;
				$date_value = date("d/m/y" , $date_id);
				?>
				<option titre="<?php echo $date_value?>" id="<?php echo $date_id?>" />
				<?php
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
			<champ id="titre_sondage" titre="Titre" valeur="<?php echo $titre; ?>"/>
			<zonetext id="contenu_form" titre="Zone d'édition avancée" type="grand"><?php echo $questions; ?></zonetext>
			<bouton titre="Mettre à jour le sondage" id="modif_<?php echo $id ?>_<?php echo $eleve_id ?>" />
			<bouton id='valid_<?php echo $id ?>_<?php echo $eleve_id ?>' titre='Valider' onClick="return window.confirm('Valider ce sondage ?')"/>
			<bouton id='suppr_<?php echo $id ?>_<?php echo $eleve_id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer ce sondage ?!!!!!')"/>
		</formulaire>
	<?php
	}
?>
</page>

<?php
require_once BASE_FRANKIZ."include/page_footer.inc.php";
?>
