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

    function feed(array $values)
    {
        if (isset($values['gid'])) {
            $this->groups[$values['gid']] = new Group($values);
            return $this->groups[$values['gid']];
        } else {
            return false;
        }
    }

    function groups()
    {
        return $this->groups;
    }

    // TODO: make it work with partial trees
    function groupsToTree($gids) {
        $L2gid = array();
        $max = 0;

        if ($gids === true) {
            foreach ($this->groups as $group) {
                $L2gid[$group->L()] = $group->gid();
                if ($group->R() - $group->L() > $max) {
                    $max  = $group->R() - $group->L() > $max;
                    $root = $group;
                }
            }
        } else {
            foreach ($gids as $gid) {
                $group = self::$groups[$gid];
                $L2gid[$group->L()] = $gid;
                if ($group->R() - $group->L() > $max) {
                    $max  = $group->R() - $group->L() > $max;
                    $root = $group;
                }
            }
        }

        return array($root->gid() => self::buildTree($L2gid, $root));
    }

    static function buildTree($L2gid, $parent) {
        $pos = $parent->L() + 1;
        $tree = array();

        while ($pos != $parent->R()) {
            $enfant = self::$groups[$L2gid[$pos]];
            $delta = $enfant->R() - $enfant->L();

            if ($delta == 1)
                $tree[$enfant->gid()] = NULL;
            else
                $tree[$enfant->gid()] = self::buildTree($L2gid, $enfant);

            $pos += $delta + 1;
        }

        return $tree;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
