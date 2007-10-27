<?

/**
 * Base class for Frankiz MiniModules (these are the small boxes displayed on the left and right column 
 * of the website)
 */
class FrankizMiniModule
{
	protected $tpl = null;
	protected $header_tpl = null;
	protected $titre = "Not Defined!";

	private $params = array();
	private static $registered_modules = array();
	private static $loaded_modules = array();

	/**
	 * Smarty callbask, used to print the template header of the minimodule
	 * @param $params[module] Name of the module
	 */
	public function print_template_header()
	{
		global $page;

		$page->assign('minimodule', $this->params);
		if ($this->header_tpl)
			$page->display($this->header_tpl);
		$page->assign('minimodule', null);
	}

	/**
	 * Smarty callbask, used to print the template of the minimodule
	 * @param $params[module] Name of the module
	 */
	public function print_template()
	{
		global $page;
		
		$page->assign('minimodule', $this->params);
		if ($this->tpl)
			$page->display($this->tpl);
		$page->assign('minimodule', $params);
	}

	/**
	 * Returns the title of the module
	 * This is different from the identifier.
	 * @return Title of the module
	 */
	public function get_titre()
	{
		return $this->titre;
	}

	/**
	 * Returns if the module contains data
	 */
	public function is_empty()
	{
		return $this->tpl == null;
	}
	
	/**
	 * Assigne une variable pour la template du minimodule uniquement. Ces variables seront accessibles dans 
	 * $minimodule.var_name à l'intérieur des template.
	 */
	protected function assign($key, $value)
	{
		$this->params[$key] = $value;
	}
	
	/**
	 * Charge un minimodule.
	 * @param name Nom du module. C'est le nom utilisé lors de l'appel a register_module.
	 * @return le module chargé
	 */
	public static function load($name)
	{
		if (!isset(FrankizMiniModule::$registered_modules[$name]))
			return 0;

		$desc = FrankizMiniModule::$registered_modules[$name];
		
		if (!call_user_func(array($desc['classname'], 'check_auth')))
			return 0;

		
		$reflect = new ReflectionClass($desc['classname']);
		$mod = $reflect->newInstanceArgs($desc['params']);
		FrankizMiniModule::$loaded_modules[$name] = $mod;

		return $mod;
	}

	/**
	 * Charge une liste de modules. 
	 * @param ... une liste vararg de modules.
	 * @return La liste des modules actuellement chargée
	 */
	public static function load_modules()
	{
		$modules = func_get_args();
		foreach ($modules as $module)
		{
			FrankizMiniModule::load($module);
		}

		return FrankizMiniModule::$loaded_modules;
	}

	/**
	 * Renvoie un tableau des descriptions des minimodules indexé par les 
	 * identifiants des minimodules.
	 */
	public static function get_minimodule_list()
	{
		$module_list = array();

		foreach (FrankizMiniModule::$registered_modules as $id => $module)
		{
			$module_list[$id] = $module['description'];
		}

		return $module_list;
	}


	/**
	 * Enregistre un module parmi les modules disponibles
	 * @param classname Nom de la classe.
	 * @param params (optionnel) Parametres à passer au constructeur de la classe.
	 */
	public static function register_module($name, $classname, $description, $params = array())
	{
		FrankizMiniModule::$registered_modules[$name] = array('classname' => $classname,
								      'params' => $params,
								      'description' => $description);
	}
}

// Inclus tous les fichiers contenant des minimodules. Ces fichiers se chargent d'eux mêmes d'enregistrer
// leurs modules.
require_once BASE_MODULES."minimodules/activites.php";
require_once BASE_MODULES."minimodules/anniversaires.php";
require_once BASE_MODULES."minimodules/fetes.php";
require_once BASE_MODULES."minimodules/lien_ik.php";
require_once BASE_MODULES."minimodules/lien_tol.php";
require_once BASE_MODULES."minimodules/lien_wikix.php";
require_once BASE_MODULES."minimodules/liens_navigation.php";
require_once BASE_MODULES."minimodules/liens_profil.php";
require_once BASE_MODULES."minimodules/liens_propositions.php";
require_once BASE_MODULES."minimodules/liens_utiles.php";
require_once BASE_MODULES."minimodules/meteo.php";
require_once BASE_MODULES."minimodules/qdj.php";
require_once BASE_MODULES."minimodules/annonce_virus.php";

?>
