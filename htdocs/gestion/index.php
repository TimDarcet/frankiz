<?php
/*
	Copyright (C) 2004 Binet R�seau
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
	Page principale d'administration�: affiche la liste des pages d'administration auxquelles
	l'utilisateur courant � acc�s.

	$Log$
	Revision 1.17  2004/11/27 15:14:46  pico
	Gestion desdrits dans l'index des pages admin

	Revision 1.16  2004/11/27 14:56:15  pico
	Debut de mise en place de droits sp�ciaux (qdj + affiches)
	+ g�n�ration de la page d'admin qui va bien
	
	Revision 1.15  2004/11/27 14:30:16  pico
	r�organisation page d'admin
	
	Revision 1.14  2004/11/27 14:16:19  pico
	Ajout du lien de modif dans la page d'admin, r�organisation de la page
	
	Revision 1.13  2004/11/27 12:58:23  pico
	jout du lien vers la planification des activit�s
	
	Revision 1.12  2004/11/25 02:03:29  kikx
	Bug d'administration des binets
	
	Revision 1.11  2004/11/22 23:07:28  kikx
	Rajout de lines vers les pages perso
	
	Revision 1.10  2004/11/17 13:32:18  kikx
	Mise en place du lien pour l'admin
	
	Revision 1.9  2004/11/12 23:32:14  schmurtz
	oublie dans le deplacement du trombino
	
	Revision 1.8  2004/11/11 17:57:52  kikx
	Permet de savoir juste sur la page prinipale d'administration ce qui reste a valider ou pas ... car sinon on peut faire trainer des truc super longtemps
	
	Revision 1.7  2004/11/11 17:39:54  kikx
	Centralisation des pages des binets
	
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
	Premier jet de page pour affecter une date de publication aux qdj valid�es
	
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
	Je sais plus trop ce que j'ai fait donc allez voir le code parce que la ca me fait chi� de refl�chir
	
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

if (count($_SESSION['user']->perms)<=1)
	rediriger_vers("/");

// G�n�ration de la page
require_once BASE_LOCAL."/include/page_header.inc.php";

$permissions_user = $_SESSION['user']->perms ;

?>
<page id="admin" titre="Frankiz : administration">
	<? 
	if (verifie_permission('admin')||verifie_permission('affiches')||verifie_permission('qdjmaster')||verifie_permission('xshare')||verifie_permission('faq')){
	?>
		<h2>Administration frankiz</h2>
	<? 
	}
	if (verifie_permission('admin')){
	?>
		<h3>Gestion de l'utilisateur</h3>
			<lien titre="Modifier un utilisateur" url="trombino.php"/>
	<?
	}
	if (verifie_permission('admin')||verifie_permission('affiches')||verifie_permission('qdjmaster')||verifie_permission('xshare')||verifie_permission('faq')){
	?>
	<h3>Validations Vari�es</h3>
	<?
	}
	if (verifie_permission('admin')){
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
		<lien titre="Valider les changements de photo trombino (<?=$nb?>)" url="<?php echo BASE_URL?>/admin/valid_trombi.php"/>
		<?
		$DB_valid->query("SELECT eleve_id FROM valid_annonces") ;
		$nb = $DB_valid->num_rows() ;
		?>
		<lien titre="Valider les annonces (<?=$nb?>)" url="<?php echo BASE_URL?>/admin/valid_annonces.php"/>
	<?
	}
	if (verifie_permission('admin')||verifie_permission('affiches')){
		$DB_valid->query("SELECT eleve_id FROM valid_affiches") ;
		$nb = $DB_valid->num_rows() ;
		?>
		<lien titre="Valider les activit�s (<?=$nb?>)" url="<?php echo BASE_URL?>/admin/valid_affiches.php"/>
	<?
	}
	if (verifie_permission('admin')){
		$DB_valid->query("SELECT eleve_id FROM valid_sondages") ;
		$nb = $DB_valid->num_rows() ;
		?>
		<lien titre="Valider les sondages (<?=$nb?>)" url="<?php echo BASE_URL?>/admin/valid_sondages.php"/>
		<?
		$DB_valid->query("SELECT eleve_id FROM valid_mailpromo") ;
		$nb = $DB_valid->num_rows() ;
		?>
		<lien titre="Valider les mails promos (<?=$nb?>)" url="<?php echo BASE_URL?>/admin/valid_mailpromo.php"/>
	<?
	}
	if (verifie_permission('admin')||verifie_permission('qdjmaster')){
		$DB_valid->query("SELECT eleve_id FROM valid_qdj") ;
		$nb = $DB_valid->num_rows() ;
		?>
		<lien titre="Valider les qdj (<?=$nb?>)" url="<?php echo BASE_URL?>/admin/valid_qdj.php"/>
	<?
	}
	if (verifie_permission('admin')){
		$DB_valid->query("SELECT id FROM valid_pageperso") ;
		$nb = $DB_valid->num_rows() ;
		?>
		<lien titre="Valider les page persos (<?=$nb?>)" url="<?php echo BASE_URL?>/admin/valid_pageperso.php"/>
		<?
		$DB_valid->query("SELECT binet_id FROM valid_binet") ;
		$nb = $DB_valid->num_rows() ;
		?>
		<lien titre="Valider les changements des Binets (<?=$nb?>)" url="<?php echo BASE_URL?>/admin/valid_binets.php"/>
	<?
	}
	
	
	if (verifie_permission('admin')||verifie_permission('affiches')||verifie_permission('qdjmaster')){
	?>
	<h3>Administration des donn�es valid�es</h3>
	<?
	}
	if (verifie_permission('admin')||verifie_permission('qdjmaster')){
	?>
		<lien titre="Planifier les qdj" url="<?php echo BASE_URL?>/admin/planif_qdj.php"/>
	<?
	}
	if (verifie_permission('admin')||verifie_permission('affiches')){
	?>
		<lien titre="Planifier les activit�s" url="<?php echo BASE_URL?>/admin/planif_affiches.php"/>
	<?
	}
	if (verifie_permission('admin')){
	?>
		<lien titre="Modifier les annonces" url="<?php echo BASE_URL?>/admin/modif_annonces.php"/>
	<?
	}
	
	
	if (verifie_permission('admin')||verifie_permission('affiches')||verifie_permission('qdjmaster')||verifie_permission('xshare')||verifie_permission('faq')){
	?>
	<h3>Administration des donn�es de Frankiz</h3>
	<?
	}
	if (verifie_permission('admin')){
	?>
		<lien titre="Changer les variables globales" url="<?php echo BASE_URL?>/admin/parametre.php"/>
		<lien titre="Liste des Binets" url="<?php echo BASE_URL?>/admin/binets_liste.php"/>
		<lien titre="Liste des sections" url="<?php echo BASE_URL?>/admin/sections.php"/>
	<?
	}
	if (verifie_permission('admin')||verifie_permission('xshare')){
	?>
		<lien titre="Gestion xshare" url="<?php echo BASE_URL?>/admin/xshare.php"/>
	<?
	}
	if (verifie_permission('admin')||verifie_permission('faq')){
	?>
		<lien titre="Gestion FAQ" url="<?php echo BASE_URL?>/admin/faq.php"/>
	<?
	}
	if (verifie_permission('admin')){
	?>
	<h3>Administration du r�seau �l�ve</h3>
		<?
		$DB_valid->query("SELECT eleve_id FROM valid_ip") ;
		$nb = $DB_valid->num_rows() ;
		?>
		<lien titre="Gerer les demandes d'ajout d'ips (<?=$nb?>)" url="<?php echo BASE_URL?>/admin/valid_ip.php"/>
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
