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

    function handler_groups(&$page)
    {
        // $root = Group::root();
        // Group::get(42);
        
        // $ptid = Group::ascendingPartialTree(array(42, 22), 2);
        // print_r(array_keys(Group::$partialTreesRoots[$ptid]));
        // $fathers = Group::get(42)->fathers(1);

        $page->assign('title', "Groupes");
        // $page->assign('depth', $depth);
        // $g = Group::get(42);
        // $g = Group::get(23);
        // Group::get(2);
        // print_r(Group::get(2)->father());
        // Group::batchChildren(2, 1);
        // print_r(array_keys(Group::get(2)->children(1))); // Juste pour faire chier l'algo
        // print_r(array_keys(Group::get(0)->children(6))); // Juste pour faire chier l'algo
        // print_r(array_keys(Group::get(2)->fathers(6)));
        // $fathers = Group::batchFathers($g);
        // $tree = $fathers + array($g);
        // print_r(array_keys(Group::get(2)->loadedChildren($tree)));

//        $br = Group::get(42);
//        $new = new Group(array("name" => 'plop', "label" => 'plup'));
//        $new->addTo($br);
        
//        $bd = Group::get(5);
//        $info = Group::get(23);
//        $lois = Group::get(21);
//        $bde = Group::get(19);
//        
//        Group::get($bd)->moveTo($lois);

        $page->changeTpl('groups/groups.tpl');
    }

    function handler_ajax_children(&$page)
    {
        $json = json_decode(Env::v('json'));

        $children = Group::get($json->{'gid'})->children();

        $json_array = array();
        foreach ($children as $child)
            $json_array[] = $child->toJson();

        $page->jsonAssign('success', true);
        $page->jsonAssign('children', $json_array);
    }

    function handler_ajax_move(&$page)
    {
        $json = json_decode(Env::v('json'));

        $moved  = $json->{'moved'};
        $target = $json->{'target'};

        Group::get(array($moved, $target));
        // TODO: check the rights
        $page->jsonAssign('success', true);
        try {
            Group::get($moved)->moveTo($target);
        } catch(Exception $e) {
            $page->jsonAssign('success', false);
        }
    }

    function handler_ajax_create(&$page)
    {
        $json = json_decode(Env::v('json'));

        $parent = $json->{'parent'};
        $label  = $json->{'label'};
        $name   = uniqid();

        $parent = Group::get($parent);

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

    function handler_ajax_rename(&$page)
    {
        $json = json_decode(Env::v('json'));

        $group = $json->{'group'};
        $label  = $json->{'label'};

        $group = Group::get($group);

        // TODO: check the rights
        $page->jsonAssign('success', true);
        try {
            $group->label($label);
        } catch(Exception $e) {
            $page->jsonAssign('success', false);
        }
    }

    function handler_ajax_remove(&$page)
    {
        $json = json_decode(Env::v('json'));

        $group = $json->{'group'};
        $group = Group::get($group);

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
