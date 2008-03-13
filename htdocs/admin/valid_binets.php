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
	
	$Id$

*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
if(!verifie_permission('admin')&&!verifie_permission('web'))
	acces_interdit();

// Génération de la page
//===============
require_once BASE_FRANKIZ."htdocs/include/page_header.inc.php";

?>
<page id="valid_binet" titre="Frankiz : Valide les modifications des binets">
<h1>Validation des modifications des binets</h1>

<?php
// On traite les différents cas de figure d'enrigistrement et validation :)

// Enregistrer ...
$message = "" ;
$DB_valid->query("LOCK TABLE valid_binet WRITE");
$DB_valid->query("SET AUTOCOMMIT=0");

if (isset($_POST['valid'])) {
	$DB_valid->query("SELECT nom,description,http,catego_id,image,format,folder FROM valid_binet WHERE binet_id={$_POST['id']}");
	if ($DB_valid->num_rows()!=0) {
		list($nom,$description,$http,$categorie,$image,$format,$folder) = $DB_valid->next_row() ;
		
		if (isset($_REQUEST['exterieur'])){
			$temp_ext = '1'  ;
			if($folder!='' && !file_exists(BASE_BINETS_EXT."$folder")) symlink (BASE_BINETS."$folder",BASE_BINETS_EXT."$folder");
		}else{
			$temp_ext = '0' ;
			if($folder!='' && file_exists(BASE_BINETS_EXT."$folder")) unlink(BASE_BINETS_EXT."$folder");
		}
		
		//Log l'action de l'admin
		log_admin($_SESSION['uid']," accepté la modification du binet $nom") ;
	
		$DB_trombino->query("UPDATE binets SET image=\"".addslashes($image)."\" ,format='$format' ,description='$description' , http='$http', catego_id=$categorie, exterieur=$temp_ext, folder='$folder' WHERE binet_id={$_POST['id']}");
		
		$DB_valid->query("DELETE FROM valid_binet WHERE binet_id={$_POST['id']}");
		$message .= "<commentaire>Le binet $nom vient d'être mis à jour</commentaire>" ;
	} else {
		$message .= "<warning>Requête deja traitée par un autre administrateur</warning>" ;
	}
}
if (isset($_POST['suppr'])) {
	$DB_valid->query("SELECT nom FROM valid_binet WHERE binet_id={$_POST['id']}");
	if ($DB_valid->num_rows()!=0) {
		list($nom) = $DB_valid->next_row() ;
		
		//Log l'action de l'admin
		log_admin($_SESSION['uid']," refusé la modification du binet $nom") ;
	
		$DB_valid->query("DELETE FROM valid_binet WHERE binet_id={$_POST['id']}");
		$message .= "<warning>Vous n'avez pas validé le changement du binet $nom</warning>" ;
	} else {
		$message .= "<warning>Requête deja traitée par un autre administrateur</warning>" ;
	}

}
$DB_valid->query("COMMIT");
$DB_valid->query("UNLOCK TABLES");


//===============================
	$DB_valid->query("SELECT binet_id,nom,description,http,categorie,exterieur FROM valid_binet LEFT JOIN trombino.binets_categorie USING(catego_id)");
	echo $message ;
	while(list($binet_id,$nom,$description,$http,$categorie,$exterieur) = $DB_valid->next_row()) {
		$DB_trombino->query("SELECT description,http,categorie,exterieur FROM binets LEFT JOIN binets_categorie USING(catego_id) WHERE binet_id=".$binet_id.";");
		list($description_old,$http_old,$categorie_old,$exterieur_old) = extdata_stripslashes($DB_trombino->next_row());
?>
		<formulaire id="binet_web" titre="<?php echo $nom?>" action="admin/valid_binets.php">
			<hidden id="id" titre="ID" valeur="<?php echo $binet_id?>"/>
			<champ titre="Catégorie" valeur="<?php echo $categorie; ?>" modifiable="non"/>
			<champ titre="(précédemment" valeur="<?php echo $categorie_old; ?>)" modifiable="non"/>
			<image source="gestion/binet.php?image=1&amp;id=<?php echo $binet_id; ?>" texte="<?php echo $nom; ?>"/>
			<lien url="<?php echo $http?>" titre="<?php echo $http?>"/><br/>
			<champ titre="(précédemment" valeur="<?php echo $http_old; ?>)" modifiable="non"/>
			
			<champ titre="Description" valeur="<?php echo stripslashes($description); ?>" modifiable="non"/>
			<champ titre="(précédemment" valeur="<?php echo $description_old; ?>)" modifiable="non"/>

			<choix titre="Extérieur" id="exterieur" type="checkbox" valeur="<?php if ($exterieur==1) echo 'exterieur' ;?>" >
				<option id="exterieur" titre=""/>
			</choix>
			<champ titre="(précédemment" valeur="<?php echo ($exterieur_old)?'oui':'non'; ?>)" modifiable="non"/>
			
			<bouton id='valid' titre="Valider" onClick="return window.confirm('Souhaitez vous valider les modifications ?')"/>
			<bouton id='suppr' titre="Ne pas valider" onClick="return window.confirm('Souhaitez vous ne pas valider les changements de ce binet ?')"/>
		</formulaire>
	<?php
	}
	?>
</page>

<?php
require_once BASE_FRANKIZ."htdocs/include/page_footer.inc.php";
?>
