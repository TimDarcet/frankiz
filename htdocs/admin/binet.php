<?php
/*
	$Id$
	
	Administration d'un binet. Le webmestre peut modifier les informations liees au site web (url,
	description), le prez peut en plus modifier les membres de son binet et leur commentaire dans
	le trombino.
	
	L'ID du binet � administrer est passer dans le param�tre GET 'binet'.
*/
	
// En-tetes
require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_FORT);
if(empty($_GET['binet']) || !verifie_permission_webmestre($_GET['binet']) || !verifie_permission_prez($_GET['binet']))
	rediriger_vers("/admin/");
$est_prez = verifie_permission_prez($_GET['binet']);

// G�n�ration de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="admin_binet" titre="Frankiz : administration binet">
	<p>Coucou <?php echo $est_prez ? "Prez" : "Webmestre"?> du binet <?php echo $_GET['binet']?></p>
</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
