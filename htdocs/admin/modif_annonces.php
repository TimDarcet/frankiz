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
	Page qui permet aux admins de modifier une annonce valid�e
	
	$Log$
	Revision 1.1  2004/11/27 13:59:27  pico
	Page pour modifier les annonces valid�es


	
*/
	
require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";

// V�rification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");



// G�n�ration de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="modif_annonce" titre="Frankiz : Modifie une annonce">
<h1>Modification d'annonces</h1>

<?
// On traite les diff�rents cas de figure d'enrigistrement et validation d'annonce :)

// Enregistrer ...

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;


	if (($temp[0]=='modif')||($temp[0]=='valid')) {
		$DB_web->query("UPDATE annonces SET perime='{$_POST['date']}', titre='{$_POST['titre']}', contenu='{$_POST['text']}' WHERE annonce_id='{$temp[1]}'");	
	?>
		<commentaire><p>Modif effectu�e</p></commentaire>
	<?	
	}
	
	if ($temp[0]=='suppr') {
		$DB_web->query("DELETE FROM annonces WHERE annonce_id='{$temp[1]}'") ;
		//On supprime aussi l'image si elle existe ...
		
		$supp_image = "" ;
		if (file_exists(DATA_DIR_LOCAL."annonces/{$temp[1]}")){
			unlink(DATA_DIR_LOCAL."annonces/{$temp[1]}") ;
			$supp_image = " et de son image associ�e" ;
		}
	?>
		<warning><p>Suppression d'une annonce<? echo $supp_image?></p></warning>
	<?
	}
	
	
}


//===============================

	$DB_web->query("SELECT v.exterieur, v.annonce_id,v.perime, v.titre, v.contenu, e.nom, e.prenom, e.surnom, e.promo, e.mail, e.login FROM annonces as v INNER JOIN trombino.eleves as e USING(eleve_id)");
	while(list($ext, $id,$date,$titre,$contenu,$nom, $prenom, $surnom, $promo,$mail,$login) = $DB_web->next_row()) {
?>
		<annonce titre="<?php  echo $titre ?>" 
				categorie=""
				auteur="<?php echo empty($surnom) ? $prenom.' '.$nom : $surnom .' (X'.$promo.')'?>"
				date="<? echo $date?>">
				<? echo wikiVersXML($contenu) ;
				if (file_exists(DATA_DIR_LOCAL."annonces/{$id}")){
				?>
					<image source="<? echo DATA_DIR_URL."annonces/{$id}" ; ?>" texte=""/>
				<?
				}
				?>
				<eleve nom="<?=$nom?>" prenom="<?=$prenom?>" promo="<?=$promo?>" surnom="<?=$surnom?>" mail="<?=$mail?>"/>
		</annonce>
<?
// Zone de saisie de l'annonce
?>

		<formulaire id="annonce_<? echo $id ?>" titre="L'annonce" action="admin/modif_annonces.php">

			<champ id="titre" titre="Le titre" valeur="<? echo $titre ;?>"/>
			<zonetext id="text" titre="Le texte"><?=$contenu?></zonetext>
			<champ id="date" titre="Date de p�remption" valeur="<? echo $date ;?>"/>
			<bouton id='modif_<? echo $id ?>' titre="Modifier"/>
			<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('Si vous supprimer cette annonce, celle-ci sera supprim� de fa�on definitive ... Voulez vous vraiment la supprimer ?')"/>
		</formulaire>
<?
	}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
