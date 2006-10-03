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
	Défini une classe de gestion d'un utilisateur. Cette classe n'est faite que pour la gestion
	de l'authentification, donc il est inutile d'y inclure toute les informations du trombino, ni
	pour modifier le trombino.
	
	La table d'authentification contient les champs 'eleve_id', 'login', 'passwd' et 'perms', les autres
	informations provenant des tables du trombino (avec jointure sur l'uid).

	$Id$
	
*/

define("AUTH_AUCUNE",0);	// Client non authentifié
define("AUTH_INTERNE",1);   // Client accédant depuis l'intérieur de l'x (ip en 129.104.*.*)
define("AUTH_COOKIE",2);	// Client authentifié par un cookie (authentification faible, mais automatique)
define("AUTH_MAIL",3);		// Client authentifié par un hash récupéré dans un mail (perte de mot de passe)
define("AUTH_MDP",4);		// Client authentifié par mot de passe

define("AUTH_MINIMUM",2);   // Valeur minimum correspondant à un client authentifié
define("AUTH_FORT",3);		// Valeur minimum correspondant à un client authentifié avec une méthode sécurisé

global $_NOUVEAU_LOGIN;	// indique si l'utilisateur vient de se loguer
$_NOUVEAU_LOGIN = false;


class User {
	// description de l'utilisateur
	public $uid;
	public $nom;
	public $prenom;
	public $perms;
	public $passwd;		// hash du mot de passe
	public $mailhash;
	public $cookiehash;
	
	// Méthode d'authentification utilisée
	public $methode;
	
	// Construit un objet à partir du login ou d'un id.
	// On suppose que l'on est déjà connecté à la base de données
	function User($islogin,$value) {
		global $DB_web;
		if(empty($value)) {
			// construit un objet à partir de rien : utilisateur anonyme.
			$this->devient_anonyme();
			return;
		}
		$value = explode(".",$value) ;
		if (count($value)!=2) {
			$login ="" ;
			$promo = "" ;	
		} else {
			$login = $value[0] ;
			$promo = $value[1] ;
		}
		
		$condition = $islogin ? "WHERE login='$login' AND promo='$promo' ORDER BY promo DESC LIMIT 1" : "WHERE eleves.eleve_id='$value[0]'"; 
		$DB_web->query("SELECT eleves.eleve_id,login,perms,nom,prenom,passwd,IF(hashstamp>NOW(),hash,''),hash FROM trombino.eleves LEFT JOIN compte_frankiz USING(eleve_id) $condition");
		if($DB_web->num_rows() == 0) {
			// l'utilisateur n'existe pas.
			$this->devient_anonyme();
		} else {
			list($this->uid,$this->login,$this->perms,$this->nom,$this->prenom,$this->passwd,$this->mailhash,$this->cookiehash) = $DB_web->next_row();
			$this->perms = split(",",$this->perms);
			$this->methode = AUTH_AUCUNE;
		}
	}
	
	function devient_anonyme() {
		$this->uid = 0;
		$this->methode = est_interne() ? AUTH_INTERNE : AUTH_AUCUNE;
		$this->perms = array();
	}
	
	// Authentification par mot de passe, cookie, mail. Si l'authentification échoue, on revient à
	// un utilisateur anonyme. Renvoie vrai si l'authentification à réussie.
	function verifie_mdp($_mdp) {
		global $_NOUVEAU_LOGIN,$DB_web,$DB_wifi;
		if($this->uid != 0 && crypt($_mdp,$this->passwd) == $this->passwd){ // Nouvelle méthode d'authentification
			$this->methode = AUTH_MDP;
			$_NOUVEAU_LOGIN = true;
			return true;
		}else if($this->uid != 0 && md5($_mdp) == $this->passwd) { // Ancienne méthode d'authentification
			$this->methode = AUTH_MDP;
			$_NOUVEAU_LOGIN = true;
			$_hash_shadow = hash_shadow($_mdp);
			$DB_web->query("UPDATE compte_frankiz SET passwd='$_hash_shadow' WHERE eleve_id='{$this->uid}'"); // Mise à jour vers le nouveau format
			$DB_wifi->query("UPDATE alias SET Password='$_hash_shadow' WHERE Alias='{$this->login}' AND Method='TTLS';"); // Synchronisation avec le wifi
			return true;
		} else {
			$this->devient_anonyme();
			return false;
		}
	}
	
	function verifie_cookiehash($_hash) {
		global $_NOUVEAU_LOGIN;
		if($this->uid != 0 && !empty($_hash) && $_hash == $this->cookiehash) {
			$this->methode = AUTH_COOKIE;
			$_NOUVEAU_LOGIN = true;
			return true;
		} else {
			$this->devient_anonyme();
			return false;
		}
	}
	
	function verifie_mailhash($_hash) {
		global $nouveau_login;
		if($this->uid != 0 && !empty($_hash) && $_hash == $this->mailhash) {
			$this->methode = AUTH_MAIL;
			$_NOUVEAU_LOGIN = true;
			return true;
		} else {
			$this->devient_anonyme();
			return false;
		}
	}
	
	// Vérifie que l'utilisateur à la permission demandée.
	// Pour les permissions prez/webmestre, il est préférable d'utiliser les fonctions dédiées
	// afin de rester indépendant de la manière dont on stocke les informations dans la base.
	function verifie_permission($perm) {
		if( $this->methode < AUTH_MINIMUM) return false;
		for ($i = 0 ; $i<count($this->perms) ; $i++)
			if ($this->perms[$i] == $perm) return true;
		return false;
	}
	
	function ses_permissions() {
		if( $this->methode < AUTH_MINIMUM) return false;
		return $this->perms ;
	}
	
	function verifie_permission_prez($binet) {
		return $this->verifie_permission("prez_$binet");
	}
	
	function verifie_permission_webmestre($binet) {
		return $this->verifie_permission("webmestre_$binet");
	}
	
	// Vérifie l'état d'authentification. Renvoie faux si c'est pas au moins $minimum
	// (AUTH_MINIMUM ou AUTH_FORT en général, pour vérifié si un utilisateur est authentifié par
	// une méthode quelconque, ou pour vérifié que l'utilisateur est authentifié par une méthode
	// sécurisée).
	function est_authentifie($minimum) {
		return $this->methode >= $minimum;
	}
}

// Fonctions simplifiées, utilisant $_SESSION['user'] directement
function verifie_permission($perm) {
	return $_SESSION['user']->verifie_permission($perm);
}
function ses_permissions() {
	return $_SESSION['user']->ses_permissions();
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

// Revoit vrai si l'utilisateur vient tout juste de se loguer
function nouveau_login() {
	global $_NOUVEAU_LOGIN;
	return $_NOUVEAU_LOGIN;
}

?>
