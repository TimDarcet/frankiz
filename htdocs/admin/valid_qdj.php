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
	Page qui permet aux admins de valider une qdj
	
	$Log$
	Revision 1.6  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site

	Revision 1.5  2004/10/14 19:59:37  pico
	Correction de bug
	
	Revision 1.4  2004/10/14 13:48:13  pico
	Amélioration du comportement de la planification des qdj
	- possibilité d'insérer une qdj et de décaler les autres
	- ou remplacer la qdj déjà placée par la courante et remettre l'ancienne dans les qdj à planifier
	
	Revision 1.3  2004/10/13 22:14:32  pico
	Premier jet de page pour affecter une date de publication aux qdj validées
	

	
	
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
		couriel($eleve_id,"[Frankiz] Ta QDJ a été retenue par le BR",$contenu);
			
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
		couriel($eleve_id,"[Frankiz] Ta QDJ n'a pas été retenue par le BR",$contenu);

		$DB_valid->query("DELETE FROM valid_qdj WHERE qdj_id='{$temp[1]}'") ;
	

	?>
		<warning><p>Suppression d'une qdj</p></warning>
	<?
	}
}

//===============================
?>

<lien titre="Planifier les qdj" url="<?php echo BASE_URL?>/admin/planif_qdj.php"/>

<?
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
// Zone de saisie de la qdj
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
