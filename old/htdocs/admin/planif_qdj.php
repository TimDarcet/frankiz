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
	
	$Id$
	
*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
if(!verifie_permission('admin') && !verifie_permission('qdjmaster'))
	acces_interdit();



// Génération de la page
//===============
require_once BASE_FRANKIZ."htdocs/include/page_header.inc.php";

?>
<page id="valid_qdj" titre="Frankiz : Planifie tes qdj">
<h1>Planification des qdj</h1>
<?php
foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

	// Fixe une date de parution à la QDJ
	
	if ($temp[0]=='valid') {
		if((strtotime($_REQUEST['date']) <(time() ))&&($_REQUEST['date']!="0000-00-00"))
		{ ?>
			<warning>ERREUR: Veuillez choisir une date supérieure à aujourd'hui</warning>
		<?php
		}
		else
		{
			$DB_web->query("SELECT qdj_id FROM qdj WHERE date='{$_REQUEST['date']}' LIMIT 1");
			if($DB_web->num_rows())
			{
				if(!(isset($_REQUEST['decalage']))) //On remplace la qdj prévue par la qdj selectionnée, l'ancienne est remise dans la liste des qdj à planifier
				{
					list($qdj_id) = ($DB_web->next_row()); 
					$DB_web->query("UPDATE qdj SET date='0000-00-00' WHERE qdj_id='{$qdj_id}'");
				}
				else //On insère cette qdj et on repousse toutes les autres.
				{
					//nb de qdj planifiées
					
					$DB_web->query("SELECT qdj_id,date FROM qdj WHERE date>='{$_REQUEST['date']}'  ORDER BY date ASC");
					while(list($id,$date_tmp) = $DB_web->next_row()) 
					{
						$DB_web->push_result();
						$date_tmp = date("Y-m-d",strtotime($date_tmp)+24*3600);
						$DB_web->query("UPDATE qdj SET date='{$date_tmp}' WHERE qdj_id='{$id}'");
						$DB_web->query("SELECT qdj_id FROM qdj WHERE date='$date_tmp'");
						if(($DB_web->num_rows())<2) break; //plus rien à décaler
						$DB_web->pop_result() ;
					} 
					
				}
				
			}
			$DB_web->query("UPDATE qdj SET date='{$_REQUEST['date']}'WHERE qdj_id='{$temp[1]}'");
	
		?>
			<commentaire>Planification effectuée</commentaire>
		<?php
		}	
	}
	
	// Déplacer un jour après
	if ($temp[0]=='augdate') {
		$date1 = date("Y-m-d",strtotime($temp[2])+24*3600);
		$DB_web->query("UPDATE qdj SET date='{$temp[2]}'  WHERE date='{$date1}'");
		$DB_web->query("UPDATE qdj SET date='{$date1}' WHERE qdj_id='{$temp[1]}'");
	}
	
	// Déplacer un jour avant
	if ($temp[0]=='reddate') {
		$date1 = date("Y-m-d",strtotime($temp[2])-24*3600);
		$DB_web->query("UPDATE qdj SET date='{$temp[2]}'   WHERE date='{$date1}'");
		
		$DB_web->query("UPDATE qdj SET date='{$date1}' WHERE qdj_id='{$temp[1]}'");
	}
	
	// Formulaire pour modifier la date de parution de la QDJ déjà planifiée
	
	if($temp[0]=='modif') {
		$DB_web->query("SELECT question,reponse1,reponse2 FROM qdj WHERE qdj_id='{$temp[1]}'");
		list($question,$reponse1,$reponse2) = $DB_web->next_row(); 
		$id = $temp[1];
?>
		<warning>Cette QDJ est déjà planifiée pour le <?php echo $temp[2] ?></warning>
		<module titre="QDJ">
			<qdj type="aujourdhui" >
				<question><?php echo $question ?></question>
				<reponse id="1"><?php echo $reponse1?></reponse>
				<reponse id="2"><?php echo $reponse2?></reponse>
			</qdj>
		</module>
		<formulaire id="qdj_<?php echo $id ?>" action="admin/planif_qdj.php">
			<champ id="date" titre="date" valeur="<?php echo $temp[2] ?>"/>
			<choix type="checkbox">
				<option id="decalage" titre="Décaler si necessaire ?"/>
			</choix>
			<bouton id='valid_<?php echo $id ?>' titre='Valider' onClick="return window.confirm('Valider la planification de cette qdj ?')"/>
			
		</formulaire>
<?php	
	}
	
	// Supprime la qdj de la liste
	
	if ($temp[0]=='suppr') {
		$DB_web->query("DELETE FROM qdj WHERE qdj_id='{$temp[1]}'") ;
	

	?>
		<warning>Suppression d'une qdj</warning>
	<?php
	}
}

//nb de qdj planifiées
$date = date("Y-m-d", time());
?>
	<p>Nous sommes le : <?php echo $date; ?></p>
<?php
//Cherche la date de la prochaine qdj libre
for ($i = 0; ; $i++) 
{
	$date_last = date("Y-m-d", time() + $i*24*3600);
	$DB_web->query("SELECT qdj_id FROM qdj WHERE date='$date_last' LIMIT 1");
	if(!$DB_web->num_rows()) break;
}
?>
	<p>La planification est faite jusqu'au : <?php echo $date_last; ?></p>
	<?php $DB_web->query("SELECT qdj_id FROM qdj WHERE date>'$date' "); ?>
	<p>Nb de QDJ planifiées: <?php echo $DB_web->num_rows() ?></p>
	<?php $DB_web->query("SELECT qdj_id FROM qdj WHERE date='0000-00-00' "); ?>
	<p>Nb de QDJ disponibles: <?php echo $DB_web->num_rows() ?></p>
	
<?php
// Affiche la planification existante
if(isset($_REQUEST['show'])) {
	?>
	<h2>Prévisions</h2>
	<?php

	$date = date("Y-m-d", time() + 24*3600);
	$DB_web->query("SELECT qdj_id,date,question,reponse1,reponse2 FROM qdj WHERE date>='$date'  ORDER BY date ASC");
	while(list($id,$date,$question,$reponse1,$reponse2) = $DB_web->next_row()){

?>
		<formulaire id="<?php echo $id ?>" action="admin/planif_qdj.php">
			<note><?php echo $date; ?></note>
			<note><?php echo $question; ?></note>
			<note><?php echo $reponse1; ?> / <?php echo $reponse2; ?></note>
		
			<?php if(strtotime($date) >time() + 24*3600){ ?><bouton titre="Un jour plus tôt" id="reddate_<?php echo $id ?>_<?php echo $date ?>"/><?php } ?>
			<bouton titre="Un jour plus tard" id="augdate_<?php echo $id ?>_<?php echo $date ?>"/>
			<bouton id='modif_<?php echo $id ?>_<?php echo $date ?>' titre='Modifier la date manuellement'/>
			<bouton id='suppr_<?php echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer cette qdj ?!!!!!')"/>
			<hidden id="show"/>
		
		</formulaire>
<?php 
	}
}


// Afficher le bouton pour montrer la plaification déjà établie
if(!isset($_REQUEST['show']))
{
?>
	<formulaire action="admin/planif_qdj.php">
			<bouton id='show' titre='Voir la planification existante' />
	</formulaire>
<?php



//===============================
// Affiche la qdj de demain et après demain
	$date = date("Y-m-d", time() + 24*3600);
	
	$DB_web->query("SELECT question,reponse1,reponse2 FROM qdj WHERE date='$date' LIMIT 1");
	list($question,$reponse1,$reponse2) = $DB_web->next_row(); 
?>
		<h2>Prévue demain</h2>
		<module titre="QDJ">
			<qdj type="aujourdhui" >
				<question><?php echo $question ?></question>
				<reponse id="1"><?php echo $reponse1?></reponse>
				<reponse id="2"><?php echo $reponse2?></reponse>
			</qdj>
		</module>
<?php
	$date = date("Y-m-d", time() + 48*3600);
	
	$DB_web->query("SELECT question,reponse1,reponse2 FROM qdj WHERE date='$date' LIMIT 1");
	list($question,$reponse1,$reponse2) = $DB_web->next_row(); 
?>
		<h2>Prévue après-demain</h2>
		<module titre="QDJ">
			<qdj type="aujourdhui" >
				<question><?php echo $question ?></question>
				<reponse id="1"><?php echo $reponse1?></reponse>
				<reponse id="2"><?php echo $reponse2?></reponse>
			</qdj>
		</module>

<?php
}
?>
 

		
<!-- Affiche les QDJ à planifier -->
		<h2>Disponibles</h2>
<?php
	$DB_web->query("SELECT qdj_id,question, reponse1, reponse2 FROM qdj WHERE date = '0000-00-00'");
	while(list($id,$question,$reponse1,$reponse2) = $DB_web->next_row()) {
?>
		<module titre="QDJ">
			<qdj type="aujourdhui" >
				<question><?php echo $question ?></question>
				<reponse id="1"><?php echo $reponse1?></reponse>
				<reponse id="2"><?php echo $reponse2?></reponse>
			</qdj>
		</module>
		<formulaire id="qdj_<?php echo $id ?>" action="admin/planif_qdj.php">

			<champ id="date" titre="date" valeur="<?php echo $date_last ?>"/>
			<choix type="checkbox">
				<option id="decalage" titre="Décaler si necessaire ?"/>
			</choix>
			<bouton id='valid_<?php echo $id ?>' titre='Valider' onClick="return window.confirm('Valider la planification de cette qdj ?')"/>
			<bouton id='suppr_<?php echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer cette qdj ?!!!!!')"/>
		</formulaire>
<?php
	}
?>
</page>

<?php
require_once BASE_FRANKIZ."htdocs/include/page_footer.inc.php";
?>
