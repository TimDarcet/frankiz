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
	Classe permettant de gérer la création et les envois de mails.
	Support les mails en mime multipart.
	
	$Log$
	Revision 1.15  2004/12/17 18:37:03  pico
	Mail admin@windows + page de demande de licence windows

	Revision 1.14  2004/12/15 19:26:09  kikx
	Les mails promo devrait fonctionner now ...
	
	Revision 1.13  2004/12/15 13:03:32  pico
	Ajout mail trombinomen
	
	Revision 1.12  2004/12/14 22:17:32  kikx
	Permet now au utilisateur de modifier les Faqqqqqqqqqqqqqqqq :)
	
	Revision 1.11  2004/12/14 00:27:40  kikx
	Pour que le FROM des mails de validation soit au nom du mec qui demande la validation... (qu'est ce que je ferai pas pour les TOS :))
	
	Revision 1.10  2004/11/16 18:32:34  schmurtz
	Petits problemes d'interpretation de <note> et <commentaire>
	
	Revision 1.9  2004/11/16 12:17:25  schmurtz
	Deplacement des skins de trombino.eleves vers frankiz.compte_frankiz
	
	Revision 1.8  2004/10/31 21:29:56  kikx
	Mise a jour du mail promo grace a la librairie de Schmurtz
	
	Revision 1.7  2004/10/29 15:41:48  kikx
	Passage des mail en HTML pour les ip
	
	Revision 1.6  2004/10/29 14:38:37  kikx
	Mise en format HTML des mails pour les validation de la qdj, des mails promos, et des annonces
	
	Revision 1.5  2004/10/29 14:09:10  kikx
	Envoie des mail en HTML pour la validation des affiche
	
	Revision 1.4  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.3  2004/10/16 01:47:45  schmurtz
	Bug dans l'envoi d'un mail
	
	Revision 1.2  2004/10/16 01:20:12  schmurtz
	Utilisation d'une classe dediee a l'envoie de mails et permettant de gerer un
	peu les contenus MIME.
	TODO : inclure dans ce fichier le code de kikx actuellement situe dans la
	gestion des mails promos
	
*/

// envoi d'un mail à un élève
function couriel($eleve_id,$titre,$contenu,$sender_id=BR_ID,$sender_string="") {

	// On gère l'envoyeur !
	
	if ($sender_id==WEBMESTRE_ID) {
		$sender = "Webmestre de Frankiz <".MAIL_WEBMESTRE.">" ;
	} else if ($sender_id==QDJMASTER_ID) {
		$sender = "QDJmaster de Frankiz <".MAIL_QDJMASTER.">" ;
	} else if ($sender_id==FAQMESTRE_ID) {
		$sender = "FAQmestre de Frankiz <".MAIL_FAQMESTRE.">" ;
	} else if ($sender_id==BR_ID) {
		$sender = "Le BR <".MAIL_BR.">" ;
	} else if ($sender_id==PREZ_ID) {
		$sender = "Président du BR <".MAIL_PREZ.">" ;
	} else if ($sender_id==ROOT_ID) {
		$sender = "Root du BR <".MAIL_ROOT.">" ;
	} else if ($sender_id==TROMBINOMEN_ID) {
		$sender = "Trombino <".MAIL_TROMBINOMEN.">" ;
	} else if ($sender_id==WINDOWS_ID) {
		$sender = "Admins Windows <".MAIL_WINDOWS.">" ;
	} else if ($sender_id==STRINGMAIL_ID) {
		$sender = $sender_string ;
	} else { // C'est une personne physique
		global $DB_trombino;
		$DB_trombino->query("SELECT nom,prenom,mail,login FROM eleves WHERE eleve_id=$sender_id") ;
		list($nom1, $prenom1, $adresse1, $login1) = $DB_trombino->next_row()  ;
		if(empty($adresse1)) $adresse1=$login1."@poly.polytechnique.fr" ;
		$sender = "$prenom1 $nom1 <$adresse1>" ;
	}
	
	// On gere le destinataire
	
	if ($eleve_id==WEBMESTRE_ID) {
		$prenom = "Webmestre de Frankiz" ;
		$nom = "" ;
		$adresse = MAIL_WEBMESTRE ;
	} else if ($eleve_id==QDJMASTER_ID) {
		$prenom = "QDJmaster de Frankiz" ;
		$nom = "" ;
		$adresse = MAIL_QDJMASTER ;
	} else if ($eleve_id==FAQMESTRE_ID) {
		$prenom = "FAQmestre de Frankiz" ;
		$nom = "" ;
		$adresse = MAIL_FAQMESTRE ;
	} else if ($eleve_id==PREZ_ID) {
		$prenom = "Président du BR" ;
		$nom = "" ;
		$adresse = MAIL_PREZ ;
	} else if ($eleve_id==ROOT_ID) {
		$prenom = "Root du BR" ;
		$nom = "" ;
		$adresse = MAIL_ROOT ;
	} else if ($eleve_id==TROMBINOMEN_ID) {
                $prenom = "Trombino" ;
                $nom = "" ;
                $adresse = MAIL_TROMBINOMEN ;		
	} else {
		global $DB_trombino;
		$DB_trombino->query("SELECT nom,prenom,mail,login FROM eleves WHERE eleve_id=$eleve_id") ;
		list($nom, $prenom, $adresse, $login) = $DB_trombino->next_row()  ;
		if(empty($adresse)) $adresse=$login."@poly.polytechnique.fr" ;
	}
	
	$mail = new Mail($sender,"$prenom $nom <$adresse>",html2plain($titre),true);
	$mail->addPartText(html2plain($contenu));
	$mail->addPartHtml($contenu);
	$mail->send();
}

// convertit un message HTML en un message plaintext.
function html2plain($html) {
	$string = str_replace ( '<br>', "\n", $html );
	$string = str_replace ( '</p>', "\n", $string );
	
	$string = str_replace ( '&amp;', '&', $string );
	$string = str_replace ( '&#039;', "'", $string );
	$string = str_replace ( '&apos;', "'", $string );
	$string = str_replace ( '&quot;', '\"', $string );
	$string = str_replace ( '&lt;', '<', $string );
	$string = str_replace ( '&gt;', '>', $string );

	$trans_tbl = get_html_translation_table (HTML_ENTITIES);

	$trans_tbl2 = array_flip ($trans_tbl);
	$ret = strtr ($string, $trans_tbl2);
	return trim(strip_tags($ret));
}

// convertit un message plaintext en un message HTML avec des liens clickables.
/*function plain2html($text) {
	$html = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]",
	"<a href=\"\\0\">\\0</a>", $text);
	$html = nl2br($html);
	return "<html><body>\n$html\n</body></html>";
}*/ //Utile ? (kikx)

class Mail {
	var $header, $body;
	var $from, $to, $cc, $bcc;
	var $subject, $boundary;
	
	// Création d'un nouveau mail
	function Mail($from, $to, $subject, $multipart=false, $cc="", $bcc="") {
		$this->from = $from;
		$this->to = $to;
		$this->cc = $cc;
		$this->bcc = $bcc;
		$this->subject = $subject;
		$this->body = "";
		$this->header = "X-Mailer: PHP/" . phpversion()."\n".
						"Mime-Version: 1.0\n";
		if($multipart) {
			$this->boundary= "-=next_part_".uniqid("")."=-";
			$this->header .= "Content-Type: multipart/alternative;\n".
							 "   boundary=\"{$this->boundary}\"\n";
		} else {
			$this->boundary= "";
			$this->header .= "Content-Type: text/plain; charset=iso-8859-1\n".
							 "Content-Disposition: inline\n".
							 "Content-Transfer-Encoding: 8bit\n";
	    }
	}
	
	// Ajout d'entêtes
	function addHeader($text) {
		$this->header .= "$text\n";
	}
	
	// Gestion des mails multipart
	function addPart($type,$value,$encoding="8bit",$charset="iso-8859-1") {
		if ($this->boundary) {
			$this->body .= "--{$this->boundary}\n".
						   "Content-Type: $type\n".
						   "Content-Transfer-Encoding: $encoding; charset=$charset\n\n".
						   "$value\n";
		} else {
			echo "<b>Erreur : addPart s'applique uniquement aux messages multipart!</b>";
		}
	}
	
	function addPartText($text,$charset="iso-8859-1") {
		$this->addPart("text/plain",$text,"8bit",$charset);
	}

	function addPartRichText($text,$charset="iso-8859-1") {
		$this->addPart("text/enriched",$text,"8bit",$charset);
	}
	
	function addPartHtml($html,$charset="iso-8859-1") {
		$this->addPart("text/html",$html,"8bit",$charset);
	}
	
	// définission du contenu d'un mail non multipart
	function setBody($text) {
		if (!$this->boundary) {
			$this->body = $text;
		} else {
			echo "<b>Erreur : setBody s'applique uniquement aux messages inline!</b>";
		}
	}
	
	// pour les envois multiples du même mail à plusieurs personnes
	function setTo($to) {
		$this->to = $to;
	}
	
	// envoie d'un mail
	function send() {
		$this->header .= "From: {$this->from}\n";
		if($this->to) $this->header .= "To: {$this->to}\n";
		if($this->cc) $this->header .= "Cc: {$this->cc}\n";
		$this->header .= "Subject: {$this->subject}\n";
		$this->header .= "\n";
		
		if($this->boundary)
			$this->body .= "--{$this->boundary}--\n";
		
		$fp = popen('/usr/sbin/sendmail -oi -f '.escapeshellarg($this->from).' '.escapeshellarg($this->to).' '.escapeshellarg($this->cc).' '.escapeshellarg($this->bcc),'w');
		if($fp) {
			if(fwrite($fp, $this->header) == -1) return false;
			if(fwrite($fp, $this->body) == -1) return false;
			if(pclose($fp) == 0) return true;
		}
		return false;
	}
}

?>
