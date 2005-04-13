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
	
	$Log$
	Revision 1.42  2005/04/13 17:09:58  pico
	Passage de tous les fichiers en utf8.

	Revision 1.41  2005/03/04 20:22:58  pico
	Demande de nouvelle adresse MAC
	Fixe les bugs #60 et #70
	
	Revision 1.40  2005/03/04 12:06:55  pico
	Fixe bug #66
	On propose automatiquement une 2 eme ip
	
	Revision 1.39  2005/02/15 19:30:40  kikx
	Mise en place de log pour surveiller l'admin :)
	
	Revision 1.38  2005/01/13 17:10:58  pico
	Mails de validations From le validateur qui va plus ou moins bien
	
	Revision 1.37  2005/01/10 09:49:11  pico
	Erreur
	
	Revision 1.36  2005/01/10 09:15:19  pico
	Grr
	
	Revision 1.34  2005/01/10 09:06:02  pico
	Pb de lock sur les tables mysql
	
	Revision 1.33  2005/01/10 08:38:04  pico
	BugFix
	
	Revision 1.32  2005/01/10 08:25:40  pico
	Plus sûr
	
	Revision 1.31  2005/01/10 08:20:57  pico
	Ajoute le numero de prise
	
	Revision 1.30  2005/01/10 08:16:39  pico
	Correction bug #21 aussi (on pouvait pas virer les ip des gens)
	
	Revision 1.29  2005/01/10 08:04:25  pico
	Mise en page
	
	Revision 1.28  2005/01/10 07:55:04  pico
	Tjs bug #21
	
	Revision 1.27  2005/01/10 07:48:58  pico
	Bug #21
	
	Revision 1.26  2005/01/02 20:36:32  pico
	Bug
	
	Revision 1.25  2004/12/17 17:25:08  schmurtz
	Ajout d'une belle page d'erreur.
	
	Revision 1.24  2004/12/17 14:34:18  pico
	J'avais fait de la merde...
	
	Revision 1.22  2004/12/16 13:00:41  pico
	INNER en LEFT
	
	Revision 1.21  2004/12/15 00:01:56  kikx
	esthetique
	
	Revision 1.20  2004/12/08 13:00:34  kikx
	Protection de la validation des ip
	
	Revision 1.19  2004/11/29 17:27:32  schmurtz
	Modifications esthetiques.
	Nettoyage de vielles balises qui trainaient.
	
	Revision 1.18  2004/11/27 20:16:55  pico
	Eviter le formatage dans les balises <note> <commentaire> et <warning> lorsque ce n'est pas necessaire
	
	Revision 1.17  2004/11/27 15:02:17  pico
	Droit xshare et faq + redirection vers /gestion et non /admin en cas de pbs de droits
	
	Revision 1.16  2004/11/23 23:30:20  schmurtz
	Modification de la balise textarea pour corriger un bug
	(return fantomes)
	
	Revision 1.15  2004/10/29 15:41:47  kikx
	Passage des mail en HTML pour les ip
	
	Revision 1.14  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.13  2004/09/17 12:45:22  kikx
	Permet de voi quel sont les ips que la personne a déjà avant de valider ... en particulier ca permet de pas se planter de sous réseau !!!!!!!!!!!!!
	
	Revision 1.10  2004/09/15 23:20:18  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.9  2004/09/15 21:42:27  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	acces_interdit();

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="admin_valid_ip" titre="Frankiz : Attribuer une nouvelle adresse IP/MAC">

<?
// On regarde quel cas c'est ...
// On envoie chié le mec pour son changement d'ip et on le supprime de la base
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
		log_admin($_SESSION['user']->uid," refusé l'ajout d'une ip à $prenom $nom ($promo) ") ;
		
		$bla = "refus_".$temp[1] ;
		$contenu = "Bonjour, <br><br>".
					"Nous sommes désolés de pas pouvoir d'attribuer une adresse IP supplémentaire pour la raison suivante :<br>".
					$_POST[$bla]."<br>".
					"Il y a certainement une autre façon de procéder pour atteindre ton but.<br>".
					"<br>" .
					"Très Cordialement<br>" .
					"Le BR<br>"  ;
	
		couriel($temp[1],"[Frankiz] Ta demande a été refusée ",$contenu,ROOT_ID);
		echo "<commentaire>Envoie d'un mail. On prévient l'utilisateur que sa demande n'a pas été acceptée.</commentaire>" ;
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
			log_admin($_SESSION['user']->uid," accepté l'ajout d'une ip à $prenom $nom ($promo) ") ;
			
			$DB_valid->query("DELETE FROM valid_ip WHERE eleve_id='{$temp[1]}'");
			$DB_admin->query("INSERT prises SET prise_id='$id_prise',piece_id='$kzert',ip='{$_POST[$temp2]}',type='secondaire'");
			
			$contenu = "Bonjour, <br><br>".
						"Nous t'avons attribué l'adresse IP suivante :<br>".
						$_POST[$temp2]."<br>".
						"<br>" .
						"Très Cordialement<br>" .
						"Le BR<br>";
		
			couriel($temp[1],"[Frankiz] Ta demande a été acceptée",$contenu,ROOT_ID);
			echo "<commentaire>Envoie d'un mail. On prévient l'utilisateur que sa demande a été acceptée (nouvelle adresse IP : ".$_POST[$temp2].")</commentaire>" ;
			
		// S'il y  a deja une entrée comme celle demandé dans la base !
		} else {
			echo "<warning>IMPOSSIBLE D'ATTRIBUER CETTE IP. Une autre personne la posséde déjà.</warning>" ;
		}
		
	}
	
	// On refuse la demande de mac supplémentaire
	//==========================
	if ($temp[0] == "vtffmac") {
		$DB_valid->query("DELETE FROM valid_ip WHERE eleve_id='{$temp[1]}'");
		
		$DB_trombino->query("SELECT nom,prenom, promo FROM eleves WHERE eleve_id='{$temp[1]}'") ;
		list($nom,$prenom,$promo) = $DB_trombino->next_row() ;
		//Log l'action de l'admin
		log_admin($_SESSION['user']->uid," refusé l'ajout d'une @mac à $prenom $nom ($promo) ") ;
		
		$bla = "refus_".$temp[1] ;
		$contenu = "Bonjour, <br><br>".
					"Nous sommes désolés de pas pouvoir t'enregistrer une adresse MAC supplémentaire pour la raison suivante :<br>".
					$_POST[$bla]."<br>".
					"Très Cordialement<br>" .
					"Le BR<br>"  ;
	
		couriel($temp[1],"[Frankiz] Ta demande a été refusée ",$contenu,ROOT_ID);
		echo "<commentaire>Envoie d'un mail. On prévient l'utilisateur que sa demande n'a pas été acceptée.</commentaire>" ;
	}
	
	// On accepte la demande de mac supplémentaire
	//===========================
	if ($temp[0] == "okmac") {
		$DB_trombino->query("SELECT nom,prenom, promo FROM eleves WHERE eleve_id='{$temp[1]}'") ;
		list($nom,$prenom,$promo) = $DB_trombino->next_row() ;
		//Log l'action de l'admin
		log_admin($_SESSION['user']->uid," accepté l'ajout d'une @mac à $prenom $nom ($promo) ") ;

		$DB_valid->query("DELETE FROM valid_ip WHERE eleve_id='{$temp[1]}'");
		
		$contenu = "Bonjour, <br><br>".
					"Nous avons rajouté l'adresse MAC que tu nous a donné dans notre base.<br>".
					"<br>" .
					"Très Cordialement<br>" .
					"Le BR<br>";
	
		couriel($temp[1],"[Frankiz] Ta demande a été acceptée",$contenu,ROOT_ID);
		echo "<commentaire>Envoie d'un mail. On prévient l'utilisateur que sa demande a été acceptée</commentaire>" ;
	}
	
	// On vire une ip qu'on avait validé
	//===========================
	if ($temp[0] == "suppr") {
		$temp2 = str_replace("x",".",$temp[1]) ; // euh c'est pas bo je suis d'accord mais bon c'est pour que ca marche sans trop de trick
		$DB_admin->query("DELETE FROM prises WHERE type='secondaire' AND ip='$temp2' AND prise_id=''");
		
		$DB_trombino->query("SELECT nom,prenom, promo FROM eleves WHERE eleve_id='{$temp[2]}'") ;
		list($nom,$prenom,$promo) = $DB_trombino->next_row() ;
		//Log l'action de l'admin
		log_admin($_SESSION['user']->uid," supprimé une ip à $prenom $nom ($promo) ") ;
		
		$contenu = "Bonjour, <br><br>".
					"Nous avons supprimé l'adresse IP suivante qui t'était actuellement attribuée :<br><br>".
					$temp2."<br><br>".
					"Très Cordialement<br>" .
					"Le BR<br>"  ;
	
		couriel($temp[2],"[Frankiz] Suppression d'une adresse IP",$contenu,ROOT_ID);
		echo "<commentaire>Envoie d'un mail. On prévient l'utilisateur que son adresse IP $temp2 vient d'être supprimée.</commentaire>" ;

	}
}
$DB_valid->query("UNLOCK TABLES");
$DB_admin->query("UNLOCK TABLES");
?>

<h2>Liste des personnes demandant</h2>
	<liste id="liste" selectionnable="non" action="admin/valid_ip.php">
		<entete id="prise" titre="Prise"/>
		<entete id="eleve" titre="Élève"/>
		<entete id="raison" titre="Raison"/>
		<entete id="ip" titre="IP"/>
<?
		$DB_valid->query("SELECT v.raison,e.nom,e.prenom,e.piece_id,e.eleve_id,p.prise_id,p.ip,v.mac,v.type FROM valid_ip as v LEFT JOIN trombino.eleves as e USING(eleve_id) LEFT JOIN admin.prises AS p USING(piece_id) WHERE p.type='principale'");
		while(list($raison,$nom,$prenom,$piece,$eleve_id,$prise,$ip0,$mac,$type) = $DB_valid->next_row()) {
?>
			<element id="<? echo $eleve_id ;?>">
				<colonne id="prise"><? echo "$prise" ?></colonne>
				<colonne id="eleve"><? echo "$nom $prenom" ?></colonne>
				<colonne id="raison">
					<strong>Nouvelle @MAC: </strong><? echo $mac ?><br/>
					<? 
						echo "<em>";
						switch($type){
							case 1: 
								echo "J'ai remplacé l'ordinateur qui était dans mon casert et je souhaite juste pouvoir acceder au réseau avec (l'ancien ne pourra plus y accéder)<br/>";
								break;
							case 2:
								echo "J'ai installé un 2ème ordinateur dans mon casert et je souhaite avoir une nouvelle adresse pour cette machine<br/>";
								break;
							default:
								echo "Autre raison<br/>";
						}
						echo "</em><br/>";
						echo "Commentaire: $raison"; 
					?>
					<br/><br/>
					<textsimple titre="" id="raison2_<? echo $eleve_id ;?>" valeur="Raison si refus :"/><br/>
					<zonetext titre="Raison du Refus si refus" id="refus_<? echo $eleve_id ;?>"></zonetext>
				</colonne>
				<colonne id="ip">
<?
				$DB_admin->query("SELECT ip FROM prises WHERE piece_id='$piece'") ;
				while(list($ip)=$DB_admin->next_row()) {
					echo "<p>" ;
						echo $ip ;
					echo "</p>" ;
				}
				if($type!=1){
					$new_ip="129.104.";
					$ssrezo=substr($ip0, 8, 3);
					$new_ip.=$ssrezo.".";
					if($ssrezo==203)
						$new_ip.=(substr($ip0, 12, 3)+50);
					else if($ssrezo==204)
						$new_ip.=(substr($ip0, 12, 3)+25);
					else if($ssrezo==214)
						$new_ip.=(substr($ip0, 12, 3)+110);
					else if(($ssrezo<=222)&&($ssrezo>=203)&&($ssrezo!=213))
						$new_ip.=(substr($ip0, 12, 3)+70);
?>
					<champ titre="" id="ajout_ip_<? echo $eleve_id ;?>" valeur="<? echo $new_ip ?>" /> 
					<bouton titre="Ok" id="ok_<? echo $eleve_id ;?>" />
					<bouton titre="Vtff" id="vtff_<? echo $eleve_id ;?>" onClick="return window.confirm('Voulez vous vraiment ne pas valider cette demande ?')"/>
<? 				} else {?>
					<bouton titre="Ok" id="okmac_<? echo $eleve_id ;?>" />
					<bouton titre="Vtff" id="vtffmac_<? echo $eleve_id ;?>" onClick="return window.confirm('Voulez vous vraiment ne pas valider cette demande ?')"/>
<? 				}?>
				</colonne>
			</element>
<?
		}
?>
	</liste>
	
	
	
	<h2>Liste des personnes ayant eu leurs ips supplémentaires</h2>
	<liste id="liste" selectionnable="non" action="admin/valid_ip.php">
		<entete id="prise" titre="Prise"/>
		<entete id="ip" titre="IP"/>
		<entete id="degage" titre=""/>
		<entete id="eleve" titre="Élève"/>

<?


		$DB_admin->query("SELECT e.eleve_id,e.nom,e.promo,e.prenom,prises.ip,prises.prise_id FROM prises LEFT JOIN trombino.eleves as e USING(piece_id) WHERE type='secondaire' ORDER BY prises.prise_id ASC, prises.ip ASC, e.nom ASC, e.prenom ASC");
		while(list($id,$nom,$promo,$prenom,$ip,$prise) = $DB_admin->next_row()) {
?>
			<element id="<? echo str_replace(".","x",$ip) ;?>">
				<colonne id="prise"><? echo "$prise" ?></colonne>
				<colonne id="ip"><? echo $ip ;?></colonne>
				<colonne id="degage"><bouton titre="Dégage!" id="suppr_<? echo str_replace(".","x",$ip) ;?>_<? echo $id?>" onClick="return window.confirm('Voulez vous vraiment supprimez cette ip ?')"/></colonne>
				<colonne id="eleve"><? echo "$nom $prenom ($promo)" ?></colonne>
			</element>
<?
		}
?>
	</liste>

</page>

<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
