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
<page id="admin_valid_ip" titre="Frankiz : Attribuer une nouvelle adresse IP">

<?
// On regarde quel cas c'est ...
// On envoie chié le mec pour son changement d'ip et on le supprime de la base
// On accepte le changement et on l'inbscrit dans la base

$DB_valid->query("LOCK TABLE valid_ip WRITE");
$DB_admin->query("LOCK TABLE prises WRITE");
$DB_valid->query("SET AUTOCOMMIT=0");

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;
	
	// On refuse la demande d'ip supplémentaire
	//==========================
	if ($temp[0] == "vtff") {
		$DB_valid->query("DELETE FROM valid_ip WHERE eleve_id='{$temp[1]}'");
		
		$bla = "refus_".$temp[1] ;
		$contenu = "Bonjour, <br><br>".
					"Nous sommes désolés de pas pouvoir d'attribuer une adresse IP supplémentaire pour la raison suivante :<br>".
					$_POST[$bla]."<br>".
					"Il y a certainement une autre façon de procéder pour atteindre ton but.<br>".
					"<br>" .
					"Très Cordialement<br>" .
					"Le BR<br>"  ;
	
		couriel($temp[1],"[Frankiz] Ta demande a été refusée ",$contenu);
		echo "<commentaire>Envoie d'un mail. On prévient l'utilisateur que sa demande n'a pas été acceptée.</commentaire>" ;
	}
	
	// On accepte la demande d'ip supplémentaire
	//===========================
	if ($temp[0] == "ok") {
		$temp2 = "ajout_ip_".$temp[1] ;
		$temp3 = "raison_".$temp[1] ;
		$DB_trombino->query("SELECT piece_id FROM eleves WHERE eleve_id='{$temp[1]}'") ;
		list($kzert) = $DB_trombino->next_row();
		
		$DB_admin->query("SELECT 0 FROM prises WHERE ip='{$_POST[$temp2]}'");
		
		// S'il n'y a aucune entrée avec cette ip dans la base
		if ($DB_admin->num_rows()==0){
			$DB_valid->query("DELETE FROM valid_ip WHERE eleve_id='{$temp[1]}'");
			$DB_admin->query("INSERT prises SET prise_id='',piece_id='$kzert',ip='{$_POST[$temp2]}',type='secondaire'");
			
			$contenu = "Bonjour, <br><br>".
						"Nous t'avons attribué l'adresse IP suivante :<br>".
						$_POST[$temp2]."<br>".
						"<br>" .
						"Très Cordialement<br>" .
						"Le BR<br>";
		
			couriel($temp[1],"[Frankiz] Ta demande a été acceptée",$contenu);
			echo "<commentaire>Envoie d'un mail. On prévient l'utilisateur que sa demande a été acceptée (nouvelle adresse IP : ".$_POST[$temp2].")</commentaire>" ;
			
		// S'il y  a deja une entrée comme celle demandé dans la base !
		} else {
			echo "<warning>IMPOSSIBLE D'ATTRIBUER CETTE IP. Une autre personne la posséde déjà.</warning>" ;
		}
		
	}
	
	// On vire une ip qu'on avait validé
	//===========================
	if ($temp[0] == "suppr") {
		$temp2 = str_replace("x",".",$temp[1]) ; // euh c'est pas bo je suis d'accord mais bon c'est pour que ca marche sans trop de trick
		$DB_admin->query("DELETE FROM prises WHERE type='secondaire' AND ip='$temp2' AND prise_id=''");
		
		$contenu = "Bonjour, <br><br>".
					"Nous avons supprimé l'adresse IP suivante qui t'était actuellement attribuée :<br><br>".
					$temp2."<br><br>".
					"Très Cordialement<br>" .
					"Le BR<br>"  ;
	
		couriel($temp[2],"[Frankiz] Suppression d'une adresse IP",$contenu);
		echo "<commentaire>Envoie d'un mail. On prévient l'utilisateur que son adresse IP $temp2 vient d'être supprimée.</commentaire>" ;

	}
}
$DB_valid->query("UNLOCK TABLES");
$DB_admin->query("UNLOCK TABLES");
?>

<h2>Liste des personnes demandant</h2>
	<liste id="liste" selectionnable="non">
		<entete id="eleve" titre="Élève"/>
		<entete id="raison" titre="Raison"/>
		<entete id="ip" titre="IP"/>
<?
		$DB_valid->query("SELECT v.raison,e.nom,e.prenom,e.piece_id,e.eleve_id FROM valid_ip as v LEFT JOIN trombino.eleves as e USING(eleve_id)");
		while(list($raison,$nom,$prenom,$piece,$eleve_id) = $DB_valid->next_row()) {
?>
			<element id="<? echo $eleve_id ;?>">
				<colonne id="eleve"><? echo "$nom $prenom" ?></colonne>
				<colonne id="raison">
					<p><textsimple titre="" id="raison_<? echo $eleve_id ;?>" valeur="Raison = <? echo $raison ;?>"/></p>
					<p><textsimple titre="" id="raison2_<? echo $eleve_id ;?>" valeur="Raison si refus :"/></p>
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
?>					
					<p>
						<champ titre="" id="ajout_ip_<? echo $eleve_id ;?>" valeur="129.104." /> 
						<bouton titre="Ok" id="ok_<? echo $eleve_id ;?>" />
						<bouton titre="Vtff" id="vtff_<? echo $eleve_id ;?>" onClick="return window.confirm('Voulez vous vraiment ne pas valider cette ip ?')"/>
					</p>
				</colonne>
			</element>
<?
		}
?>
	</liste>
	
	
	
	<h2>Liste des personnes ayant eu leurs ips supplémentaires</h2>
	<liste id="liste" selectionnable="non">
		<entete id="eleve" titre="Élève"/>
		<entete id="ip" titre="IP"/>
<?


		$DB_admin->query("SELECT e.eleve_id,e.nom,e.promo,e.prenom,prises.ip FROM prises LEFT JOIN trombino.eleves as e USING(piece_id) WHERE type='secondaire' ORDER BY e.nom ASC, e.prenom ASC");
		while(list($id,$nom,$promo,$prenom,$ip) = $DB_admin->next_row()) {
?>
			<element id="<? echo str_replace(".","x",$ip) ;?>">
				<colonne id="eleve"><? echo "$nom $prenom ($promo)" ?></colonne>
				<colonne id="ip"><? echo $ip ;?><bouton titre="Dégage!" id="suppr_<? echo str_replace(".","x",$ip) ;?>_<? echo $id?>" onClick="return window.confirm('Voulez vous vraiment supprimez cette ip ?')"/></colonne>
			</element>
<?
		}
?>
	</liste>

</page>

<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
