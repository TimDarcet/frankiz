<?php
/***************************************************************************
 *  Copyright (C) 2003-2010 Polytechnique.org                              *
 *  http://opensource.polytechnique.org/                                   *
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
        $this->ns = $ns;
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('g.ns = {?}', $this->ns);
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
    private $right;

    public function __construct($us, $right = null)
    {
        $this->right = $right;
        $this->uids  = User::toIds(unflatten($us));
    }

    public function buildCondition(PlFilter $uf)
    {
        $sub = $uf->addUserFilter();
        if ($this->right === null)
            return XDB::format($sub . '.uid IN {?}', $this->uids);
        else
            return XDB::format("( $sub.uid IN {?} AND FIND_IN_SET({?}, $sub.rights) ", $this->uids, $this->right);
    }
}

abstract class GroupFilterOrder extends FrankizFilterOrder
{
}

class GFO_Frequency extends GroupFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $gf)
    {
        $sub = $gf->addUserFilter();
        return "COUNT($sub.uid)";
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

    protected $with_user = false;

    public function addUserFilter()
    {
        $this->with_user = true;
        return 'ug';
    }

    protected function userJoins()
    {
        $joins = array();
        if ($this->with_user)
            $joins['ug'] = PlSqlJoin::left('users_groups', '$ME.gid = g.gid');

        return $joins;
    }

}
// }}}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
