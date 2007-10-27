<?php
/*
	Copyright (C) 2007 Binet Réseau
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

require_once BASE_FRANKIZ."platal-classes/session.php";
require_once BASE_FRANKIZ."platal-classes/s.php";
require_once BASE_FRANKIZ."platal-includes/globals.inc.php";
require_once BASE_FRANKIZ."htdocs/include/skin.inc.php";

define("AUTH_AUCUNE",  0); // Client non authentifié
define("AUTH_INTERNE", 1); // Client accédant depuis l'intérieur de l'x (ip en 129.104.*.*)
define("AUTH_COOKIE",  2); // Client authentifié par un cookie (authentification faible, mais automatique)
define("AUTH_MAIL",    3); // Client authentifié par un hash récupéré dans un mail (perte de mot de passe)
define("AUTH_MDP",     4); // Client authentifié par mot de passe

define("AUTH_MINIMUM", 2); // Valeur minimum correspondant à un client authentifié
define("AUTH_FORT",    3); // Valeur minimum correspondant à un client authentifié avec une méthode sécurisé

define("COOKIE_AUTH", FRANKIZ_SESSION_NAME."_auth");
define("COOKIE_SKIN", FRANKIZ_SESSION_NAME."_skin");

/**
 * Gestion de la session en cours d'un utilisateur. Se charge de l'authentification
 * et du chargement de la skin.
 */
class FrankizSession extends Session
{
	/**
	 * Initialisation de la session. Authentifie l'utilisateur si necessaire, et
	 * charge la skin requise.
	 */
	public static function init()
	{
		S::init();
	
		// Protection contre le vol de session : une session est associée à un IP,
		// si l'IP change pendant la session, il y a eu vol.
		if (!isset($_SESSION['ip'])) {
			$_SESSION['ip'] = ip_get();
		} elseif ($_SESSION['ip'] != ip_get()) {
			S::destroy();
		}
		
		if (isset($_REQUEST['logout']))
			S::destroy();

		if (!FrankizSession::doAuth(false))
		{
			if (FrankizSession::est_interne())
				$_SESSION['auth'] = AUTH_INTERNE;
			else
				$_SESSION['auth'] = AUTH_NONE;
		}

		FrankizSession::load_skin(false);
	}

	// ----------------------------------------------- Auth -------------------------------------------

	/**
	 * Authentifie l'utilisateur, en testant successivement:
	 *  - authentification par mot de passe
	 *  - authentification par hash transmis par mail
	 *  - si l'utilisateur a une session en cours
	 *  - authentification par cookie
	 *
	 * @return le succes de l'authentification (un utilisateur deja authentifie est
	 * considéré comme un succes.
	 */
	public static function doAuth($new_name = false)
	{
		global $globals;

		if (FrankizSession::doAuth_mdp())
			return FrankizSession::start_session();
	
		if (FrankizSession::doAuth_mail())
			return FrankizSession::start_session();

		if (S::identified())
			return true;

		if (FrankizSession::doAuth_cookie())
			return FrankizSession::start_session();

		return false;
	}

	/**
	 * Authentification par mot de passe.
	 */
	private static function doAuth_mdp()
	{
		global $DB_web;
		
		if (!isset($_POST['login_login']) || !isset($_POST['passwd_login']))
			return false;
		
		$value = explode(".",$_POST['login_login']) ;
		if (count($value) != 2)
			return false;

		$login = $value[0] ;
		$promo = $value[1] ;
		
		$DB_web->query("SELECT e.eleve_id, c.passwd 
		                  FROM compte_frankiz AS c
			     LEFT JOIN trombino.eleves AS e USING(eleve_id)
		                 WHERE e.login = '$login' AND e.promo = '$promo'");
		list ($uid, $passwd) = $DB_web->next_row();

		if ($uid != 0 && md5($_POST['passwd_login']) == $passwd) 
		{
			$_hash_shadow = hash_shadow($_POST['passwd_login']);
			$DB_web->query("UPDATE compte_frankiz SET passwd='$_hash_shadow' WHERE eleve_id='{$uid}'");
		}
		else if ($uid == 0 || crypt($_POST['passwd_login'], $passwd) != $passwd)
			return false;


		$_SESSION['uid'] = $uid;
		$_SESSION['auth'] = AUTH_MDP;
		
		return true;
	}

	/**
	 * Authentification par hash transmis par mail
	 */
	private static function doAuth_mail()
	{
		global $DB_web;
		
		if (empty($_REQUEST['hash']) || !isset($_REQUEST['uid']))
			return false;

		$DB_web->query("SELECT eleve_id,hash
		                  FROM compte_frankiz
				 WHERE eleve_id = '{$_REQUEST['uid']}'");

		list ($uid, $hash) = $DB_web->next_row();

		if ($uid == 0 || $hash != $_REQUEST['hash'])
			return false;

		$_SESSION['uid'] = $uid;
		$_SESSION['auth'] = AUTH_MAIL;
	}
	
	/**
	 * Authentification par cookie
	 */
	private static function doAuth_cookie()
	{
		global $DB_web;
		
		if (empty($_COOKIE[COOKIE_AUTH]))
			return false;
		$cookie = unserialize(base64_decode(wordwrap($_COOKIE[COOKIE_AUTH])));

		$DB_web->query("SELECT eleve_id,hash
		                  FROM compte_frankiz
				 WHERE eleve_id = '{$_COOKIE['uid']}'");
		list ($uid, $hash) = $DB_web->next_row();

		if ($uid == 0 || !$hash || $cookie['hash'] != $hash)
			return false;

		$_SESSION['uid'] = $uid;
		$_SESSION['auth'] = AUTH_COOKIE;
		return true;
	}

	/**
	 * Appellé quand une authentification est réussie.
	 */
	private static function start_session()
	{
		global $DB_web;

		$DB_web->query("SELECT e.eleve_id, e.nom, e.prenom, c.perms, c.skin
		                  FROM compte_frankiz AS c
			     LEFT JOIN trombino.eleves AS e USING(eleve_id)
				 WHERE eleve_id = '{$_SESSION['uid']}'");
		list ($uid, $nom, $prenom, $perms, $skin) = $DB_web->next_row();

		$_SESSION['nom'] = $nom;
		$_SESSION['prenom'] = $prenom;
		$_SESSION['fkz_perms'] = explode(",", $perms);

		// Mise à jour de la skin.
		$_SESSION['skin'] = new Skin;
		$_SESSION['skin']->unserialize($skin);
		FrankizSession::update_skin_cookie();
	
		return true;
	}
	
	/**
	 * Active ou désactive le cookie d'authentification.
	 * @param $activer Indique si l'on doit activer ou désactiver le cookie
	 * @return void
	 */
	public static function activer_cookie($activer)
	{
		if ($activer)
		{
			// Si l'utilisateur a activé l'authentification par cookie, mise à jour du cookie.
			$DB_web->query("SELECT hash
		                  	  FROM compte_frankiz
				 	 WHERE eleve_id = '{$_SESSION['uid']}'");
			list ($hash) = $DB_web->next_row();
		
			$cookie = base64_encode(serialize(array('uid' => $_SESSION['uid'],
								'hash' => $hash)));
			setcookie(COOKIE_AUTH, $cookie,  time() + 3600*24*365, "/");
		}
		else
		{
			setcookie(COOKIE_AUTH, "", 0, "/");
		}
	}

	
	// ---------------------------------------- Skins ------------------------------------------------

	/**
	 * Charge la skin depuis le cookie.
	 * @param force (booleen) Si vrai, la skin sera rechargée depuis le cookie même si une
	 * skin est actuellement chargée.
	 */
	public static function load_skin($force = false)
	{
		if (isset($_SESSION['skin']) && !$force)
			return;
			
		$_SESSION['skin'] = new Skin;
		
		if (isset($_COOKIE[COOKIE_SKIN]))
			$_SESSION['skin']->unserialize(stripslashes($_COOKIE[COOKIE_SKIN]));
	}

	/**
	 * Sauvegarde d'eventuelles modification de la skin pour que l'utilisateur les retrouvent
	 * sur une session future.
	 */
	public static function save_skin()
	{
		global $DB_web;
		
		if (!isset($_SESSION['skin']))
			return;

		if (isset($_SESSION['uid']))
		{
			$skin = $_SESSION['skin']->serialize();
			$DB_web->query("UPDATE compte_frankiz
			                   SET skin='$skin'
					 WHERE eleve_id='{$_SESSION['uid']}'");
		}
		FrankizSession::update_skin_cookie();
	}

	/**
	 * Met à jour le cookie contenant la skin en cours, depuis la variable de session
	 * $_SESSION['skin'].
	 */
	private static function update_skin_cookie()
	{
		$skin = $_SESSION['skin']->serialize();
		setcookie(COOKIE_SKIN, $skin, time() + 365*24*3600, "/");
	}

	// ----------------------------------------- QUERIES -----------------------------------------------------

	public static function verifie_permission($perm) 
	{
		if (!isset($_SESSION['uid'])) 
			return false;
		
		for ($i = 0; $i < count($_SESSION['fkz_perms']); $i++)
		{
			if ($_SESSION['fkz_perms'][$i] == $perm) 
				return true;
		}

		return false;
	}
	
	public static function verifie_permission_prez($binet) 
	{
		return FrankizSession::verifie_permission("prez_$binet");
	}
	
	public static function verifie_permission_webmestre($binet) 
	{
		return FrankizSession::verifie_permission("webmestre_$binet");
	}
	
	/**
	 * Vérifie l'état d'authentification. Renvoie faux si c'est pas au moins $minimum
	 * (AUTH_MINIMUM ou AUTH_FORT en général, pour vérifié si un utilisateur est authentifié par
	 * une méthode quelconque, ou pour vérifié que l'utilisateur est authentifié par une méthode
	 * sécurisée).
	 */
	public static function est_authentifie($minimum)
	{
		return $_SESSION['auth'] >= $minimum;
	}

	/**
	 * Renvoie si le client est dans le reseau interne à l'X
	 */
	public static function est_interne()
	{
		$ip = ip_get();
		return $ip == '127.0.0.1' || (substr($ip, 0, 8) == '129.104.' && $ip != '129.104.30.4');
	}

	// ------------------------------ FONCTIONS SIMPLIFIEES POUR LES TEMPLATES --------------------------------
	public static function est_auth()
	{
		return FrankizSession::est_authentifie(AUTH_MINIMUM);
	}

	public static function est_auth_fort()
	{
		return FrankizSession::est_authentifie(AUTH_FORT);
	}

	public static function is_admin()
	{
		return count($_SESSION['fkz_perms']) > 1;
	}
}

// Fonctions simplifiées (temporaires) 
function verifie_permission($perm) {
	return FrankizSession::verifie_permission($perm);
}
function verifie_permission_prez($binet) {
	return FrankizSession::verifie_permission("prez_$binet");
}
function verifie_permission_webmestre($binet) {
	return FrankizSession::verifie_permission("webmestre_$binet");
}
function est_authentifie($minimum) {
	return FrankizSession::est_authentifie($minimum);
}
function demande_authentification($minimum) {
	if (est_authentifie($minimum))
		return;

	require_once BASE_LOCAL."include/page_header.inc.php";

	call('CoreModule', 'do_login');
	require_once BASE_LOCAL."include/page_footer.inc.php";
	exit;
}
function est_interne() {
	return FrankizSession::est_interne();
}

?>
