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
	Page qui permet aux admins de valider une annonce
	
	$Log$
	Revision 1.16  2004/11/25 11:52:10  pico
	Correction des liens mysql_id

	Revision 1.15  2004/11/24 13:32:23  kikx
	Passage des annonces en wiki !
	
	Revision 1.14  2004/11/23 23:30:20  schmurtz
	Modification de la balise textarea pour corriger un bug
	(return fantomes)
	
	Revision 1.13  2004/10/29 15:14:40  kikx
	Correction mineur
	
	Revision 1.12  2004/10/29 14:58:36  kikx
	Passage en HTML la page de validation des annonces, de plus il y a la possibilité de mettre pourquoi on refuse la validation d'une annonce
	
	Revision 1.11  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.10  2004/10/20 23:21:39  schmurtz
	Creation d'un element <html> qui permet d'afficher du html brute sans verification
	C'est ce qui est maintenant utilise dans les annonces/cadres
	
	Revision 1.9  2004/10/11 11:01:38  kikx
	Correction des pages de proposition et de validation des annonces pour permettre
	- de stocker les image au bon endroit
	- de mettre les annonces su l'esterieur
	
	Revision 1.8  2004/10/08 06:51:43  pico
	Premier (re)commit:
	
	Pour la validation des annonces, il manquait un champ -> erreur sql.
	
	Il faudrait pas rajouter les options pour définir l'annonce en haut ou visible de l'extérieur ??
	
	Revision 1.7  2004/10/06 14:12:27  kikx
	Page de mail promo quasiment en place ...
	envoie en HTML ...
	Page pas tout a fait fonctionnel pour l'instant
	
	Revision 1.6  2004/09/18 16:04:52  kikx
	Beaucoup de modifications ...
	Amélioration des pages qui gèrent les annonces pour les rendre compatible avec la nouvelle norme de formatage xml -> balise web et balise image qui permette d'afficher une image et la signature d'une personne
	
	Revision 1.5  2004/09/17 22:49:29  kikx
	Rajout de ce qui faut pour pouvoir faire des telechargeement de fichiers via des formulaires (ie des champs 'file' des champ 'hidden') de plus maintenant le formulaire sont en enctype="multipart/form-data" car sinon il parait que ca marche pas !
	
	
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
<page id="valid_annonce" titre="Frankiz : Valide une annonce">
<h1>Validation d'annonces</h1>

<?
// On traite les différents cas de figure d'enrigistrement et validation d'annonce :)

// Enregistrer ...

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;


	if (($temp[0]=='modif')||($temp[0]=='valid')) {
		$DB_valid->query("UPDATE valid_annonces SET perime='{$_POST['date']}', titre='{$_POST['titre']}', contenu='{$_POST['text']}' WHERE annonce_id='{$temp[1]}'");	
	?>
		<commentaire><p>Modif effectuée</p></commentaire>
	<?	
	}
	
	if ($temp[0]=='valid') {
		$DB_valid->query("SELECT eleve_id FROM valid_annonces WHERE annonce_id='{$temp[1]}'");
		list($eleve_id) = $DB_valid->next_row() ;
		// envoi du mail
		$contenu = 	"Ton annonce vient d'être validé par le BR... Elle est dès à present visible sur la page d'accueil<br><br> ".
					"Merci de ta participation <br><br>".
					"Très BR-ement<br>" .
					"Le Webmestre de Frankiz<br>"  ;
		couriel($eleve_id,"[Frankiz] Ton annonce a été validé par le BR",$contenu);
		
		if (isset($_REQUEST['ext_auth']))
			$temp_ext = '1'  ;
		else 
			$temp_ext = '0' ;
			
		$DB_web->query("INSERT INTO annonces  SET stamp=NOW(), perime='{$_POST['date']}', titre='{$_POST['titre']}', contenu='{$_POST['text']}', eleve_id=$eleve_id, exterieur=$temp_ext");
		
		// On déplace l'image si elle existe dans le répertoire prevu à cette effet
		$index = mysql_insert_id($DB_web->link) ;
		if (file_exists(DATA_DIR_LOCAL."annonces/a_valider_{$temp[1]}")){
			rename(DATA_DIR_LOCAL."annonces/a_valider_{$temp[1]}",DATA_DIR_LOCAL."annonces/$index") ;
		}
		$DB_valid->query("DELETE FROM valid_annonces WHERE annonce_id='{$temp[1]}'") ;
	?>
		<commentaire><p>Validation effectuée</p></commentaire>
	<?	

	}
	if ($temp[0]=='suppr') {
		$DB_valid->query("SELECT eleve_id FROM valid_annonces WHERE annonce_id='{$temp[1]}'");
		list($eleve_id) = $DB_valid->next_row() ;
		// envoi du mail
		$contenu = 	"Ton annonce n'a pas été validé par le BR pour la raison suivante :<br>".
					$_POST['refus']."<br>".
					"Désolé <br><br>".
					"Très BR-ement<br>" .
					"Le Webmestre de frankiz<br>"  ;
		couriel($eleve_id,"[Frankiz] Ton annonce n'a pas été validé par le BR",$contenu);

		$DB_valid->query("DELETE FROM valid_annonces WHERE annonce_id='{$temp[1]}'") ;
		//On supprime aussi l'image si elle existe ...
		
		$supp_image = "" ;
		if (file_exists(DATA_DIR_LOCAL."annonces/a_valider_{$temp[1]}")){
			unlink(DATA_DIR_LOCAL."annonces/a_valider_{$temp[1]}") ;
			$supp_image = " et de son image associée" ;
		}
	?>
		<warning><p>Suppression d'une annonce<? echo $supp_image?></p></warning>
	<?
	}
	
	
}


//===============================

	$DB_valid->query("SELECT v.exterieur, v.annonce_id,v.perime, v.titre, v.contenu, e.nom, e.prenom, e.surnom, e.promo, e.mail, e.login FROM valid_annonces as v INNER JOIN trombino.eleves as e USING(eleve_id)");
	while(list($ext, $id,$date,$titre,$contenu,$nom, $prenom, $surnom, $promo,$mail,$login) = $DB_valid->next_row()) {
?>
		<annonce titre="<?php  echo $titre ?>" 
				categorie=""
				auteur="<?php echo empty($surnom) ? $prenom.' '.$nom : $surnom .' (X'.$promo.')'?>"
				date="<? echo $date?>">
				<? echo wikiVersXML($contenu) ;
				if (file_exists(DATA_DIR_LOCAL."annonces/a_valider_{$id}")){
				?>
					<image source="<? echo DATA_DIR_URL."annonces/a_valider_{$id}" ; ?>" texte=""/>
				<?
				}
				?>
				<eleve nom="<?=$nom?>" prenom="<?=$prenom?>" promo="<?=$promo?>" surnom="<?=$surnom?>" mail="<?=$mail?>"/>
		</annonce>
<?
// Zone de saisie de l'annonce
?>

		<formulaire id="annonce_<? echo $id ?>" titre="L'annonce" action="admin/valid_annonces.php">
			<? 
			if ($ext==1) {
				echo "<warning>L'utilisateur a demandé que son activité soit visible de l'exterieur</warning>" ;
				$ext_temp='ext' ; 
			} else $ext_temp="" ;
			?>

			<champ id="titre" titre="Le titre" valeur="<? echo $titre ;?>"/>
			<zonetext id="text" titre="Le texte"><?=$contenu?></zonetext>
			<note valeur="La signature sera automatiquement généré"/>
			<champ id="date" titre="Date de péremption" valeur="<? echo $date ;?>"/>
			
			<choix titre="Exterieur" id="exterieur" type="checkbox" valeur="<? echo $ext_temp." " ; if ((isset($_REQUEST['ext_auth']))&&(isset($_REQUEST['modif_'.$id]))) echo 'ext_auth' ;?>">
				<option id="ext" titre="Demande de l'utilisateur" modifiable='non'/>
				<option id="ext_auth" titre="Décision du Webmestre"/>
			</choix>
			<zonetext id="refus" titre="La raison du refus si refus"></zonetext>

			<bouton id='modif_<? echo $id ?>' titre="Modifier"/>
			<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Cette annonce apparaitra dès maintenant sur la page d'accueil de frankiz... Voulez vous valider cette annonce ?')"/>
			<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('Si vous supprimer cette annonce, celle-ci sera supprimé de façon definitive ... Voulez vous vraiment la supprimer ?')"/>
		</formulaire>
<?
	}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
