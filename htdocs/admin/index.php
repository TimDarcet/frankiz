<?php
/*
	Page principale d'administration : affiche la liste des pages d'administration auxquelles
	l'utilisateur courant à accès.

	$Log$
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
?>
<page id="admin" titre="Frankiz : administration">
	<? if (verifie_permission('admin')){?>
	<h2>Administration frankiz</h2>
		<lien titre="Modifier un utilisateur" url="<?php echo BASE_URL?>/trombino"/>
		<lien titre="Liste des Binets" url="<?php echo BASE_URL?>/admin/binets.php"/>
		<lien titre="Liste des Binets qui ont un site WEB" url="<?php echo BASE_URL?>/admin/binets_web.php"/>
		<lien titre="Liste des sections" url="<?php echo BASE_URL?>/admin/sections.php"/>
		<lien titre="Liste des IPs" url="<?php echo BASE_URL?>/admin/ip.php"/>
		<lien titre="Gerer les demandes d'ajout d'ips" url="<?php echo BASE_URL?>/admin/valid_ip.php"/>
		<lien titre="Changer les variables globales" url="<?php echo BASE_URL?>/admin/parametre.php"/>
		<lien titre="Valider les annonces" url="<?php echo BASE_URL?>/admin/valid_annonces.php"/>
		<lien titre="Valider les activités" url="<?php echo BASE_URL?>/admin/valid_affiches.php"/>
		<lien titre="Valider les mails promos" url="<?php echo BASE_URL?>/admin/valid_mailpromo.php"/>
		<lien titre="Valider les qdj" url="<?php echo BASE_URL?>/admin/valid_qdj.php"/>
		<lien titre="Planifier les qdj" url="<?php echo BASE_URL?>/admin/planif_qdj.php"/>
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
				echo "<h2>Prez binet X</h2>" ;
			echo "<lien titre=\"Gerer la page du binet\" url=\"". BASE_URL."/gestion/binet.php?binet=".$binet."\"/>" ;
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
			echo "<lien titre=\"Gerer les membres du binet\" url=\"". BASE_URL."/gestion/binet.php?binet=".$binet."\"/>" ;
		}
	}

	?>

</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php"; ?>
