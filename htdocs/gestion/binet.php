<?php
/*
	Administration d'un binet. Le webmestre peut modifier les informations liees au site web (url,
	description), le prez peut en plus modifier les membres de son binet et leur commentaire dans
	le trombino.
	
	L'ID du binet � administrer est passer dans le param�tre GET 'binet'.
	
	$Log$
	Revision 1.4  2004/10/17 23:17:18  kikx
	Maintenant le prez peut supprimer les personnes qui sont dans son binet et modifier leur commentaires

	Revision 1.3  2004/10/17 20:27:35  kikx
	Permet juste au prez des binets de consulter les perosnne adherant aux binet ainsi que leur commentaires
	
	Revision 1.2  2004/10/17 17:31:32  kikx
	Micro modif avant que j'oublie
	
	Revision 1.1  2004/10/17 15:22:05  kikx
	Mise en place d'un repertoire de gestion qui se diff�rencie de admin car ce n'est pas l'admin :)
	En gros il servira a tout les modification des prez des webmestres , des pages persos, ...
	
	Revision 1.4  2004/09/15 23:20:18  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.3  2004/09/15 21:42:27  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
	
// En-tetes
require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_FORT);
if ((empty($_GET['binet'])) || ((!verifie_permission_webmestre($_GET['binet'])) && (!verifie_permission_prez($_GET['binet']))))
	rediriger_vers("/admin/");
	
$DB_web->query("SELECT nom FROM binets WHERE id=".$_GET['binet']);
list($nom_binet) = $DB_web->next_row() ;
$message ="" ;

//=================================
// Suppression d'une personne
//=================================
$ids ="" ;
if(isset($_POST['suppr'])) {
	foreach($_POST['elements'] as $id => $on) {
		$ids .= (empty($ids) ? "" : ",") . "'$id'";
	}
	$DB_trombino->query("DELETE FROM membres  WHERE eleve_id IN ($ids)");
	$message .= "<warning>".count($_POST['elements'])." personnes viennent d'�tre supprim�es.</warning>\n";
}
//=================================
// Modification des commentaires des differents membres
//=================================
$ids ="" ;
if(isset($_POST['modif'])) {
	//$DB_trombino->query("SELECT m.eleve_id FROM membres as m INNER JOIN eleves USING(eleve_id) WHERE binet_id=".$_GET['binet']." AND promo=$promo_prez");
	//while(list($id) = $DB_trombino->next_row()) {

	foreach($_POST['description'] as $id => $on) {
		$DB_trombino->query("UPDATE  membres SET remarque='$on' WHERE eleve_id=$id");
	}
	$message .= "<commentaire> Sauvegardes des commentaires des diff�rents membres du binet</commentaire>\n";
}
//=================================
// G�n�ration de la page
//=================================

require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="admin_binet" titre="Frankiz : administration binet">
<?
if(verifie_permission_prez($_GET['binet'])){

	$DB_trombino->query("SELECT promo FROM eleves WHERE eleve_id='".$_SESSION['user']->uid."'");
	list($promo_prez) = $DB_trombino->next_row() ;
	?>
	<h1>Administration par le </h1>
	<h1>pr�z du binet <?=$nom_binet?></h1>
	<?
	echo $message ;
	?>
	<h2>Liste des membres</h2>
	<?
	$DB_trombino->query("SELECT m.eleve_id,remarque,nom,prenom,surnom,promo FROM membres as m INNER JOIN eleves USING(eleve_id) WHERE binet_id=".$_GET['binet']." AND promo=$promo_prez");
	?>
	<liste id="liste_binets" selectionnable="oui" action="gestion/binet.php?binet=<?=$_GET['binet']?>">
		<entete id="nom" titre="Nom"/>
		<entete id="description" titre="Description"/>
	<?
	while(list($id,$remarque,$nom,$prenom,$surnom,$promo) = $DB_trombino->next_row()) {
			echo "\t\t<element id=\"$id\">\n";
			$surnom = (empty($surnom) ? "" : " (".$surnom.")") ; 
			echo "\t\t\t<colonne id=\"nom\">$nom $prenom ".$surnom."</colonne>\n";
			echo "\t\t\t<colonne id=\"description\"><champ id=\"description[$id]\" valeur=\"$remarque\"/></colonne>\n";
			echo "\t\t</element>\n";
	}
?>
		
		<bouton titre="Supprimer" id="suppr" onClick="return window.confirm('Supprimer cette personne de mon binet ?')"/>
		<bouton titre="Modifier tous les commentaires" id="modif"/>
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
