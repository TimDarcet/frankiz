<?

/**
 * Base class for Frankiz MiniModules (these are the small boxes displayed on the left and right column 
 * of the website)
 */
abstract class FrankizMiniModule
{
	protected $tpl = null;
	protected $header_tpl = null;
	protected $titre = "Not Defined!";

	private $params = array();
	
	public function get_params()
	{
		return $this->params;
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

    public function get_template()
    {
        return $this->tpl;
    }
	/**
	 * Assigne une variable pour la template du minimodule uniquement. Ces variables seront accessibles dans 
	 * $minimodule.var_name à l'intérieur des template.
	 */
	protected function assign($key, $value)
	{
        $this->params[$key] = $value;
	}
	
	
	//Initializes a minimodule, which should then register with FrankizMiniModule::register()
	abstract static function init();

	/* static stuff */
	private static $minimodules = array();
    //Stores minimodules handlers
    private static $minimodules_handlers = array();
    //stores the name of the module being executed
    private static $curr_name;
    /**
     * registers the list of minimodules
     */
    public static function register_modules()
    {
        // Load list of available minimodules
        require_once 'minimodules.inc.php';
        foreach($minimodules_list as $name)
        {
            FrankizMiniModule::_init($name);
        }
    }

    //Includes
    private static function _init($name)
    {
        global $globals;
        $cls=ucfirst($name)."MiniModule";
        $path=$globals->spoolroot . "/modules/minimodules/" . strtolower($name) . ".php";
        include_once $path;
        call_user_func(array($cls, 'init'));
    }

    /** registers a minimmodule
     * @param name name of the minimodule
     * @param minimodule object of the minimodule class
     * @param handler name of the function that realizes the minimodule
     * @param auth minimal auth to see the minimodule
     * @param perms minimal perms to see the minimodule
     */
	public static function register($name, $minimodule, $handler, $auth, $perms='user')
	{
        if(!FrankizSkin::is_minimodule_disabled($name)){
            FrankizMiniModule::$minimodules[$name]=array(
                'object' => $minimodule,
                'handler' => array($minimodule, $handler),
		    	'auth'  => $auth);
                if(!is_null($perms)){
    		        FrankizMiniModule::$minimodules[$name]['perms']=$perms;
                }
        }
    }

    /** runs the modules
     */
    public static function run_modules()
    {
        foreach(self::$minimodules as $name=>$data)
        {
            if(self::check_perms($data))
            {
                $data['object']->name = $name;
                call_user_func($data['handler']);
            }else{
                unset(self::$minimodules[$name]);
            }
        }
    }
	private static function check_perms($data)
    {
        if($data['auth'] > S::v('auth'))
        {
            return false;
        }

		if (!array_key_exists('perms', $data)) 
        { // No perms, no check
			return true;
		}
		$s_perms = S::v('perms');
		return $s_perms->hasFlagCombination($data['perms']);
//		return true;
	}
	
    /**
	 * Renvoie un tableau des descriptions des minimodules indexé par les 
	 * identifiants des minimodules.
	 */
	public static function get_minimodules()
	{
	    $res=array();
        foreach(self::$minimodules as $name => $data)
        {
            $res[$name] = $data['object'];
        }
        return $res;
    }


}

?>
