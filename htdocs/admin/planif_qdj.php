<?php
/*
	Page qui permet aux admins de valider une qdj
	
	$Log$
	Revision 1.1  2004/10/13 22:14:32  pico
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
<h1>Planification des qdj</h1>
<?
foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

	
	if ($temp[0]=='valid') {
			
		$DB_web->query("UPDATE qdj SET date='{$_REQUEST['date']}'WHERE qdj_id='{$temp[1]}'");


		$DB_valid->query("DELETE FROM valid_qdj WHERE qdj_id='{$temp[1]}'") ;
	?>
		<commentaire><p>Validation effectuée</p></commentaire>
	<?	


	}
}



//===============================
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

			<champ id="date" titre="date" valeur="0000-00-00"/>
			<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Valider la planification de cette qdj ?')"/>
		</formulaire>
<?
	}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
