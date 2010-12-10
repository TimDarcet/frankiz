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
            'groups/subscribe'        => $this->make_hook('group_subscribe',   AUTH_COOKIE),
            'groups/unsubscribe'      => $this->make_hook('group_unsubscribe', AUTH_COOKIE),
            'groups/insert'           => $this->make_hook('group_insert',      AUTH_COOKIE),
        );
    }

    function handler_groups($page)
    {
        $gf = new GroupFilter(null, new GFO_Score(true));
        $gs = $gf->get(new PlLimit(20));
        $gs->select(Group::SELECT_BASE);

        $total = $gf->getTotalCount();

        $page->assign('groups', $gs);
        $page->assign('total', $total);
        $page->assign('title', 'Groupes');
        $page->changeTpl('groups/groups.tpl');
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
        $page->jsonAssign('groups', $all->toJson());

        return PL_JSON;
    }

    function handler_group_see($page, $group)
    {
        $filter = (isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

        if ($group)
        {
            $group->select(Group::SELECT_BASE | Group::SELECT_DESCRIPTION);
            $group->select(array(Group::SELECT_CASTES =>
                                 array(Caste::SELECT_BASE => null,
                                       Caste::SELECT_USERS => User::SELECT_BASE)));
            $page->assign('group', $group);

            $page->assign('title', $group->label());
            $page->changeTpl('groups/group.tpl');
        }
        else
        {
            $page->assign('title', "Ce groupe n'existe pas");
            $page->changeTpl('groups/no_group.tpl');
        }
    }

    function handler_group_subscribe($page, $group)
    {
        $filter = (isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

        if ($group)
        {
            $group->select();

            if ($group->enter())
                $group->caste(Rights::member())->addUser(S::user());
            else
                $group->caste(Rights::friend())->addUser(S::user());

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
        $filter = (isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

        if ($group)
        {
            $group->select();

            // TODO: check the personn doesn't leave a group if it is the only admin !
            if ($group->leave())
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
