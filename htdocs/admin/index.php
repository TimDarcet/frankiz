<?php
/*
	$Id$
	
	Page principale d'administration : affiche la liste des pages d'administration auxquelles
	l'utilisateur courant à accès.
*/
	
// En-tetes
require_once "../include/global.inc.php";

demande_authentification(AUTH_FORT);
if(empty($_SESSION['user']->perms)) {
	header("Location: ".BASE_URL."/");
	exit;
}

// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="admin" titre="Frankiz : administration">
	<h2>Administration frankiz</h2>
	<p>En construction…</p>
	<h2>Webmestre binet X</h2>
	<p>En construction…</p>
	<h2>Prez binet X</h2>
	<p>En construction…</p>
	<h2>Site web perso</h2>
	<p>En construction…</p>
	<h2>Gestion des utilisateurs</h2>
	<p><a href="<? echo BASE_URL?>/trombino">Chercher</a>…</p>
</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php"; ?>
