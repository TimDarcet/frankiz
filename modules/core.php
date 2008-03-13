<?
class CoreModule extends PLModule
{
	function handlers()
	{
		return array('403' 	=> $this->make_hook('403', 	AUTH_PUBLIC),
			     '4O4' 	=> $this->make_hook('404', 	AUTH_PUBLIC),
			     '500'      => $this->make_hook('500',      AUTH_PUBLIC),
			     'login'	=> $this->make_hook('login', 	AUTH_MDP),
			     'do_login' => $this->make_hook('do_login', AUTH_PUBLIC));
	}

	function handler_403(&$page)
	{
		header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
		$page->assign("title", "403 Forbidden");
		$page->changeTpl('core/403.tpl');
	}

	function handler_404(&$page)
	{
		header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
		$page->assign("title", "404 Not Found");
		$page->changeTpl('core/404.tpl');
	}

	function handler_500(&$page)
	{
		header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Error');
		$page->assign("title", "500 Internal Error");
		$page->changeTpl('core/500.tpl');
	}

	function handler_login(&$page)
	{
		$page->assign('title', "Accueil");
		$page->changeTpl('core/accueil.tpl');
	}

	function handler_do_login(&$page)
	{
		$page->assign('title', "Connexion");
		$page->changeTpl('core/password_prompt.tpl');
	}
}
?>
