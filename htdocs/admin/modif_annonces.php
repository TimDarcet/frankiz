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
	Page qui permet aux admins de modifier une annonce validée
	
	$Log$
	Revision 1.10  2004/12/14 14:18:12  schmurtz
	Suppression de la page de doc wiki : doc directement dans les pages concernees.

	Revision 1.9  2004/12/13 19:36:21  kikx
	Pour changer exterieur ou non apres la validation de l'annonce (Pour Alban)
	
	Revision 1.8  2004/12/07 13:10:56  pico
	Passage du nettoyage en formulaire
	
	Revision 1.7  2004/12/07 12:06:17  kikx
	Ben c'est un lien cassé
	
	Revision 1.6  2004/12/07 08:36:39  pico
	Ajout d'une page pour pouvoir vider un peu les bases de données (genre pas garder les news qui datent de vieux)
	
	Revision 1.5  2004/11/27 20:16:55  pico
	Eviter le formatage dans les balises <note> <commentaire> et <warning> lorsque ce n'est pas necessaire
	
	Revision 1.4  2004/11/27 15:29:22  pico
	Mise en place des droits web (validation d'annonces + sondages)
	
	Revision 1.3  2004/11/27 14:56:15  pico
	Debut de mise en place de droits spéciaux (qdj + affiches)
	+ génération de la page d'admin qui va bien
	
	Revision 1.2  2004/11/27 14:12:31  pico
	Ajout d'un lien pour supprimmer les annonces périmées depuis plus de 5 jours
	(histoire de pas garder des archives inutiles)
	
	Revision 1.1  2004/11/27 13:59:27  pico
	Page pour modifier les annonces validées
	

	
*/
	
require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')&&!verifie_permission('web'))
	rediriger_vers("/gestion/");



// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="modif_annonce" titre="Frankiz : Modifie une annonce">
<h1>Modification d'annonces</h1>

<?
// On traite les différents cas de figure d'enrigistrement et validation d'annonce :)

// Enregistrer ...
$DB_valid->query("LOCK TABLE valid_annonces WRITE");
$DB_valid->query("SET AUTOCOMMIT=0");

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;


	if (($temp[0]=='modif')||($temp[0]=='valid')) {
		if (isset($_REQUEST['ext_auth']))
			$temp_ext = '1'  ;
		else 
			$temp_ext = '0' ;

		$DB_web->query("UPDATE annonces SET perime='{$_POST['date']}', titre='{$_POST['titre']}', contenu='{$_POST['text']}',exterieur=$temp_ext  WHERE annonce_id='{$temp[1]}'");	
	?>
		<commentaire>Modif effectuée</commentaire>
	<?	
	}
	
	if ($temp[0]=='suppr') {
		$DB_web->query("DELETE FROM annonces WHERE annonce_id='{$temp[1]}'") ;
		//On supprime aussi l'image si elle existe ...
		
		$supp_image = "" ;
		if (file_exists(DATA_DIR_LOCAL."annonces/{$temp[1]}")){
			unlink(DATA_DIR_LOCAL."annonces/{$temp[1]}") ;
			$supp_image = " et de son image associée" ;
		}
	?>
		<warning>Suppression d'une annonce<? echo $supp_image?></warning>
	<?
	}
}
$DB_valid->query("COMMIT");
$DB_valid->query("UNLOCK TABLES");

//===============================

	$DB_web->query("SELECT v.exterieur, v.annonce_id,v.perime, v.titre, v.contenu, e.nom, e.prenom, e.surnom, e.promo, e.mail, e.login FROM annonces as v INNER JOIN trombino.eleves as e USING(eleve_id) WHERE (perime>=".date("Ymd000000",time()).") ORDER BY perime DESC");
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
			<champ id="date" titre="Date de péremption" valeur="<? echo $date ;?>"/>
			<choix titre="Éxtérieur" id="exterieur" type="checkbox" valeur="<? if ($ext==1) echo "ext_auth" ?>">
				<option id="ext_auth" titre="Décision du Webmestre"/>
			</choix>

			<bouton id='modif_<? echo $id ?>' titre="Modifier"/>
			<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('Si vous supprimer cette annonce, celle-ci sera supprimé de façon definitive ... Voulez vous vraiment la supprimer ?')"/>
		</formulaire>
<?
		affiche_syntaxe_wiki();
	}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
