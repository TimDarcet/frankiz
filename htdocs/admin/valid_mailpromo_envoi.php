<?
set_time_limit(0) ;
require_once "../include/global.inc.php";
//require_once "../include/mail.inc.php";
//require_once "../include/mysql.inc.php";
require_once "../include/wiki.inc.php";

demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/gestion/");
//
// ON NE PAS LES ENTETES !!!! C'est normal !
//
//====================================================
// Procedure d'envoie de masse
//
$DB_valid->query("SELECT titre, mail FROM valid_mailpromo WHERE mail_id={$_REQUEST['id']}");
list($titre, $mail) = $DB_valid->next_row() ;

$log = "" ;
$cnt = 0 ;

$DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus'");
list($promo_temp) = $DB_web->next_row() ;

if ($_REQUEST['promo'] == 2) {
	$to = " promo=$promo_temp OR promo=".($promo_temp-1) ;
	$titre_mail="Mail BiPromo :" ;
} else {
	$titre_mail="Mail Promo :" ;
	$to = " promo=".$_REQUEST['promo'] ;
}

$mail_contenu = wikiVersXML($mail,true)  ; // On met true pour dire que c'est du HTML qu'on récupere

//
// Envoi du mail à propremeent parler ...
//-------------------------------------------------------------------------

	$DB_trombino->query("SELECT eleve_id,nom,prenom,promo FROM eleves WHERE ".$to." ORDER BY nom ASC") ;
	
	// On crée le fichier de log qui va bien
	$fich_log = BASE_DATA."mailpromo/mail.log.".$temp[1] ; 
	touch($fich_log) ;
	
	//$from = str_replace("&gt;",">",str_replace("&lt;","<",$_REQUEST['sender'])) ;
	//echo base64_decode($_REQUEST['sender'])."<br>" ;
	$from = html_entity_decode(base64_decode($_REQUEST['sender'])) ; 
	exec("echo \"".$mail_contenu."\" >>".$fich_log) ;
	
	while(list($eleve_id,$nom,$prenom,$promo) = $DB_trombino->next_row() ) {
		couriel($eleve_id, $titre_mail." ".$_POST['titre'],$mail_contenu, STRINGMAIL_ID, $from) ;
		//print $from."<br>" ;
		//couriel("5059", $titre_mail." ".$titre, $mail_contenu, STRINGMAIL_ID, $from) ;
		print("Envoi à $nom $prenom ($promo) .... DONE<br>") ;
		flush() ;
		$cnt ++ ;
		exec("echo \"Mail envoyé à $nom $prenom ($eleve_id)\n\" >>".$fich_log) ;
		usleep(100000); // Attends 100 millisecondes
		//break ;////////////////////////////////////////////////////////////////////////////////////
	}
	
	// fin de la procédure
	
	
	$DB_valid->query("DELETE FROM valid_mailpromo WHERE mail_id='{$temp[1]}'") ;
?>