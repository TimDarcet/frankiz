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
	
	$Log$
	Revision 1.17  2004/12/16 13:00:41  pico
	INNER en LEFT

	Revision 1.16  2004/12/08 12:22:40  kikx
	Protection de la validation des activités
	
	Revision 1.15  2004/11/29 19:41:08  kikx
	Micro Bug
	
	Revision 1.14  2004/11/27 20:16:55  pico
	Eviter le formatage dans les balises <note> <commentaire> et <warning> lorsque ce n'est pas necessaire
	
	Revision 1.13  2004/11/27 16:10:52  pico
	Correction d'erreur de redirection et ajout des web à la validation des activités.
	
	Revision 1.12  2004/11/27 15:02:17  pico
	Droit xshare et faq + redirection vers /gestion et non /admin en cas de pbs de droits
	
	Revision 1.11  2004/11/27 14:56:15  pico
	Debut de mise en place de droits spéciaux (qdj + affiches)
	+ génération de la page d'admin qui va bien
	
	Revision 1.10  2004/11/25 23:50:04  pico
	Possibilité de rajouter une heure pour l'activité (ex: scéances du BRC)
	
	Revision 1.9  2004/11/25 10:40:08  pico
	Correction activités (sinon l'image était tjs écrite en tant que 0 et ct pas glop du coup)
	
	Revision 1.8  2004/11/23 23:30:20  schmurtz
	Modification de la balise textarea pour corriger un bug
	(return fantomes)
	
	Revision 1.7  2004/10/29 15:14:40  kikx
	Correction mineur
	
	Revision 1.6  2004/10/29 15:10:27  kikx
	Passage de la page de validation des activité en HTML (pour l'envoie des mail) et rajout du champs pour mettre la raison du refus de validation
	
	Revision 1.5  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.4  2004/10/10 22:31:41  kikx
	Voilà ... Maintenant le webmestre prut ou non valider des activité visibles de l'exterieur
	
	Revision 1.3  2004/10/07 22:52:20  kikx
	Correction de la page des activites (modules + proposition + administration)
		rajout de variables globales : DATA_DIR_LOCAL
						DATA_DIR_URL
	
	Comme ca si ca change, on est safe :)
	
	Revision 1.2  2004/10/06 14:12:27  kikx
	Page de mail promo quasiment en place ...
	envoie en HTML ...
	Page pas tout a fait fonctionnel pour l'instant
	
	Revision 1.1  2004/09/20 22:19:27  kikx
	test
	

	
*/
	
require_once "../include/global.inc.php";

// Vérification des droits
if(verifie_permission('admin')||verifie_permission('web'))
	$user_id = '%';
else if(verifie_permission('affiches'))
	$user_id = $_SESSION['user']->uid;
else
	rediriger_vers("/gestion/");
	
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
			$DB_valid->query("UPDATE valid_affiches SET date='{$_POST['date']}', titre='{$_POST['titre']}' WHERE affiche_id='{$temp[1]}'");
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
			// envoi du mail
			$contenu = 	"Ton activité vient d'être validé par le BR... Elle est dès à present visible sur le site<br><br> ".
						"Merci de ta participation <br><br>".
						"Très BR-ement<br>" .
						"Le Webmestre de Frankiz<br>"  ;
			couriel($eleve_id,"[Frankiz] Ton activité a été validé par le BR",$contenu);
	
			if (isset($_REQUEST['ext_auth']))
				$temp_ext = '1'  ;
			else 
				$temp_ext = '0' ;
	
			$DB_web->query("INSERT INTO affiches SET stamp=NOW(), date='{$_POST['date']}', titre='{$_POST['titre']}', url='{$_POST['url']}', eleve_id=$eleve_id, exterieur=$temp_ext");
			
			
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
			// envoi du mail
			$contenu = 	"Ton activité n'a pas été validé par le BR pour la raison suivante :<br>".
						$_POST['refus']."<br>".
						"Désolé <br><br>".
						"Très BR-ement<br>" .
						"Le Webmestre de frankiz<br>"  ;
			couriel($eleve_id,"[Frankiz] Ton activité n'a pas été validé par le BR",$contenu);
			
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

	$DB_valid->query("SELECT v.exterieur,v.affiche_id,v.date, v.titre, v.url, e.nom, e.prenom, e.surnom, e.promo, e.mail, e.login FROM valid_affiches as v LEFT JOIN trombino.eleves as e USING(eleve_id) WHERE v.eleve_id LIKE '$user_id'");
	while(list($ext,$id,$date,$titre,$url,$nom, $prenom, $surnom, $promo,$mail,$login) = $DB_valid->next_row()) {
		echo "<module id=\"activites\" titre=\"Activités\">\n";
?>
		<annonce titre="<?php  echo $titre ?>" 
				categorie=""
				date="<? echo $date?>">
				<?
				if (file_exists(DATA_DIR_LOCAL."affiches/a_valider_{$id}")){
				?>
					<a href="<?php echo $url ?>">
						<image source="<? echo DATA_DIR_URL."affiches/a_valider_{$id}" ; ?>" texte="Affiche"/>
					</a>
				<?
				}
				?>
				<p><?php echo $titre ?></p>
				<eleve nom="<?=$nom?>" prenom="<?=$prenom?>" promo="<?=$promo?>" surnom="<?=$surnom?>" mail="<?=$mail?>"/>
		</annonce>
<?
		echo "</module>\n" ;
// Zone de saisie de l'affiche
?>

		<formulaire id="affiche_<? echo $id ?>" titre="L'activité" action="admin/valid_affiches.php">
			<champ id="titre" titre="Le titre" valeur="<?  echo $titre ;?>"/>
			<champ id="url" titre="URL du lien" valeur="<? echo $url ;?>"/>
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
			<zonetext id="refus" titre="La raison du refus si refus"></zonetext>

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
