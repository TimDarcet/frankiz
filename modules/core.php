<?
class CoreModule extends PLModule
{
	function handlers()
	{
		return array('403' 	=> $this->make_hook('403', 	AUTH_AUCUNE),
			     '4O4' 	=> $this->make_hook('404', 	AUTH_AUCUNE),
			     '500'      => $this->make_hook('500',      AUTH_AUCUNE),
			     'login'	=> $this->make_hook('login', 	AUTH_FORT),
			     'do_login' => $this->make_hook('do_login', AUTH_AUCUNE));
	}

	function handle_403(&$page)
	{
		header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
		$page->assign("title", "403 Forbidden");
?>
		<page id="erreur">
		  <warning>Une erreur 403 (Accès interdit) est survenue, empéchant l'accès à la page demandée.</warning>
		</page>
<?
	}

	function handle_404(&$page)
	{
		header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
		$page->assign("title", "404 Not Found");
?>
		<page id="erreur">
		  <warning>Une erreur 404 (Page inexistante) est survenue, empéchant l'accès à la page demandée.</warning>
		</page>
<?
	}

	function handle_500(&$page)
	{
		header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Error');
		$page->assign("title", "500 Internal Error");
?>
		<page id="erreur">
		  <warning>Une erreur 500 (Erreur interne au serveur) est survenue, empéchant l'accès à la page demandée.</warning>
		</page>
<?
	}

	function handle_login(&$page)
	{
		$page->assign('title', "Accueil");

?>
<page id="accueil">
        <h2>Tu es bien authentifié !</h2>
        <p>Te voilà prêt à accéder au fabuleux monde du campus de l'X.</p>
        <p>Si tu veux éviter de te ré-identifier à chaque fois que tu accèdes à cette page, active l'authentification
        par cookie dans tes <a href="profil/profil.php">préférences</a>.</p>
</page>
<?
	}

	function handle_do_login(&$page)
	{
		$page->assign('title', "Connexion");
?>
<page id="page_login"> 
  <?php if (a_erreur(ERR_LOGIN)): ?>
    <warning>Une erreur est survenue lors de l'authentification. Vérifie qu'il n'y a
    pas d'erreur dans le login ou le mot de passe.</warning>
  <?php endif; ?> 
  <note>Ton login est loginpoly.promo</note> 
  <formulaire id="login" titre="Connexion" action=<?php echo '"'.htmlentities($_SERVER['REQUEST_URI']).'"';?>>
    <?php  foreach ($_REQUEST AS $keys => $val) { 
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
<?
	}
}
?>
