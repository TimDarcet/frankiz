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
            'accueil'   => $this->make_hook('accueil', AUTH_PUBLIC),
            'exit'      => $this->make_hook('exit', AUTH_PUBLIC),
        );
    }

    function handler_accueil(&$page)
    {
        $page->assign('title', 'Accueil');
        $page->changeTpl('frankiz/accueil.tpl');
    }

    function handler_exit(&$page, $level = null)
    {
        if(S::has('suid')) {
            Platal::session()->stopSUID();
            pl_redirect('/');
        }
        if ($level == 'forget' || $level == 'forgetall') {
            Platal::session()->killAccessCookie();
        }
        if ($level == 'forgetuid' || $level == 'forgetall') {
            Platal::session()->killLoginFormCookies();
        }
        Platal::session()->destroy();
        $page->changeTpl('exit.tpl');
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
