<?php
/*
	Page principale d'administration : affiche la liste des pages d'administration auxquelles
	l'utilisateur courant à accès.

	$Log$
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
	<h2>Administration frankiz</h2>
		<lien titre="Modifier un utilisateur" url="<?php echo BASE_URL?>/trombino"/>
		<lien titre="Liste des Binets" url="<?php echo BASE_URL?>/admin/binets.php"/>
		<lien titre="Liste des sections" url="<?php echo BASE_URL?>/admin/sections.php"/>
		<lien titre="Liste des IPs" url="<?php echo BASE_URL?>/admin/ip.php"/>
		<lien titre="Gerer les demandes d'ajout d'ips" url="<?php echo BASE_URL?>/admin/valid_ip.php"/>
		<lien titre="Changer les variables globales" url="<?php echo BASE_URL?>/admin/parametre.php"/>
		<lien titre="Valider les annonces" url="<?php echo BASE_URL?>/admin/valid_annonces.php"/>
	<h2>Webmestre binet X</h2>
	<p>En construction
</p>
	<h2>Prez binet X</h2>
	<p>En construction
</p>
</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php"; ?>
