<?php
/*
	$Id$
	
	Gestion des utilisateurs et de leur droits. L'acc�s se fait par le trombino,
	apr�s une recherche.
	
	ATTENTION�: il n'y a volontairement pas de pages web d'administration permettant l'ajout
	ou la suppression d'un utilisateur. En effet, il n'y a aucune raison de supprimer un utilisateur,
	et pour l'ajout d'utilisateur, l'op�ration a lieu par bloc pour toute une promo or c'est beaucoup
	plus facile de le faire via un fichier de commande MySQL que par une interface web.
	
	L'ID de l'utilisateur � modifier est passer dans le param�tre GET 'user'.
*/
	
// En-tetes
require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')) {
	header("Location: ".BASE_URL."/admin/");
	exit;
}

// G�n�ration de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="admin_user" titre="Frankiz : gestion des utilisateurs">
</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
