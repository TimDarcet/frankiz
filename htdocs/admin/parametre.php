<?php
/*
	Cette page permet de modifier les paramètres globaux mysql du site
	genre : 
	# la dernière promo qui est sur le site
	# la dernière promo qui est dans le trombi (qui normalment devrait être mis a jour automatiquement)
	
*/

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="admin_parametre" titre="Frankiz : Modifier les paramètres globaux">

<?
?>

<commentaire>
<p>Cette page est une page pour pouvoir modifier directement les paramètres du sites : </p>
<p> Si vous devez créer des varibles dans la table essayer qu'elles soient assez explicite :)</p>
</commentaire>
<h2>Paramètres du site</h2>
	<liste id="liste" selectionnable="non" action="admin/parametre.php">
		<entete id="nom_var" titre="Nom de la varible"/>
		<entete id="valeur" titre="Valeur"/>
<?
		$DB_web->query("SELECT nom,valeur FROM parametres ORDER by nom");
		while(list($nom,$valeur) = $DB_web->next_row()) {
?>
			<element id="<? echo $nom ;?>">
				<colonne id="eleve"><? echo "$nom" ?></colonne>
				<colonne id="valeur">
<?
				// Cas Particuliers traité à la main
				if ($nom=="lastpromo_ontrombino") {
					echo $valeur." &nbsp; &nbsp; &nbsp;" ;
					echo "<bouton titre='Update' id='update_lastpromo_ontrombino'/>" ;
				} else {
				// fin des cas particuliers 
?>
					<champ titre="" id="<? echo $nom ;?>" valeur="<? echo $valeur ;?>"/>
					<bouton titre='Ok' id='update_<? echo $nom ;?>'/>
<?
				}
?>
				</colonne>
			</element>
<?
		}
?>
	</liste>
	
</page>

<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
