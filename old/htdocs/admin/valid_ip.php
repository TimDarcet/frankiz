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
	Cette page gère l'attribution d'adresses IP supplémentaires aux élèves.
	L'élève fait une demande grâce à la page profil/demande_ip.php, on valide
	ou refuse la demande ici.
	
	$Id$
	
*/

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
if(!verifie_permission('admin'))
	acces_interdit();

// Génération de la page
//===============
require_once BASE_FRANKIZ."htdocs/include/page_header.inc.php";

?>
<page id="admin_valid_ip" titre="Frankiz : Attribuer une nouvelle adresse IP">

<?php
// On regarde quel cas c'est ...
// On envoie chier le mec pour son changement d'ip et on le supprime de la base
// On accepte le changement et on l'inscrit dans la base

$DB_valid->query("LOCK TABLE valid_ip WRITE");
$DB_valid->query("SET AUTOCOMMIT=0");

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;
	
	// On refuse la demande d'ip supplémentaire
	//==========================
	if ($temp[0] == "vtff") {
		$DB_valid->query("DELETE FROM valid_ip WHERE eleve_id='{$temp[1]}'");
		
		$DB_trombino->query("SELECT nom,prenom, promo FROM eleves WHERE eleve_id='{$temp[1]}'") ;
		list($nom,$prenom,$promo) = $DB_trombino->next_row() ;
		//Log l'action de l'admin
		log_admin($_SESSION['uid']," refusé l'ajout d'une ip à $prenom $nom ($promo) ") ;
		
		$bla = "refus_".$temp[1] ;
		$contenu = "Bonjour, <br><br>".
					"Nous sommes désolés de ne pas pouvoir t'attribuer une adresse IP supplémentaire pour la raison suivante :<br>".
					$_POST[$bla]."<br>".
					"Tu peux contacter les roots (root@frankiz) pour de plus amples informations.<br>".
					"<br>" .
					"Très Cordialement<br>" .
					"Le BR<br>"  ;
	
		couriel($temp[1],"[Frankiz] Ta demande a été refusée ",$contenu,ROOT_ID);
		echo "<commentaire>Envoi d'un mail. On prévient l'utilisateur que sa demande n'a pas été acceptée.</commentaire>" ;
	}
	
	// On accepte la demande d'ip supplémentaire
	//===========================
	if ($temp[0] == "ok") {
		$temp2 = "ajout_ip_".$temp[1] ;
		$temp3 = "raison_".$temp[1] ;
		$DB_trombino->query("SELECT e.piece_id,p.prise_id FROM eleves AS e LEFT JOIN admin.prises AS p USING(piece_id) WHERE e.eleve_id='{$temp[1]}' AND p.type='principale'") ;
		list($kzert,$id_prise) = $DB_trombino->next_row();
		
		$DB_admin->query("SELECT 0 FROM prises WHERE ip='{$_POST[$temp2]}'");
		
		// S'il n'y a aucune entrée avec cette ip dans la base
		if ($DB_admin->num_rows()==0){
			$DB_trombino->query("SELECT nom,prenom, promo FROM eleves WHERE eleve_id='{$temp[1]}'") ;
			list($nom,$prenom,$promo) = $DB_trombino->next_row() ;
			//Log l'action de l'admin
			log_admin($_SESSION['uid']," accepté l'ajout d'une ip à $prenom $nom ($promo) ") ;
			
			$DB_valid->query("DELETE FROM valid_ip WHERE eleve_id='{$temp[1]}'");
			$DB_admin->query("INSERT prises SET prise_id='$id_prise',piece_id='$kzert',ip='{$_POST[$temp2]}',type='secondaire'");
			
			$contenu = "Bonjour, <br><br>".
						"Nous t'avons attribué l'adresse IP suivante :<br>".
						$_POST[$temp2]."<br>".
						"<br>" .
						"Très Cordialement<br>" .
						"Le BR<br>";
		
			couriel($temp[1],"[Frankiz] Ta demande a été acceptée",$contenu,ROOT_ID);
			echo "<commentaire>Envoi d'un mail. On prévient l'utilisateur que sa demande a été acceptée (nouvelle adresse IP : ".$_POST[$temp2].").</commentaire>" ;
			
		// S'il y  a deja une entrée comme celle demandé dans la base !
		} else {
			echo "<warning>IMPOSSIBLE D'ATTRIBUER CETTE IP. Une autre personne la posséde déjà.</warning>" ;
		}
		
	}

/* Plus de SMAC

	// On refuse la demande de mac supplémentaire
	//==========================
	if ($temp[0] == "vtffmac") {
		$DB_valid->query("DELETE FROM valid_ip WHERE eleve_id='{$temp[1]}'");
		
		$DB_trombino->query("SELECT nom,prenom, promo FROM eleves WHERE eleve_id='{$temp[1]}'") ;
		list($nom,$prenom,$promo) = $DB_trombino->next_row() ;
		//Log l'action de l'admin
		log_admin($_SESSION['uid']," refusé l'ajout d'une @mac à $prenom $nom ($promo) ") ;
		
		$bla = "refus_".$temp[1] ;
		$contenu = "Bonjour, <br><br>".
					"Nous sommes désolés de pas pouvoir t'enregistrer une adresse MAC supplémentaire pour la raison suivante :<br>".
					$_POST[$bla]."<br>".
					"Très Cordialement<br>" .
					"Le BR<br>"  ;
	
		couriel($temp[1],"[Frankiz] Ta demande a été refusée ",$contenu,ROOT_ID);
		echo "<commentaire>Envoi d'un mail. On prévient l'utilisateur que sa demande n'a pas été acceptée.</commentaire>" ;
	}
	
	// On accepte la demande de mac supplémentaire
	//===========================
	if ($temp[0] == "okmac") {
		$DB_trombino->query("SELECT nom,prenom, promo FROM eleves WHERE eleve_id='{$temp[1]}'") ;
		list($nom,$prenom,$promo) = $DB_trombino->next_row() ;
		//Log l'action de l'admin
		log_admin($_SESSION['uid']," accepté l'ajout d'une @mac à $prenom $nom ($promo) ") ;

		$DB_valid->query("DELETE FROM valid_ip WHERE eleve_id='{$temp[1]}'");
		
		$contenu = "Bonjour, <br><br>".
					"Nous avons rajouté l'adresse MAC que tu nous a donné dans notre base. ".
					"Ton ordinateur devrait pouvoir accéder au réseau d'ici une demi-heure.<br><br>".
					"En cas de problème, n'hésite pas à envoyer un mail à root@frankiz.<br>".
					"<br>" .
					"Très Cordialement<br>" .
					"Le BR<br>";
	
		couriel($temp[1],"[Frankiz] Ta demande a été acceptée",$contenu,ROOT_ID);
		echo "<commentaire>Envoi d'un mail. On prévient l'utilisateur que sa demande a été acceptée</commentaire>" ;
	}
*/	


	// On vire une ip qu'on avait validée
	//===========================
	if ($temp[0] == "suppr") {
		$temp2 = str_replace("x",".",$temp[1]) ; // euh c'est pas bo je suis d'accord mais bon c'est pour que ca marche sans trop de trick
		$DB_admin->query("DELETE FROM prises WHERE type='secondaire' AND ip='$temp2'");
		
		$DB_trombino->query("SELECT nom,prenom, promo FROM eleves WHERE eleve_id='{$temp[2]}'") ;
		list($nom,$prenom,$promo) = $DB_trombino->next_row() ;
		//Log l'action de l'admin
		log_admin($_SESSION['uid']," supprimé une ip à $prenom $nom ($promo) ") ;
		
		$contenu = "Bonjour, <br><br>".
					"Nous avons supprimé l'adresse IP suivante qui t'était actuellement attribuée :<br><br>".
					$temp2."<br><br>".
					"Très Cordialement<br>" .
					"Le BR<br>"  ;
	
		couriel($temp[2],"[Frankiz] Suppression d'une adresse IP",$contenu,ROOT_ID);
		echo "<commentaire>Envoi d'un mail. On prévient l'utilisateur que son adresse IP $temp2 vient d'être supprimée.</commentaire>" ;

	}
}
$DB_valid->query("UNLOCK TABLES");
$DB_admin->query("UNLOCK TABLES");
?>

<h2>Liste des personnes demandant une IP supplémentaire :</h2>
	<liste id="liste" selectionnable="non" action="admin/valid_ip.php">
		<entete id="prise" titre="Prise"/>
		<entete id="casert" titre="Casert"/>
		<entete id="eleve" titre="Élève"/>
		<entete id="raison" titre="Raison"/>
		<entete id="ip" titre="IP"/>
<?php
		$DB_valid->query("SELECT v.raison,e.nom,e.prenom,e.piece_id,e.eleve_id,p.prise_id,p.ip,v.type FROM valid_ip as v LEFT JOIN trombino.eleves as e USING(eleve_id) LEFT JOIN admin.prises AS p USING(piece_id) WHERE p.type='principale'");
		while(list($raison,$nom,$prenom,$piece,$eleve_id,$prise,$ip0,$type) = $DB_valid->next_row()) {
?>
			<element id="<?php echo $eleve_id ;?>">
				<colonne id="prise"><?php echo "$prise" ?></colonne>
				<colonne id="casert"><?php echo "$piece" ?></colonne>
				<colonne id="eleve"><?php echo "$nom $prenom" ?></colonne>
				<colonne id="raison">
					<?php 
						echo "<em>";
						switch($type){
							case 1:
								echo "J'ai installé un 2ème ordinateur dans mon casert et je souhaite avoir une nouvelle adresse IP pour cette machine.<br/>";
								break;
							default:
								echo "Autre raison qu'un 2ème ordinateur.<br/>";
						}
						echo "</em><br/>";
						echo "Commentaire : $raison"; 
					?>
					<br/><br/>
					<textsimple titre="" id="raison2_<?php echo $eleve_id ;?>" valeur="Raison si refus :"/><br/>
					<zonetext titre="Raison du Refus si refus" id="refus_<?php echo $eleve_id ;?>"></zonetext>
				</colonne>
				<colonne id="ip">
<?php
				$DB_admin->query("SELECT ip FROM prises WHERE piece_id='$piece'") ;
				while(list($ip)=$DB_admin->next_row()) {
					echo "<p>" ;
						echo $ip ;
					echo "</p>" ;
				}

				$new_ip="129.104.";
				$ssrezo=substr($ip0, 8, 3);
				$new_ip.=$ssrezo.".";

				// BEM /PEM: +128
				if($ssrezo == 203 || $ssrezo == 204 || $ssrezo == 205)
					$new_ip .= (substr($ip0, 12, 3)+128);
				// Foch, Fayolle, Joffre: +64
				else if ($ssrezo >= 208 && $ssrezo <= 219)
					$new_ip .= (substr($ip0, 12, 3)+64);
				// Nouveaux batiments: +64
				else if ($ssrezo >= 224 && $ssrezo <= 229)
					$new_ip .= (substr($ip0, 12, 3)+64);
				// Maunoury: +64
				else if ($ssrezo >= 232 && $ssrezo <= 235)
					$new_ip .= (substr($ip0, 12, 3)+64);
?>
				<champ titre="" id="ajout_ip_<?php echo $eleve_id ;?>" valeur="<?php echo $new_ip ?>" /> 
				<bouton titre="Ok" id="ok_<?php echo $eleve_id ;?>" />
				<bouton titre="Vtff" id="vtff_<?php echo $eleve_id ;?>" onClick="return window.confirm('Voulez vous vraiment ne pas valider cette demande ?')"/>
<?php /* Plus de SMAC
				<bouton titre="Ok" id="okmac_<?php echo $eleve_id ;?>" />
				<bouton titre="Vtff" id="vtffmac_<?php echo $eleve_id ;?>" onClick="return window.confirm('Voulez vous vraiment ne pas valider cette demande ?')"/>
*/ ?>
				</colonne>
			</element>
<?php
		}
?>
	</liste>
	
	
	
	<h2>Liste des personnes ayant eu leurs IPs supplémentaires :</h2>
	<liste id="liste" selectionnable="non" action="admin/valid_ip.php">
		<entete id="prise" titre="Prise"/>
		<entete id="ip" titre="IP"/>
		<entete id="degage" titre=""/>
		<entete id="eleve" titre="Élève"/>

<?php


		$DB_admin->query("SELECT e.eleve_id,e.nom,e.promo,e.prenom,prises.ip,prises.prise_id FROM prises LEFT JOIN trombino.eleves as e USING(piece_id) WHERE type='secondaire' ORDER BY prises.prise_id ASC, prises.ip ASC, e.nom ASC, e.prenom ASC");
		while(list($id,$nom,$promo,$prenom,$ip,$prise) = $DB_admin->next_row()) {
?>
			<element id="<?php echo str_replace(".","x",$ip) ;?>">
				<colonne id="prise"><?php echo "$prise" ?></colonne>
				<colonne id="ip"><?php echo $ip ;?></colonne>
				<colonne id="degage"><bouton titre="Dégage!" id="suppr_<?php echo str_replace(".","x",$ip) ;?>_<?php echo $id?>" onClick="return window.confirm('Voulez vous vraiment supprimez cette ip ?')"/></colonne>
				<colonne id="eleve"><?php echo "$nom $prenom ($promo)" ?></colonne>
			</element>
<?php
		}
?>
	</liste>

</page>

<?php require_once BASE_FRANKIZ."htdocs/include/page_footer.inc.php" ?>
