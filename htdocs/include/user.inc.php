<?php
/*
	$Id$

	D�fini une classe de gestion d'un utilisateur. Cette classe n'est faite que pour la gestion
	de l'authentification, donc il est inutile d'y inclure toute les informations du trombino, ni
	pour modifier le trombino.
	
	La table d'authentification contient les champs 'eleve_id', 'login', 'passwd' et 'perms', les autres
	informations provenant des tables du trombino (avec jointure sur l'uid).
*/

define("AUTH_AUCUNE",0);	// Client non authentifi�
define("AUTH_INTERNE",1);   // Client acc�dant depuis l'int�rieur de l'x (ip en 129.104.*.*)
define("AUTH_COOKIE",2);	// Client authentifi� par un cookie (authentification faible, mais automatique)
define("AUTH_MAIL",3);		// Client authentifi� par un hash r�cup�r� dans un mail (perte de mot de passe)
define("AUTH_MDP",4);		// Client authentifi� par mot de passe

define("AUTH_MINIMUM",2);   // Valeur minimum correspondant � un client authentifi�
define("AUTH_FORT",3);		// Valeur minimum correspondant � un client authentifi� avec une m�thode s�curis�

class User {
	// description de l'utilisateur
	var $uid;
	var $nom;
	var $prenom;
	var $perms;
	var $passwd;		// hash md5 du mot de passe
	var $mailhash;
	var $cookiehash;
	
	// M�thode d'authentification utilis�e
	var $methode;
	
	// Construit un objet � partir du login ou d'un id.
	// On suppose que l'on est d�j� connect� � la base de donn�es
	function User($islogin,$value) {
		global $DB_web;
		if(empty($value)) {
			// construit un objet � partir de rien : utilisateur anonyme.	
			$this->devient_anonyme();
			return;
		}
		
		$condition = $islogin ? "WHERE login='$value' ORDER BY promo DESC LIMIT 1" : "WHERE eleves.eleve_id='$value'";
		$DB_web->query("SELECT eleves.eleve_id,login,perms,nom,prenom,passwd,IF(hashstamp>NOW(),hash,''),hash FROM eleves INNER JOIN compte_frankiz USING(eleve_id) $condition");
		list($this->uid,$this->login,$this->perms,$this->nom,$this->prenom,$this->passwd,$this->mailhash,$this->cookiehash) = $DB_web->next_row();
		
		$this->perms = split(",",$this->perms);
		$this->methode = AUTH_AUCUNE;
	}
	
	function devient_anonyme() {
		$this->uid = 0;
		$this->methode = substr($_SERVER['REMOTE_ADDR'],0,8) == "129.104." ? AUTH_INTERNE : AUTH_AUCUNE;
		$this->perms = array();
	}
	
	// Authentification par mot de passe, cookie, mail. Si l'authentification �choue, on revient �
	// un utilisateur anonyme. Renvoie vrai si l'authentification � r�ussie.
	function verifie_mdp($_mdp) {
		if($this->uid != 0 && md5($_mdp) == $this->passwd) {
			$this->methode = AUTH_MDP;
			return true;
		} else {
			$this->devient_anonyme();
			return false;
		}
	}
	
	function verifie_cookiehash($_hash) {
		if($this->uid != 0 && !empty($_hash) && $_hash == $this->cookiehash) {
			$this->methode = AUTH_COOKIE;
			return true;
		} else {
			$this->devient_anonyme();
			return false;
		}
	}
	
	function verifie_mailhash($_hash) {
		if($this->uid != 0 && !empty($_hash) && $_hash == $this->mailhash) {
			$this->methode = AUTH_MAIL;
			return true;
		} else {
			$this->devient_anonyme();
			return false;
		}
	}
	
	// V�rifie que l'utilisateur � la permission demand�e.
	// Pour les permissions prez/webmestre, il est pr�f�rable d'utiliser les fonctions d�di�es
	// afin de rester ind�pendant de la mani�re dont on stocke les informations dans la base.
	function verifie_permission($perm) {
		if( $this->methode < AUTH_COOKIE) return false;
		for ($i = 0 ; $i<count($this->perms) ; $i++)
			if ($this->perms[$i] == $perm) return true;
		return false;
	}
	
	function verifie_permission_prez($binet) {
		return $this->verifie_permission("prez_$binet");
	}
	
	function verifie_permission_webmestre($binet) {
		return $this->verifie_permission("webmestre_$binet");
	}
	
	// V�rifie l'�tat d'authentification. Renvoie faux si c'est pas au moins $minimum
	// (AUTH_MINIMUM ou AUTH_FORT en g�n�ral, pour v�rifi� si un utilisateur est authentifi� par
	// une m�thode quelconque, ou pour v�rifi� que l'utilisateur est authentifi� par une m�thode
	// s�curis�e).
	function est_authentifie($minimum) {
		return $this->methode >= $minimum;
	}
}

// Fonctions simplifi�e, utilisant $_SESSION['user'] directement
function verifie_permission($perm) {
	return $_SESSION['user']->verifie_permission($perm);
}
function verifie_permission_prez($binet) {
	return $_SESSION['user']->verifie_permission("prez_$binet");
}
function verifie_permission_webmestre($binet) {
	return $_SESSION['user']->verifie_permission("webmestre_$binet");
}
function est_authentifie($minimum) {
	return $_SESSION['user']->methode >= $minimum;
}
