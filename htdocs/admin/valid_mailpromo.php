<?php
/*
	Page qui permet aux admins de valider un mail promo
	
	$Log$
	Revision 1.1  2004/10/06 14:12:27  kikx
	Page de mail promo quasiment en place ...
	envoie en HTML ...
	Page pas tout a fait fonctionnel pour l'instant


	
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
<page id="valid_mailpromo" titre="Frankiz : Valide un mail promo">
<h1>Validation des activités</h1>

<?
// On traite les différents cas de figure d'enrigistrement et validation d'affiche :)

// Enregistrer ...

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;


	if (($temp[0]=='modif')||($temp[0]=='valid')) {
		$DB_valid->query("UPDATE valid_mailpromo SET titre='{$_POST['titre']}', mail='{$_POST['mail']}' WHERE mail_id='{$temp[1]}'");	
	?>
		<commentaire><p>Modif effectuée</p></commentaire>
	<?	
	}
	
	if ($temp[0]=='valid') {
		$DB_valid->query("SELECT eleve_id FROM valid_mailpromo WHERE mail_id='{$temp[1]}'");
		list($eleve_id) = $DB_valid->next_row() ;
		// envoi du mail
		$contenu = "Merci de ta participation \n\n".
			"Très BR-ement\n" .
			"L'automate :)\n"  ;
		couriel($eleve_id,"[Frankiz] Ton mail promo a été validé par le BR",$contenu);
		
		//====================================================
		// Pocedure d'envoie de masse
		
	        echo "<p>Envoi en cours...</p><p>" ;
		$log = "" ;
		$cnt = 0 ;
		
		$DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus'");
		list($promo_temp) = $DB_web->next_row() ;

		if ($_POST['promo'] == "") {
			$to = " promo=$promo_temp OR promo=".($promo_temp-1) ;
		} else {
			$to = " promo=".$_POST['promo'] ;
		}
		
		$DB_trombino->query("SELECT login FROM eleves WHERE ".$to) ;
		while(list($login) = $DB_trombino->next_row() ) {
			$mail_envoie = $login."@poly" ;
			
			//if (mail($mail_envoie, $_POST['titre'], $_POST['mail'], "From: ".$from."\r\nX-Mailer: PHP/" . phpversion())){
			$mail_contenu = str_replace("&gt;",">",str_replace("&lt;","<",$_POST['mail'])) ;
			$sender = str_replace("&gt;",">",str_replace("&lt;","<",$_POST['from'])) ;
			
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			$headers .= "From: ".$sender."\r\nX-Mailer: PHP/" . phpversion()."\r\n" ;
			
			if (mail('gruson@poly', $_POST['titre'],$mail_contenu , $headers)){
				$cnt ++ ;
				sleep(5); // Attends 1 secondes
			} else {
				$log .= "Erreur lors de l'envoi vers ".$mail ;
			}
			if ($cnt==3)break ;//========================= A VIRER !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		}
		echo $log ;
		echo "</p><p> Nb de mail envoyé avec succès : ".$cnt."</p>" ;
	
		// fin de la procédure
		
		
		$DB_valid->query("DELETE FROM valid_mailpromo WHERE mail_id='{$temp[1]}'") ;
	?>
		<commentaire><p>Validation effectuée</p></commentaire>
	<?	

	}
	if ($temp[0]=='suppr') {
		$DB_valid->query("SELECT eleve_id FROM valid_mailpromo WHERE mail_id='{$temp[1]}'");
		list($eleve_id) = $DB_valid->next_row() ;
		// envoi du mail
		$contenu = "Désolé \n\n".
			"Très BR-ement\n" .
			"L'automate :)\n"  ;
		couriel($eleve_id,"[Frankiz] Ton mail promo n'a pas été validé par le BR",$contenu);

		$DB_valid->query("DELETE FROM valid_mailpromo WHERE mail_id='{$temp[1]}'") ;

	?>
		<warning><p>Suppression d'un mail promo</p></warning>
	<?
	}
}


//===============================

$DB_valid->query("SELECT v.mail_id,v.stamp, v.titre, v.mail, e.nom, e.prenom, e.surnom, e.promo, e.mail, e.login FROM valid_mailpromo as v INNER JOIN trombino.eleves as e USING(eleve_id)");
while(list($id,$date,$titre,$mailpromo,$nom, $prenom, $surnom, $promo,$mail,$login) = $DB_valid->next_row()) {
	if (empty($mail)) $mail="$login@poly" ;
?>
	<commentaire>
		<p><u>FROM</u>: <?php  echo "$prenom $nom &lt;$mail&gt; " ?></p>
		<p>Posté le 
			<?php  echo substr($date,6,2) ."/".substr($date,4,2) ."/".substr($date,2,2)." à ".substr($date,8,2).":".substr($date,10,2) ?>
		</p>
	</commentaire>
	<cadre titre="Mail Promo : <?php  echo $titre ?>">
			<?php echo $mailpromo ?>
	</cadre>
<?

// Zone de saisie de l'affiche
?>

	<formulaire id="mailpromo_<? echo $id ?>" titre="Mail Promo" action="admin/valid_mailpromo.php">
		<champ id="titre" titre="Sujet " valeur="<?  echo $titre ;?>"/>
		<hidden id="from"  valeur="<? echo "$prenom $nom &lt;$mail&gt; " ?>"/>
		<zonetext id="mail" titre="Mail " valeur="<?  echo $mailpromo ;?>"/>
		<choix titre="Promo" id="promo" type="combo" valeur="<? if (isset($_POST['promo'])) echo  $_POST['promo'] ;?>">
		<?
			$DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus'");
			list($promo_temp) = $DB_web->next_row() ;
			$promo1 = $promo_temp ;
			$promo2 = $promo_temp-1 ;
				echo "<option titre='$promo1 et $promo2' id='' />" ;
				echo "<option titre='$promo1' id='$promo1' />" ;
				echo "<option titre='$promo2' id='$promo2' />" ;
		?>
		</choix>
				
		<bouton id='modif_<? echo $id ?>' titre="Modifier"/>
		<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Envoyer ce mail promo ?')"/>
		<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('Supprimer ce mail promo et ne pas l'envoyer ?')"/>
	</formulaire>
<?
}

?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
