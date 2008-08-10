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
    	$skin = new FrankizSkin(1);
    	S::set('skin', $skin);
	//Run with the default skin disposition (i.e disposition du contenu)
    	$this->_run("skin/{$skin->base}.tpl");
    }
}

?>
