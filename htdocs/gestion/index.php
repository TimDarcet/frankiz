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

	$Log$
	Revision 1.6  2004/10/25 14:05:09  kikx
	Correction d'un bug sur la page

	Revision 1.5  2004/10/25 10:35:50  kikx
	Page de validation (ou pas) des modif de trombi
	
	Revision 1.4  2004/10/21 22:52:19  kikx
	C'est plus bo
	
	Revision 1.3  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.2  2004/10/21 20:37:59  kikx
	C'est moche mais c'est achement pratique
	
	Revision 1.1  2004/10/20 21:45:01  kikx
	Pour que ca soit propre
	
	Revision 1.25  2004/10/19 19:08:17  kikx
	Permet a l'administrateur de valider les modification des binets
	
	Revision 1.24  2004/10/19 18:16:24  kikx
	hum
	
	Revision 1.23  2004/10/18 21:16:33  pico
	Partie admin FAQ
	chgt table sql de la faq
	
	Revision 1.22  2004/10/18 20:29:44  kikx
	Enorme modification pour la fusion des bases des binets (Merci Schmurtz)
	
	Revision 1.21  2004/10/17 22:02:45  pico
	Ajout lien admin xshare
	
	Revision 1.20  2004/10/17 20:27:35  kikx
	Permet juste au prez des binets de consulter les perosnne adherant aux binet ainsi que leur commentaires
	
	Revision 1.19  2004/10/17 17:16:28  kikx
	prtit oubli de definitions d'une variable
	
	Revision 1.18  2004/10/17 17:13:20  kikx
	Pour rendre la page d'administration plus belle
	n'affiche le truc d'admin que si on est admin
	meme chsoe pour le prez et le webmestre
	
	Revision 1.17  2004/10/15 22:03:07  kikx
	Mise en place d'une page pour la gestion des sites des binets
	
	Revision 1.16  2004/10/13 22:14:32  pico
	Premier jet de page pour affecter une date de publication aux qdj validées
	
	Revision 1.14  2004/10/06 14:12:27  kikx
	Page de mail promo quasiment en place ...
	envoie en HTML ...
	Page pas tout a fait fonctionnel pour l'instant
	
	Revision 1.13  2004/10/04 21:19:11  kikx
	Rajour d'une page pour les mails promos
	
	Revision 1.12  2004/09/20 22:19:27  kikx
	test
	
	Revision 1.11  2004/09/17 16:14:43  kikx
	Pffffff ...
	Je sais plus trop ce que j'ai fait donc allez voir le code parce que la ca me fait chié de refléchir
	
	Revision 1.10  2004/09/16 15:22:51  kikx
	Rajout de la ligne qui va bien pour les parametres (pour ne pas perdre de page d'administration ca serait balot)
	
	Revision 1.9  2004/09/15 23:20:18  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.8  2004/09/15 21:42:27  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
	
// En-tetes
require_once "../include/global.inc.php";

demande_authentification(AUTH_FORT);
if(empty($_SESSION['user']->perms))
	rediriger_vers("/");

// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";

$permissions_user = ses_permissions() ;
?>
<page id="admin" titre="Frankiz : administration">
	<? if (verifie_permission('admin')){?>
	<h2>Administration frankiz</h2>
	<h3>Gestion de l'utilisateur</h3>
		<lien titre="Modifier un utilisateur" url="<?php echo BASE_URL?>/trombino"/>
		<lien titre="Valider son changement de photo trombino" url="<?php echo BASE_URL?>/admin/valid_trombi.php"/>
		<lien titre="Gestion des webmestres et des prez" url="<?php echo BASE_URL?>/admin/binet_web_prez.php"/>
	<h3>Validations Variées</h3>
		<lien titre="Gerer les demandes d'ajout d'ips" url="<?php echo BASE_URL?>/admin/valid_ip.php"/>
		<lien titre="Valider les annonces" url="<?php echo BASE_URL?>/admin/valid_annonces.php"/>
		<lien titre="Valider les activités" url="<?php echo BASE_URL?>/admin/valid_affiches.php"/>
		<lien titre="Valider les mails promos" url="<?php echo BASE_URL?>/admin/valid_mailpromo.php"/>
		<lien titre="Valider les qdj" url="<?php echo BASE_URL?>/admin/valid_qdj.php"/>
	<h3>Administration des données de Frankiz</h3>
		<lien titre="Liste des Binets" url="<?php echo BASE_URL?>/admin/binets.php"/>
		<lien titre="Liste des Binets qui ont un site WEB" url="<?php echo BASE_URL?>/admin/binets_web.php"/>
		<lien titre="Liste des sections" url="<?php echo BASE_URL?>/admin/sections.php"/>
		<lien titre="Changer les variables globales" url="<?php echo BASE_URL?>/admin/parametre.php"/>
		<lien titre="Planifier les qdj" url="<?php echo BASE_URL?>/admin/planif_qdj.php"/>
		<lien titre="Gestion xshare" url="<?php echo BASE_URL?>/admin/xshare.php"/>
		<lien titre="Gestion FAQ" url="<?php echo BASE_URL?>/admin/faq.php"/>
		<lien titre="Valider les changements des Binets" url="<?php echo BASE_URL?>/admin/valid_binets.php"/>
	<h3>Administration du réseau élève</h3>
		<lien titre="Liste des IPs" url="<?php echo BASE_URL?>/admin/ip.php"/>


	<?
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

			echo "<lien titre=\"Gerer la page du binet : $nom\" url=\"". BASE_URL."/gestion/binet.php?binet=".$binet."\"/>" ;
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

				
			echo "<lien titre=\"Gerer les membres du binet : $nom\" url=\"". BASE_URL."/gestion/binet.php?binet=".$binet."\"/>" ;
		}
	}

	?>

</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php"; ?>
