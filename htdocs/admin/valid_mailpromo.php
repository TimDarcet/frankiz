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
	Page qui permet aux admins de valider un mail promo
	
	$Log$
	Revision 1.12  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site

	Revision 1.11  2004/10/20 23:21:39  schmurtz
	Creation d'un element <html> qui permet d'afficher du html brute sans verification
	C'est ce qui est maintenant utilise dans les annonces/cadres
	
	Revision 1.10  2004/10/13 21:21:14  kikx
	Le rend opérationnel
	
	Revision 1.9  2004/10/13 21:19:52  kikx
	Rajout de bug fix
	
	Revision 1.8  2004/10/13 20:45:16  kikx
	Mises en place de log pour l'envoie des mail promos
	
	Revision 1.7  2004/10/13 19:36:44  kikx
	Correction de la page mail promo pour le text plain
	
	Revision 1.6  2004/10/12 18:23:15  kikx
	On retire le petit logo de merde dans les mails promo
	
	Revision 1.5  2004/10/10 20:13:18  kikx
	Bug fix pour les envoie de mail promo
	-> n'envoie plus en texte brut les truc comme &apo; &nbps; ...
	
	Revision 1.4  2004/10/10 20:10:05  kikx
	Ne sert plus a rien
	
	Revision 1.3  2004/10/06 21:29:29  kikx
	Mail promo != mail bi-promo
	
	Revision 1.2  2004/10/06 19:29:53  kikx
	La page d'envoi de mail promo est terminéééééééééééééééééééééééé
	
	Revision 1.1  2004/10/06 14:12:27  kikx
	Page de mail promo quasiment en place ...
	envoie en HTML ...
	Page pas tout a fait fonctionnel pour l'instant
	

	
*/
set_time_limit(0) ;
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
<h1>Validation des mails promos</h1>

<?

if ((isset($_POST['promo']))&&($_POST['promo'] == "")) {
	$titre_mail = "Mail Bi-Promo :" ;
} else {
	$titre_mail = "Mail Promo :" ;
}
		
// On traite les différents cas de figure d'enrigistrement et validation d'affiche :)

// Enregistrer ...

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;


	if (($temp[0]=='modif')||($temp[0]=='valid')) {
		$DB_valid->query("UPDATE valid_mailpromo SET titre='{$_POST['titre']}', mail='{$_POST['mail']}', promo='{$_POST['promo']}' WHERE mail_id='{$temp[1]}'");	
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
	// Procedure d'envoie de masse
	//---------------------------------------------------------------------------------------------------------------------
		
	        echo "<p>" ;
		$log = "" ;
		$cnt = 0 ;
		
		$DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus'");
		list($promo_temp) = $DB_web->next_row() ;

		if ($_POST['promo'] == "") {
			$to = " promo=$promo_temp OR promo=".($promo_temp-1) ;
		} else {
			$to = " promo=".$_POST['promo'] ;
		}
		
		//=================================
		// Constuction du mail à propremeent parler ...
		//-------------------------------------------------------------------------
		
		function unhtmlentities ($string)  {
			$string = str_replace ( '&amp;', '&', $string );
			$string = str_replace ( '&#039;', "'", $string );
			$string = str_replace ( '&apos;', "'", $string );
			$string = str_replace ( '&quot;', '\"', $string );
			$string = str_replace ( '&lt;', '<', $string );
			$string = str_replace ( '&gt;', '>', $string );
		
			$trans_tbl = get_html_translation_table (HTML_ENTITIES);

			$trans_tbl2 = array_flip ($trans_tbl);
			$ret = strtr ($string, $trans_tbl2);
			
			return $ret ;
			//return  preg_replace('/\&\#([0-9]+)\;/me',"chr('\\1')",$ret);
		}
		
		// Message que l'on retravaille
		$mail_contenu = str_replace("&gt;",">",str_replace("&lt;","<",$_POST['mail'])) ;
		
		// Création du délimiteurs
		$limite = md5(uniqid (rand()));
		$limite2 = md5(uniqid (rand()));
					
		//Le message en texte simple pour les navigateurs qui n'acceptent pas le HTML
		$texte = "This is a multi-part message in MIME format.\n";
		$texte .= "Ceci est un message est au format MIME.\n\n";
		
		$texte .= "------$limite\n";
		$texte .= "Content-Type: multipart/alternative;\n";
		$texte .= " boundary=\"----$limite2\"\n\n";
		
		$texte .= "------$limite2\n";
		$texte .= "Content-Type: text/plain; charset=\"iso-8859-1\"\n";
		$texte .= "Content-Transfer-Encoding: 8bit\n\n";
		$texte .= strip_tags(unhtmlentities(nl2br($mail_contenu)));
		$texte .= "\n\n";
		//Le message en texte HTML pour les navigateurs qui acceptent  le HTML
		$texte .= "------$limite2\n";
		$texte .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
		$texte .= "Content-Transfer-Encoding: 8bit\n\n";
		$texte .="<html>\n\t<head>\n\t</head>\n<body font-size:12pt font-family: arial>\n" ;
		$texte .= $mail_contenu;
//		$texte .= "<center><img src=\"cid:4923555B-0D28-4533-B917-07177C51A263\" alt=\"Validation par le BR\"></center>" ;
		$texte .="\n\t</body>\n</html>" ;
		$texte .= "\n\n\n";
		
		$texte .= "------$limite2--\n\n";
		
		// JE GARDE CETTE PARTIE COMMENTEE CAR SI UN JOUR ON VEUX ENVOYER DES MAILS AVEC DES PIECES JOINTES C'EST IMPLEMENTE
		
		//le fichier si il existe ...
//		$fichier = 'Logo.png' ;
		
//		$texte .= "------$limite\n";
//		$texte .= "Content-Type: image/png; name=\"$fichier\"\n";
//		$texte .= "Content-Transfer-Encoding: base64\n";
//		$texte .= "Content-ID: <4923555B-0D28-4533-B917-07177C51A263>\n";
//		$texte .= "Content-Disposition: attachment; filename=\"$fichier\"\n\n";
//	
//		$fd = fopen( $fichier, "r" );
//		$contenu = fread( $fd, filesize( $fichier ) );
//		fclose( $fd );
//		$texte .= chunk_split(base64_encode($contenu));

//		$texte .= "\n\n\n------$limite--\n";			
		
		// On met en place les headers
		$sender = unhtmlentities($_POST['from']) ;
		$headers = "From: ".$sender."\r\nX-Mailer: PHP/" . phpversion()."\r\n" ;
		$headers .= "Date: ".date("l j F Y, G:i")."\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: multipart/related; type=multipart/alternative ;\n";
		$headers .= " boundary=\"----$limite\"";

	//=================================
	// Envoi du mail à propremeent parler ...
	//-------------------------------------------------------------------------
	
		$DB_trombino->query("SELECT login FROM eleves WHERE ".$to) ;
		
		// On crée le fichier de log qui va bien
		$fich_log = BASE_LOCAL."/../data/mailpromo/mail.log.".$temp[1] ; 
		touch($fich_log) ;
			
		exec("echo \"Subjet : ".$_POST['titre']."\n\" >>".$fich_log) ;
		exec("echo \"".$headers."\n\" >>".$fich_log) ;
		exec("echo \"".$texte."\" >>".$fich_log) ;
		
		while(list($login) = $DB_trombino->next_row() ) {
			$mail_envoie = $login."@poly" ;
			//$mail_envoie = "gruson@poly" ;
			
					
			if (mail($mail_envoie, $titre_mail." ".$_POST['titre'],$texte , $headers)){
				$cnt ++ ;
				exec("echo \"Mail envoyé à ".$mail_envoie."\n\" >>".$fich_log) ;
 				usleep(200000); // Attends 200 millisecondes
			} else {
				$log .= "Erreur lors de l'envoi vers ".$mail ;
			}
//			if ($cnt==1) break ;//================= A SUPPRIMER
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

$DB_valid->query("SELECT v.mail_id,v.stamp, v.titre,v.promo, v.mail, e.nom, e.prenom, e.surnom, e.promo, e.mail, e.login FROM valid_mailpromo as v INNER JOIN trombino.eleves as e USING(eleve_id)");
while(list($id,$date,$titre,$promo_mail,$mailpromo,$nom, $prenom, $surnom, $promo,$mail,$login) = $DB_valid->next_row()) {
	if (empty($mail)) $mail="$login@poly" ;
?>
	<commentaire>
		<p><u>FROM</u>: <?php  echo "$prenom $nom &lt;$mail&gt; " ?></p>
		<p>Posté le 
			<?php  echo substr($date,6,2) ."/".substr($date,4,2) ."/".substr($date,2,2)." à ".substr($date,8,2).":".substr($date,10,2) ?>
		</p>
	</commentaire>
	<cadre titre="<?php  echo $titre_mail." ".$titre ?>">
			<html><?php echo $mailpromo ?></html>
	</cadre>
<?

// Zone de saisie de l'affiche
?>

	<formulaire id="mailpromo_<? echo $id ?>" titre="Mail Promo" action="admin/valid_mailpromo.php">
		<champ id="titre" titre="Sujet " valeur="<?  echo $titre ;?>"/>
		<?
			if ((!isset($_POST['from']))||((isset($temp))&&($temp[1]!=$id)))
				$_POST['from'] = "$prenom $nom &lt;$mail&gt; " ;
		?>
		<champ titre="From " id="from"  valeur="<? echo  $_POST['from'] ?>"/>
		<zonetext id="mail" titre="Mail " valeur="<?  echo $mailpromo ;?>"/>
		<choix titre="Promo" id="promo" type="combo" valeur="<?echo  $promo_mail ;?>">
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
