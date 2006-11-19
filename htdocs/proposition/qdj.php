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
	Permet de proposer une QDJ
	
	$Id$

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
<?php
if (!isset($_REQUEST['envoie'])) {
?>
	<formulaire id="qdj" titre="QDJ" action="proposition/qdj.php">
		<champ titre="Question" id="question" valeur="<?php if (isset($_REQUEST['question'])) echo $_REQUEST['question']?>" />
		<champ titre="Réponse 1" id="reponse1" valeur="<?php if (isset($_REQUEST['reponse1'])) echo $_REQUEST['reponse1']?>" />
		<champ titre="Réponse 2" id="reponse2" valeur="<?php if (isset($_REQUEST['reponse2'])) echo $_REQUEST['reponse2']?>" />
		<bouton titre="Tester" id="upload"/>
		<bouton titre="Proposer" id="envoie"  onClick="return window.confirm('Voulez vous vraiment proposer cette QDJ ?')"/>
	</formulaire>
<?php
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
<?php
	}
//==================================================
//=
//= Stockage de la qdj en attente de validation par un qdjmaster
//=
//==================================================
} else {
?>
	<commentaire>
		Merci d'avoir proposé une QDJ. Le responsable au BR essayera de la publier le plus tôt possible.
	</commentaire>
<?php
	// Stockage dans la base SQL
	$DB_valid->query("INSERT INTO valid_qdj SET eleve_id='{$_SESSION['user']->uid}',question='{$_REQUEST['question']}',reponse1='{$_REQUEST['reponse1']}',reponse2='{$_REQUEST['reponse2']}'") ;

	//Envoie du mail à l'admin pour la validation
	$tempo = explode("proposition",$_SERVER['REQUEST_URI']) ;
	
	
	$contenu = "<strong>Bonjour,</strong><br><br>".
			"$prenom $nom a soumis une qdj : <br>".
			$_REQUEST['question']."<br> ".
			$_REQUEST['reponse1']."<br> ".
			$_REQUEST['reponse2']."<br><br>".
			"Pour valider ou non cette qdj va sur la page suivante<br>".
			"<div align='center'><a href='http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_qdj.php'>".
			"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_qdj.php</a></div><br><br>" .
			"Cordialement,<br>" .
			"Le QDJmaster<br>"  ;
			
	couriel(QDJMASTER_ID,"[Frankiz] Validation d'une QDJ",$contenu,$_SESSION['user']->uid);

}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
