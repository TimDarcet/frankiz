<?php
/*
	Administration d'un binet. Le webmestre peut modifier les informations liees au site web (url,
	description), le prez peut en plus modifier les membres de son binet et leur commentaire dans
	le trombino.
	
	L'ID du binet à administrer est passer dans le paramètre GET 'binet'.
	
	$Log$
	Revision 1.4  2004/09/15 23:20:18  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")

	Revision 1.3  2004/09/15 21:42:27  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
	
// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(empty($_GET['binet']) || !verifie_permission_webmestre($_GET['binet']) || !verifie_permission_prez($_GET['binet']))
	rediriger_vers("/admin/");
$est_prez = verifie_permission_prez($_GET['binet']);

// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="admin_binet" titre="Frankiz : administration binet">
	<p>Coucou <?php echo $est_prez ? "Prez" : "Webmestre"?> du binet <?php echo $_GET['binet']?></p>
</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
