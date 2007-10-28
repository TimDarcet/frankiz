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
if(!verifie_permission('admin')&&!verifie_permission('web'))
	acces_interdit();

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="admin_valid_perso" titre="Frankiz : Valider les pages perso">

<?php
// On regarde quel cas c'est ...
// On envoie chié le mec pour son changement d'ip et on le supprime de la base
// On accepte le changement et on l'inscrit dans la base
$DB_valid->query("LOCK TABLE valid_pageperso WRITE");
$DB_valid->query("SET AUTOCOMMIT=0");

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;
	
	// On refuse la demande d'ip supplémentaire
	//==========================
	if ($temp[0] == "vtff") {
		$DB_valid->query("SELECT 0 FROM valid_pageperso WHERE eleve_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {
		
			$DB_trombino->query("SELECT nom,prenom,promo FROM eleves WHERE eleve_id='{$temp[1]}'");
			list($nom,$prenom,$promo) = $DB_trombino->next_row();
			//Log l'action de l'admin
			log_admin($_SESSION['uid']," refusé la page perso de $nom $prenom ($promo) ") ;

			$DB_valid->query("DELETE FROM valid_pageperso WHERE eleve_id='{$temp[1]}'");
			
			$bla = "refus_".$temp[1] ;
			$contenu = "<b>Bonjour,</b> <br><br>".
				"Nous sommes désolé mais le BR n'a pas approuvé ta demande pour la raison suivante <br>".
				$_POST[$bla]."<br>".
				"<br>" .
				"Très Cordialement<br>" .
				"Le Webmestre de Frankiz<br>"  ;
		
			couriel($temp[1],"[Frankiz] La demande pour ton site a été refusée ",$contenu,WEBMESTRE_ID);
			echo "<warning>Envoie d'un mail <br/>Le prévient que sa demande n'est pas acceptée</warning>" ;
		} else {
	?>
			<warning>Requête deja traitée par un autre administrateur</warning>
	<?php
		}
	}
	// On accepte la demande d'ip supplémentaire
	//===========================
	if ($temp[0] == "ok") {
		$DB_valid->query("SELECT 0 FROM valid_pageperso WHERE eleve_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {
		
			$DB_trombino->query("SELECT nom,prenom,promo FROM eleves WHERE eleve_id='{$temp[1]}'");
			list($nom,$prenom,$promo) = $DB_trombino->next_row();
			//Log l'action de l'admin
			log_admin($_SESSION['uid']," accepté la page perso de $nom $prenom ($promo) ") ;
		
			$DB_web->query("INSERT INTO sites_eleves SET eleve_id='{$temp[1]}'");
			$DB_trombino->query("SELECT login,promo FROM eleves WHERE eleve_id='{$temp[1]}'");
			list($login,$promo) = $DB_trombino->next_row();
			symlink (BASE_PAGESPERSOS."$login-$promo",BASE_PAGESPERSOS_EXT."$login-$promo");
			
			$contenu = "<b>Bonjour,</b> <br><br>".
					"Ton site perso apparaitra desormais sur le site élève<br>".
					"<br>" .
					"Très Cordialement<br>" .
					"Le Webmestre de Frankiz<br>"  ;
			
			couriel($temp[1],"[Frankiz] La demande pour ton site perso a été acceptée",$contenu,WEBMESTRE_ID);
				echo "<commentaire>Envoie d'un mail<br/>Le prévient que sa demande à été acceptée</commentaire>" ;
				
			$DB_valid->query("DELETE FROM valid_pageperso WHERE eleve_id='{$temp[1]}'");
		} else {
	?>
			<warning>Requête deja traitée par un autre administrateur</warning>
	<?php
		}
	}
}
$DB_valid->query("COMMIT");
$DB_valid->query("UNLOCK TABLES");
?>
<note>Si tu refuses une demande, met un commentaire pour que la personne comprenne pourquoi le BR ne veux pas valider sa demande</note>
<h2>Liste des personnes demandant une entrée sur la page des sites élèves</h2>
	<liste id="liste" selectionnable="non" action="admin/valid_pageperso.php">
		<entete id="eleve" titre="Élève"/>
		<entete id="url" titre="Url"/>
<?php
		$DB_valid->query("SELECT e.eleve_id,e.nom,e.prenom,e.promo,e.login FROM valid_pageperso as v LEFT JOIN trombino.eleves as e USING(eleve_id)");
		while(list($id,$nom,$prenom,$promo,$login) = $DB_valid->next_row()) {
?>
			<element id="<?php echo $id ;?>">
				<colonne id="eleve"><?php echo "$nom $prenom ($promo)" ?></colonne>
				<colonne id="url">
					<lien id="<?php echo $id; ?>" titre="Site" url="<?php echo URL_PAGEPERSO."$login-$promo"?>"/><br/>
					<zonetext titre="Raison du Refus si refus" id="refus_<?php echo $id ;?>" valeur=""/>
					<bouton titre="Ok" id="ok_<?php echo $id ;?>" />
					<bouton titre="Vtff" id="vtff_<?php echo $id ;?>" onClick="return window.confirm('Voulez vous vraiment ne pas valider cette page ?')"/>

				</colonne>
			</element>
<?php
		}
?>
	</liste>

</page>

<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
