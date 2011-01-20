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

class GroupsModule extends PLModule
{
    public function handlers()
    {
        return array(
            'groups'                  => $this->make_hook('groups',            AUTH_PUBLIC),
            'groups/ajax/search'      => $this->make_hook('ajax_search',       AUTH_PUBLIC),
            'groups/ajax/insert'      => $this->make_hook('ajax_insert',       AUTH_COOKIE),
            'groups/ajax/rename'      => $this->make_hook('ajax_rename',       AUTH_COOKIE),
            'groups/ajax/delete'      => $this->make_hook('ajax_delete',       AUTH_COOKIE),
            'groups/see'              => $this->make_hook('group_see',         AUTH_PUBLIC),
            'groups/ajax/users'       => $this->make_hook('group_ajax_users',  AUTH_INTERNAL, ''),
            'groups/admin'            => $this->make_hook('group_admin',       AUTH_PUBLIC),
            'groups/subscribe'        => $this->make_hook('group_subscribe',   AUTH_COOKIE),
            'groups/unsubscribe'      => $this->make_hook('group_unsubscribe', AUTH_COOKIE),
            'groups/insert'           => $this->make_hook('group_insert',      AUTH_COOKIE),
        );
    }

    function handler_groups($page)
    {
        $except = new PFC_True();

        $max = 10;

        // Fetch samples of other groups
        $binet = new GroupFilter(new PFC_And(new GFC_Namespace(Group::NS_BINET), $except), new GFO_Score(true));
        $binet = $binet->get(new PlLimit($max));

        $study = new GroupFilter(new PFC_And(new GFC_Namespace(Group::NS_STUDY), $except), new GFO_Score(true));
        $study = $study->get(new PlLimit($max));

        $free = new GroupFilter(new PFC_And(new GFC_Namespace(Group::NS_FREE), $except), new GFO_Score(true));
        $free = $free->get(new PlLimit($max));

        // Load associated datas
        $temp = new Collection('Group');
        $temp->merge($binet)->merge($study)->merge($free);
        $temp->select(Group::SELECT_BASE);

        // Fetch the total count of groups
        $allf = new GroupFilter(new GFC_Visible());
        $total = $allf->getTotalCount();

        $user_binet = S::user()->castes()->groups()->filter('ns', Group::NS_BINET)->remove($binet);
        $page->assign('binet', $binet);
        $page->assign('user_binet', $user_binet);

        $user_study = S::user()->castes()->groups()->filter('ns', Group::NS_STUDY)->remove($study);
        $page->assign('study', $study);
        $page->assign('user_study', $user_study);

        $user_free = S::user()->castes()->groups()->filter('ns', Group::NS_FREE)->remove($free);
        $page->assign('free', $free);
        $page->assign('user_free', $user_free);

        $page->assign('user', S::user());
        $page->assign('total', $total);
        $page->assign('title', 'Groupes');
        $page->changeTpl('groups/groups.tpl');
        $page->addCssLink('groups.css');
    }

    function handler_ajax_search($page)
    {
        $json = json_decode(Env::v('json'));

        $conditions = new PFC_And(new GFC_Namespace($json->ns),
                                  new PFC_OR(new GFC_Label($json->token, GFC_Label::CONTAINS),
                                             new GFC_Name($json->token)));

        $own = new GroupFilter(new PFC_And($conditions, new GFC_User(S::user()->id())), new GFO_Score(true));
        $all = new GroupFilter($conditions, new GFO_Score(true));
        $own = $own->get(new PlLimit(5));
        $all = $all->get(new PlLimit(5));

        $all->merge($own)->select(Group::SELECT_BASE);

        $page->jsonAssign('success', true);
        $page->jsonAssign('groups', $all->export());

        return PL_JSON;
    }

    function handler_group_see($page, $group)
    {
        global $globals, $platal;

        $filter = (Group::isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

        if ($group) {
            // Fetch the group
            $group->select(Group::SELECT_BASE);
            $page->assign('group', $group);

            if (S::i('auth') > AUTH_PUBLIC || $group->external()) {
                $group->select(array(Group::SELECT_DESCRIPTION => null,
                                     Group::SELECT_CASTES => Caste::SELECT_BASE));

                // Current promos ?
                foreach (json_decode($globals->core->promos) as $promo) {
                    $groupes_names[] = 'promo_' . $promo;
                }
                $promos = new Collection('Group');
                $promos->add($groupes_names)->select(Group::SELECT_BASE);
                $page->assign('promos', $promos);

                // Fetch the news
                $nf = new NewsFilter(new PFC_And(new NFC_Origin($group),
                                                 new PFC_Or(new NFC_User(S::user(), Rights::member()),
                                                            new NFC_Private(false))
                                                 ), new NFO_End(true));
                $news = $nf->get()->select();
                $page->assign('news', $news);
                $page->assign('title', $group->label());
                $page->changeTpl('groups/group.tpl');
            } else {
                $platal->force_login($page);
            }
        } else {
            $page->assign('title', "Ce groupe n'existe pas");
            $page->changeTpl('groups/no_group.tpl');
        }
        $page->addCssLink('groups.css');
    }

    function handler_group_ajax_users($page)
    {
        $json = json_decode(Env::v('json'));
        $group = $json->gid;

        $filter = (Group::isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

        $users = false;
        if ($group) {
            $users = array('admin' => array(), 'member' => array());
            $group->select(Group::SELECT_CASTES);

            $filters = new PFC_True();
            if (count($json->promo) > 0) {
                $filters = new UFC_Group(explode(';', $json->promo));
            }

            $uf = new UserFilter(new PFC_And(new UFC_Caste($group->caste(Rights::admin())), $filters));
            $admins = $uf->get()->select(User::SELECT_BASE);
            foreach ($admins as $user) {
                $users['admin'][$user->id()] = $user->export(User::EXPORT_MICRO | User::EXPORT_SMALL);
            }

            $uf = new UserFilter(new PFC_And(new UFC_Caste($group->caste(Rights::member())), $filters));
            $members = $uf->get()->select(User::SELECT_BASE);
            foreach ($members as $user) {
                $page->assign('user', $user);
                $users['member'][$user->id()] = $user->export(User::EXPORT_MICRO | User::EXPORT_SMALL);
            }
        }

        $page->jsonAssign('users', $users);
        return PL_JSON;
    }

    function handler_group_admin($page, $group)
    {
        $filter = (Group::isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

        if ($group)
        {
            $group->select(Group::SELECT_BASE | Group::SELECT_DESCRIPTION);
            $group->select(array(Group::SELECT_CASTES =>
                                 array(Caste::SELECT_BASE => null,
                                       Caste::SELECT_USERS => User::SELECT_BASE)));
            $page->assign('group', $group);

            $page->assign('title', 'Administration de "' . $group->label() . '"');
            $page->changeTpl('groups/admin.tpl');
        }
        else
        {
            $page->assign('title', "Ce groupe n'existe pas");
            $page->changeTpl('groups/no_group.tpl');
        }
    }

    function handler_group_subscribe($page, $group)
    {
        $filter = (Group::isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

        if ($group)
        {
            $group->select();

            if ($group->priv())
                $group->caste(Rights::friend())->addUser(S::user());
            else
                $group->caste(Rights::member())->addUser(S::user());

            $page->assign('group', $group);
            $page->assign('title', $group->label());
            $page->changeTpl('groups/subscribe.tpl');
        }
        else
        {
            $page->assign('title', "Ce groupe n'existe pas");
            $page->changeTpl('groups/no_group.tpl');
        }
    }

    function handler_group_unsubscribe($page, $group)
    {
        $filter = (Group::isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

        if ($group)
        {
            $group->select();

            // TODO: check the person doesn't leave the group if he is the only admin !
            if ($group->leavable())
                $group->removeUser(S::user());

            $page->assign('group', $group);
            $page->assign('title', $group->label());
            $page->changeTpl('groups/unsubscribe.tpl');
        }
        else
        {
            $page->assign('title', "Ce groupe n'existe pas");
            $page->changeTpl('groups/no_group.tpl');
        }
    }

    function handler_group_insert($page)
    {
        //TODO
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
