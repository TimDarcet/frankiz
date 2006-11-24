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
	Page d'envoi des mails promo.
	
	$Id$
	
*/

set_time_limit(0) ;
require_once "../include/global.inc.php";
//require_once "../include/mail.inc.php";
//require_once "../include/mysql.inc.php";
require_once "../include/wiki.inc.php";

demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')&&!verifie_permission('kes'))
	acces_interdit();

//
// ON NE PAS LES ENTETES !!!! C'est normal !
//
//====================================================
// Procedure d'envoie de masse
//
$DB_valid->query("SELECT titre, mail FROM valid_mailpromo WHERE mail_id={$_REQUEST['id']}");
list($titre, $mail) = extdata_stripslashes($DB_valid->next_row());

$log = "" ;
$cnt = 0 ;

$DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus'");
list($promo_temp) = $DB_web->next_row() ;

if ($_REQUEST['promo'] == '') {
	$to = " promo=$promo_temp OR promo=".($promo_temp-1) ;
	$titre_mail="Mail BiPromo :" ;
} else {
	$titre_mail="Mail Promo :" ;
	$to = " promo=".intval($_REQUEST['promo']);
}

$mail_contenu = wikiVersXML($mail,true)  ; // On met true pour dire que c'est du HTML qu'on récupere
//
// Envoi du mail à proprement parler ...
//-------------------------------------------------------------------------

	$DB_trombino->query("SELECT eleve_id,nom,prenom,promo FROM eleves WHERE ".$to." ORDER BY eleve_id ASC") ;

	// On crée le fichier de log qui va bien
	$fich_log = BASE_DATA."mailpromo/mail.log".intval($_REQUEST['id']); 
	touch($fich_log) ;
	
	//$from = str_replace("&gt;",">",str_replace("&lt;","<",$_REQUEST['sender'])) ;
	//echo base64_decode($_REQUEST['sender'])."<br>" ;
	$from = "=?UTF-8?b?".base64_encode(html_entity_decode(base64_decode(gpc_stripslashes($_REQUEST['sendername']))))."?= <".html_entity_decode(base64_decode(gpc_stripslashes($_REQUEST['sendermail']))).">" ; 
	exec("echo \"".$mail_contenu."\" >>".$fich_log);
	while(list($eleve_id,$nom,$prenom,$promo) = extdata_stripslashes($DB_trombino->next_row()) ) {
		$DB_trombino->push_result() ;
		couriel($eleve_id, $titre_mail." ".$titre,$mail_contenu, STRINGMAIL_ID, $from) ;
		//print $from."<br>" ;
		
		$DB_trombino->pop_result() ;
		print("Envoi à $nom $prenom ($promo) [".($cnt+1)."]<br>") ;
		flush() ;
		$cnt ++ ;
		exec("echo \"Mail envoyé à $nom $prenom ($eleve_id)\n\" >>".$fich_log) ;
		usleep(100000); // Attends 100 millisecondes
		//break ;////////////////////////////////////////////////////////////////////////////////////
	}
	
	// fin de la procédure
	
	
	$DB_valid->query("DELETE FROM valid_mailpromo WHERE mail_id='".intval($_REQUEST['id'])."'") ;
?>
