<?php
/*
	Copyright (C) 2004 Binet R�seau
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
	D�fini une classe de gestion d'un utilisateur. Cette classe n'est faite que pour la gestion
	de l'authentification, donc il est inutile d'y inclure toute les informations du trombino, ni
	pour modifier le trombino.
	
	La table d'authentification contient les champs 'eleve_id', 'login', 'passwd' et 'perms', les autres
	informations provenant des tables du trombino (avec jointure sur l'uid).

	$Log$
	Revision 1.17  2004/12/16 16:45:14  schmurtz
	Correction d'un bug dans la gestion des authentifications par cookies
	Ajout de fonctionnalitees de log d'erreur de connections ou lors des bugs
	affichant une page "y a un bug, contacter l'admin"

	Revision 1.16  2004/12/16 13:00:41  pico
	INNER en LEFT
	
	Revision 1.15  2004/11/24 15:55:34  pico
	Code pour g�rer les liens perso + les rss au lancement de la session
	
	Revision 1.14  2004/11/22 07:59:00  pico
	Correction du login pour le su.
	
	Revision 1.13  2004/11/17 22:40:50  schmurtz
	Bug lors du login par mdp si l'utilisateur ne mets pas son login sous la forme
	loginpoly.promo
	
	Revision 1.12  2004/11/16 15:09:15  kikx
	Le login est now login.promo
	
	Revision 1.11  2004/11/16 14:55:46  schmurtz
	On evite les appels frequents a la BD pour recuperer la skin
	
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

define("AUTH_AUCUNE",0);	// Client non authentifi�
define("AUTH_INTERNE",1);   // Client acc�dant depuis l'int�rieur de l'x (ip en 129.104.*.*)
define("AUTH_COOKIE",2);	// Client authentifi� par un cookie (authentification faible, mais automatique)
define("AUTH_MAIL",3);		// Client authentifi� par un hash r�cup�r� dans un mail (perte de mot de passe)
define("AUTH_MDP",4);		// Client authentifi� par mot de passe

define("AUTH_MINIMUM",2);   // Valeur minimum correspondant � un client authentifi�
define("AUTH_FORT",3);		// Valeur minimum correspondant � un client authentifi� avec une m�thode s�curis�

global $_NOUVEAU_LOGIN;	// indique si l'utilisateur vient de se loguer
$_NOUVEAU_LOGIN = false;

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
		$this->methode = substr($_SERVER['REMOTE_ADDR'],0,8) == "129.104." ? AUTH_INTERNE : AUTH_AUCUNE;
		$this->perms = array();
	}
	
	// Authentification par mot de passe, cookie, mail. Si l'authentification �choue, on revient �
	// un utilisateur anonyme. Renvoie vrai si l'authentification � r�ussie.
	function verifie_mdp($_mdp) {
		global $_NOUVEAU_LOGIN;
		if($this->uid != 0 && md5($_mdp) == $this->passwd) {
			$this->methode = AUTH_MDP;
			$_NOUVEAU_LOGIN = true;
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
	
	// V�rifie que l'utilisateur � la permission demand�e.
	// Pour les permissions prez/webmestre, il est pr�f�rable d'utiliser les fonctions d�di�es
	// afin de rester ind�pendant de la mani�re dont on stocke les informations dans la base.
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
	
	// V�rifie l'�tat d'authentification. Renvoie faux si c'est pas au moins $minimum
	// (AUTH_MINIMUM ou AUTH_FORT en g�n�ral, pour v�rifi� si un utilisateur est authentifi� par
	// une m�thode quelconque, ou pour v�rifi� que l'utilisateur est authentifi� par une m�thode
	// s�curis�e).
	function est_authentifie($minimum) {
		return $this->methode >= $minimum;
	}
}

// Fonctions simplifi�es, utilisant $_SESSION['user'] directement
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
