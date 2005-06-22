<?php
/*
	Copyright (C) 2004 Binet Réseau
	http://www.polytechnique.fr/eleves/binets/br/
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/
/*
	Gestion de la liste des binets.

	$Id$
	
*/

// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')&&!verifie_permission('web'))
	acces_interdit();

$message = "";
$texte_image ="" ;
// =====================================
// Verification en douce que tout les binets
// ont une catégorie valide
// =====================================
	
$DB_trombino->query("SELECT catego_id FROM binets_categorie WHERE categorie='Divers'");
list($catego_divers) = $DB_trombino->next_row() ;
$where = "" ;
$DB_trombino->query("SELECT catego_id FROM binets_categorie") ;
while(list($catego) = $DB_trombino->next_row()) {
	$where .= " AND catego_id !=$catego " ; 
}
$warning = "" ;
$DB_trombino->query("SELECT binet_id,nom FROM binets WHERE 1=1 $where") ;
while(list($id,$nom) = $DB_trombino->next_row()) {
	$warning .= " $nom " ; 
	$DB_trombino->push_result() ;
	$DB_trombino->query("UPDATE binets SET catego_id=$catego_divers WHERE binet_id=$id") ;
	$DB_trombino->pop_result() ;
}
if ($warning!="")
	$message .= "<warning> <p>Les binets suivant n'avaient pas de categorie valide :</p><p>$warning</p><p>Leur categorie vient d'être remis à 'Divers'</p></warning>" ;

// =====================================
// Modification d'un binet
// =====================================

	// On crée un binet
	//==========================

	if (isset($_POST['ajout'])) {
		$DB_trombino->query("INSERT INTO binets SET nom='{$_POST['nom']}'");
		$index = mysql_insert_id($DB_trombino->link) ;
		$message .= "<commentaire>Création du binet ' {$_POST['nom']}' effectuée</commentaire>" ;
	}
	

// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="binets_web" titre="Frankiz : Binets Web">
<? echo $message ;?>

<h1>Liste des Binets</h1>

	<?
	$categorie_precedente = -1;
	
	$DB_trombino->query("SELECT nom,binet_id FROM binets ORDER BY nom ASC");
	?>
	<liste id='liste_binet' titre='Liste de binets' selectionnable='non'>
		<entete id="login" titre="Login"/>
	<?
		while(list($nom_binet,$binet_id) = $DB_trombino->next_row()) {
		?>
			<element id="<?=$binet_id?>">
				<colonne id="login">
				<lien titre="<?=$nom_binet?>" url="admin/binets.php?id=<?=$binet_id?>"/>
				</colonne>
			</element>
		<?
		}
	?>	
	</liste>



<h1>Création d'un binet</h1>

		<formulaire id="binet_web" titre="Nouveau Binet" action="admin/binets_liste.php">
			<hidden id="id" titre="ID" valeur=""/>
			<champ id="nom" titre="Nom" valeur=""/>
			<bouton id='ajout' titre="Ajouter"/>
		</formulaire>
		

</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>

