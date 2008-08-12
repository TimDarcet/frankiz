<?php
/*
	Copyright (C) 2008 Binet Réseau
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

class FrankizPage extends PlPage
{
    public function __construct()
    {
       parent::__construct();

       // Set the default page
       $this->changeTpl('annonces.tpl');
    }
    
    private function get_minimodules()
    {
    	require_once "minimodules.inc.php";
		return $minimodules_list;
    }

	private function load_skin()
	{
    	$skin = new FrankizSkin(1);
    	S::set('skin', $skin);
		//TODO : do only if we are serving the webpage, not the RSS or a webservice/minipage
		$skin->select_minimodules($this->get_minimodules());
		return $skin;
	}

    public function run()
    {
		$skin = $this->load_skin();
	//Run with the default skin disposition (i.e disposition du contenu)
    	call_user_func_array(array('FrankizMiniModule', 'load_modules'), array_keys($skin->minimodules));
		$this->_run("skin/{$skin->base}.tpl");
    }
}

?>
