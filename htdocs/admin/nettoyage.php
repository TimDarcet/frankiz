<?php
/*
	Copyright (C) 2004 Binet R�seau
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
	Page qui permet aux admins de vider la bdd des activit�s p�rim�es
	
	$Log$
	Revision 1.2  2004/12/07 08:45:13  pico
	Nettoyage des qdj

	Revision 1.1  2004/12/07 08:36:39  pico
	Ajout d'une page pour pouvoir vider un peu les bases de donn�es (genre pas garder les news qui datent de vieux)
		
*/
	
require_once "../include/global.inc.php";


// V�rification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')&&!verifie_permission('web'))
	rediriger_vers("/gestion/");



// G�n�ration de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="nettoyage" titre="Frankiz : Nettoyage des bases de donn�es du site.">
<h1>Modification d'annonces</h1>

<?
// On traite les diff�rents cas de figure d'enrigistrement et validation d'annonce :)

// Enregistrer ...

foreach ($_REQUEST AS $keys => $val){
	$temp = explode("_",$keys) ;


	if ($temp[0]=='annonces') {
		
		$DB_web->query("SELECT annonce_id FROM annonces WHERE perime<".date("Ymd000000",time()- 5 * 24 * 3600)."") ;
		//On supprime aussi l'image si elle existe ...
		$compteur = 0;
		while(list($id)=$DB_web->next_row()) {
			$compteur++;
			if (file_exists(DATA_DIR_LOCAL."annonces/$id")){
				unlink(DATA_DIR_LOCAL."annonces/$id") ;
			}
		}
		$DB_web->query("DELETE FROM annonces WHERE perime<".date("Ymd000000",time()- 5 * 24 * 3600)."") ;
	?>
		<warning>Suppression de <? echo $compteur?> annonces p�rim�es</warning>
	<?
	}
	
	if ($temp[0]=='affiches') {
		$DB_web->query("SELECT affiche_id FROM affiches WHERE date<'".date("Y-m-d 00:00",time()- 5 * 24 * 3600)."' ") ;
		$compteur = $DB_web->num_rows();
		$DB_web->query("DELETE FROM affiches WHERE date<'".date("Y-m-d 00:00",time()- 5 * 24 * 3600)."' ") ;
	?>
		<warning>Suppression de <? echo $compteur?> affiches p�rim�es</warning>
	<?
	}
	
	if ($temp[0]=='qdj') {
		$DB_web->query("SELECT qdj_id FROM qdj WHERE date<'".date("Y-m-d", time()-3025 - 5 * 24 * 3600)."' AND date>'0000-00-00'");
		$compteur = $DB_web->num_rows();
		$DB_web->query("DELETE FROM qdj WHERE date<'".date("Y-m-d", time()-3025 - 5 * 24 * 3600)."' AND date>'0000-00-00'");
	?>
		<warning>Suppression de <? echo $compteur?> qdj p�rim�es</warning>
	<?
	}
}


echo "<lien titre=\"Supprimer les annonces p�rim�es depuis plus de 5 jours\" url=\"admin/nettoyage.php?annonces\"/>" ;
echo "<lien titre=\"Supprimer les affiches p�rim�es depuis plus de 5 jours\" url=\"admin/nettoyage.php?affiches\"/>" ;
echo "<lien titre=\"Supprimer les qdj p�rim�es depuis plus de 5 jours\" url=\"admin/nettoyage.php?qdj\"/>" ;
	
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
