<?php
/*
	Permet de proposer une QDJ
	
	$Log$
	Revision 1.1  2004/10/13 20:03:30  pico
	Page pour soumettre des qdj

	
	
*/

// En-tetes
require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_MINIMUM);

// G�n�ration de la page
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
		<champ titre="R�ponse 1" id="reponse1" valeur="<? if (isset($_REQUEST['reponse1'])) echo $_REQUEST['reponse1']?>" />
		<champ titre="R�ponse 2" id="reponse2" valeur="<? if (isset($_REQUEST['reponse2'])) echo $_REQUEST['reponse2']?>" />
		<bouton titre="Mise � jour" id="upload"/>
		<bouton titre="Valider" id="envoie"  onClick="return window.confirm('Voulez vous vraiment envoyer ce mail ?')"/>
	</formulaire>
<?
//==================================================
//=
//= Permet de visualiser son mail avant de l'envoyer
//=
//==================================================
	if (isset($_REQUEST['upload'])) {
?>
		<cadre  titre="QDJ : <? if (isset($_REQUEST['question'])) echo $_REQUEST['question']?>" >
			<? if (isset($_REQUEST['reponse1'])) {echo "- "; echo $_REQUEST['reponse1'];echo "&lt;br/&gt;";} ?>
			<? if (isset($_REQUEST['reponse2'])) {echo "- ";echo $_REQUEST['reponse2'];} ?>
		</cadre>
<?
	}
//==================================================
//=
//= Stockage du mail en attente de validation par un webmestre
//=
//==================================================
} else {
?>
	<commentaire>
		<p>Merci d'avoir propos� une QDJ</p>
		<p>Le responsable au BR essayera de la publier le plus t�t possible</p>
	</commentaire>
<?
	// Stockage dans la base SQL
	$DB_valid->query("INSERT INTO valid_qdj SET question='{$_REQUEST['question']}',reponse1='{$_REQUEST['reponse1']}',reponse2='{$_REQUEST['reponse2']}',eleve_id={$_SESSION['user']->uid}") ;

	//Envoie du mail � l'admin pour la validation
	$tempo = explode("proposition",$_SERVER['REQUEST_URI']) ;
	
	$contenu = "$prenom $nom a soumis une qdj : \n".
				$_REQUEST['question']."\n- ".
				$_REQUEST['reponse1']."\n- ".
				$_REQUEST['reponse2']."\n\n".
				"Pour valider ou non cette demande va sur la page suivante : \n".
				"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_qdj.php\n\n" .
				"Tr�s BR-ement\n" .
				"L'automate :)\n"  ;
				
	mail(MAIL_WEBMESTRE,"[Frankiz] Validation d'une QDJ",$contenu);

}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
