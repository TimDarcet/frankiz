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
            'groups'                 => $this->make_hook('groups',        AUTH_PUBLIC),
            'groups/ajax/children'   => $this->make_hook('ajax_children', AUTH_PUBLIC),
            'groups/ajax/move'       => $this->make_hook('ajax_move',     AUTH_COOKIE),
            'groups/ajax/create'     => $this->make_hook('ajax_create',   AUTH_COOKIE),
            'groups/ajax/rename'     => $this->make_hook('ajax_rename',   AUTH_COOKIE),
            'groups/ajax/remove'     => $this->make_hook('ajax_remove',   AUTH_COOKIE)
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

        $tree = new Tree(GroupsTreeInfo::get());
        $tree->descending(array(new Group($json->{'gid'})), 1)->load(Group::BASE)->behead();

        $page->jsonAssign('success', true);
        $page->jsonAssign('children', $tree->toJson(0));
    }

    function handler_ajax_move($page)
    {
        $json = json_decode(Env::v('json'));

        $moved  = new Group($json->{'moved'});
        $target = new Group($json->{'target'});

        // TODO: check the rights
        $page->jsonAssign('success', true);
        try {
            $moved->moveTo($target);
        } catch(Exception $e) {
            $page->jsonAssign('success', false);
        }
    }

    function handler_ajax_create($page)
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
            $new->addTo($parent);
            $page->jsonAssign('group', $new->toJson());
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
        } catch(Exception $e) {
            $page->jsonAssign('success', false);
        }
    }

    function handler_ajax_remove($page)
    {
        $json = json_decode(Env::v('json'));

        $group = $json->{'group'};
        $group = new Group($group);

        // TODO: check the rights
        $page->jsonAssign('success', true);
        try {
            $group->remove();
        } catch(Exception $e) {
            $page->jsonAssign('success', false);
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
