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
	Revision 1.12  2004/12/13 16:32:14  kikx
	Protection de la validation d'une QDJ ...

	Revision 1.11  2004/11/27 20:16:55  pico
	Eviter le formatage dans les balises <note> <commentaire> et <warning> lorsque ce n'est pas necessaire
	
	Revision 1.10  2004/11/27 15:02:17  pico
	Droit xshare et faq + redirection vers /gestion et non /admin en cas de pbs de droits
	
	Revision 1.9  2004/11/27 14:56:15  pico
	Debut de mise en place de droits spéciaux (qdj + affiches)
	+ génération de la page d'admin qui va bien
	
	Revision 1.8  2004/10/29 15:48:43  kikx
	Mise ne forme du php
	
	Revision 1.7  2004/10/29 15:47:34  kikx
	Ca ne sert a rien d'envoyer des mails qd on valide ou qu'on refuse les qdj ...
	
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
if(!verifie_permission('admin') && !verifie_permission('qdjmaster'))
	rediriger_vers("/gestion/");

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="valid_qdj" titre="Frankiz : Valide une qdj">

<h1>Validation de qdj</h1>

<?
// On traite les différents cas de figure d'enrigistrement et validation de qdj :)

// Enregistrer ...
$DB_valid->query("LOCK TABLE valid_qdj WRITE");
$DB_valid->query("SET AUTOCOMMIT=0");

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;


	if (($temp[0]=='modif')||($temp[0]=='valid')) {
		$DB_valid->query("SELECT 0 FROM valid_qdj WHERE qdj_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {

			$DB_valid->query("UPDATE valid_qdj SET question='{$_POST['question']}', reponse1='{$_POST['reponse1']}', reponse2='{$_POST['reponse2']}' WHERE qdj_id='{$temp[1]}'");
		?>
			<commentaire>Modif effectuée</commentaire>
		<?
		} else {
	?>
			<warning>Requête deja traitée par un autre administrateur</warning>
	<?
		}
	}
	
	if ($temp[0]=='valid') {
		$DB_valid->query("SELECT eleve_id FROM valid_qdj WHERE qdj_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {

			list($eleve_id) = $DB_valid->next_row() ;
				
			$DB_web->query("INSERT INTO qdj SET question='{$_POST['question']}', reponse1='{$_POST['reponse1']}', reponse2='{$_POST['reponse2']}'");
			
			$DB_valid->query("DELETE FROM valid_qdj WHERE qdj_id='{$temp[1]}'") ;
		?>
			<commentaire>Validation effectuée</commentaire>
		<?
		}
	}
	if ($temp[0]=='suppr') {
		$DB_valid->query("SELECT eleve_id FROM valid_qdj WHERE qdj_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {
	
			list($eleve_id) = $DB_valid->next_row() ;
			$DB_valid->query("DELETE FROM valid_qdj WHERE qdj_id='{$temp[1]}'") ;
		?>
			<warning>Suppression d'une qdj</warning>
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
