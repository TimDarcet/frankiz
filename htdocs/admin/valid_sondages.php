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

			$DB_valid->query("DELETE FROM valid_sondages WHERE sondage_id='{$temp[1]}'");
			
			$bla = "refus_".$temp[1] ;
			$contenu = "<strong>Bonjour</strong>, <br><br>".
						"Nous sommes désolé mais ton sondage n'a pas été validé par le BR pour la raison suivante : <br>".
						$_POST[$bla]."<br>".
						"<br>" .
						"Très Cordialement<br>" .
						"Le BR<br>"  ;
		
			couriel($temp[2],"[Frankiz] Ton sondage a été refusé ",$contenu);
			echo "<warning>Envoie d'un mail <br/>Le prévient que sa demande n'est pas acceptée</warning>" ;
		} else {
	?>
			<warning>Requête deja traitée par un autre administrateur</warning>
	<?			
		}
	}
	// On accepte le sondage
	//==========================
	if ($temp[0] == "valid") {
		cache_supprimer('sondages') ;// On supprime le cache pour reloader
		
		$DB_valid->query("SELECT v.perime,v.questions,v.titre,v.eleve_id, e.nom, e.prenom, e.promo FROM valid_sondages as v LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE sondage_id={$temp[1]}");
		if ($DB_valid->num_rows()!=0) {
		
			list($date,$questions,$titre,$eleve_id,$nom, $prenom, $promo) = $DB_valid->next_row() ;
	
			$DB_web->query("INSERT INTO sondage_question SET eleve_id=$eleve_id, questions='$questions', titre='$titre', perime='$date'") ;
		
			$DB_valid->query("DELETE FROM valid_sondages WHERE sondage_id='{$temp[1]}'");
			
			$contenu = "<strong>Bonjour</strong>, <br><br>".
						"Ton sondage vient d'être mis en ligne par le BR <br>".
						"<br>" .
						"Très Cordialement<br>" .
						"Le BR<br>"  ;
		
			couriel($temp[2],"[Frankiz] Ton sondage a été validé ",$contenu);
			echo "<commentaire>Envoie d'un mail <br/>Prévient $prenom $nom que sa demande est acceptée</commentaire>" ;
		} else {
	?>
			<warning>Requête deja traitée par un autre administrateur</warning>
	<?			
		}

	}

}

$DB_valid->query("COMMIT");
$DB_valid->query("UNLOCK TABLES");
$DB_trombino->query("UNLOCK TABLES");

//===============================

	$DB_valid->query("SELECT v.perime, v.sondage_id,v.questions,v.titre,v.eleve_id, e.nom, e.prenom, e.promo FROM valid_sondages as v LEFT JOIN trombino.eleves as e USING(eleve_id)");
	while(list($date,$id,$questions,$titre,$eleve_id,$nom, $prenom, $promo) = $DB_valid->next_row()) {
	?>
		<formulaire id="form" titre="<?=$titre?> (<?=date("d/m",strtotime($date))?>)">	
	<?
		decode_sondage($questions) ;
	?>
		</formulaire>
		
		<formulaire id="sond_<? echo $id ?>" titre="Validation de '<?=$titre?>'" action="admin/valid_sondages.php">
			<note>Sondage proposé par <?=$prenom?> <?=$nom?> (<?=$promo?>)</note>
			<zonetext titre="Raison du Refus si refus" id="refus_<? echo $id ;?>"></zonetext>
			
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
