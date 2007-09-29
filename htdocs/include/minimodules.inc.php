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

	private static $loaded_modules = array();

	/**
	 * Smarty callbask, used to print the template header of the minimodule
	 * @param $params[module] Name of the module
	 */
	public function print_template_header()
	{
		global $globals;

		if ($this->header_tpl)
			$globals->smarty->display($this->header_tpl);
	}

	/**
	 * Smarty callbask, used to print the template of the minimodule
	 * @param $params[module] Name of the module
	 */
	public function print_template()
	{
		global $globals;

		if ($this->tpl)
			$globals->smarty->display($this->tpl);
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
	 * Load a minimodule
	 * @param name Module name. This must match a filename in modules/
	 * @return the load module
	 */
	public static function load($name)
	{
		$name_full = $name."MiniModule";
		if (!call_user_func(array($name_full, "check_auth")))
			return 0;
		
		$module = new $name_full;
		FrankizMiniModule::$loaded_modules[$name] = $module;

		return $module;
	}

	/**
	 * Load a list of modules 
	 * @param ... a vararg list of modules
	 * @return the list of modules currently loaded
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

}

?>
