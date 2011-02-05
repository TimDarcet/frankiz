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

abstract class ValidateFilterCondition extends FrankizFilterCondition
{
}

/** Filters Validate based on the user asking for it
 * @param $user A User, a Uid or an array of it
 */
class VFC_Asker extends ValidateFilterCondition
{
    private $uids;

    public function __construct($us)
    {
        $this->uids = User::toIds(unflatten($us));
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('v.user IN {?}', $this->uids);
    }
}

/** Filters Validate based on the group validating it
 * @param $gs A Group, a Gid or an array of it
 */
class VFC_Group extends ValidateFilterCondition
{
    private $gids;

    public function __construct($gs)
    {
        if ($gs instanceof Collection) {
            if ($gs->className() != 'Group') {
                throw new Exception('VFC_Group constructor takes a Collection<Group>');
            }
            $this->gids = $gs->ids();
        } else {
            $this->gids = Group::toIds(unflatten($gs));
        }
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('v.`group` IN {?}', $this->gids);
    }
}

/** Filters Validate based on their types
 * @param $types A type or an array of types
 */
class VFC_Type extends ValidateFilterCondition
{
    private $types;

    public function __construct($types)
    {
        $this->types = unflatten($types);
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('v.type IN {?}', $this->types);
    }
}

/** Returns Validates that users are allowed to see because they
 * are admin of the targeted groups
 * @param $us     A User, a uid or an array
 */
class VFC_User extends ValidateFilterCondition
{
    private $uids;

    public function __construct($us)
    {
        $this->uids = User::toIds(unflatten($us));
    }

    public function buildCondition(PlFilter $f)
    {
        $c = $f->addCasteFilter();
        $cu = $f->addUserFilter();
        return XDB::format("$c.rights = {?} AND $cu.uid IN {?}", (string) Rights::admin(), $this->uids);
    }
}

class VFC_Writer extends ValidateFilterCondition
{
    private $uids;

    public function __construct($us)
    {
        $this->uids = User::toIds(unflatten($us));
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('v.writer IN {?}', $this->uids);
    }
}

abstract class ValidateFilterOrder extends FrankizFilterOrder
{
}

class VFO_Created extends ValidateFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $gf)
    {
        return "v.created";
    }
}

/***********************************
  *********************************
        VALIDATE FILTER CLASS
  *********************************
 ***********************************/

class ValidateFilter extends FrankizFilter
{
    protected function schema()
    {
        return array('table' => 'validate',
                     'as'    => 'v',
                     'id'    => 'id');
    }

    private $with_caste = false;

    public function addCasteFilter()
    {
        $this->with_caste = true;
        return 'c';
    }

    protected function casteJoins()
    {
        $joins = array();
        if ($this->with_caste) {
            $joins['c'] = PlSqlJoin::left('castes', '$ME.`group` = v.`group`');
        }
        return $joins;
    }

    private $with_user = false;

    public function addUserFilter()
    {
        $this->with_user = true;
        return 'cu';
    }

    protected function userJoins()
    {
        $joins = array();
        if ($this->with_user) {
            $joins['cu'] = PlSqlJoin::left('castes_users', '$ME.cid = c.cid');
        }
        return $joins;
    }
}
// }}}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
