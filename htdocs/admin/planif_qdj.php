<?php
/*
	Page qui permet aux admins de valider une qdj
	
	$Log$
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

	if ($temp[0]=='valid') {
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
//nb de qdj planifiées
$date = date("Y-m-d", time()-3025);
$DB_web->query("SELECT qdj_id FROM qdj WHERE date>'$date' ");
?>
<commentaire><p>Nb de QDJ planifiées: <? echo $DB_web->num_rows() ?></p>
<?
//Cherche la date de la prochaine qdj libre
	for ($i = 1; ; $i++) 
	{
	$date = date("Y-m-d", time()-3025 + $i*24*3600);
	$DB_web->query("SELECT qdj_id FROM qdj WHERE date='$date' LIMIT 1");
	if(!$DB_web->num_rows()) break;
	}
	?>
	<p>Prochaine date disponible: <? echo $date ?></p></commentaire>
		
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
		</formulaire>
<?
	}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
