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
	Gestion de la liste des webmestres et des prez des binets.

	$Log$
	Revision 1.2  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site

	Revision 1.1  2004/10/21 20:37:59  kikx
	C'est moche mais c'est achement pratique
*/

// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");

$message = "";

//============================
// Modification d'une entrée de prez
//=============================
if(isset($_POST['maj_prez'])) {
	foreach($_POST['maj_prez'] as $id => $val) {
		$binet_id = $_POST['binet_id'][$id] ;
		$eleve_login = $_POST['prez'][$id] ;
		$ancien_eleve_login = $_POST['ancien_prez'][$id] ;
				
	// on supprime les droit de l'ancien prez (si il existe bien sur)
		if ($ancien_eleve_login!="") {
			$DB_web->query("SELECT perms,e.eleve_id FROM compte_frankiz LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE login='$ancien_eleve_login'" );
			while(list($perms,$eleve_id) = $DB_web->next_row()) {
				$perms = str_replace("prez_".$binet_id.",","",$perms) ;
				$DB_web->query("UPDATE compte_frankiz SET perms='$perms' WHERE eleve_id='$eleve_id'");
				$message .= "<commentaire>$ancien_eleve_login n'a plus ses droit de prez de ce binet</commentaire>\n";
			}
		}
		
	// on donne les droits au nouveau prez
		if ($eleve_login!="") {
			$DB_web->query("SELECT perms,e.eleve_id FROM compte_frankiz LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE login='$eleve_login' " );
			if ($DB_web->num_rows()==0) 
				$message .= "<warning>Ce login n'existe pas ou ne s'est jamais connecté a Frankiz</warning>" ;
			while(list($perms,$eleve_id) = $DB_web->next_row()) {
				$perms = $perms."prez_".$binet_id."," ;
				$DB_web->query("UPDATE compte_frankiz SET perms='$perms' WHERE eleve_id='$eleve_id'");
				$message .= "<commentaire>$eleve_login a reçu les droits de président du binet</commentaire>\n";
			}
		}
	}
}

//============================
// Modification d'une entrée de webmestre
//=============================
if(isset($_POST['maj_web'])) {
	foreach($_POST['maj_web'] as $id => $val) {
		$binet_id = $_POST['binet_id'][$id] ;
		$eleve_login = $_POST['web'][$id] ;
		$ancien_eleve_login = $_POST['ancien_web'][$id] ;
				
	// on supprime les droit de l'ancien web (si il existe bien sur)
		if ($ancien_eleve_login!="") {
			$DB_web->query("SELECT perms,e.eleve_id FROM compte_frankiz LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE login='$ancien_eleve_login'" );
			while(list($perms,$eleve_id) = $DB_web->next_row()) {
				$perms = str_replace("webmestre_".$binet_id.",","",$perms) ;
				$DB_web->query("UPDATE compte_frankiz SET perms='$perms' WHERE eleve_id='$eleve_id'");
				$message .= "<commentaire>$ancien_eleve_login n'a plus ses droit de webmestre de ce binet</commentaire>\n";
			}
		}
		
	// on donne les droits au nouveau prez
		if ($eleve_login!="") {
			$DB_web->query("SELECT perms,e.eleve_id FROM compte_frankiz LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE login='$eleve_login' " );
			if ($DB_web->num_rows()==0) 
				$message .= "<warning>Ce login n'existe pas ou ne s'est jamais connecté a Frankiz</warning>" ;
			while(list($perms,$eleve_id) = $DB_web->next_row()) {
				$perms = $perms."webmestre_".$binet_id."," ;
				$DB_web->query("UPDATE compte_frankiz SET perms='$perms' WHERE eleve_id='$eleve_id'");
				$message .= "<commentaire>$eleve_login a reçu les droits de webmestre du binet</commentaire>\n";
			}
		}
	}
}

// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="admin_binets_web_prez" titre="Frankiz : liste des prez et des webmestres">
	<h1>Liste des prez et de webmestres</h1>
<?
echo $message ;
?>
	<liste id="liste_prez" selectionnable="non" action="admin/binet_web_prez.php">
		<entete id="binet" titre="Binet"/>
		<entete id="login" titre="Login"/>
		<entete id="nom" titre="Nom"/>
<?php
		$memoire =-1 ;
		$count = 0 ;

		$DB_trombino->query("SELECT b.nom,b.binet_id,e.login,e.nom,e.prenom,e.promo FROM binets as b LEFT JOIN frankiz2_tmp.compte_frankiz as c ON c.perms LIKE CONCAT(CONCAT('%prez_',b.binet_id),',%') LEFT JOIN eleves as e USING(eleve_id) ORDER BY b.nom ASC ,e.promo ASC");
		while(list($nom_binet,$binet_id,$login,$nom,$prenom,$promo) = $DB_trombino->next_row()) {
		//Pour le prez
			if ($memoire==$binet_id) $nom_binet = "######## IDEM ########" ;
			$memoire = $binet_id ;
			if ($promo!="") $promo ="(X$promo)" ;
			echo "\t\t<element id=\"$binet_id\">\n";
			echo "\t\t\t<colonne id=\"binet\">$nom_binet</colonne>\n";
			echo "\t\t\t<colonne id=\"login\">" ;
				echo "<hidden id=\"ancien_prez[$count]\" valeur=\"$login\"/>" ;
				echo "<hidden id=\"binet_id[$count]\" valeur=\"$binet_id\"/>" ;
				echo "Prez : <champ id=\"prez[$count]\" valeur=\"$login\"/>" ;
				echo "<bouton titre=\"MàJ\" id=\"maj_prez[$count]\"/>";
			echo"</colonne>\n";
			echo "\t\t\t<colonne id=\"nom\">$prenom $nom $promo</colonne>\n";
			echo "\t\t</element>\n";
			$count ++ ;
		}
?>
	</liste>
	<liste id="liste_web" selectionnable="non" action="admin/binet_web_prez.php">
		<entete id="binet" titre="Binet"/>
		<entete id="login" titre="Login"/>
		<entete id="nom" titre="Nom"/>
<?php
		$memoire =-1 ;
		$count = 0 ;

		$DB_trombino->query("SELECT b.nom,b.binet_id,e.login,e.nom,e.prenom,e.promo FROM binets as b LEFT JOIN frankiz2_tmp.compte_frankiz as c ON c.perms LIKE CONCAT(CONCAT('%webmestre_',b.binet_id),',%') LEFT JOIN eleves as e USING(eleve_id) ORDER BY b.nom ASC ,e.promo ASC");
		while(list($nom_binet,$binet_id,$login,$nom,$prenom,$promo) = $DB_trombino->next_row()) {
		//Pour le prez
			if ($memoire==$binet_id) $nom_binet = "######## IDEM ########" ;
			$memoire = $binet_id ;
			if ($promo!="") $promo ="(X$promo)" ;
			echo "\t\t<element id=\"$binet_id\">\n";
			echo "\t\t\t<colonne id=\"binet\">$nom_binet</colonne>\n";
			echo "\t\t\t<colonne id=\"login\">" ;
				echo "<hidden id=\"ancien_web[$count]\" valeur=\"$login\"/>" ;
				echo "<hidden id=\"binet_id[$count]\" valeur=\"$binet_id\"/>" ;
				echo "Webmestre : <champ id=\"web[$count]\" valeur=\"$login\"/>" ;
				echo "<bouton titre=\"MàJ\" id=\"maj_web[$count]\"/>";
			echo"</colonne>\n";
			echo "\t\t\t<colonne id=\"nom\">$prenom $nom $promo</colonne>\n";
			echo "\t\t</element>\n";
			$count ++ ;
		}
?>
	</liste>
</page>
<?php


require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
