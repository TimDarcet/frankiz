<?php

class FrankizSkin
{
	public $path;
	public $params;
	public $base;
	public function __construct($skin_id){
		$res = XDB::query("SELECT  sb.name as base, sb.params as params, sc.name as path
					FROM skin_css as sc LEFT JOIN skin_base as sb USING (skin_base_id) WHERE skin_css_id={?}", $skin_id);
		if($res->numRows()){
			list($this->base, $this->params, $this->path) = $res->fetchOneRow();
		}
	}

}

?>
