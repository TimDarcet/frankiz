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
	Affichage de la liste des binets ayant un site web.

	$Log$
	Revision 1.11  2004/11/11 19:22:52  kikx
	Permet de gerer l'affichage externe interne des binets
	Permet de pas avoir de binet sans catégorie valide

	Revision 1.10  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.9  2004/10/19 13:45:00  schmurtz
	Classement des binets par categorie puis par nom
	
	Revision 1.8  2004/10/18 20:29:44  kikx
	Enorme modification pour la fusion des bases des binets (Merci Schmurtz)
	
	Revision 1.7  2004/09/16 13:56:32  kikx
	Modification de skins (détails)
	
	Revision 1.4  2004/09/15 23:20:39  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.3  2004/09/15 21:42:36  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
require_once "../include/global.inc.php";

// demande_authentification(AUTH_MINIMUM);

// Récupération d'une image
if(isset($_REQUEST['image'])){
	$DB_trombino->query("SELECT image,format FROM binets WHERE binet_id='{$_GET['id']}'");
	list($image,$format) = $DB_trombino->next_row() ;
	header("content-type: $format");
	echo $image;
	exit;
}


// Affichage de la liste des binets
require BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="binets" titre="Frankiz : Binets">
<?php
	$auth = "" ;
	if(!$_SESSION['user']->est_authentifie(AUTH_MINIMUM)) $auth = " AND exterieur=1 " ;

	$categorie_precedente = -1;
	$DB_trombino->query("SELECT binet_id,nom,description,http,b.catego_id,categorie ".
						"FROM binets as b RIGHT JOIN binets_categorie as c USING(catego_id) ".
						"WHERE http IS NOT NULL AND http != '' $auth".
						"ORDER BY b.catego_id ASC, b.nom ASC");
	while(list($id,$nom,$description,$http,$cat_id,$categorie) = $DB_trombino->next_row()) {

?>
		<binet id="<?=$id?>" categorie="<?=$categorie?>" nom="<?=$nom?>">
			<image source="binets/?image=1&amp;id=<?=$id?>"/>
			<description><?=stripslashes($description)?></description>
			<url><?=$http?></url>
		</binet>
<?php
	}
?>
</page>
<?php require "../include/page_footer.inc.php" ?>
