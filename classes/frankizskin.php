<?php

class FrankizSkin
{
	public $path;
	public $params;
	public $base;
	public $minimodules;
	public function __construct($skin_id){
		$res = XDB::query("SELECT  sb.name as base, sb.params as params, sc.name as path
					FROM skin_css as sc LEFT JOIN skin_base as sb USING (skin_base_id) WHERE skin_css_id={?}", $skin_id);
		if($res->numRows()){
			list($this->base, $this->params, $this->path) = $res->fetchOneRow();
		}
	}

	public function select_minimodules($minimodules_list)
	{
		$this->minimodules = array();
		if(S::has("minimodules_disabled")){
			$minimodules_disabled=S::v("minimodules_disabled");
		}else{
			$minimodules_disabled=array();
		}
		foreach($minimodules_list as $minimodule)
		{
			if(!array_key_exists($minimodule, $minimodules_disabled))
			{
				$this->minimodules[$minimodule] = true;
			}
		}
	}

	public function has_minimodule($name)
	{
		return array_key_exists($name, $minimodules);
	}

}

?>
