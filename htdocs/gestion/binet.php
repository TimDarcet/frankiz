<?php
/*
	Administration d'un binet. Le webmestre peut modifier les informations liees au site web (url,
	description), le prez peut en plus modifier les membres de son binet et leur commentaire dans
	le trombino.
	
	L'ID du binet à administrer est passer dans le paramètre GET 'binet'.
	
	$Log$
	Revision 1.3  2004/10/17 20:27:35  kikx
	Permet juste au prez des binets de consulter les perosnne adherant aux binet ainsi que leur commentaires

	Revision 1.2  2004/10/17 17:31:32  kikx
	Micro modif avant que j'oublie
	
	Revision 1.1  2004/10/17 15:22:05  kikx
	Mise en place d'un repertoire de gestion qui se différencie de admin car ce n'est pas l'admin :)
	En gros il servira a tout les modification des prez des webmestres , des pages persos, ...
	
	Revision 1.4  2004/09/15 23:20:18  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.3  2004/09/15 21:42:27  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
	
// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if ((empty($_GET['binet'])) || ((!verifie_permission_webmestre($_GET['binet'])) && (!verifie_permission_prez($_GET['binet']))))
	rediriger_vers("/admin/");
	
$DB_web->query("SELECT nom FROM binets WHERE id=".$_GET['binet']);
list($nom_binet) = $DB_web->next_row() ;


require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="admin_binet" titre="Frankiz : administration binet">
<?
if(verifie_permission_prez($_GET['binet'])){

	$DB_trombino->query("SELECT promo FROM eleves WHERE eleve_id='".$_SESSION['user']->uid."'");
	list($promo_prez) = $DB_trombino->next_row() ;
	?>
	<h1>Administration par le </h1>
	<h1>prèz du binet <?=$nom_binet?></h1>
	
	<h2>Liste des membres</h2>
	<?
	$DB_trombino->query("SELECT m.eleve_id,remarque,nom,prenom,surnom,promo FROM membres as m INNER JOIN eleves USING(eleve_id) WHERE binet_id=".$_GET['binet']." AND promo=$promo_prez");
	?>
	<liste id="liste_binets" selectionnable="oui" action="admin/binets.php">
		<entete id="nom" titre="Nom"/>
		<entete id="description" titre="Description"/>
	<?
	while(list($id,$remarque,$nom,$prenom,$surnom,$promo) = $DB_trombino->next_row()) {
			echo "\t\t<element id=\"$id\">\n";
			echo "\t\t\t<colonne id=\"nom\">$nom</colonne>\n";
			echo "\t\t\t<colonne id=\"description\">$remarque</colonne>\n";
			echo "\t\t</element>\n";
	}
?>
		<bouton titre="Modifier" id="modif"/>
		<bouton titre="Supprimer" id="suppr" onClick="return window.confirm('Supprimer cette personne de mon binet ?')"/>
	</liste>
<?
}
if(verifie_permission_prez($_GET['binet'])){


}
?>
</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
