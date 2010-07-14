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

class GroupFactory
{
    protected $groups;

    public function __construct()
    {
        $this->groups = array();
    }

    static function gf()
    {
        if (!S::has('GroupFactory'))
            S::set('GroupFactory', new GroupFactory());

        return S::v('GroupFactory');
    }

    function feed($values)
    {
        if ($values instanceof Group) {
            $this->groups[$values->gid()] = $values;
        } else {
            if (isset($values['gid'])) {
                $this->groups[$values['gid']] = new Group($values);
                return $this->groups[$values['gid']];
            } else {
                return false;
            }
        }
    }

    function unfeed($group)
    {
        $gid = Group::toGid($group);
        unset($this->groups[$gid]);
    }

    function groups()
    {
        return $this->groups;
    }

    function group($gid)
    {
        return $this->groups[$gid];
    }

    function boundaryFilter($L, $R, $old_gids)
    {
        $gids = array();
        foreach ($old_gids as $gid) {
            if (($this->group($gid)->L() > $L) && ($this->group($gid)->R() < $R))
                $gids[] = $gid;
        }
        return $gids;
    }

    function groupsToTree($gids) {
        if ($gids === true)
            $gids = array_keys($this->groups);

        $gid2weight = array();

        foreach ($gids as $gid)
            $gid2weight[$gid] = $this->group($gid)->R() - $this->group($gid)->L();

        asort($gid2weight);

        $sortedGids = array_keys($gid2weight);

        $root = $this->group(array_pop($sortedGids));

        return array($root->gid() => $this->buildTree($sortedGids));
    }

    function buildTree(array $sortedGids) {
        $tree = array();
        while (count($sortedGids) > 0)
        {
            $widest = $this->group(array_pop($sortedGids));
            if ($widest->L() == $widest->R() - 1) {
                $tree[$widest->gid()] = NULL;
            } else {
                $filtered_gids = $this->boundaryFilter($widest->L(), $widest->R(), $sortedGids);
                $sortedGids = array_diff($sortedGids, $filtered_gids);
                $tree[$widest->gid()] = $this->buildTree($filtered_gids, $widest);
            }
        }

        return $tree;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
