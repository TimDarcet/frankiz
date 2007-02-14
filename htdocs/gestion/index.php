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
	Page principale d'administration : affiche la liste des pages d'administration auxquelles
	l'utilisateur courant à accès.

	$Id$

*/
	
// En-tetes
require_once "../include/global.inc.php";

demande_authentification(AUTH_FORT);

if (count($_SESSION['user']->perms)<=1)
	acces_interdit();

// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";

$permissions_user = $_SESSION['user']->perms ;

?>
<page id="admin" titre="Frankiz : administration">
	<?php 
	if (verifie_permission('admin')||verifie_permission('web')||verifie_permission('affiches')||verifie_permission('qdjmaster')||verifie_permission('xshare')||verifie_permission('faq')){
	?>
		<h2>Administration frankiz</h2>
	<?php 
	}
	if (verifie_permission('admin')||verifie_permission('trombino')||verifie_permission('windows')){
	?>
		<h3>Gestion de l'utilisateur</h3>
			<lien titre="Rerchercher/modifier un utilisateur" url="trombino.php?toladmin"/><br/>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('trombino')){
	?>
			<lien titre="Ajouter un utilisateur" url="admin/user_rajout.php"/>
	<?php
	}
	if (verifie_permission('admin')){
	?>
		<h3>Gestion du site</h3>
			<lien titre="Log de la partie d'administration" url="admin/log_admin.php"/><br/>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('affiches')||verifie_permission('qdjmaster')||verifie_permission('web')||verifie_permission('trombino')||verifie_permission('kes')){
	?>
	<h3>Validations Variées</h3>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('trombino')){
		$nb =0 ;
		$rep = BASE_DATA."trombino/";
		$dir = opendir($rep); 

		while ($namefile = readdir($dir)) {
			$namefile = explode("_",$namefile) ;
			if ((count($namefile)>=2)&&($namefile[1]=="valider")) {
				$nb++ ;
			}
		}
		?>
		<lien titre="Valider les changements de photo trombino (<?php echo $nb; ?>)" url="admin/valid_trombi.php"/><br/>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('web')){
		$DB_valid->query("SELECT eleve_id FROM valid_annonces") ;
		$nb = $DB_valid->num_rows() ;
		?>
		<lien titre="Valider les annonces (<?php echo $nb; ?>)" url="admin/valid_annonces.php"/><br/>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('affiches')||verifie_permission('web')){
		$DB_valid->query("SELECT eleve_id FROM valid_affiches") ;
		$nb = $DB_valid->num_rows() ;
		?>
		<lien titre="Valider les activités (<?php echo $nb; ?>)" url="admin/valid_affiches.php"/><br/>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('web')){
		$DB_valid->query("SELECT eleve_id FROM valid_sondages") ;
		$nb = $DB_valid->num_rows() ;
		?>
		<lien titre="Valider les sondages (<?php echo $nb; ?>)" url="admin/valid_sondages.php"/><br/>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('qdjmaster')){
		$DB_valid->query("SELECT eleve_id FROM valid_qdj") ;
		$nb = $DB_valid->num_rows() ;
		?>
		<lien titre="Valider les qdj (<?php echo $nb; ?>)" url="admin/valid_qdj.php"/><br/>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('web')){
		$DB_valid->query("SELECT id FROM valid_pageperso") ;
		$nb = $DB_valid->num_rows() ;
		?>
		<lien titre="Valider les page persos (<?php echo $nb; ?>)" url="admin/valid_pageperso.php"/><br/>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('trombino')||verifie_permission('web')){
		$DB_valid->query("SELECT binet_id FROM valid_binet") ;
		$nb = $DB_valid->num_rows() ;
		?>
		<lien titre="Valider les changements des Binets (<?php echo $nb; ?>)" url="admin/valid_binets.php"/><br/>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('faq')||verifie_permission('web')){
		$DB_valid->query("SELECT 0 FROM valid_modiffaq") ;
		$nb = $DB_valid->num_rows() ;
		?>
		<lien titre="Valider les modifications des FAQ (<?php echo $nb; ?>)" url="admin/valid_faqmodif.php"/><br/>
	<?php
	}
	
	
	if (verifie_permission('admin')||verifie_permission('affiches')||verifie_permission('qdjmaster')||verifie_permission('web')){
	?>
	<h3>Administration des données validées</h3>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('qdjmaster')){
	?>
		<lien titre="Planifier les qdj" url="admin/planif_qdj.php"/><br/>
		<lien titre="Historique des qdj" url="admin/histo_qdj.php"/><br/>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('affiches')||verifie_permission('web')){
	?>
		<lien titre="Planifier les activités" url="admin/planif_affiches.php"/><br/>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('web')){
	?>
		<lien titre="Modifier les annonces" url="admin/modif_annonces.php"/><br/>
		<lien titre="Modifier les sondages" url="admin/modif_sondages.php"/><br/>
		<lien titre="Nettoyer les bases de données" url="admin/nettoyage.php"/><br/>
	<?php
	}
	
	
	if (verifie_permission('admin')||verifie_permission('web')||verifie_permission('xshare')||verifie_permission('faq')||verifie_permission('trombino')){
	?>
	<h3>Administration des données de Frankiz</h3>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('web')){
	?>
		<lien titre="Liste des droits accordés" url="admin/liste_droits.php"/><br/>
	<?php
	}
	if (verifie_permission('admin')){
	?>
		<lien titre="Changer les variables globales" url="admin/parametre.php"/><br/>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('trombino')||verifie_permission('web')){
	?>
		<lien titre="Liste des Binets" url="admin/binets_liste.php"/><br/>
		<lien titre="Liste des sections" url="admin/sections.php"/><br/>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('xshare')||verifie_permission('web')){
	?>
		<lien titre="Gestion xshare" url="admin/xshare.php"/><br/>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('faq')||verifie_permission('web')){
	?>
		<lien titre="Gestion FAQ" url="admin/faq.php"/><br/>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('web')){
	?>
		<lien titre="Gestion du Vocabulaire" url="admin/vocabulaire.php"/><br/>
		<lien titre="Gestion des liens utiles" url="admin/liens_utiles.php"/><br/>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('trombino')){
	?>
		<lien titre="Gestion des num utiles" url="admin/num_utiles.php"/><br/>
	<?php
	}
	if (verifie_permission('admin')||verifie_permission('windows')){
	?>
	<h3>Administration du réseau élève</h3>
	<?php
	}
	if (verifie_permission('admin')){
		$DB_valid->query("SELECT eleve_id FROM valid_ip") ;
		$nb = $DB_valid->num_rows() ;
		?>
		<lien titre="Gérer les demandes d'ajout d'ips (<?php echo $nb; ?>)" url="admin/valid_ip.php"/><br/>
		<lien titre="Surveiller la DNS" url="admin/dns.php"/><br/>
		<?php
	}
	if (verifie_permission('admin')||verifie_permission('windows')){
		?>
		<lien titre="Liste des IPs" url="admin/ip.php"/><br/>
		<lien titre="Arpwatch" url="admin/arpwatch.php"/><br/>
		<?php
		$DB_msdnaa->query("SELECT eleve_id FROM valid_licence") ;
		$nb = $DB_msdnaa->num_rows() ;
		?>
		<lien titre="Gérer les demandes de licences (<?php echo $nb; ?>)" url="admin/valid_licences.php"/><br/>
		<lien titre="Gérer les virus detectés sur le réseau " url="admin/nettoyer_virus.php"/><br/>
	<?php
	}
	
	//
	// Pour les webmestres des binets
	//======================================
	
	$counter = 0 ;
	for ($i = 0 ; $i<count($permissions_user) ; $i++){
		$temp = $permissions_user[$i]; 
		if (strpos($temp,"webmestre")!==false) { // il y a bien !== attention ...
			$binet = explode("_",$permissions_user[$i]) ;
			$binet = $binet[1] ;
			$counter ++ ;
			if ($counter == 1) 
				echo "<h2>Webmestre binet X</h2>" ;
			
			$DB_trombino->query("SELECT nom FROM binets WHERE binet_id=$binet");
			list($nom) = $DB_trombino->next_row() ;

			echo "<lien titre=\"Gérer la page du binet : $nom\" url=\"gestion/binet.php?binet=".$binet."\"/><br/>" ;
		}
	}

	
	//
	// Pour les prez des binets
	//======================================
	
	$counter = 0 ;
	for ($i = 0 ; $i<count($permissions_user) ; $i++){
		$temp = $permissions_user[$i]; 
		if (strpos($temp,"prez")!==false) { // il y a bien !== attention ...
			$binet = explode("_",$permissions_user[$i]) ;
			$binet = $binet[1] ;
			$counter ++ ;
			if ($counter == 1) 
				echo "<h2>Prez binet X</h2>" ;
			
			$DB_trombino->query("SELECT nom FROM binets WHERE binet_id=$binet");
			list($nom) = $DB_trombino->next_row() ;

				
			echo "<lien titre=\"Gérer les membres du binet : $nom\" url=\"gestion/binet.php?binet=".$binet."\"/><br/>" ;
		}
	}


	//
	// Pour le bob
	//======================================
	if (verifie_permission('admin')||verifie_permission('bob')){
		echo "<h2>Gestion du BôB</h2>" ;
		echo "<lien titre=\"Gérer l'ouverture du BôB\" url=\"gestion/etat_bob.php\"/><br/>" ;
		echo "<lien titre=\"Gérer les tours kawa\" url=\"gestion/etat_bob.php\"/><br/>" ;
	}
	
	//
	// Pour la Kès
	//======================================
	if (verifie_permission('admin')||verifie_permission('kes')){
		echo "<h2>Gestion de la Kès</h2>" ;
		echo "<lien titre=\"Gérer l'ouverture de la Kès\" url=\"gestion/etat_kes.php\"/><br/>" ;
		echo "<lien titre=\"Modifier le lien vers l'IK électronique\" url=\"gestion/etat_kes.php\" /><br />";
		$DB_valid->query("SELECT eleve_id FROM valid_mailpromo") ;
		$nb = $DB_valid->num_rows() ;
		echo "<lien titre=\"Valider les mails promos ($nb)\" url=\"admin/valid_mailpromo.php\"/><br/>";
	}

	//
	// Pour le module 'Post-it'
	//======================================
	if (verifie_permission('admin')||verifie_permission('web')||verifie_permission('postit')) {
?>
		<h2>Gestion du module Post-it</h2>
		<lien titre="Gérer le module Post-it" url="gestion/module_postit.php"/><br/>
<?php
	}
?>

</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php"; ?>
