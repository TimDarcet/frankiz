<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                       *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                     *
 *                                                                         *
 *  This program is free software; you can redistribute it and/or modify   *
 *  it under the terms of the GNU General Public License as published by   *
 *  the Free Software Foundation; either version 2 of the License, or      *
 *  (at your option) any later version.                                    *
 *                                                                         *
 *  This program is distributed in the hope that it will be useful,        *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of         *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          *
 *  GNU General Public License for more details.                           *
 *                                                                         *
 *  You should have received a copy of the GNU General Public License      *
 *  along with this program; if not, write to the Free Software            *
 *  Foundation, Inc.,                                                      *
 *  59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                *
 ***************************************************************************/

/**
 * Class for frankiz pages
 */

class FrankizPage extends PlPage
{
    public function __construct()
    {
        parent::__construct();
        FrankizMiniModule::preload(FrankizMiniModule::FLOAT_RIGHT);
        // Set the default page
        $this->changeTpl('frankiz.tpl');
    }
    
    private function load_skin()
    {
        global $globals;
        if(!S::has('skin') || S::v('skin') == ""){
            //TODO : do only if we are serving the webpage, not the RSS or a webservice/minipage
            if (Cookie::has('skin')) {
                $skin = Cookie::v('skin');
            } else {
                $skin = $globals->skin;
            }
            S::set('skin', $skin);
        } else {
            $skin=S::v('skin');
            if (S::v('auth')>= AUTH_COOKIE && Cookie::v('skin') != $skin){
                Cookie::set('skin', $skin, 300);
            }
        }
        return $skin;
    }

    public function run()
    {
        $skin = $this->load_skin();
        FrankizMiniModule::run();
        $this->assign('minimodules', FrankizMiniModule::get_minimodules());
        $this->assign('minimodules_layout', FrankizMiniModule::get_layout());
        $this->assign('minimodules_js', FrankizMiniModule::get_js());

        if (S::logged())
        {
            $this->assign('clubs_layout', Group::getLayout(group::CLUB));
            $this->assign('free_layout' , Group::getLayout(group::FREE));
            //nav_layout contains the json datas describing if a sub-menu is collapsed or not
            $this->assign('nav_layout'  , S::user()->nav_layout());
        } else {
            //if not logged, show all sub-menus
            $this->assign('nav_layout'  , '{}');
        }

        $this->assign('logged', S::logged());
        $this->assign('level', substr_count(trim(Get::v('n'),'/'),'/'));
        //Run with the default skin disposition (i.e content disposition)
        $this->_run("skin/{$skin}/frankiz.tpl");
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
