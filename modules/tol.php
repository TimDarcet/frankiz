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
            'tol'             => $this->make_hook('tol'     ,        AUTH_INTERNAL),
            'tol/ajax/search' => $this->make_hook('tol_ajax_search', AUTH_INTERNAL),
            'tol/ajax/sheet'  => $this->make_hook('tol_ajax_sheet',  AUTH_INTERNAL)
            );
    }

    function fillFields($json = false)
    {
        $fields = array(     'free' => '',
                        'firstname' => '',
                         'lastname' => '',
                         'nickname' => '',
                    'nationalities' => '',
                            'promo' => '',
                          'studies' => '',
                           'sports' => '',
                           'binets' => '',
                             'room' => '',
                            'phone' => '',
                               'ip' => '');

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
                // $freeconds[] = new UFC_Roomphone($piece);
                $freeconds[] = new UFC_Ip($piece);
                // $freeconds[] = new UFC_Formation($piece);
                // $freeconds[] = new UFC_Bestalias($piece);
                // $freeconds[] = new UFC_Birthday($piece);
                // $freeconds[] = new UFC_Cellphone($piece);
                $conds[] = new PFC_Or($freeconds);
            }
        }

        if ($fields['firstname'])
            $conds[] = new UFC_Name($fields['firstname'], UFC_Name::FIRSTNAME, UFC_Name::CONTAINS);

        if ($fields['lastname'])
            $conds[] = new UFC_Name($fields['lastname'], UFC_Name::LASTNAME, UFC_Name::CONTAINS);

        if ($fields['nickname'])
            $conds[] = new UFC_Name($fields['nickname'], UFC_Name::NICKNAME, UFC_Name::CONTAINS);

        if ($fields['nationalities'])
            $conds[] = new UFC_Group(explode(';', $fields['nationalities']));

        if ($fields['promo'])
            $conds[] = new UFC_Group(explode(';', $fields['promo']));

        if ($fields['studies'])
            $conds[] = new UFC_Group(explode(';', $fields['studies']));

        if ($fields['sports'])
            $conds[] = new UFC_Group(explode(';', $fields['sports']));

        if ($fields['binets'])
            $conds[] = new UFC_Group(explode(';', $fields['binets']));

        if ($fields['room'])
            $conds[] = new UFC_Room($fields['room']);

        if ($fields['phone'])
            $conds[] = new UFC_Roomphone($fields['phone']);

        if ($fields['ip'])
            $conds[] = new UFC_Ip($fields['ip']);

        if (count($conds) > 0)
            return new PFC_And($conds);
        else
            return false;
    }

    function toSelect()
    {
        return array(User::SELECT_BASE => null, User::SELECT_ROOMS => null, User::SELECT_POLY =>null,
                     User::SELECT_CASTES => array(Caste::SELECT_BASE => Group::SELECT_BASE));
    }

    function handler_tol($page)
    {
        $fields = $this->fillFields();
        $filter = $this->buildFilter($fields);

        if ($filter) {
            $uf = new UserFilter($filter);
            $users = $uf->get(new PlLimit(50,0))->select($this->toSelect());
            $page->assign('results', $users);
            $page->assign('total', $uf->getTotalCount());
        }

        $page->assign('fields', $fields);
        $page->assign('title', 'Trombino On Line');
        $page->addCssLink('tol.css');
        $page->changeTpl('tol/tol.tpl');
    }

    function handler_tol_ajax_search($page)
    {
        $json = json_decode(Env::v('json'));

        $fields = $this->fillFields($json);
        $filter = $this->buildFilter($fields);

        $fiches = array();
        if ($filter) {
            $uf = new UserFilter($filter);
            if ($json->mode == 'card')
                $users = $uf->get(new PlLimit(20,0))->select(User::SELECT_BASE);
            else
                $users = $uf->get(new PlLimit(50,0))->select($this->toSelect());

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
            $u->select($this->toSelect());
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
