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
        FrankizMiniModule::register_modules();
        // Set the default page
        $this->changeTpl('annonces.tpl');
    }
    
    private function load_skin()
    {
        global $globals;
        if(!S::has('skin')){
	        //TODO : do only if we are serving the webpage, not the RSS or a webservice/minipage
            if(!($skin_id = $this->try_skin_cookie())){
                $skin_id = $globals->skin;
            }
        	S::set('skin', $skin_id);
        }else{
            $skin_id=S::v('skin');
            if(S::v('auth')>= AUTH_COOKIE && Cookie::v('skin') != S::v('skin')){
                setcookie('skin', $skin_id, (time() + 25920000), '/', '', 0);
            }
        }
        return $skin_id;
    }

    private function try_skin_cookie()
    {
//    var_dump($_COOKIE);
        if(Cookie::has('skin')){
            $res = XDB::query("SELECT skin_id FROM skins WHERE name = {?}", Cookie::v('skin'));
            if($res->numRows() != 1){
                return false;
            }
            return Cookie::v('skin');
        }
        return false;
    }
    public function run()
    {
    	global $globals;
        $globals->skin = $this->load_skin();
	    FrankizMiniModule::run_modules();
        $this->assign('minimodules', FrankizMiniModule::get_minimodules());
	    //Run with the default skin disposition (i.e content disposition)
        $this->_run("skin/{$globals->skin}.tpl");
    }
}

?>
