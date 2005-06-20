<?
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
	
	$Log$
	Revision 1.10  2005/06/20 14:25:52  pico
	Pour avoir un encodage correct sur les entetes des mails promo

	Revision 1.9  2005/04/13 17:09:58  pico
	Passage de tous les fichiers en utf8.
	
	Revision 1.8  2005/01/20 13:07:02  pico
	On envoit par ordre d'id d'élève (plus facile de voir si un id a été sauté)
	Devrait mettre ça dans un nouveau fichier de log maintenant.
	
	Revision 1.7  2005/01/17 20:15:38  pico
	Mail promo pour les kessiers
	
	Revision 1.6  2005/01/13 17:10:58  pico
	Mails de validations From le validateur qui va plus ou moins bien
	
	Revision 1.5  2004/12/17 17:25:08  schmurtz
	Ajout d'une belle page d'erreur.
	
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
list($titre, $mail) = $DB_valid->next_row() ;

$log = "" ;
$cnt = 0 ;

$DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus'");
list($promo_temp) = $DB_web->next_row() ;

if ($_REQUEST['promo'] == '') {
	$to = " promo=$promo_temp OR promo=".($promo_temp-1) ;
	$titre_mail="Mail BiPromo :" ;
} else {
	$titre_mail="Mail Promo :" ;
	$to = " promo=".$_REQUEST['promo'] ;
}

$mail_contenu = wikiVersXML($mail,true)  ; // On met true pour dire que c'est du HTML qu'on récupere

//
// Envoi du mail à proprement parler ...
//-------------------------------------------------------------------------

	$DB_trombino->query("SELECT eleve_id,nom,prenom,promo FROM eleves WHERE ".$to." ORDER BY eleve_id ASC") ;
	
	// On crée le fichier de log qui va bien
	$fich_log = BASE_DATA."mailpromo/mail.log.{$_REQUEST['id']}"; 
	touch($fich_log) ;
	
	//$from = str_replace("&gt;",">",str_replace("&lt;","<",$_REQUEST['sender'])) ;
	//echo base64_decode($_REQUEST['sender'])."<br>" ;
	$from = "=?UTF-8?b?".base64_encode(html_entity_decode(base64_decode($_REQUEST['sendername'])))."?= <".html_entity_decode(base64_decode($_REQUEST['sendermail'])).">" ; 
	exec("echo \"".$mail_contenu."\" >>".$fich_log) ;
	while(list($eleve_id,$nom,$prenom,$promo) = $DB_trombino->next_row() ) {
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
	
	
	$DB_valid->query("DELETE FROM valid_mailpromo WHERE mail_id='{$_REQUEST['id']}'") ;
?>