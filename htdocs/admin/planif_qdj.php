<?php
/*
	Page qui permet aux admins de valider une qdj
	
	$Log$
	Revision 1.6  2004/10/15 20:32:01  pico
	Reorganisation de la page

	Revision 1.5  2004/10/14 22:15:24  pico
	- Ajout de boutons "un jour plus tôt" "un jour plus tard"
	- Empèche de définir une date passée pour la qdj
	
	Revision 1.4  2004/10/14 19:59:37  pico
	Correction de bug
	
	Revision 1.3  2004/10/14 19:21:41  pico
	- Affichage de la planification existante
	- Possibilité de replanifier une QDJ
	
	Revision 1.2  2004/10/14 13:48:13  pico
	Amélioration du comportement de la planification des qdj
	- possibilité d'insérer une qdj et de décaler les autres
	- ou remplacer la qdj déjà placée par la courante et remettre l'ancienne dans les qdj à planifier
	

	
	
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
<page id="valid_qdj" titre="Frankiz : Planifie tes qdj">
<h1>Planification des qdj</h1>
<?
foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

	// Fixe une date de parution à la QDJ
	
	if ($temp[0]=='valid') {
		if(strtotime($_REQUEST['date']) <=(time()-3025 ))
		{ ?>
			<warning>ERREUR: Veuillez choisir une date supérieure à aujourd'hui</warning>
		<?
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
			<commentaire><p>Planification effectuée</p></commentaire>
		<?
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
		<warning><p>Cette QDJ est déjà planifiée pour le <?echo $temp[2] ?></p></warning>
		<module titre="QDJ">
			<qdj type="aujourdhui" >
				<question><?php echo $question ?></question>
				<reponse id="1"><?php echo $reponse1?></reponse>
				<reponse id="2"><?php echo $reponse2?></reponse>
			</qdj>
		</module>
		<formulaire id="qdj_<? echo $id ?>" action="admin/planif_qdj.php">
			<champ id="date" titre="date" valeur="<? echo $temp[2] ?>"/>
			<choix type="checkbox">
				<option id="decalage" titre="Décaler si necessaire ?"/>
			</choix>
			<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Valider la planification de cette qdj ?')"/>
			
		</formulaire>
<?	
	}
	
	// Supprime la qdj de la liste
	
	if ($temp[0]=='suppr') {
		$DB_web->query("DELETE FROM qdj WHERE qdj_id='{$temp[1]}'") ;
	

	?>
		<warning><p>Suppression d'une qdj</p></warning>
	<?
	}
}

//nb de qdj planifiées
$date = date("Y-m-d", time()-3025);
?>
<commentaire>
	<p>Nous sommes le: <? echo $date ?></p>
<?
//Cherche la date de la prochaine qdj libre
for ($i = 1; ; $i++) 
{
	$date = date("Y-m-d", time()-3025 + $i*24*3600);
	$DB_web->query("SELECT qdj_id FROM qdj WHERE date='$date' LIMIT 1");
	if(!$DB_web->num_rows()) break;
}
?>
	<p>La planification est faite jusqu'au: <? echo $date ?></p>
<? $DB_web->query("SELECT qdj_id FROM qdj WHERE date>'$date' "); ?>
	<p>Nb de QDJ planifiées: <? echo $DB_web->num_rows() ?></p>
<? $DB_web->query("SELECT qdj_id FROM qdj WHERE date='0000-00-00' "); ?>
	<p>Nb de QDJ disponibles: <? echo $DB_web->num_rows() ?></p>
</commentaire>
	
<?
// Affiche la planification existante
if(isset($_REQUEST['show'])) {
	?>
	<h2>Prévisions</h2>
	<?

	$date = date("Y-m-d", time()-3025 + 24*3600);
	$DB_web->query("SELECT qdj_id,date,question,reponse1,reponse2 FROM qdj WHERE date>='$date'  ORDER BY date ASC");
	while(list($id,$date,$question,$reponse1,$reponse2) = $DB_web->next_row()){

?>
		<formulaire id="<? echo $id ?>" action="admin/planif_qdj.php">
			<textsimple valeur="<? echo $date ?>"/>
			<textsimple valeur="<? echo $question ?>"/>
			<textsimple valeur="<? echo $reponse1 ?>"/>
			<textsimple valeur="<? echo $reponse2 ?>"/>
		
			<? if(strtotime($date) >time()-3025 + 24*3600){ ?><bouton titre="Un jour plus tôt" id="reddate_<? echo $id ?>_<? echo $date ?>"/><? } ?>
			<bouton titre="Un jour plus tard" id="augdate_<? echo $id ?>_<? echo $date ?>"/>
			<bouton id='modif_<? echo $id ?>_<? echo $date ?>' titre='Modifier la date manuellement'/>
			<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer cette qdj ?!!!!!')"/>
			<hidden id="show"/>
		
		</formulaire>
<? 
	}
}


// Afficher le bouton pour montrer la plaification déjà établie
if(!isset($_REQUEST['show']))
{
?>
	<formulaire action="admin/planif_qdj.php">
			<bouton id='show' titre='Voir la planification existante' />
	</formulaire>
<?



//===============================
// Affiche la qdj de demain et après demain
	$date = date("Y-m-d", time()-3025 + 24*3600);
	
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
<?
	$date = date("Y-m-d", time()-3025 + 48*3600);
	
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

<?
}
?>
 

		
<!-- Affiche les QDJ à planifier -->
		<h2>Disponibles</h2>
<?
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
		<formulaire id="qdj_<? echo $id ?>" action="admin/planif_qdj.php">

			<champ id="date" titre="date" valeur="<? echo $date ?>"/>
			<choix type="checkbox">
				<option id="decalage" titre="Décaler si necessaire ?"/>
			</choix>
			<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Valider la planification de cette qdj ?')"/>
			<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer cette qdj ?!!!!!')"/>
		</formulaire>
<?
	}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
