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

	$Log$
	Revision 1.10  2004/11/13 00:12:24  schmurtz
	Ajout du su

	Revision 1.9  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.8  2004/10/17 17:13:20  kikx
	Pour rendre la page d'administration plus belle
	n'affiche le truc d'admin que si on est admin
	meme chsoe pour le prez et le webmestre
	
	Revision 1.7  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.6  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

define("AUTH_AUCUNE",0);	// Client non authentifié
define("AUTH_INTERNE",1);   // Client accédant depuis l'intérieur de l'x (ip en 129.104.*.*)
define("AUTH_COOKIE",2);	// Client authentifié par un cookie (authentification faible, mais automatique)
define("AUTH_MAIL",3);		// Client authentifié par un hash récupéré dans un mail (perte de mot de passe)
define("AUTH_MDP",4);		// Client authentifié par mot de passe

define("AUTH_MINIMUM",2);   // Valeur minimum correspondant à un client authentifié
define("AUTH_FORT",3);		// Valeur minimum correspondant à un client authentifié avec une méthode sécurisé

class User {
	// description de l'utilisateur
	var $uid;
	var $nom;
	var $prenom;
	var $perms;
	var $passwd;		// hash md5 du mot de passe
	var $mailhash;
	var $cookiehash;
	
	// Méthode d'authentification utilisée
	var $methode;
	
	// Construit un objet à partir du login ou d'un id.
	// On suppose que l'on est déjà connecté à la base de données
	function User($islogin,$value) {
		global $DB_web;
		if(empty($value)) {
			// construit un objet à partir de rien : utilisateur anonyme.
			$this->devient_anonyme();
			return;
		}
		
		$condition = $islogin ? "WHERE login='$value' ORDER BY promo DESC LIMIT 1" : "WHERE eleves.eleve_id='$value'";
		$DB_web->query("SELECT eleves.eleve_id,login,perms,nom,prenom,passwd,IF(hashstamp>NOW(),hash,''),hash FROM trombino.eleves INNER JOIN compte_frankiz USING(eleve_id) $condition");
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
		$this->methode = substr($_SERVER['REMOTE_ADDR'],0,8) == "129.104." ? AUTH_INTERNE : AUTH_AUCUNE;
		$this->perms = array();
	}
	
	// Authentification par mot de passe, cookie, mail. Si l'authentification échoue, on revient à
	// un utilisateur anonyme. Renvoie vrai si l'authentification à réussie.
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
	
	// Vérifie que l'utilisateur à la permission demandée.
	// Pour les permissions prez/webmestre, il est préférable d'utiliser les fonctions dédiées
	// afin de rester indépendant de la manière dont on stocke les informations dans la base.
	function verifie_permission($perm) {
		if( $this->methode < AUTH_COOKIE) return false;
		for ($i = 0 ; $i<count($this->perms) ; $i++)
			if ($this->perms[$i] == $perm) return true;
		return false;
	}
	
	function ses_permissions() {
		if( $this->methode < AUTH_COOKIE) return false;
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
