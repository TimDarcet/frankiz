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
	Page de validation d'une modification d'un binet
	
	$Log$
	Revision 1.6  2004/11/27 15:02:17  pico
	Droit xshare et faq + redirection vers /gestion et non /admin en cas de pbs de droits

	Revision 1.5  2004/11/08 18:26:40  kikx
	Coorige des bugs
	
	Revision 1.4  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.3  2004/10/19 19:08:17  kikx
	Permet a l'administrateur de valider les modification des binets
	
	Revision 1.2  2004/10/19 18:16:24  kikx
	hum
	

*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/gestion/");

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="valid_binet" titre="Frankiz : Valide les modifications des binets">
<h1>Validation des modifications des binets</h1>

<?
// On traite les différents cas de figure d'enrigistrement et validation :)

// Enregistrer ...
$message = "" ;
	
if (isset($_POST['valid'])) {


	$DB_valid->query("SELECT nom,description,http,catego_id,image,format,folder FROM valid_binet WHERE binet_id={$_POST['id']}");
	list($nom,$description,$http,$categorie,$image,$format,$folder) = $DB_valid->next_row() ;
	
	if (isset($_REQUEST['exterieur']))
		$temp_ext = '1'  ;
	else 
		$temp_ext = '0' ;

	$DB_trombino->query("UPDATE binets SET image=\"".addslashes($image)."\" ,format='$format' ,description='$description' , http='$http', catego_id=$categorie, exterieur=$temp_ext, folder='$folder' WHERE binet_id={$_POST['id']}");
	
	$DB_valid->query("DELETE FROM valid_binet WHERE binet_id={$_POST['id']}");
	$message .= "<commentaire>Le binet $nom vient d'être mis à jour</commentaire>" ;
}
if (isset($_POST['suppr'])) {
	$DB_valid->query("SELECT nom FROM valid_binet WHERE binet_id={$_POST['id']}");
	list($nom) = $DB_valid->next_row() ;

	$DB_valid->query("DELETE FROM valid_binet WHERE binet_id={$_POST['id']}");
	$message .= "<warning>Vous n'avez pas validé le changement du binet $nom</warning>" ;

}



//===============================
	$DB_valid->query("SELECT binet_id,nom,description,http,categorie,exterieur FROM valid_binet LEFT JOIN trombino.binets_categorie USING(catego_id)");
	echo $message ;
	while(list($binet_id,$nom,$description,$http,$categorie,$exterieur) = $DB_valid->next_row()) {
?>
		<formulaire id="binet_web" titre="<? echo $nom?>" action="admin/valid_binets.php">
			<hidden id="id" titre="ID" valeur="<? echo $binet_id?>"/>
			<textsimple  titre="Catégorie" valeur="<? echo $categorie?>"/>
			<image source="gestion/binet.php?image=1&amp;id=<?=$binet_id?>"/>
			<lien  url="<? echo $http?>" titre="<? echo $http?>"/>
			<textsimple  titre="Description" valeur="<? echo stripslashes($description)?>"/>

			<choix titre="Exterieur" id="exterieur" type="checkbox" valeur="<? if ($exterieur==1) echo 'exterieur' ;?>" >
				<option id="exterieur" titre=""/>
			</choix>
			
			<bouton id='valid' titre="Valider" onClick="return window.confirm('Souhaitez vous valider les modifications ?')"/>
			<bouton id='suppr' titre="Ne pas valider" onClick="return window.confirm('Souhaitez vous ne pas valider les changements de ce binet ?')"/>
		</formulaire>
	<?
	}
	?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
