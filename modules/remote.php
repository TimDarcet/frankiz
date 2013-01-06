<?php
/***************************************************************************
 *  Copyright (C) 2004-2013 Binet Réseau                                   *
 *  http://br.binets.fr/                                                   *
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

class RemoteModule extends PlModule
{
    function handlers()
    {
        return array(
            'remote'       => $this->make_hook('remote', AUTH_PUBLIC),
            'remote/admin' => $this->make_hook('admin',  AUTH_MDP, 'admin')
        );
    }

    function handler_remote($page)
    {
        global $globals, $platal;

        if (!(Env::has('timestamp') && Env::has('site') && Env::has('hash') && Env::has('request'))) {
            $page->trigError("Requête non valide");
            return;
        }

        // Read request
        $timestamp = Env::s('timestamp');
        if (abs($timestamp - time()) > $globals->remote->lag) {
            $page->trigError("Delai d'attente dépassé");
            return;
        }
        $site    = Env::s('site');
        $request = Env::s('request');

        // Load remote information
        try {
            $remote = Remote::from(Env::s('site'));
            $remote->select(RemoteSelect::groups());
        } catch (ItemNotFoundException $e) {
            $page->trigError("Ton site n'est pas renseigné dans la base de données");
            return;
        }

        // Check request
        if (md5($timestamp . $site . $remote->privkey() . $request) != Env::s('hash')) {
            $page->trigError("Erreur de validation de la requête d'authentification");
            return;
        }
        $request = json_decode($request, true);

        // Force login
        $user = Platal::session()->doAuthWithoutStart(AUTH_COOKIE);
        if (empty($user)) {
            $page->assign('remote_site', $remote->label());
            $platal->force_login($page);
            return PL_FORBIDDEN;
        }

        // Build response
        $response = array('uid' => $user->id());

        if ($remote->hasRight('names') && in_array('names', $request)) {
            $response['hruid']     = $user->login();
            $response['firstname'] = $user->firstname();
            $response['lastname']  = $user->lastname();
            $response['nickname']  = $user->nickname();
        }

        if ($remote->hasRight('email') && in_array('email', $request)) {
            $response['email'] = $user->email();
        }

        if ($remote->hasRight('rights') && in_array('rights', $request)) {
            $r = array();
            foreach ($remote->groups() as $g) {
                $r[$g->name()] = array_map(function($r) { return (string) $r; }, $user->rights($g));
            }
            if (!empty($r))
                $response['rights'] = $r;
        }

        if ($remote->hasRight('sport') && in_array('sport', $request)) {
            $groups = $user->castes()->groups();
            $group = $groups->filter('ns', Group::NS_SPORT)->first();
            if ($group)
                $response['sport'] = $group->label();
        }

        if ($remote->hasRight('promo') && in_array('promo', $request)) {
            $groups = $user->castes(Rights::member())->groups()->filter('ns', Group::NS_PROMO);
            $groups = $groups->remove(Group::from('on_platal'));
            // Extract promos from group labels
            // For backward compatibility, compute the minimal promo year
            $promo = 0;
            $promos = array();
            foreach ($groups as $g) {
                $matches = array();
                if (preg_match('/^promo_([a-z_]+)([1-9][0-9]{3})$/', $g->name(), $matches)) {
                    $promos[] = $matches[1] . $matches[2];
                    $year = (integer)$matches[2];
                    if (!$promo || $year < $promo) {
                        $promo = $year;
                    }
                }
            }
            if ($promo) {
                $response['promo'] = $promo;
                $response['promos'] = $promos;
            }
        }

        if ($remote->hasRight('photo') && in_array('photo', $request)) {
            $img = $user->photo();
            if ($img === false)
                $img = $user->original();
            if ($img !== false)
                $response['photo'] = $globals->baseurl . '/' . $img->src('full');
        }

        if ($remote->hasRight('binets_admin') && in_array('binets_admin', $request)) {
            $gf = new GroupFilter(new PFC_And(new GFC_User($user, Rights::admin()), new GFC_Namespace('binet')));
            $gs = $gf->get();

            if ($gs->count() > 0) {
                $gs->select(GroupSelect::base());

                $r = array();
                foreach ($gs as $g) {
                    $r[$g->name()] = $g->label();
                }
                if (!empty($r))
                    $response['binets_admin'] = $r;
            }
        }

        // Send response
        $response = json_encode($response);
        $location = Env::s('location');
        header('Location: ' . $site . '?location=' . $location . '&timestamp=' . $timestamp
           . '&response='  . $response
           . '&hash='      . md5($timestamp . $remote->privkey() . $response));
    }

    function handler_admin($page, $id = null, $action = null)
    {
        $page->assign('title', "Administration de l'authentification externe");
        $page->assign('remoterights_available', implode(',', Remote::availableRights()));

        // Find remote
        $remote = null;
        if ($id == 'new') {
            $remote = new Remote();
            $remote->insert();
        } elseif (Remote::isId($id)) {
            $remote = new Remote($id);
            // Delete a remote
            if ($action == 'delete') {
                $remote->delete();
                $remote = null;
            }
        }

        if (!empty($remote)) {
            $remote->select(RemoteSelect::groups());

            if (Env::has('change_remote')) {
                $remote->site(Env::t('site'));
                $remote->label(Env::t('label'));
                $remote->privkey(Env::t('privkey'));
                $rights = explode(',', Env::t('rights'));
                foreach ($rights as $k => $v) {
                    $rights[$k] = strtolower(trim($v));
                }
                $rights = array_intersect($rights, Remote::availableRights());
                $remote->rights(new PlFlagSet(implode(',', $rights)));
                $groups = new Collection('Group');
                $groups_fields = array('binets', 'frees');
                foreach ($groups_fields as $field) {
                    foreach(explode(';', Env::t($field)) as $gid) {
                        $gid = trim($gid);
                        if ($gid)
                            $groups->add(new Group($gid));
                    }
                }
                $groups->select(GroupSelect::base());
                $remote->groups($groups);
            }

            $page->assign('remote', $remote);
            $page->changeTpl('remote/admin.tpl');
        } else {
            $remotes = Remote::selectAll(RemoteSelect::groups());
            $page->assign('remotes', $remotes);
            $page->changeTpl('remote/list.tpl');
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
