<?php
/*
	Page qui permet aux admins de valider une qdj
	
	$Log$
	Revision 1.2  2004/10/13 21:19:02  pico
	Oubié d'enlever ça


	
	
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
<page id="valid_qdj" titre="Frankiz : Valide une qdj">
<h1>Validation de qdj</h1>

<?
// On traite les différents cas de figure d'enrigistrement et validation de qdj :)

// Enregistrer ...

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;


	if (($temp[0]=='modif')||($temp[0]=='valid')) {
		$DB_valid->query("UPDATE valid_qdj SET question='{$_POST['question']}', reponse1='{$_POST['reponse1']}', reponse2='{$_POST['reponse2']}' WHERE qdj_id='{$temp[1]}'");	
	?>
		<commentaire><p>Modif effectuée</p></commentaire>
	<?	
	}
	
	if ($temp[0]=='valid') {
		$DB_valid->query("SELECT eleve_id FROM valid_qdj WHERE qdj_id='{$temp[1]}'");
		list($eleve_id) = $DB_valid->next_row() ;
		// envoi du mail
		$contenu = "Merci de ta participation \n\n".
			"Très BR-ement\n" .
			"L'automate :)\n"  ;
		couriel($eleve_id,"[Frankiz] Ta QDJ a été validé par le BR",$contenu);
			
		$DB_web->query("INSERT INTO qdj SET question='{$_POST['question']}', reponse1='{$_POST['reponse1']}', reponse2='{$_POST['reponse2']}'");


		$DB_valid->query("DELETE FROM valid_qdj WHERE qdj_id='{$temp[1]}'") ;
	?>
		<commentaire><p>Validation effectuée</p></commentaire>
	<?	

	}
	if ($temp[0]=='suppr') {
		$DB_valid->query("SELECT eleve_id FROM valid_qdj WHERE qdj_id='{$temp[1]}'");
		list($eleve_id) = $DB_valid->next_row() ;
		// envoi du mail
		$contenu = "Désolé \n\n".
			"Très BR-ement\n" .
			"L'automate :)\n"  ;
		couriel($eleve_id,"[Frankiz] Ta QDJ n'a pas été validé par le BR",$contenu);

		$DB_valid->query("DELETE FROM valid_qdj WHERE qdj_id='{$temp[1]}'") ;
	

	?>
		<warning><p>Suppression d'une qdj<? echo $supp_image?></p></warning>
	<?
	}
	
	
}


//===============================

	$DB_valid->query("SELECT v.qdj_id,v.question, v.reponse1, v.reponse2, e.nom, e.prenom, e.surnom, e.promo, e.mail, e.login FROM valid_qdj as v INNER JOIN trombino.eleves as e USING(eleve_id)");
	while(list($id,$question,$reponse1,$reponse2,$nom, $prenom, $surnom, $promo,$mail,$login) = $DB_valid->next_row()) {
?>
	<module titre="QDJ">
		<qdj type="aujourdhui" >
			<question><?php echo $question ?></question>
			<reponse id="1"><?php echo $reponse1?></reponse>
			<reponse id="2"><?php echo $reponse2?></reponse>
		</qdj>
	</module>
<?
// Zone de saisie de l'annonce
?>

		<formulaire id="qdj_<? echo $id ?>" titre="La QDJ" action="admin/valid_qdj.php">


			<champ id="question" titre="La question" valeur="<? echo $question ;?>"/>
			<champ id="reponse1" titre="Réponse1" valeur="<? echo $reponse1 ;?>"/>
			<champ id="reponse2" titre="Réponse2" valeur="<? echo $reponse2 ;?>"/>
			
			<bouton id='modif_<? echo $id ?>' titre="Modifier"/>
			<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Valider cette qdj ?')"/>
			<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer cette qdj ?!!!!!')"/>
		</formulaire>
<?
	}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
