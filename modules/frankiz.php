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
            'home'                  => $this->make_hook('home',           AUTH_PUBLIC),
            'home/contact'          => $this->make_hook('contact',        AUTH_PUBLIC),
            'home/howtocome'        => $this->make_hook('howtocome',      AUTH_PUBLIC),
            'masters'               => $this->make_hook('masters',        AUTH_PUBLIC),
            'universe'              => $this->make_hook('universe',       AUTH_PUBLIC),
            'remote'                => $this->make_hook('remote',         AUTH_COOKIE),
            'exit'                  => $this->make_hook('exit',           AUTH_PUBLIC)
        );
    }

    function handler_home($page)
    {
        $page->assign('MiniModules_COL_LEFT'  , FrankizMiniModule::get(S::user()->minimodules(FrankizMiniModule::COL_LEFT)));
        $page->assign('MiniModules_COL_MIDDLE', FrankizMiniModule::get(S::user()->minimodules(FrankizMiniModule::COL_MIDDLE)));
        $page->assign('MiniModules_COL_RIGHT' , FrankizMiniModule::get(S::user()->minimodules(FrankizMiniModule::COL_RIGHT)));

        $postit = Group::from('postit');
        // /!\ : Everybody can read the post-it, you don't have to be member of the group
        $nf = new NewsFilter(new PFC_And(new NFC_Current(), new NFC_TargetGroup($postit)), new NFO_Begin(true));
        $postit_news = $nf->get(true);
        if ($postit_news) {
            $postit_news->select(NewsSelect::news());
        }

        $page->assign('postit_news', $postit_news);
        $page->assign('title', 'Accueil');
        $page->changeTpl('frankiz/home.tpl');
    }

    function handler_contact($page)
    {
        $page->assign('title', 'Contact');
        $page->changeTpl('frankiz/contact.tpl');
    }

    function handler_howtocome($page)
    {
        $page->assign('title', 'Comment venir ?');
        $page->changeTpl('frankiz/howtocome.tpl');
    }

    function handler_universe($page)
    {
        echo $page->fetch('universe.tpl');
        exit;
    }

    function handler_masters($page)
    {
        $page->assign('title', 'Ouverture de compte master');
        $page->changeTpl('frankiz/masters.tpl');
    }

    function handler_remote($page)
    {
        global $globals;

        if (!(Env::has('timestamp') && Env::has('site') && Env::has('hash') && Env::has('request'))) {
            $page->trigError("Requête non valide");
            return;
        }
        $res = XDB::query('SELECT  id, privkey, rights
                             FROM  remote
                            WHERE  site = {?}', Env::s('site'));

        if ($res->numRows() != 1) {
            $page->trigError("Ton site n'est pas renseigné dans la base de données");
            return;
        }
        list($remote_id, $key, $rights) = $res->fetchOneRow();

        $timestamp = Env::s('timestamp');
        if (abs($timestamp - time()) > $globals->remote->lag) {
            $page->trigError("Delai d'attente dépassé");
            return;
        }

        $site    = Env::s('site');
        $request = Env::s('request');

        if (md5($timestamp . $site . $key . $request) != Env::s('hash')) {
            $page->trigError("Erreur de validation de la requête d'authentification");
            return;
        }

        $request = json_decode($request, true);
        $rights  = new PlFlagSet($rights);

        $response = array('uid' => S::user()->id());

        if ($rights->hasFlag('names') && in_array('names', $request)) {
            $response['hruid']     = S::user()->login();
            $response['firstname'] = S::user()->firstname();
            $response['lastname']  = S::user()->lastname();
            $response['nickname']  = S::user()->nickname();
        }

        if ($rights->hasFlag('email') && in_array('email', $request)) {
            $response['email'] = S::user()->email();
        }

        if ($rights->hasFlag('rights') && in_array('rights', $request)) {
            $res = XDB::query('SELECT name FROM remote_groups WHERE remote_id = {?}', $remote_id);

            $gf = new GroupFilter(new GFC_Name($res->fetchColumn()));
            $gs = $gf->get();

            if ($gs->count() > 0) {
                $gs->select(GroupSelect::base());

                $r = array();
                foreach ($gs as $g) {
                    $r[$g->name()] = array_map(function($r) { return (string) $r; }, S::user()->rights($g));
                }
                $response['rights'] = $r;
            }
        }

        if ($rights->hasFlag('sport') && in_array('sport', $request)) {
            $gf = new GroupFilter(new PFC_And(new GFC_Namespace('sport'), new GFC_User(S::user())));
            $groups = $gf->get()->select(GroupSelect::base())->toArray();
            if (count($groups) > 0) {
                $group = array_pop($groups);
                $response['sport'] = $group->label();
            }
        }

        if ($rights->hasFlag('photo') && in_array('photo', $request)) {
            $img = S::user()->photo();
            if ($img === false)
                $img = S::user()->original();
            if ($img !== false)
                $response['photo'] = $globals->baseurl . '/' . $img->src('full');
        }

        if ($rights->hasFlag('binets_admin') && in_array('binets_admin', $request)) {
            $gf = new GroupFilter(new PFC_And(new GFC_User(S::user(), Rights::admin()), new GFC_Namespace('binet')));
            $gs = $gf->get();

            if ($gs->count() > 0) {
                $gs->select(GroupSelect::base());

                $r = array();
                foreach ($gs as $g) {
                    $r[$g->name()] = $g->label();
                }
                $response['binets_admin'] = $r;
            }
        }

        $response = json_encode($response);
        $location = Env::s('location');
        header('Location: ' . $site . '?location=' . $location . '&timestamp=' . $timestamp
           . '&response='  . $response
           . '&hash='      . md5($timestamp . $key . $response));
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
