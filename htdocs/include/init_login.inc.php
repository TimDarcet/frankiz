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
	Gestion du login et de la session PHP.
	
	Les informations sur l'utilisateur sont stockées dans une variable de session,
	$_SESSION['user'], contenant une instance d'un objet User.
	
	L'authentification par mot de passe utilise les variables POST 'passwd' et 'login'.
	L'authentification par mail utilise les varibales GET 'hash' et 'uid'.
	L'authentification par cookie utilise le cookie 'auth' contenant un tableau à deux entrées,
	'hash' et 'uid', sérialisé et encodé en base64.
	Une authentification permettant de faire un su. L'id de l'utilisateur dont on veut prendre
	l'identité la variable GET 'su'. (pour les admins uniquement)
	
	Le logout s'effectue en mettant une variable GET 'logout' sur n'importe quelle page.
	
	Ce fichier définie aussi la fonction demande_authentification qui vérifie si le client est
	authentifié, et si ce n'est pas le cas affiche la page d'authentifictaion par mot de passe.

	$Id$
	
*/

require_once "global.inc.php";
require_once "user.inc.php";

session_name("frankiz");
session_start();

/*
	Protection contre le vol de session : une session est associé à une IP,
	si l'IP change pendant la session, c'est qu'il y a eu vol.
*/
if (!isset($_SESSION['ip'])) {
	$_SESSION['ip'] = ip_get();
	
} elseif ($_SESSION['ip'] != ip_get()) {
	// vol : on détruit la session
	session_unset();
	session_destroy();
	rediriger_vers("/");
}

/*
	Si un logout a été effectué, on détruit la session, puis on la recrer, vierge.
	Si un su est en cours, on en sort.
*/
if(isset($_REQUEST['logout'])) {
	if(isset($_SESSION['sueur'])) {
		// on sort juste du su
		$_SESSION['user'] = $_SESSION['sueur'];
		unset($_SESSION['sueur']);
		
	} else {
		session_unset();
		session_destroy();
	}
	rediriger_vers("/");
}

/*
	Gestion du login (mot de passe, mail, cookie, su, annonyme)
*/
// Login par mot de passe
if(isset($_POST['login_login']) && isset($_POST['passwd_login'])) {
	$_SESSION['user'] = new User(true,$_POST['login_login']);
	if(!$_SESSION['user']->verifie_mdp($_POST['passwd_login'])) {
		ajoute_erreur(ERR_LOGIN);
		ajouter_access_log("erreur de mot de passe login={$_POST['login_login']}");
	}
	// Un message d'erreur s'affichera automatiquement par la page à l'origine
	// de cette authentification.
	
// Login par mail
} else if(isset($_REQUEST['hash']) && isset($_REQUEST['uid'])) {
	$_SESSION['user'] = new User(false,$_REQUEST['uid']);
	if(!$_SESSION['user']->verifie_mailhash($_REQUEST['hash'])) {
		ajoute_erreur(ERR_MAILLOGIN);
		$DB_web->query("SELECT hashstamp,hash FROM compte_frankiz WHERE eleve_id='{$_REQUEST['uid']}'");
		list($hashstamp,$hash) = $DB_web->next_row();
		ajouter_access_log("erreur de log par mail uid={$_REQUEST['uid']} hash=$hash stamp=$hashstamp");
	}
	
	// Quel que soit le résultat, on supprime le hash d'authentification par mail.
	$DB_web->query("UPDATE compte_frankiz SET hashstamp=0 WHERE eleve_id='".$_REQUEST['uid']."'");
	
	// On affiche un message d'erreur si l'authentification a échouée.
	if(a_erreur(ERR_MAILLOGIN)) {
		require_once "init_skin.inc.php";	// init_skin.inc.php est inclus juste après login.inc.php, donc
											// c'est pas encore fait
		require "page_header.inc.php";
?>
		<page id="login" titre="Frankiz : erreur">
			<p>Une erreur est survenue lors de la vérification du lien d'authentification. Il s'agit
			peut être d'un dépassement des 6 heures de validité du lien. Si c'est le cas, recommence
			la procédure en cliquant <lien titre="ici" href="<?php echo BASE_URL.'/profil/mdp_perdu.php'?>"/>.</p>
		</page>
<?php
		require "page_footer.inc.php";
		
		exit;
	}
// Login par cookie (si la session est déjà existante, on oublie le cookie)
} else if(!isset($_SESSION['user']) && isset($_COOKIE['auth'])) {
	$cookie = unserialize(base64_decode(wordwrap($_COOKIE['auth'])));
	$_SESSION['user'] = new User(false,$cookie['uid']);
	if( !$_SESSION['user']->verifie_cookiehash($cookie['hash']) )
		ajouter_access_log("erreur de log par cookie uid={$cookie['uid']} cookie={$_COOKIE['auth']}");

// Login par utilisation du su (utilisable par les admins uniquement)
}
if(isset($_REQUEST['su']) && verifie_permission('admin')) {
	demande_authentification(AUTH_FORT);
	
	$newuser = new User(false,$_REQUEST['su']);
	if($newuser->uid == 0) {
		require_once "init_skin.inc.php";	// init_skin.inc.php est inclus juste après login.inc.php, donc
											// c'est pas encore fait
		require "page_header.inc.php";
		echo "<page id='su' titre='Frankiz : erreur'>\n";
		echo "<p>L'utilisateur n'a pas encore de compte Frankiz ou alors n'existe pas.</p>\n";
		echo "</page>\n";
		require "page_footer.inc.php";
		exit;
	}

	$newuser->methode = AUTH_MDP;
	if(!isset($_SESSION['sueur']))
		$_SESSION['sueur'] = $_SESSION['user']; // on sauvegarde l'utilisateur actuel
	$_SESSION['user'] = $newuser;
	rediriger_vers("/");
}

// Aucune information de login. Si la variable de session 'user' n'existe toujours pas
// on crée un utilisateur anonyme.
if(!isset($_SESSION['user']))
	$_SESSION['user'] = new User(false,'');


/*
	Fonction de gestion de la demande d'authentification ($minimum est la méthode
	d'authentification minimale pour laquelle une réauthentification par mot de passe
	n'est pas indispensable).
*/
function demande_authentification($minimum) {
	if($_SESSION['user']->est_authentifie($minimum)) return;
	
	require_once "init_skin.inc.php"; // n'y est pas encore dans le cas d'un "su"
	require "page_header.inc.php";
?>
	<page id="page_login" titre="Frankiz : connexion">
		<?php if(a_erreur(ERR_LOGIN)):?>
			<warning>Une erreur est survenue lors de l'authentification. Vérifie qu'il n'y a pas d'erreur
			dans le login ou le mot de passe.</warning>
		<?php endif; ?>
		<note>Ton login est loginpoly.promo</note>
		<formulaire id="login" titre="Connexion" action=
		<?php
			echo '"'.htmlentities($_SERVER['REQUEST_URI']).'"';
		?>
		>
		<?php  foreach ($_REQUEST AS $keys => $val){
				if ($keys != "login_login" && $keys != "passwd_login") {
					echo "<hidden id=\"".$keys."\" valeur=\"".$val."\" />";
				}
		       }
		?>
			
			<champ id="login_login" titre="Identifiant" valeur="<?php if(isset($_POST['login_login'])) echo $_POST['login_login']?>"/>
			<champ id="passwd_login" titre="Mot de passe" valeur=""/>
			<bouton id="connect" titre="Connexion"/>
		</formulaire>
		<p>Si tu as oublié ton mot de passe ou que tu n'as pas encore de compte,
		clique <a href="<?php echo BASE_URL.'/profil/mdp_perdu.php'?>">ici</a>.</p>
	</page>
<?php
	require "page_footer.inc.php";
	
	exit;
}
?>
