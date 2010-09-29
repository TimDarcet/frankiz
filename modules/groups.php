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
            'groups'                 => $this->make_hook('groups',          AUTH_PUBLIC),
            'groups/ajax/children'   => $this->make_hook('ajax_children',   AUTH_PUBLIC),
            'groups/ajax/moveunder'  => $this->make_hook('ajax_moveunder',  AUTH_COOKIE),
            'groups/ajax/movebefore' => $this->make_hook('ajax_movebefore', AUTH_COOKIE),
            'groups/ajax/insert'     => $this->make_hook('ajax_insert',     AUTH_COOKIE),
            'groups/ajax/rename'     => $this->make_hook('ajax_rename',     AUTH_COOKIE),
            'groups/ajax/delete'     => $this->make_hook('ajax_delete',     AUTH_COOKIE)
        );
    }

    function handler_groups($page)
    {
        $page->assign('title', "Groupes");
        $page->changeTpl('groups/groups.tpl');
    }

    function handler_ajax_children($page)
    {
        $json = json_decode(Env::v('json'));

        $tree = new Tree("Group");
        $tree->descending(array(new Group($json->{'gid'})), 1)->select(Group::SELECT_BASE)->behead();

        $page->jsonAssign('success', true);
        $page->jsonAssign('children', $tree->toJson(0));
    }

    function handler_ajax_moveunder($page)
    {
        $json = json_decode(Env::v('json'));

        $group  = new Group($json->{'group'});
        $parent = new Group($json->{'parent'});

        // TODO: check the rights
        $page->jsonAssign('success', true);
        try {
            $group->moveUnder($parent);
        } catch(Exception $e) {
            $page->jsonAssign('success', false);
        }
    }

    function handler_ajax_movebefore($page)
    {
        $json = json_decode(Env::v('json'));

        $group  = new Group($json->{'group'});
        $sibling = new Group($json->{'sibling'});

        // TODO: check the rights
        $page->jsonAssign('success', true);
        try {
            $group->moveBefore($sibling);
        } catch(Exception $e) {
            $page->jsonAssign('success', false);
        }
    }

    function handler_ajax_insert($page)
    {
        $json = json_decode(Env::v('json'));

        $parent = $json->{'parent'};
        $label  = $json->{'label'};
        $name   = uniqid();

        $parent = new Group($parent);
        $new = new Group(array("name" => $name, "label" => $label));

        // TODO: check the rights
        $page->jsonAssign('success', true);
        try {
            $new->insert($parent);
            $datas = array('group' => $new->toJson(), 'parent' => $parent->toJson());
            $page->jsonAssign('ok', APE::send('groupInserted', $datas));
        } catch(Exception $e) {
            $page->jsonAssign('success', false);
        }
    }

    function handler_ajax_rename($page)
    {
        $json = json_decode(Env::v('json'));

        $group = $json->{'group'};
        $label = $json->{'label'};

        $group = new Group($group);

        // TODO: check the rights
        $page->jsonAssign('success', true);
        try {
            $group->label($label);
            $datas = array('group' => $group->toJson());
            $page->jsonAssign('ok', APE::send('groupRenamed', $datas));
        } catch(Exception $e) {
            $page->jsonAssign('success', false);
        }
    }

    function handler_ajax_delete($page)
    {
        $json = json_decode(Env::v('json'));

        $group = $json->{'group'};
        $group = new Group($group);

        // TODO: check the rights
        $page->jsonAssign('success', true);
        try {
            $group->delete();
            $datas = array('group' => $group->toJson());
            $page->jsonAssign('ok', APE::send('groupDeleted', $datas));
        } catch(Exception $e) {
            $page->jsonAssign('success', false);
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
