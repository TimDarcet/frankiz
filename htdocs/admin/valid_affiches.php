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
	Page qui permet aux admins de valider une activit�
	
	$Log$
	Revision 1.7  2004/10/29 15:14:40  kikx
	Correction mineur

	Revision 1.6  2004/10/29 15:10:27  kikx
	Passage de la page de validation des activit� en HTML (pour l'envoie des mail) et rajout du champs pour mettre la raison du refus de validation
	
	Revision 1.5  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.4  2004/10/10 22:31:41  kikx
	Voil� ... Maintenant le webmestre prut ou non valider des activit� visibles de l'exterieur
	
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

// V�rification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");

// G�n�ration de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="valid_activit�" titre="Frankiz : Valide une activit�">
<h1>Validation des activit�s</h1>

<?
// On traite les diff�rents cas de figure d'enrigistrement et validation d'affiche :)

// Enregistrer ...

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;


	if (($temp[0]=='modif')||($temp[0]=='valid')) {
		$DB_valid->query("UPDATE valid_affiches SET date='{$_POST['date']}', titre='{$_POST['titre']}' WHERE affiche_id='{$temp[1]}'");	
	?>
		<commentaire><p>Modif effectu�e</p></commentaire>
	<?	
	}
	
	if ($temp[0]=='valid') {
		$DB_valid->query("SELECT eleve_id FROM valid_affiches WHERE affiche_id='{$temp[1]}'");
		list($eleve_id) = $DB_valid->next_row() ;
		// envoi du mail
		$contenu = 	"Ton activit� vient d'�tre valid� par le BR... Elle est d�s � present visible sur le site<br><br> ".
					"Merci de ta participation <br><br>".
					"Tr�s BR-ement<br>" .
					"Le Webmestre de Frankiz<br>"  ;
		couriel($eleve_id,"[Frankiz] Ton activit� a �t� valid� par le BR",$contenu);

		if (isset($_REQUEST['ext_auth']))
			$temp_ext = '1'  ;
		else 
			$temp_ext = '0' ;

		$DB_web->query("INSERT INTO affiches SET stamp=NOW(), date='{$_POST['date']}', titre='{$_POST['titre']}', url='{$_POST['url']}', eleve_id=$eleve_id, exterieur=$temp_ext");
		
		
		// On d�place l'image si elle existe dans le r�pertoire prevu � cette effet
		$index = mysql_insert_id() ;
		if (file_exists(DATA_DIR_LOCAL."affiches/a_valider_{$temp[1]}")){
			rename(DATA_DIR_LOCAL."affiches/a_valider_{$temp[1]}",DATA_DIR_LOCAL."affiches/$index") ;
		}
		$DB_valid->query("DELETE FROM valid_affiches WHERE affiche_id='{$temp[1]}'") ;
	?>
		<commentaire><p>Validation effectu�e</p></commentaire>
	<?	

	}
	if ($temp[0]=='suppr') {
		$DB_valid->query("SELECT eleve_id FROM valid_affiches WHERE affiche_id='{$temp[1]}'");
		list($eleve_id) = $DB_valid->next_row() ;
		// envoi du mail
		$contenu = 	"Ton activit� n'a pas �t� valid� par le BR pour la raison suivante :<br>".
					$_POST['refus']."<br>".
					"D�sol� <br><br>".
					"Tr�s BR-ement<br>" .
					"Le Webmestre de frankiz<br>"  ;
		couriel($eleve_id,"[Frankiz] Ton activit� n'a pas �t� valid� par le BR",$contenu);
		
		$DB_valid->query("DELETE FROM valid_affiches WHERE affiche_id='{$temp[1]}'") ;
		//On supprime aussi l'image si elle existe ...
		
		$supp_image = "" ;
		if (file_exists(DATA_DIR_LOCAL."affiches/a_valider_{$temp[1]}")){
			unlink(DATA_DIR_LOCAL."affiches/a_valider_{$temp[1]}") ;
			$supp_image = " et de son image associ�e" ;
		}
		

	?>
		<warning><p>Suppression d'une affiche<? echo $supp_image?></p></warning>
	<?
	}
	
	
}


//===============================

	$DB_valid->query("SELECT v.exterieur,v.affiche_id,v.date, v.titre, v.url, e.nom, e.prenom, e.surnom, e.promo, e.mail, e.login FROM valid_affiches as v INNER JOIN trombino.eleves as e USING(eleve_id)");
	while(list($ext,$id,$date,$titre,$url,$nom, $prenom, $surnom, $promo,$mail,$login) = $DB_valid->next_row()) {
		echo "<module id=\"activites\" titre=\"Activit�s\">\n";
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

		<formulaire id="affiche_<? echo $id ?>" titre="L'activit�" action="admin/valid_affiches.php">
			<champ id="titre" titre="Le titre" valeur="<?  echo $titre ;?>"/>
			<champ id="url" titre="URL du lien" valeur="<? echo $url ;?>"/>
			<champ id="date" titre="Date d'affichage" valeur="<? echo $date ;?>"/>
			<? 
			if ($ext==1) {
				echo "<warning>L'utilisateur a demand� que son activit� soit visible de l'exterieur</warning>" ;
				$ext_temp='ext' ; 
			} else $ext_temp="" ;
			?>
			<choix titre="Exterieur" id="exterieur" type="checkbox" valeur="<? echo $ext_temp." " ; if ((isset($_REQUEST['ext_auth']))&&(isset($_REQUEST['modif_'.$id]))) echo 'ext_auth' ;?>">
				<option id="ext" titre="Demande de l'utilisateur" modifiable='non'/>
				<option id="ext_auth" titre="D�cision du Webmestre"/>
			</choix>
			<zonetext id="refus" titre="La raison du refus si refus" valeur=""/>

			<bouton id='modif_<? echo $id ?>' titre="Modifier"/>
			<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Cette annonce apparaitra d�s maintenant sur le site ... Voulez vous valider cette activit� ?')"/>
			<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('Si vous supprimer cette activit�, celle-ci sera supprim� de fa�on definitive ... Voulez vous vraiment la supprimer ?')"/>

		</formulaire>
<?
	}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
