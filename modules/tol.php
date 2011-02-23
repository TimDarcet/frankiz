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

class TolModule extends PLModule
{
    function handlers()
    {
        return array(
            'tol'             => $this->make_hook('tol'     ,        AUTH_INTERNAL, ''),
            'tol/see'         => $this->make_hook('see'     ,        AUTH_INTERNAL, ''),
            'tol/ajax/search' => $this->make_hook('tol_ajax_search', AUTH_INTERNAL, ''),
            'tol/ajax/sheet'  => $this->make_hook('tol_ajax_sheet',  AUTH_INTERNAL, '')
            );
    }

    function fillFields($json = false)
    {
        $fields = array(     'free' => '',
                            'hruid' => '',
                        'firstname' => '',
                         'lastname' => '',
                         'nickname' => '',
                    'nationalities' => '',
                            'promo' => '',
                          'studies' => '',
                           'sports' => '',
                           'binets' => '',
                             'frees' => '',
                             'room' => '',
                            'phone' => '',
                               'ip' => '',
                           'gender' => '');

        foreach(array_keys($fields) as $field) {
            if ($json)
                $fields[$field] = (isset($json->{$field}) && trim($json->{$field}) != '') ? trim($json->{$field}) : false;
            else
                $fields[$field] = (Env::t($field, '') != '') ? Env::t($field, '') : false;
        }

        return $fields;
    }

    function buildFilter($fields)
    {
        $conds = array();

        if ($fields['free']) {
            $pieces = explode(' ', $fields['free']);
            foreach ($pieces as $piece) {
                $freeconds = array();
                $freeconds[] = new UFC_Name($piece, UFC_Name::LASTNAME|UFC_Name::FIRSTNAME|UFC_Name::NICKNAME, UFC_Name::CONTAINS);
                $freeconds[] = new UFC_Room($piece);
                $freeconds[] = new UFC_Roomphone($piece);
                // Very simple regex to check if $piece might be a piece of an IP adress
                if (preg_match('/[0-9.]*/', $piece)) {
                    $freeconds[] = new UFC_Ip($piece);
                }
                $conds[] = new PFC_Or($freeconds);
            }
        }

        if ($fields['hruid'])
            $conds[] = new UFC_Hruid($fields['hruid']);

        if ($fields['firstname'])
            $conds[] = new UFC_Name($fields['firstname'], UFC_Name::FIRSTNAME, UFC_Name::CONTAINS);

        if ($fields['lastname'])
            $conds[] = new UFC_Name($fields['lastname'], UFC_Name::LASTNAME, UFC_Name::CONTAINS);

        if ($fields['nickname'])
            $conds[] = new UFC_Name($fields['nickname'], UFC_Name::NICKNAME, UFC_Name::CONTAINS);

        if ($fields['room'])
            $conds[] = new UFC_Room($fields['room']);

        if ($fields['phone'])
            $conds[] = new UFC_Roomphone($fields['phone']);

        if ($fields['ip'])
            $conds[] = new UFC_Ip($fields['ip']);

        if ($fields['gender'] == User::GENDER_FEMALE)
            $conds[] = new UFC_Gender(User::GENDER_FEMALE);

        // Fields corresponding in fact to groups
        $groups_fields = array('nationalities', 'promo', 'studies',
                              'sports', 'binets', 'frees');
        foreach ($groups_fields as $field) {
            if ($fields[$field]) {
                $conds[] = new UFC_Group(explode(';', $fields[$field]));
            }
        }

        if (count($conds) > 0)
            return new PFC_And($conds);
        else
            return false;
    }

    function handler_tol($page)
    {
        global $globals;

        $fields = $this->fillFields();
        $filter = $this->buildFilter($fields);

        if ($filter) {
            $uf = new UserFilter($filter, array(new UFO_Promo(true), new UFO_Name(UFO_Name::LASTNAME)));
            $users = $uf->get(new PlLimit(50,0))->select(UserSelect::tol());
            if (Env::has('quicksearch') && $users->count() == 0) {
                header('Location: http://wikix.polytechnique.org/eleves/wikix/Sp%C3%A9cial:Recherche?search=' . Env::t('free'));
                exit;
            }
            $page->assign('results', $users);
            $page->assign('mode', 'sheet');
            $page->assign('total', $uf->getTotalCount());
        }

        foreach (json_decode($globals->core->promos) as $promo) {
            $groupes_names[] = 'promo_' . $promo;
        }
        $promos = new Collection('Group');
        $promos->add($groupes_names)->select(GroupSelect::base());
        $page->assign('promos', $promos);

        $page->assign('user', S::user());
        $page->assign('fields', $fields);
        $page->assign('title', 'Trombino On Line');
        $page->addCssLink('tol.css');
        $page->changeTpl('tol/tol.tpl');
    }

    function handler_see($page, $hruid)
    {
        $uf = new UserFilter(new UFC_Hruid($hruid));
        $user = $uf->get(true);

        if ($user) {
            $user->select(UserSelect::tol());
        }

        $page->assign('result', $user);
        $page->assign('mode', 'sheet');

        $page->assign('title', 'Trombino On Line');
        $page->addCssLink('tol.css');
        $page->changeTpl('tol/see.tpl');
    }

    function handler_tol_ajax_search($page)
    {
        $json = json_decode(Env::v('json'));

        $fields = $this->fillFields($json);
        $filter = $this->buildFilter($fields);

        $fiches = array();
        if ($filter) {
            $uf = new UserFilter($filter, array(new UFO_Promo(false), new UFO_Name(UFO_Name::LASTNAME)));
            if ($json->mode == 'card') {
                $users = $uf->get(new PlLimit(20,(JSON::i('page', 1) - 1) * 20))->select(UserSelect::base());
            } else {
                $users = $uf->get(new PlLimit(50,(JSON::i('page', 1) - 1) * 50))->select(UserSelect::tol());
            }

            $page->jsonAssign('total', $uf->getTotalCount());
            foreach($users as $k => $user) {
                $page->assign('result', $user);
                if ($json->mode == 'card') {
                    $page->assign('mode', 'card');
                } else {
                    $page->assign('mode', 'sheet');
                }
                $fiches[$user->id()] = $page->fetch(FrankizPage::getTplPath('tol/result.tpl'));
            }
        }

        $page->jsonAssign('mode', $json->mode);
        $page->jsonAssign('results', $fiches);
        $page->jsonAssign('success', true);

        return PL_JSON;
    }

    function handler_tol_ajax_sheet($page, $uid)
    {
        $f = new UserFilter(new UFC_Uid($uid));
        $u = $f->get(true);
        $page->assign('result', $u);

        if ($u) {
            $u->select(UserSelect::tol());
        }

        $page->assign('result', $u);
        $sheet = $page->fetch(FrankizPage::getTplPath('tol/sheet.tpl'));

        $page->jsonAssign('sheet', $sheet);
        $page->jsonAssign('success', true);

        return PL_JSON;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
