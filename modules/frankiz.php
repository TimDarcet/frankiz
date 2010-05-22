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
            'accueil'                  => $this->make_hook('accueil',                 AUTH_PUBLIC),
            'exit'                     => $this->make_hook('exit',                    AUTH_PUBLIC),
            'minimodules/ajax/layout'  => $this->make_hook('ajax_minimodules_layout', AUTH_PUBLIC),
            'minimodules/ajax/add'     => $this->make_hook('ajax_minimodules_add',    AUTH_PUBLIC),
            'minimodules/ajax/remove'  => $this->make_hook('ajax_minimodules_remove', AUTH_PUBLIC),
            'minimodules/ajax/get'     => $this->make_hook('ajax_minimodules_get',    AUTH_PUBLIC),
            'navigation/ajax/order'    => $this->make_hook('ajax_navigation_order',   AUTH_PUBLIC),
            'navigation/ajax/layout'   => $this->make_hook('ajax_navigation_layout',  AUTH_PUBLIC),
        );
    }

    function handler_accueil(&$page)
    {
        FrankizMiniModule::preload(array(FrankizMiniModule::MAIN_LEFT, FrankizMiniModule::MAIN_MIDDLE, FrankizMiniModule::MAIN_RIGHT));
        $page->assign('title', 'Accueil');
        $page->changeTpl('frankiz/accueil.tpl');
    }

    function handler_exit(&$page, $level = null)
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

    function handler_ajax_minimodules_layout(&$page)
    {
        $json = json_decode(Env::v('json'));

        $layout = array();
        for($i = 1; $i <= 4; $i++)
        {
            if (isset($json->{$i}))
            {
                foreach ($json->{$i} as $row => $name)
                {
                    $layout[] = '('.S::user()->id().', "'.$name.'", '.$i.', '.intval($row).')';
                }
            }
        }

        XDB::execute('INSERT INTO users_minimodules (uid, name, col, row)
                           VALUES '.implode(', ', $layout).'
          ON DUPLICATE KEY UPDATE col=VALUES(col), row = VALUES(row)');

        if (XDB::affectedRows() > 0) {
            $page->jsonAssign('success', true);
        } else {
            $page->jsonAssign('success', false);
            $page->jsonAssign('error', "Le réagencement des minimodules n'a pas pu se faire");
        }
    }

    function handler_ajax_minimodules_add(&$page)
    {
        $json = json_decode(Env::v('json'));

        if (is_string($json->{'name'}))
        {
            XDB::execute('INSERT INTO users_minimodules
                                  SET uid = {?}, name = {?}, col = 4, row = (SELECT COALESCE(MIN(row),0) FROM users_minimodules AS um WHERE um.uid = {?})-1
              ON DUPLICATE KEY UPDATE row = (SELECT COALESCE(MIN(row),0) FROM users_minimodules AS um WHERE um.uid = {?})-1',
                                  S::user()->id(),
                                  $json->{'name'},
                                  S::user()->id(),
                                  S::user()->id());
            $done = (XDB::affectedRows() > 0);

            if (XDB::affectedRows() > 0) {
                $page->jsonAssign('success', true);
            } else {
                $page->jsonAssign('success', false);
                $page->jsonAssign('error', "Impossible d'activer le minimodule");
            }
        } else {
            $page->jsonAssign('success', false);
            $page->jsonAssign('error', "Requête erronée");
        }
    }

    function handler_ajax_minimodules_remove(&$page)
    {
        $json = json_decode(Env::v('json'));

        if (is_string($json->{'name'}))
        {
            XDB::execute('DELETE FROM users_minimodules
                                WHERE uid = {?} AND name = {?}
                                LIMIT 1',
                              S::user()->id(),
                              $json->{'name'});

            $done = (XDB::affectedRows() > 0);

            if (XDB::affectedRows() > 0) {
                $page->jsonAssign('success', true);
            } else {
                $page->jsonAssign('success', false);
                $page->jsonAssign('error', 'Impossible de désactiver le minimodule');
            }
        } else {
            $page->jsonAssign('success', false);
            $page->jsonAssign('error', 'Requête erronée');
        }
    }

    function handler_ajax_minimodules_get(&$page)
    {
        $json = json_decode(Env::v('json'));
        $name = $json->{'name'};

        if (is_string($name))
        {
            $page->assign('module_name', $name);

            FrankizMiniModule::oneShot($name);
            $minimodules = FrankizMiniModule::get_minimodules();

            if (count($minimodules) == 1) {
                $page->assign('minimodules', $minimodules);
                $page->jsonAssign('name', $name);
                $page->jsonAssign('html', $page->fetch(FrankizPage::getTplPath('minimodule.tpl')));
                $js = FrankizMiniModule::get_js();
                if ($js[$name] != '') $page->jsonAssign('js', $js[$name]);
                $page->jsonAssign('success', true);
            } else {
            $page->jsonAssign('success', false);
            $page->jsonAssign('error', 'Le minimodule n\'existe pas');
            }
        }
        else
        {
            $page->jsonAssign('success', false);
            $page->jsonAssign('error', 'Requête erronée');
        }
    }

    // Save the order the user selects for his groups
    function handler_ajax_navigation_order(&$page)
    {
        $json = json_decode(Env::v('json'));

        $layout = array();

        if (isset($json->{'layout'}))
        {
            foreach ($json->{'layout'} as $rank => $gid)
            {
                $layout[] = '('.S::user()->id().', '.$gid.', '.$rank.', "temp", "")';
            }
        }

        XDB::execute('INSERT INTO users_groups (uid, gid, rank, job, title)
                           VALUES '.implode(', ', $layout).'
          ON DUPLICATE KEY UPDATE rank = VALUES(rank)');

        if (XDB::affectedRows() > 0) {
            $page->jsonAssign('success', true);
        } else {
            $page->jsonAssign('success', false);
            $page->jsonAssign('error', "Réagencement du menu impossible");
        }
        XDB::execute('DELETE FROM users_groups WHERE job = "temp"');

        S::user()->buildGroups();
    }

    // Save the state of the sub-menus : collapsed or not
    function handler_ajax_navigation_layout(&$page)
    {
        $json = json_decode(Env::v('json'));

        if (isset($json->{'layout'}))
        {
            S::set('nav_layout', json_encode($json->{'layout'}));
            XDB::execute('UPDATE account
                             SET nav_layout = {?}
                           WHERE uid = {?}',
                        json_encode($json->{'layout'}),
                        S::user()->id());
        }

        if (XDB::affectedRows() > 0) {
            $page->jsonAssign('success', true);
        } else {
            $page->jsonAssign('success', false);
            $page->jsonAssign('error', "Réagencement du menu impossible");
        }
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
