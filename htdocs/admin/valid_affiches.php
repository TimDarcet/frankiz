<?php
/*
	Page qui permet aux admins de valider une activité
	
	$Log$
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
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="valid_activité" titre="Frankiz : Valide une activité">
<h1>Validation des activités</h1>

<?
// On traite les différents cas de figure d'enrigistrement et validation d'affiche :)

// Enregistrer ...

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;


	if (($temp[0]=='modif')||($temp[0]=='valid')) {
		$DB_valid->query("UPDATE valid_affiches SET date='{$_POST['date']}', titre='{$_POST['titre']}' WHERE affiche_id='{$temp[1]}'");	
	?>
		<commentaire><p>Modif effectuée</p></commentaire>
	<?	
	}
	
	if ($temp[0]=='valid') {
		$DB_valid->query("SELECT eleve_id FROM valid_affiches WHERE affiche_id='{$temp[1]}'");
		list($eleve_id) = $DB_valid->next_row() ;
		// envoi du mail
		$contenu = "Merci de ta participation \n\n".
			"Très BR-ement\n" .
			"L'automate :)\n"  ;
		couriel($eleve_id,"[Frankiz] Ton activité a été validé par le BR",$contenu);

		if (isset($_REQUEST['ext_auth']))
			$temp_ext = '1'  ;
		else 
			$temp_ext = '0' ;

		$DB_web->query("INSERT INTO affiches SET stamp=NOW(), date='{$_POST['date']}', titre='{$_POST['titre']}', url='{$_POST['url']}', eleve_id=$eleve_id, exterieur=$temp_ext");
		
		
		// On déplace l'image si elle existe dans le répertoire prevu à cette effet
		$index = mysql_insert_id() ;
		if (file_exists(DATA_DIR_LOCAL."affiches/a_valider_{$temp[1]}")){
			rename(DATA_DIR_LOCAL."affiches/a_valider_{$temp[1]}",DATA_DIR_LOCAL."affiches/$index") ;
		}
		$DB_valid->query("DELETE FROM valid_affiches WHERE affiche_id='{$temp[1]}'") ;
	?>
		<commentaire><p>Validation effectuée</p></commentaire>
	<?	

	}
	if ($temp[0]=='suppr') {
		$DB_valid->query("SELECT eleve_id FROM valid_affiches WHERE affiche_id='{$temp[1]}'");
		list($eleve_id) = $DB_valid->next_row() ;
		// envoi du mail
		$contenu = "Désolé \n\n".
			"Très BR-ement\n" .
			"L'automate :)\n"  ;
		couriel($eleve_id,"[Frankiz] Ton affiche n'a pas été validé par le BR",$contenu);

		$DB_valid->query("DELETE FROM valid_affiches WHERE affiche_id='{$temp[1]}'") ;
		//On supprime aussi l'image si elle existe ...
		
		$supp_image = "" ;
		if (file_exists(DATA_DIR_LOCAL."affiches/a_valider_{$temp[1]}")){
			unlink(DATA_DIR_LOCAL."affiches/a_valider_{$temp[1]}") ;
			$supp_image = " et de son image associée" ;
		}
		

	?>
		<warning><p>Suppression d'une affiche<? echo $supp_image?></p></warning>
	<?
	}
	
	
}


//===============================

	$DB_valid->query("SELECT v.exterieur,v.affiche_id,v.date, v.titre, v.url, e.nom, e.prenom, e.surnom, e.promo, e.mail, e.login FROM valid_affiches as v INNER JOIN trombino.eleves as e USING(eleve_id)");
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
			?>
			<choix titre="Exterieur" id="exterieur" type="checkbox" valeur="<? echo $ext_temp." " ; if ((isset($_REQUEST['ext_auth']))&&(isset($_REQUEST['modif_'.$id]))) echo 'ext_auth' ;?>">
				<option id="ext" titre="Demande de l'utilisateur" modifiable='non'/>
				<option id="ext_auth" titre="Décision du Webmestre"/>
			</choix>

			<bouton id='modif_<? echo $id ?>' titre="Modifier"/>
			<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Valider cette affiche ?')"/>
			<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer cette affiche ?!!!!!')"/>
		</formulaire>
<?
	}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
