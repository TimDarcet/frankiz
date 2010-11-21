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

/* contains all global stuff (exit, register, ...) */
class FrankizModule extends PlModule
{
    function handlers()
    {
        return array(
            'home'                  => $this->make_hook('home', AUTH_PUBLIC),
            'exit'                  => $this->make_hook('exit', AUTH_PUBLIC)
        );
    }

    function handler_home($page)
    {
        $page->assign('MiniModules_COL_LEFT'  , FrankizMiniModule::get(S::user()->minimodules(FrankizMiniModule::COL_LEFT)));
        $page->assign('MiniModules_COL_MIDDLE', FrankizMiniModule::get(S::user()->minimodules(FrankizMiniModule::COL_MIDDLE)));
        $page->assign('MiniModules_COL_RIGHT' , FrankizMiniModule::get(S::user()->minimodules(FrankizMiniModule::COL_RIGHT)));

        $postit = Group::from('postit');
        // /!\ : Everybody can read the post-it, you don't have to be member of the group
        $nf = new NewsFilter(new PFC_And(new NFC_Current(), new NFC_Target($postit)), new NFO_Begin(true));
        $postit_news = $nf->get(true)->select();

        $page->assign('postit_news', $postit_news);
        $page->assign('title', 'Accueil');
        $page->changeTpl('frankiz/home.tpl');
    }

    function handler_exit($page, $level = null)
    {
        global $globals;

        if(S::has('suid')) {
            Platal::session()->stopSUID();
            pl_redirect('/');
        }

        Platal::session()->destroy();
        http_redirect($globals->baseurl_http);
        $page->changeTpl('exit.tpl');
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
