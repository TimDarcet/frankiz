<?php
/*
	Administration d'un binet. Le webmestre peut modifier les informations liees au site web (url,
	description), le prez peut en plus modifier les membres de son binet et leur commentaire dans
	le trombino.
	
	L'ID du binet à administrer est passer dans le paramètre GET 'binet'.
	
	$Log$
	Revision 1.6  2004/10/18 20:29:44  kikx
	Enorme modification pour la fusion des bases des binets (Merci Schmurtz)

	Revision 1.5  2004/10/17 23:30:44  kikx
	Juste un petit bug si on supprime zero entrées
	
	Revision 1.4  2004/10/17 23:17:18  kikx
	Maintenant le prez peut supprimer les personnes qui sont dans son binet et modifier leur commentaires
	
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
	
$DB_trombino->query("SELECT nom FROM binets WHERE binet_id=".$_GET['binet']);
list($nom_binet) = $DB_trombino->next_row() ;
$message ="" ;


//=================================
// Génération de la page
//=================================

require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="admin_binet" titre="Frankiz : administration binet">
<?
//==============================================================
//
// Si le mec est PREZIDENT
//
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

if(verifie_permission_prez($_GET['binet'])){

	//=================================
	// Suppression d'une personne
	//=================================
	$ids ="" ;
	if(isset($_POST['suppr'])) {
		if(isset($_POST['elements'])) {
	
			foreach($_POST['elements'] as $id => $on) {
				$ids .= (empty($ids) ? "" : ",") . "'$id'";
			}
			$DB_trombino->query("DELETE FROM membres  WHERE eleve_id IN ($ids)");
			$message .= "<warning>".count($_POST['elements'])." personnes viennent d'être supprimées.</warning>\n";
		}
	}
	//=================================
	// Modification des commentaires des differents membres
	//=================================
	$ids ="" ;
	if(isset($_POST['modif'])) {
		foreach($_POST['description'] as $id => $on) {
			$DB_trombino->query("UPDATE  membres SET remarque='$on' WHERE eleve_id=$id");
		}
		$message .= "<commentaire> Sauvegardes des commentaires des différents membres du binet</commentaire>\n";
	}

	$DB_trombino->query("SELECT promo FROM eleves WHERE eleve_id='".$_SESSION['user']->uid."'");
	list($promo_prez) = $DB_trombino->next_row() ;
	?>
	<h1>Administration par le </h1>
	<h1>prèz du binet <?=$nom_binet?></h1>
	<?
	echo $message ;
	?>
	<h2>Liste des membres</h2>
	<?
	$DB_trombino->query("SELECT m.eleve_id,remarque,nom,prenom,surnom,promo FROM membres as m INNER JOIN eleves USING(eleve_id) WHERE binet_id=".$_GET['binet']." AND promo>=$promo_prez ORDER BY promo ASC,nom ASC");
	?>
	<liste id="liste_binets" selectionnable="oui" action="gestion/binet.php?binet=<?=$_GET['binet']?>">
		<entete id="nom" titre="Nom"/>
		<entete id="description" titre="Description"/>
	<?
	while(list($id,$remarque,$nom,$prenom,$surnom,$promo) = $DB_trombino->next_row()) {
			echo "\t\t<element id=\"$id\">\n";
			$surnom = (empty($surnom) ? "" : " (".$surnom.")") ; 
			echo "\t\t\t<colonne id=\"nom\">X$promo : $prenom $nom $surnom</colonne>\n";
			echo "\t\t\t<colonne id=\"description\"><champ id=\"description[$id]\" valeur=\"$remarque\"/></colonne>\n";
			echo "\t\t</element>\n";
	}
?>
		
		<bouton titre="Supprimer" id="suppr" onClick="return window.confirm('Supprimer cette personne de mon binet ?')"/>
		<bouton titre="Modifier tous les commentaires" id="modif"/>
	</liste>
<?
}
//==============================================================
//
// Si le mec est WEBMESTRE
//
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

if(verifie_permission_webmestre($_GET['binet'])){
	$liste_catego ="" ;
	$DB_trombino->query("SELECT catego_id,categorie FROM binets_categorie ORDER BY categorie ASC");
	while( list($catego_id,$catego_nom) = $DB_trombino->next_row() )
		$liste_catego .= "\t\t\t<option titre=\"$catego_nom\" id=\"$catego_id\"/>\n";

	$DB_trombino->query("SELECT binet_id,nom,description,http,catego_id FROM binets as b WHERE binet_id=".$_GET['binet']);
	list($id,$nom,$descript,$http,$cat_id) = $DB_trombino->next_row()
?>
	<h1>Administration par le </h1>
	<h1>webmestre du binet <?=$nom_binet?></h1>
	<?
	echo $message ;
	?>

		<formulaire id="binet_web_<? echo $id?>" titre="<? echo $nom?>" action="admin/binets_web.php">
			<hidden id="id" titre="ID" valeur="<? echo $id?>"/>
			<champ id="nom" titre="Nom" valeur="<? echo $nom?>"/>
			<choix titre="Catégorie" id="catego" type="combo" valeur="<?=$cat_id?>">
<?php
				echo $liste_catego ;
?>
			</choix>
			<champ id="http" titre="Http" valeur="<? echo $http?>"/>
			<zonetext id="descript" titre="Description" valeur="<? echo stripslashes($descript)?>"/>
			<image source="binets/?image=1&amp;id=<?=$id?>"/>
			<champ id="file" titre="Ton image de 100x100 px" valeur="" taille="50000"/>
			<bouton id='modif' titre="Modifier" onClick="return window.confirm('Souhaitez vous valider les changements')"/>
		</formulaire>
<?php
}
?>
</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
