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
	Page qui permet aux admins de valider une activité
	
	$Id$
	
*/
	
require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";

// Vérification des droits
if(verifie_permission('admin')||verifie_permission('web'))
	$user_id = '%';
else if(verifie_permission('affiches'))
	$user_id = $_SESSION['user']->uid;
else
	acces_interdit();
	
// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="valid_activité" titre="Frankiz : Valide une activité">
<h1>Validation des activités</h1>

<?
// On traite les différents cas de figure d'enrigistrement et validation d'affiche :)

// Enregistrer ...
$DB_valid->query("LOCK TABLE valid_affiches WRITE");
$DB_valid->query("SET AUTOCOMMIT=0");
foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

	

	if (($temp[0]=='modif')||($temp[0]=='valid')) {
		$DB_valid->query("SELECT 0 FROM valid_affiches WHERE affiche_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {
			$DB_valid->query("UPDATE valid_affiches SET date='{$_POST['date']}', titre='{$_POST['titre']}', description='{$_POST['text']}' WHERE affiche_id='{$temp[1]}'");
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
		$DB_valid->query("SELECT eleve_id FROM valid_affiches WHERE affiche_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {
			list($eleve_id) = $DB_valid->next_row() ;
			//Log l'action de l'admin
			log_admin($_SESSION['user']->uid," accepté l'affiche {$_POST['titre']}") ;
			// envoi du mail
			$contenu = 	"Ton activité vient d'être validée par le BR... Elle est dès à present visible sur le site<br><br> ".
						$_POST['explication']."<br>".
						"Merci de ta participation <br><br>".
						"Cordialement<br>" .
						"Le Webmestre de Frankiz<br>"  ;
			couriel($eleve_id,"[Frankiz] Ton activité a été validée par le BR",$contenu,WEBMESTRE_ID);
	
			if (isset($_REQUEST['ext_auth']))
				$temp_ext = '1'  ;
			else 
				$temp_ext = '0' ;
	
			$DB_web->query("INSERT INTO affiches SET stamp=NOW(), date='{$_POST['date']}', titre='{$_POST['titre']}', url='{$_POST['url']}', eleve_id=$eleve_id, exterieur=$temp_ext, description='{$_POST['text']}'");
			
			
			// On déplace l'image si elle existe dans le répertoire prevu à cette effet
			$index = mysql_insert_id($DB_web->link) ;
			if (file_exists(DATA_DIR_LOCAL."affiches/a_valider_{$temp[1]}")){
				rename(DATA_DIR_LOCAL."affiches/a_valider_{$temp[1]}",DATA_DIR_LOCAL."affiches/{$index}") ;
			}
			$DB_valid->query("DELETE FROM valid_affiches WHERE affiche_id='{$temp[1]}'") ;
		?>
			<commentaire>Validation effectuée</commentaire>
		<?	
		} 
	}
	if ($temp[0]=='suppr') {
		$DB_valid->query("SELECT eleve_id FROM valid_affiches WHERE affiche_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {
			list($eleve_id) = $DB_valid->next_row() ;
			//Log l'action de l'admin
			log_admin($_SESSION['user']->uid," refusé l'affiche {$_POST['titre']}") ;
			// envoi du mail
			$contenu = 	"Ton activité n'a pas été validée par le BR pour la raison suivante :<br>".
						$_POST['explication']."<br>".
						"Désolé <br><br>".
						"Cordialement,<br>" .
						"Le Webmestre de Frankiz<br>"  ;
			couriel($eleve_id,"[Frankiz] Ton activité n'a pas été validée par le BR",$contenu,WEBMESTRE_ID);
			
			$DB_valid->query("DELETE FROM valid_affiches WHERE affiche_id='{$temp[1]}'") ;
			//On supprime aussi l'image si elle existe ...
			
			$supp_image = "" ;
			if (file_exists(DATA_DIR_LOCAL."affiches/a_valider_{$temp[1]}")){
				unlink(DATA_DIR_LOCAL."affiches/a_valider_{$temp[1]}") ;
				$supp_image = " et de son image associée" ;
			}
			
	
		?>
			<warning>Suppression d'une affiche<? echo $supp_image?></warning>
		<?
		}else {
	?>
			<warning>Requête deja traitée par un autre administrateur</warning>
	<?
		}
	}
	
}
$DB_valid->query("COMMIT") ;
$DB_valid->query("UNLOCK TABLES");

//===============================

	$DB_valid->query("SELECT v.exterieur,v.affiche_id,v.date, v.titre, v.url,v.description, e.nom, e.prenom, e.surnom, e.promo, e.mail, e.login FROM valid_affiches as v LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE v.eleve_id LIKE '$user_id'");
	while(list($ext,$id,$date,$titre,$url,$description,$nom, $prenom, $surnom, $promo,$mail,$login) = $DB_valid->next_row()) {
		echo "<module id=\"activites\" titre=\"Activités\">\n";
?>
	<annonce date="<? echo $date ?>">
		<lien url="<?php echo $url ;?>">
		<?
		if(file_exists(DATA_DIR_LOCAL."affiches/a_valider_{$id}")){
		?>
		<image source="<? echo DATA_DIR_URL."affiches/a_valider_{$id}" ; ?>" texte="Affiche" legende="<?php echo $titre?>"/>
		<?
		}
		?>
		</lien>
		<? echo wikiVersXML($description); ?>
		<eleve nom="<?=$nom?>" prenom="<?=$prenom?>" promo="<?=$promo?>" surnom="<?=$surnom?>" mail="<?=$mail?>"/>
	</annonce>
<?
		echo "</module>\n" ;
// Zone de saisie de l'affiche
?>

		<formulaire id="affiche_<? echo $id ?>" titre="L'activité" action="admin/valid_affiches.php">
			<champ id="titre" titre="Le titre" valeur="<?  echo $titre ;?>"/>
			<champ id="url" titre="URL du lien" valeur="<? echo $url ;?>"/>
			<zonetext id="text" titre="Description plus détaillée"><?=$description?></zonetext>
			<champ id="date" titre="Date d'affichage" valeur="<? echo $date ;?>"/>
			<? 
			if ($ext==1) {
				echo "<warning>L'utilisateur a demandé que son activité soit visible de l'exterieur</warning>" ;
				$ext_temp='ext' ; 
			} else $ext_temp="" ;
			
			// L'utilisateur qui a les droits de valider ses propres affiches ne peut pas les afficher à l'exterieur, seul un admin le peut.
			if(!verifie_permission('affiches')){
			?>
				<choix titre="Exterieur" id="exterieur" type="checkbox" valeur="<? echo $ext_temp." " ; if ((isset($_REQUEST['ext_auth']))&&(isset($_REQUEST['modif_'.$id]))) echo 'ext_auth' ;?>">
					<option id="ext" titre="Demande de l'utilisateur" modifiable='non'/>
					<option id="ext_auth" titre="Décision du Webmestre"/>
				</choix>
			<?
			}
			?>
			<zonetext id="explication" titre="La raison du choix du modérateur (Surtout si refus)"></zonetext>

			<bouton id='modif_<? echo $id ?>' titre="Modifier"/>
			<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Cette annonce apparaitra dès maintenant sur le site ... Voulez vous valider cette activité ?')"/>
			<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('Si vous supprimer cette activité, celle-ci sera supprimé de façon definitive ... Voulez vous vraiment la supprimer ?')"/>

		</formulaire>
<?
	}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
