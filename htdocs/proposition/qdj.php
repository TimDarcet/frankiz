<?php
/*
	Permet de proposer une QDJ
	
	$Log$
	Revision 1.2  2004/10/13 21:11:16  pico
	QDJ

	
	
*/

// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MINIMUM);

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

$DB_trombino->query("SELECT eleve_id,nom,prenom,surnom,mail,login,promo FROM eleves WHERE eleve_id='".$_SESSION['user']->uid."'");
list($eleve_id,$nom,$prenom,$surnom,$mail,$login,$promo) = $DB_trombino->next_row();

?>
<page id="propoz_qdj" titre="Frankiz : Proposition de QDJ">
<?
if (!isset($_REQUEST['envoie'])) {
?>
	<formulaire id="qdj" titre="QDJ" action="proposition/qdj.php">
		<champ titre="Question" id="question" valeur="<? if (isset($_REQUEST['question'])) echo $_REQUEST['question']?>" />
		<champ titre="Réponse 1" id="reponse1" valeur="<? if (isset($_REQUEST['reponse1'])) echo $_REQUEST['reponse1']?>" />
		<champ titre="Réponse 2" id="reponse2" valeur="<? if (isset($_REQUEST['reponse2'])) echo $_REQUEST['reponse2']?>" />
		<bouton titre="Mise à jour" id="upload"/>
		<bouton titre="Valider" id="envoie"  onClick="return window.confirm('Voulez vous vraiment envoyer ce mail ?')"/>
	</formulaire>
<?
//==================================================
//=
//= Permet de visualiser sa qdj avant de l'envoyer
//=
//==================================================
	if (isset($_REQUEST['upload'])) {
?>
	<module titre="QDJ">
		<qdj type="aujourdhui" >
			<question><?php echo $_REQUEST['question'] ?></question>
			<reponse id="1"><?php echo $_REQUEST['reponse1'] ?></reponse>
			<reponse id="2"><?php echo $_REQUEST['reponse2'] ?></reponse>
		</qdj>
	</module>		
<?
	}
//==================================================
//=
//= Stockage de la qdj en attente de validation par un qdjmaster
//=
//==================================================
} else {
?>
	<commentaire>
		<p>Merci d'avoir proposé une QDJ</p>
		<p>Le responsable au BR essayera de la publier le plus tôt possible</p>
	</commentaire>
<?
	// Stockage dans la base SQL
	$DB_valid->query("INSERT INTO valid_qdj SET eleve_id='{$_SESSION['user']->uid}',question='{$_REQUEST['question']}',reponse1='{$_REQUEST['reponse1']}',reponse2='{$_REQUEST['reponse2']}'") ;

	//Envoie du mail à l'admin pour la validation
	$tempo = explode("proposition",$_SERVER['REQUEST_URI']) ;
	
	$contenu = "$prenom $nom a soumis une qdj : \n".
				$_REQUEST['question']."\n- ".
				$_REQUEST['reponse1']."\n- ".
				$_REQUEST['reponse2']."\n\n".
				"Pour valider ou non cette demande va sur la page suivante : \n".
				"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_qdj.php\n\n" .
				"Très BR-ement\n" .
				"L'automate :)\n"  ;
				
	mail(MAIL_WEBMESTRE,"[Frankiz] Validation d'une QDJ",$contenu);

}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
