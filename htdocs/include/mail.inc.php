<?php
/*
	Fichier gérant les différents mails
	
	$Log $

	
*/
/*
	envoie un mail
*/
function couriel($eleve_id,$titre,$contenu) {
	global $DB_trombino ;
	$DB_trombino->query("SELECT nom,prenom,mail,login FROM eleves WHERE eleve_id='$eleve_id'") ;
	list($nom, $prenom, $mail, $login) = $DB_trombino->next_row()  ;
	if (empty($mail)) $mail=$login."@poly.polytechnique.fr" ;
	mail("$prenom $nom <$mail>",$titre,$contenu) ;
}




?>