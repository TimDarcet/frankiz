<?php
/*
	Classe permettant de gérer la création et les envois de mails.
	Support les mails en mime multipart.
	
	$Log$
	Revision 1.2  2004/10/16 01:20:12  schmurtz
	Utilisation d'une classe dediee a l'envoie de mails et permettant de gerer un
	peu les contenus MIME.
	TODO : inclure dans ce fichier le code de kikx actuellement situe dans la
	gestion des mails promos

*/

// envoi d'un mail à un élève
function couriel($eleve_id,$titre,$contenu) {
	global $DB_trombino;
	$DB_trombino->query("SELECT nom,prenom,mail,login FROM eleves WHERE eleve_id='$eleve_id'") ;
	list($nom, $prenom, $adresse, $login) = $DB_trombino->next_row()  ;
	if(empty($adresse)) $adresse=$login."@poly.polytechnique.fr" ;
	
	$mail = new Mail("Binet Réseau <br@frankiz.polytechnique.fr>","$prenom $nom <$adresse>",$titre);
	$mail->setBody($contenu);
	$mail->send();
}

// convertit un message HTML en un message plaintext.
function html2plain($html) {
	$text = html_entity_decode($html);
	return trim(strip_tags($text));
}

// convertit un message plaintext en un message HTML avec des liens clickables.
function plain2html($text) {
	$html = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]",
	"<a href=\"\\0\">\\0</a>", $text);
	$html = nl2br($html);
	return "<html><body>\n$html\n</body></html>";
}

class Mail {
	var $header, $body;
	var $from, $to, $cc, $bcc;
	var $subject, $boundary;
	
	// Création d'un nouveau mail
	function mailer($from, $to, $subject, $multipart=false, $cc="", $bcc="") {
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
	function addPart($type,$charset,$encoding,$value) {
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

	function addPartRichText($text,$charset="iso-8859-1") {
		$this->addPart("text/enriched; charset=$charset","8bit", $tex);
	}
	
	function addPartHtml($html,$charset="iso-8859-1") {
		$this->addPart("text/html; charset=$charset","8bit", $html);
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
