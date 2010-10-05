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

class TrombinoModule extends PLModule
{
    function handlers()
    {
        return array(
            'tol'       => $this->make_hook('tol'     , AUTH_COOKIE),   // TODO : set necessary perms
            'tol/ajax'  => $this->make_hook('tol_ajax', AUTH_COOKIE)
            );
    }

    function fillFields($json = false)
    {
        $fields = array(     'name' => '',
                        'firstname' => '',
                         'lastname' => '',
                         'nickname' => '',
                    'nationalities' => '',
                          'studies' => '',
                           'sports' => '',
                     'associations' => '',
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

        if ($fields['name']) {
            $pieces = explode(' ', $fields['name']);
            foreach ($pieces as $piece)
                $conds[] = new UFC_Name($piece, UFC_Name::LASTNAME|UFC_Name::FIRSTNAME|UFC_Name::NICKNAME, UFC_Name::CONTAINS);
        }

        if ($fields['firstname'])
            $conds[] = new UFC_Name($fields['firstname'], UFC_Name::FIRSTNAME, UFC_Name::CONTAINS);

        if ($fields['lastname'])
            $conds[] = new UFC_Name($fields['lastname'], UFC_Name::LASTNAME, UFC_Name::CONTAINS);

        if ($fields['nickname'])
            $conds[] = new UFC_Name($fields['nickname'], UFC_Name::NICKNAME, UFC_Name::CONTAINS);

        if ($fields['nationalities'])
            $conds[] = new UFC_Group(explode(';', $fields['nationalities']), Rights::MEMBER);

        if ($fields['studies'])
            $conds[] = new UFC_Group(explode(';', $fields['studies']), Rights::MEMBER);

        if ($fields['sports'])
            $conds[] = new UFC_Group(explode(';', $fields['sports']), Rights::MEMBER);

        if ($fields['associations'])
            $conds[] = new UFC_Group(explode(';', $fields['associations']), Rights::MEMBER);

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

    function handler_tol($page)
    {
        $fields = $this->fillFields();

        $filter = $this->buildFilter($fields);

        if ($filter) {
            $uf = new UserFilter($filter);
            $users = $uf->getUsers(new PlLimit(50,0))->select(User::SELECT_BASE | User::SELECT_GROUPS);
            $page->assign('results', $users);
            $page->assign('total', $uf->getTotalCount());
        }

        $roots = new Collection();
        $roots->className('Group');
        $roots->add(array('nationalities', 'studies', 'sports', 'associations'));
        $roots = $roots->toArray('name');

        $page->assign('fields', $fields);
        $page->assign('roots', $roots);
        $page->assign('title', 'Trombino On Line');
        $page->addCssLink('tol.css');
        $page->changeTpl('trombino/tol.tpl');
    }

    function handler_tol_ajax($page)
    {
        $json = json_decode(Env::v('json'));

        $fields = $this->fillFields($json);

        $filter = $this->buildFilter($fields);
        $fiches = array();

        if ($filter) {
            $uf = new UserFilter($filter);
            if ($json->mode == 'micro')
                $users = $uf->getUsers(new PlLimit(20,0))->select(User::SELECT_BASE)->toArray();
            else
                $users = $uf->getUsers(new PlLimit(50,0))->select(User::SELECT_BASE | User::SELECT_GROUPS);

            $page->jsonAssign('total', $uf->getTotalCount());
            foreach($users as $k => $user) {
                $page->assign('result', $user);
                if ($json->mode == 'micro')
                    $fiches[$user->id()] = $page->fetch(FrankizPage::getTplPath('trombino/microfiche.tpl'));
                else
                    $fiches[$user->id()] = $page->fetch(FrankizPage::getTplPath('trombino/fiche.tpl'));
            }
        }

        $page->jsonAssign('mode', $json->mode);
        $page->jsonAssign('results', $fiches);
        $page->jsonAssign('success', true);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
