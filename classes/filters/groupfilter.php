<?php
/***************************************************************************
 *  Copyright (C) 2004-2013 Binet Réseau                                   *
 *  http://br.binets.fr/                                                   *
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

abstract class GroupFilterCondition extends FrankizFilterCondition
{
}

class GFC_Id extends GroupFilterCondition
{
    private $gids;

    public function __construct($gs)
    {
        $this->gids = Group::toIds(unflatten($gs));
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('g.gid IN {?}', $this->gids);
    }
}

class GFC_Name extends GroupFilterCondition
{
    private $name;

    public function __construct($val)
    {
        $this->name = unflatten($val);
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('g.name IN {?}', $this->name);
    }
}

class GFC_Namespace extends GroupFilterCondition
{
    private $ns;

    public function __construct($ns)
    {
        $this->ns = unflatten($ns);
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('g.ns IN {?}', $this->ns);
    }
}

class GFC_Visible extends GroupFilterCondition
{
    private $visible;

    public function __construct($visible = true)
    {
        $this->visible = $visible;
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('g.visible = {?}', $this->visible);
    }
}

class GFC_External extends GroupFilterCondition
{
    private $external;

    public function __construct($external = true)
    {
        $this->external = $external;
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('g.external = {?}', $this->external);
    }
}

class GFC_Label extends GroupFilterCondition
{
    // Modes
    const PREFIX   = XDB::WILDCARD_PREFIX;   // 0x001
    const SUFFIX   = XDB::WILDCARD_SUFFIX;   // 0x002
    const CONTAINS = XDB::WILDCARD_CONTAINS; // 0x003

    private $text;
    private $mode;

    public function __construct($text, $mode)
    {
        $this->text = $text;
        $this->mode = $mode;
    }

    public function buildCondition(PlFilter $uf)
    {
        $right = XDB::formatWildcards($this->mode, $this->text);

        return 'g.label' . $right;
    }
}

class GFC_User extends GroupFilterCondition
{
    private $uids;
    private $rights;

    public function __construct($us, $rights = null)
    {
        $this->uids  = User::toIds(unflatten($us));
        $this->rights = (string) ((empty($rights)) ? Rights::member() : $rights);
    }

    public function buildCondition(PlFilter $f)
    {
        $c = $f->addCasteFilter();
        $u = $f->addUserFilter();
        return XDB::format("( $u.uid IN {?} AND $c.rights = {?} )", $this->uids, $this->rights);
    }
}

/** Filters groups based on their room's id
 * @param $rooms a collection of Rooms
 */
class GFC_Room extends GroupFilterCondition
{
    private $rooms;
    private $exact;

    public function __construct($rooms, $exact = false)
    {
        $this->rooms  = Room::toIds(unflatten($rooms));
        $this->exact = $exact;
    }

    public function buildCondition(PlFilter $uf)
    {
        $sub = $uf->addRoomFilter();
        if ($this->exact) {
            return XDB::format("$sub.rid IN {?}", $this->rooms);
        }
        else {
            if (count($this->rooms) == 0) {
                return false;
            } else if (count($this->rooms) == 1) {
                return $sub . '.rid ' . XDB::formatWildcards(XDB::WILDCARD_CONTAINS, $this->rooms[0]);
            } else {
                foreach ($this->rooms as $room) {
                    $temp[] = $sub . '.rid ' . XDB::formatWildcards(XDB::WILDCARD_CONTAINS, $room);
                }
                return '(' . implode(') ' . 'OR' . ' (', $temp) . ')';
            }
        }
    }
}

abstract class GroupFilterOrder extends FrankizFilterOrder
{
}

class GFO_Score extends GroupFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $gf)
    {
        return "g.score";
    }
}

class GFO_Name extends GroupFilterOrder
{

    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $gf)
    {
        return 'g.name';
    }
}

/***********************************
  *********************************
          GROUP FILTER CLASS
  *********************************
 ***********************************/

class GroupFilter extends FrankizFilter
{
    protected function schema()
    {
        return array('table' => 'groups',
                     'as'    => 'g',
                     'id'    => 'gid');
    }

    protected $with_caste = false;

    public function addCasteFilter()
    {
        $this->with_caste = true;
        return 'c';
    }

    protected function casteJoins()
    {
        $joins = array();
        if ($this->with_caste)
            $joins['c']  = PlSqlJoin::left('castes', '$ME.`group` = g.gid');

        return $joins;
    }

    protected $with_user = false;

    public function addUserFilter()
    {
        $this->addCasteFilter();
        $this->with_user = true;
        return 'cu';
    }

    protected function userJoins()
    {
        $joins = array();
        if ($this->with_user) {
            $joins['cu'] = PlSqlJoin::left('castes_users',
                '$ME.cid = c.cid AND ($ME.visibility IN {?} OR $ME.uid = {?})',
                S::user()->visibleGids(), S::user()->id());
        }

        return $joins;
    }


    /** ROOM
     */
    private $with_room = false;

    public function addRoomFilter()
    {
        $this->with_room = true;
        return 'r';
    }

    protected function roomJoins()
    {
        $joins = array();
        if ($this->with_room) {
            $joins['rg'] = PlSqlJoin::left('rooms_groups', '$ME.gid = g.gid');
            $joins['r']  = PlSqlJoin::left('rooms', '$ME.rid = rg.rid');
        }
        return $joins;
    }

}
// }}}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
