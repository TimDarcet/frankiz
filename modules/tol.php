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
            'tol'             => $this->make_hook('tol',             AUTH_INTERNAL, ''),
            'tol/birthday'    => $this->make_hook('tol_birthday',    AUTH_INTERNAL, ''),
            'tol/apv'         => $this->make_hook('tol_apv',         AUTH_INTERNAL, ''),
            'tol/see'         => $this->make_hook('see',             AUTH_INTERNAL, ''),
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
                        'cellphone' => '',
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

    function buildFilter($fields, Collection $already_groups = null)
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

        if ($fields['cellphone'])
            $conds[] = new UFC_Cellphone(preg_replace('/[^0-9]/', '', $fields['cellphone']));

        if ($fields['room'])
            $conds[] = new UFC_Room(str_replace(' ', '', $fields['room']));

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
                $gids = explode(';', $fields[$field]);
                if ($already_groups !== null) {
                    $already_groups->add($gids);
                }
                $conds[] = new UFC_Group($gids);
            }
        }

        if (count($conds) > 0)
            return new PFC_And($conds);
        else
            return false;
    }

    function handler_tol_birthday($page)
    {
        $on_platal = Group::from('on_platal');
        $next_week = new FrankizDateTime();
        $week = new DateInterval('P7D');
        $next_week = $next_week->add($week);

        $uf = new UserFilter(new PFC_And(new UFC_Group($on_platal, Rights::member()),
                                         new UFC_Birthday('>=', new FrankizDateTime()),
                                         new UFC_Birthday('<=', $next_week)),
                             new UFO_Birthday());
        $users = $uf->get()->select(UserSelect::tol());

        header('Content-Type: text/html; charset=utf-8');
        echo '<pre>';
        foreach ($users as $u) {
            $study = reset($u->studies());
            echo $study->promo() . ',' . $u->birthdate()->format('d/m/Y') . ',' . $u->lastname() . ',' . $u->firstname() . "\n";
        }
        echo '</pre>';
        exit;
    }

    function handler_tol_apv($page, $poly)
    {
        $uf = new UserFilter(new UFC_Poly($poly));
        $u = $uf->get(true);

        header('Content-Type: text/html; charset=utf-8');
        if ($u) {
            $u->select(UserSelect::base());
            echo $u->image()->src('full');
        }

        exit;
    }

    function handler_tol($page)
    {
        $fields = $this->fillFields();
        $already_groups = new Collection('Group');
        $filter = $this->buildFilter($fields, $already_groups);

        $page->assign('promoDefaultFilters', S::user()->defaultFilters()->filter('ns', Group::NS_PROMO));

        if ($filter) {
            if ($already_groups->count() > 0) {
                $already_groups->select(GroupSelect::base());
            }

            $filterWithDefaultFilters = $filter;
            if (S::user()->defaultFilters()->count() > 0) {
                $filterWithDefaultFilters = new PFC_And($filter, new UFC_Group(S::user()->defaultFilters()));
            }

            $order = array(new UFO_Promo(true), new UFO_Name(UFO_Name::LASTNAME));
            $uf = new UserFilter($filterWithDefaultFilters, $order);

            $users = $uf->get(new PlLimit(50,0));
            if ($users->count() == 0) {
                $uf = new UserFilter($filter, $order);
                $users = $uf->get(new PlLimit(50,0));
                $page->assign('promoDefaultFilters', null);
            }

            $users->select(UserSelect::tol());
            $page->assign('results', $users);
            $page->assign('mode', 'sheet');
            $page->assign('total', $uf->getTotalCount());
        }

        $page->assign('already_groups', $already_groups);
        $page->assign('user', S::user());
        $page->assign('fields', $fields);
        $page->assign('su', S::user()->isAdmin());
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

        $page->assign('su', S::user()->isAdmin());
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
            $uf = new UserFilter($filter, array(new UFO_Promo(true), new UFO_Name(UFO_Name::LASTNAME)));
            if ($json->mode == 'card') {
                $users = $uf->get(new PlLimit(20,(JSON::i('page', 1) - 1) * 20))->select(UserSelect::base());
            } else {
                $users = $uf->get(new PlLimit(50,(JSON::i('page', 1) - 1) * 50))->select(UserSelect::tol());
            }

            $page->assign('su', S::user()->isAdmin());
            $page->jsonAssign('total', $uf->getTotalCount());
            foreach($users as $k => $user) {
                $page->assign('result', $user);
                if ($json->mode == 'card') {
                    $page->assign('mode', 'card');
                } else {
                    $page->assign('mode', 'sheet');
                }
                try {
                    $fiches[$user->id()] = $page->filteredFetch(FrankizPage::getTplPath('tol/result.tpl'));
                } catch (Exception $e) {
                    XDB::execute('INSERT INTO tol_errors SET error = {?}', $user->id());
                }
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

        if ($u) {
            $u->select(UserSelect::tol());
        }

        $page->assign('su', S::user()->isAdmin());
        $page->assign('result', $u);
        try {
            $sheet = $page->filteredFetch(FrankizPage::getTplPath('tol/sheet.tpl'));
        } catch (Exception $e) {
            $sheet = "La fiche de l'utilisateur comporte des erreurs";
            XDB::execute('INSERT INTO tol_errors SET error = {?}', $u->id());
        }

        $page->jsonAssign('sheet', $sheet);
        $page->jsonAssign('success', true);

        return PL_JSON;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
