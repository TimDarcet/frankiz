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

	$Log$
	Revision 1.4  2004/12/02 11:47:30  pico
	C'est bizarre, le // marche pas partout :(

	Revision 1.3  2004/12/02 11:46:20  pico
	Correction login
	
	Revision 1.2  2004/12/01 21:05:53  pico
	Correction lien login
	
	Revision 1.1  2004/11/25 00:44:35  schmurtz
	Ajout de init_ devant les fichier d'include servant d'initialisation de page
	Permet de mieux les distinguer des autres fichiers d'include ne faisant que definir
	des fonctions.
	
	Revision 1.12  2004/11/24 20:26:38  schmurtz
	Reorganisation des skins (affichage melange skin/css + depacement des css)
	
	Revision 1.11  2004/11/16 15:09:15  kikx
	Le login est now login.promo
	
	Revision 1.10  2004/11/13 00:12:24  schmurtz
	Ajout du su
	
	Revision 1.9  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.8  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.7  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

require_once "global.inc.php";
require_once "user.inc.php";

session_start();

/*
	Si un logout a été effectué, on détruit la session, puis on la recrer, vierge.
	Si un su est en cours, on en sort.
*/
if(isset($_GET['logout'])) {
	if(isset($_SESSION['sueur'])) {
		// on sort juste du su
		$_SESSION['user'] = $_SESSION['sueur'];
		unset($_SESSION['sueur']);
		
	} else {
		session_unset();
		session_destroy();
		SetCookie("auth","",0,"/");
	}
	rediriger_vers("/");
}

/*
	Gestion du login (mot de passe, mail, cookie, su, annonyme)
*/
// Login par mot de passe
if(isset($_POST['login']) && isset($_POST['passwd'])) {
	$_SESSION['user'] = new User(true,$_POST['login']);
	if(!$_SESSION['user']->verifie_mdp($_POST['passwd']))
		ajoute_erreur(ERR_LOGIN);
	
	// Un message d'erreur s'affichera automatiquement par la page à l'origine
	// de cette authentification.
	
// Login par mail
} else if(isset($_GET['hash']) && isset($_GET['uid'])) {
	$_SESSION['user'] = new User(false,$_GET['uid']);
	if(!$_SESSION['user']->verifie_mailhash($_GET['hash']))
		ajoute_erreur(ERR_MAILLOGIN);
	
	// Quel que soit le résultat, on supprime le hash d'authentification par mail.
	$DB_web->query("UPDATE compte_frankiz SET hashstamp=0 WHERE eleve_id='".$_GET['uid']."'");
	
	// On affiche un message d'erreur si l'authentification a échouée.
	if(a_erreur(ERR_MAILLOGIN)) {
		require_once "skin.inc.php";	// skin.inc.php est inclus juste après login.inc.php, donc
										// c'est pas encore fait
		require "page_header.inc.php";
?>
		<page id="login" titre="Frankiz : erreur">
			<p>Une erreur est survenue lors de la vérification du lien d'authentification. Il s'agit
			peut être d'un dépassement des 6 heures de validité du lien. Si c'est le cas, recommence
			la procédure en cliquant <a href="<?php echo BASE_URL.'/profil/mdp_perdu.php'?>">ici</a>.</p>
		</page>
<?php
		require "page_footer.inc.php";
		
		exit;
	}

// Login par cookie (si la session est déjà existante, on oublie le cookie)
} else if(!isset($_SESSION['user']) && isset($_COOKIE['auth'])) {
	$cookie = unserialize(base64_decode($_COOKIE['auth']));
	$_SESSION['user'] = new User(false,$cookie['uid']);
	$_SESSION['user']->verifie_cookiehash($cookie['hash']);

// Login par utilisation du su (utilisable par les admins uniquement)
} else if(isset($_GET['su']) && verifie_permission('admin')) {
	demande_authentification(AUTH_FORT);
	
	$newuser = new User(false,$_GET['su']);
	if($newuser->uid == 0) {
		require_once "skin.inc.php";	// skin.inc.php est inclus juste après login.inc.php, donc
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

	require "page_header.inc.php";
?>
	<page id="login" titre="Frankiz : connexion">
		<?php if(a_erreur(ERR_LOGIN)):?>
			<warning>Une erreur est survenue lors de l'authentification. Vérifie qu'il n'y a pas d'erreur
			dans le login ou le mot de passe.</warning>
		<?php endif; ?>
		<note>Ton login est loginpoly.promo</note>
		<formulaire id="login" titre="Connexion" action="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']?>">
			<champ id="login" titre="Login" valeur="<?php if(isset($_POST['login'])) echo $_POST['login']?>"/>
			<champ id="passwd" titre="Mot de passe" valeur=""/>
			<bouton id="connect" titre="Connexion"/>
		</formulaire>
		<p>Si tu as oublié ton mot de passe ou que tu pas encore de compte,
		clique <a href="<?php echo BASE_URL.'/profil/mdp_perdu.php'?>">ici</a>.</p>
	</page>
<?php
	require "page_footer.inc.php";
	
	exit;
}
?>
