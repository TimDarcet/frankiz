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
	
	$Id$
	
*/
	
require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')&&!verifie_permission('web'))
	acces_interdit();



// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="valid_annonce" titre="Frankiz : Valide une annonce">
<h1>Validation d'annonces</h1>

<?
// On traite les différents cas de figure d'enrigistrement et validation d'annonce :)

// Enregistrer ...
$DB_valid->query("LOCK TABLE valid_annonces WRITE");
$DB_valid->query("SET AUTOCOMMIT=0");

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

	
	
	
	if (($temp[0]=='modif')||($temp[0]=='valid')) {
		$DB_valid->query("SELECT 0 FROM valid_annonces WHERE annonce_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {
	
			$DB_valid->query("UPDATE valid_annonces SET perime='{$_POST['date']}', titre='{$_POST['titre']}', contenu='{$_POST['text']}' WHERE annonce_id='{$temp[1]}'");
			if ($temp[0]!='valid') {
	?>
				<commentaire>Modif effectuée</commentaire>
	<?	
			}
		} else {
	?>
			<warning>Requête deja traitée par un autre administrateur</warning>
	<?			
		}
	}
	
	if ($temp[0]=='valid') {
		$DB_valid->query("SELECT eleve_id FROM valid_annonces WHERE annonce_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {
			list($eleve_id) = $DB_valid->next_row() ;
			
			//Log l'action de l'admin
			log_admin($_SESSION['user']->uid,"validé l'annonce '{$_POST['titre']}' ") ;
			
			// envoi du mail
			$contenu = 	"Ton annonce vient d'être validée par le BR... Elle est dès à present visible sur la page d'accueil<br><br> ".
						$_POST['explication']."<br>".
						"Merci de ta participation <br><br>".
						"Cordialement,<br>" .
						"Le Webmestre de Frankiz<br>"  ;
			couriel($eleve_id,"[Frankiz] Ton annonce a été validée par le BR",$contenu,WEBMESTRE_ID);
			
			if (isset($_REQUEST['ext_auth']))
				$temp_ext = '1'  ;
			else 
				$temp_ext = '0' ;
				
			if (isset($_REQUEST['important']))
				$temp_imp = ', en_haut=\'1\'' ;
			else 
				$temp_imp = '' ;
				
			$DB_web->query("INSERT INTO annonces  SET stamp=NOW(), perime='{$_POST['date']}', titre='{$_POST['titre']}', contenu='{$_POST['text']}', eleve_id=$eleve_id, exterieur=$temp_ext $temp_imp");
			
			// On déplace l'image si elle existe dans le répertoire prevu à cette effet
			$index = mysql_insert_id($DB_web->link) ;
			if (file_exists(DATA_DIR_LOCAL."annonces/a_valider_{$temp[1]}")){
				rename(DATA_DIR_LOCAL."annonces/a_valider_{$temp[1]}",DATA_DIR_LOCAL."annonces/$index") ;
			}
			$DB_valid->query("DELETE FROM valid_annonces WHERE annonce_id='{$temp[1]}'") ;
		?>
			<commentaire>Validation effectuée</commentaire>
		<?	
		} 
		
	}
	if ($temp[0]=='suppr') {
		$DB_valid->query("SELECT eleve_id FROM valid_annonces WHERE annonce_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {
			list($eleve_id) = $DB_valid->next_row() ;
			
			//Log l'action de l'admin
			log_admin($_SESSION['user']->uid,"supprimé l'annonce '{$_POST['titre']}' ") ;
			
			// envoi du mail
			$contenu = 	"Ton annonce n'a pas été validée par le BR pour la raison suivante :<br>".
						$_POST['explication']."<br>".
						"Désolé <br><br>".
						"Cordialement,<br>" .
						"Le Webmestre de Frankiz<br>"  ;
			couriel($eleve_id,"[Frankiz] Ton annonce n'a pas été validée par le BR",$contenu,WEBMESTRE_ID);
	
			$DB_valid->query("DELETE FROM valid_annonces WHERE annonce_id='{$temp[1]}'") ;
			//On supprime aussi l'image si elle existe ...
			
			$supp_image = "" ;
			if (file_exists(DATA_DIR_LOCAL."annonces/a_valider_{$temp[1]}")){
				unlink(DATA_DIR_LOCAL."annonces/a_valider_{$temp[1]}") ;
				$supp_image = " et de son image associée" ;
			}
		?>
			<warning>Suppression d'une annonce<? echo $supp_image?></warning>
		<?
		} else {
	?>
			<warning>Requête deja traitée par un autre administrateur</warning>
	<?
		}
	}
	
}

$DB_valid->query("COMMIT");
$DB_valid->query("UNLOCK TABLES");

//===============================

	$DB_valid->query("SELECT v.exterieur, v.annonce_id,v.perime, v.titre, v.contenu, e.nom, e.prenom, e.surnom, e.promo, e.mail, e.login FROM valid_annonces as v LEFT JOIN trombino.eleves as e USING(eleve_id)");
	while(list($ext, $id,$date,$titre,$contenu,$nom, $prenom, $surnom, $promo,$mail,$login) = $DB_valid->next_row()) {
?>
		<annonce titre="<?php  echo $titre ?>" 
				categorie=""
				auteur="<?php echo empty($surnom) ? $prenom.' '.$nom : $surnom .' (X'.$promo.')'?>"
				date="<? echo $date?>">
				<?
				if (file_exists(DATA_DIR_LOCAL."annonces/a_valider_$id"))
					echo "<image source=\"".DATA_DIR_URL."annonces/a_valider_$id\" texte=\"image\"/>\n";
				
				echo wikiVersXML($contenu) ;
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
			<note>La signature sera automatiquement générée</note>
			<champ id="date" titre="Date de péremption" valeur="<? echo $date ;?>"/>
			
			<choix titre="Éxtérieur" id="exterieur" type="checkbox" valeur="<? echo $ext_temp." " ; if ((isset($_REQUEST['ext_auth']))&&(isset($_REQUEST['modif_'.$id]))) echo 'ext_auth' ;?>">
				<option id="ext" titre="Demande de l'utilisateur" modifiable='non'/>
				<option id="ext_auth" titre="Décision du Webmestre"/>
			</choix>
			<note>Si l'annonce est très très importante</note>
			<choix titre="Important" id="important" type="checkbox" valeur="<? if ((isset($_REQUEST['important']))&&(isset($_REQUEST['modif_'.$id]))) echo 'important' ;?>">
				<option id="important" titre=""/>
			</choix>
			<zonetext id="explication" titre="La raison du choix du modérateur (Surtout si refus)"></zonetext>

			<bouton id='modif_<? echo $id ?>' titre="Modifier"/>
			<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Cette annonce apparaitra dès maintenant sur la page d'accueil de frankiz... Voulez vous valider cette annonce ?')"/>
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
