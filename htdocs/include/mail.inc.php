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
	
	$Id$

*/

// envoi d'un mail à un élève
function couriel($eleve_id,$titre,$contenu,$sender_id=WEBMESTRE_ID,$sender_string="") {

	// On gère l'envoyeur !
	
	
	if ($sender_id==WEBMESTRE_ID) {
		$prenom1 = "Webmestre de Frankiz" ;
		$nom1 = "" ;
		$adresse1 = MAIL_WEBMESTRE ;
	} else if ($sender_id==QDJMASTER_ID) {
		$prenom1 = "QDJmaster de Frankiz" ;
		$nom1 = "" ;
		$adresse1 = MAIL_QDJMASTER ;
	} else if ($sender_id==FAQMESTRE_ID) {
		$prenom1 = "FAQmestre de Frankiz" ;
		$nom1 = "" ;
		$adresse1 = MAIL_FAQMESTRE ;
	} else if ($sender_id==PREZ_ID) {
		$prenom1 = "Président du BR" ;
		$nom1 = "" ;
		$adresse1 = MAIL_PREZ ;
	} else if ($sender_id==ROOT_ID) {
		$prenom1 = "Root du BR" ;
		$nom1 = "" ;
		$adresse1 = MAIL_ROOT ;
	} else if ($sender_id==TROMBINOMEN_ID) {
		$prenom1 = "Trombino" ;
		$nom1 = "" ;
		$adresse1 = MAIL_TROMBINOMEN ;
	} else if ($sender_id==MAILPROMO_ID) {
		$prenom1 = "Mail Promo";
		$nom1 = "" ;
		$adresse1 = MAIL_MAILPROMO;
	} else if ($sender_id==WINDOWS_ID) {
		$prenom1 = "Admins Windows";
		$nom1= "";
		$adresse1 = MAIL_WINDOWS ;
	} else if ($sender_id==BR_ID) {
		$prenom1 = "Le BR";
		$nom1= "";
		$adresse1 = MAIL_BR ;
	} else { // C'est une personne physique
		global $DB_trombino;
		$DB_trombino->query("SELECT nom,prenom,mail,login FROM eleves WHERE eleve_id=$sender_id") ;
		list($nom1, $prenom1, $adresse1, $login1) = $DB_trombino->next_row()  ;
		if(empty($adresse1)) $adresse1=$login1."@poly.polytechnique.fr" ;
		$prenom1 = str_replace(array('&amp;','&lt;','&gt;','&apos;','&quot;',''),array('&','<','>','\'','"','\\'),$prenom1);
		$nom1 = str_replace(array('&amp;','&lt;','&gt;','&apos;','&quot;',''),array('&','<','>','\'','"','\\'),$nom1);
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
	} else if ($eleve_id==MAILPROMO_ID) {
		$prenom = "Mail Promo";
		$nom = "" ;
		$adresse = MAIL_MAILPROMO;
	} else if ($eleve_id==WINDOWS_ID) {
		$prenom = "Admins Windows";
		$nom= "";
		$adresse = MAIL_WINDOWS ;
	} else {
		global $DB_trombino;
		$DB_trombino->query("SELECT nom,prenom,mail,login FROM eleves WHERE eleve_id=$eleve_id") ;
		list($nom, $prenom, $adresse, $login) = $DB_trombino->next_row();
		if(empty($adresse)) $adresse=$login."@poly.polytechnique.fr" ;
		$prenom = str_replace(array('&amp;','&lt;','&gt;','&apos;','&quot;',''),array('&','<','>','\'','"','\\'),$prenom);
		$nom = str_replace(array('&amp;','&lt;','&gt;','&apos;','&quot;',''),array('&','<','>','\'','"','\\'),$nom);
	}
	$mail = new Mail( ($sender_id!=STRINGMAIL_ID)?"=?UTF-8?b?".base64_encode("$prenom1 $nom1")."?= <$adresse1>":$sender_string  , "=?UTF-8?b?".base64_encode("$prenom $nom")."?= <$adresse>" , html2plain($titre),true,"", (($sender_id<0) && ($sender_id!=$eleve_id) && ($sender_id!=STRINGMAIL_ID))?"=?UTF-8?b?".base64_encode("$prenom1 $nom1")."?= <$adresse1>":"");
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

// Converti les entetes:
function header_encode($text){
	return "=?UTF-8?b?".base64_encode($text)."?=";
}

// convertit un message plaintext en un message HTML avec des liens clickables.
/*function plain2html($text) {
	$html = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]",
	"<a href=\"\\0\">\\0</a>", $text);
	$html = nl2br($html);
	return "<html><body>\n$html\n</body></html>";
}*/ //Utile ? (kikx)

class Mail {
	private $header, $body;
	private $from, $to, $cc, $bcc;
	private $subject, $boundary;
	
	// Création d'un nouveau mail
	function Mail($from, $to, $subject, $multipart=false, $cc="", $bcc="") {
		$this->from = $from;
		$this->to = $to;
		$this->cc = $cc;
		$this->bcc = $bcc;
		$this->subject = "=?UTF-8?b?".base64_encode($subject)."?=";
		$this->body = "";
		$this->header = "X-Mailer: PHP/" . phpversion()."\n".
						"Mime-Version: 1.0\n";
		if($multipart) {
			$this->boundary= "-=next_part_".uniqid("")."=-";
			$this->header .= "Content-Type: multipart/alternative;\n".
							 "   boundary=\"{$this->boundary}\"\n";
		} else {
			$this->boundary= "";
			$this->header .= "Content-Type: text/plain; charset=\"utf-8\"\n".
							 "Content-Disposition: inline\n".
							 "Content-Transfer-Encoding: 8bit\n";
	    }
	}
	
	// Ajout d'entêtes
	function addHeader($text) {
		$this->header .= "=?UTF-8?b?".base64_encode($text)."?=";
	}
	
	// Gestion des mails multipart
	function addPart($type,$value,$encoding="8bit",$charset="\"utf-8\"") {
		if ($this->boundary) {
			$this->body .= "--{$this->boundary}\n".
						   "Content-Type: $type; charset=$charset\n".
						   "Content-Transfer-Encoding: $encoding\n".
						   "$value\n";
		} else {
			echo "<b>Erreur : addPart s'applique uniquement aux messages multipart!</b>";
		}
	}
	
	function addPartText($text,$charset="\"utf-8\"") {
		$this->addPart("text/plain",$text,"8bit",$charset);
	}

	function addPartRichText($text,$charset="\"utf-8\"") {
		$this->addPart("text/enriched",$text,"8bit",$charset);
	}
	
	function addPartHtml($html,$charset="\"utf-8\"") {
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
