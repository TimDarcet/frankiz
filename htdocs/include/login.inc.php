<?php
/*
	$Id$

	Gestion du login et de la session PHP.
	
	Les informations sur l'utilisateur sont stockées dans une variable de session,
	$_SESSION['user'], contenant une instance d'un objet User.
	
	L'authentification par mot de passe utilise les variables POST 'passwd' et 'login'.
	L'authentification par mail utilise les varibales GET 'hash' et 'uid'.
	L'authentification par cookie utilise le cookie 'auth' contenant un tableau à deux entrées,
	'hash' et 'uid', sérialisé et encodé en base64.
	
	Le logout s'effectue en mettant une variable GET 'logout' sur n'importe quelle page.
	
	Ce fichier définie aussi la fonction demande_authentification qui vérifie si le client est
	authentifié, et si ce n'est pas le cas affiche la page d'authentifictaion par mot de passe.
*/

require_once "global.inc.php";
require_once "user.inc.php";

session_start();

// Si un logout a été effectué, on détruit la session, puis on la recrer, vierge.
if(isset($_GET['logout'])) {
	session_unset();
	session_destroy();
	session_start();
}

connecter_mysql_frankiz();

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
	mysql_query("UPDATE compte_frankiz SET hashstamp=0 WHERE eleve_id='".$_GET['uid']."'");
	
	// On affiche un message d'erreur si l'authentification a échouée.
	if(a_erreur(ERR_MAILLOGIN)) {
		require "page_header.inc.php";
		require_once "skin.inc.php";	// skin.inc.php est inclus juste après login.inc.php, donc
										// c'est pas encore fait
		echo "<page id='login' titre='Frankiz : connexion'>\n";
		require "modules.inc.php";
?>
		<contenu>
			<p>Une erreur est survenue lors de la vérification du lien d'authentification. Il s'agit
			peut être d'un dépassement des 6 heures de validité du lien. Si c'est le cas, recommence
			la procédure en cliquant <a href="<?php echo BASE_URL.'/profil/mdp_perdu.php'?>">ici</a>.</p>
		</contenu>
<?php
		echo "</page>\n";
		require "page_footer.inc.php";
		
		exit;
	}

// Login par cookie (si la session est déjà existante, on oublie le cookie)
} else if(!isset($_SESSION['user']) && isset($_COOKIE['auth'])) {
	$cookie = unserialize(base64_decode($_COOKIE['auth']));
	$_SESSION['user'] = new User(false,$cookie['uid']);
	$_SESSION['user']->verifie_cookiehash($cookie['hash']);
}

deconnecter_mysql_frankiz();

// Aucune information de login. Si la variable de session 'user' n'existe toujours pas
// on crée un utilisateur anonyme.
if(!isset($_SESSION['user']))
	$_SESSION['user'] = new User(false,'');

// Fonction de gestion de la demande d'authentification ($minimum est la méthode
// d'authentification minimale pour laquelle une réauthentification par mot de passe
// n'est pas indispensable).
function demande_authentification($minimum) {
	if($_SESSION['user']->est_authentifie($minimum)) return;

	require "page_header.inc.php";
	echo "<page id='login' titre='Frankiz : connexion'>\n";
	require "modules.inc.php";
?>
	<contenu>
		<?php if(a_erreur(ERR_LOGIN)):?>
			<p>Une erreur est survenue lors de l'authentification. Vérifie qu'il n'y a pas d'erreur
			dans le login ou le mot de passe.</p>
		<?php endif; ?>
		<formulaire id="login" titre="Connexion" action="<?php echo $_SERVER['PHP_SELF']?>">
			<champ id="login" titre="Login" valeur="<?php echo $_POST['login']?>"/>
			<champ id="passwd" titre="Mot de passe" valeur=""/>
			<bouton id="connect" titre="Connexion"/>
		</formulaire>
		<p>Si tu as oublié ton mot de passe ou que tu pas encore de compte,
		clique <a href="<?php echo BASE_URL.'/profil/mdp_perdu.php'?>">ici</a>.</p>
	</contenu>
<?php
	echo "</page>\n";
	require "page_footer.inc.php";
	
	exit;
}
?>