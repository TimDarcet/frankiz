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
function couriel($eleve_id,$titre,$contenu,$sender="le BR <br@frankiz.polytechnique.fr>") {
	if ($eleve_id==WEBMESTRE_ID) {
		$prenom = "Webmestre de Frankiz" ;
		$nom = "" ;
		$adresse = MAIL_WEBMESTRE ;
	} else {
		global $DB_trombino;
		$DB_trombino->query("SELECT nom,prenom,mail,login FROM eleves WHERE eleve_id=$eleve_id") ;
		list($nom, $prenom, $adresse, $login) = $DB_trombino->next_row()  ;
		if(empty($adresse)) $adresse=$login."@poly.polytechnique.fr" ;
	}
	
	$mail = new Mail($sender,"$prenom $nom <$adresse>",$titre,true);
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
	function addPart($type,$encoding,$value) {
		if ($this->boundary) {
			$this->body .= "--{$this->boundary}\n".
						   "Content-Type: $type\n".
						   "Content-Transfer-Encoding: $encoding\n\n".
						   "$value\n";
		} else {
			echo "<b>Erreur : addPart s'applique uniquement aux messages multipart!</b>";
		}
	}
	
	function addPartText($text,$charset="iso-8859-1") {
		$this->addPart("text/plain; charset=$charset","8bit", $text);
	}

/*	function addPartRichText($text,$charset="iso-8859-1") {
		$this->addPart("text/enriched; charset=$charset","8bit", $tex);
	}*/ //Kikx se demande si ça sert vraiment ? 
	
	function addPartHtml($html,$charset="iso-8859-1") {
		$this->addPart("text/html; charset=$charset","8bit", $html);
	}
	
	// définission du contenu d'un mail non multipart
/*	function setBody($text) {
		if (!$this->boundary) {
			$this->body = $text;
		} else {
			echo "<b>Erreur : setBody s'applique uniquement aux messages inline!</b>";
		}
	}*/ // On dit que l'on envoie que des mail en HTML ... donc en multipart (kikx)
	
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

require_once BASE_LOCAL."/include/mail_contenu.inc.php" ;

?>
