<?

class FrankizPage extends PlatalPage
{
	public function __construct($tpl, $type = SKINNED)
	{
		parent::__construct($tpl, $type);
		$this->assign('page_base', BASE_URL);
		$this->assign('page_no_title', 0);
	}

	private function load_minimodules()
	{
		return FrankizMiniModule::load_modules('activites',
						       'anniversaires',
						       'fetes', 
				'meteo',
				'lien_ik', 
				'lien_tol', 
				'lien_wikix', 
				'liens_navigation', 
				'liens_perso', 
				'liens_profil', 
				'liens_propositions',
				'liens_utiles',
				'sondages',
				'qdj',
				'qdj_hier',
				'virus');
	}

	public function run()
	{
		global $minimodules;

		$this->assign('skin', $_SESSION['skin']);
		$this->assign('session', new FrankizSession);
		$this->assign('minimodules', $this->load_minimodules());
		if (isset($_SESSION['sueur']))
			$this->assign('sueur', $_SESSION['sueur']);
	
		$this->register_function("minimodule", array('FrankizMiniModule', "print_template"));
		$this->register_function("minimodule_header", array('FrankizMiniModule', "print_template_header"));
		$this->register_modifier("wiki_vers_html", "wikiVersXML");

		affiche_erreurs_php();
		$this->_run("main.tpl");
		affiche_debug_php();
	}
}

function new_skinned_page()
{
	global $page;

	$page = new FrankizPage('main.tpl');
}

?>
