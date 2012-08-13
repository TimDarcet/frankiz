<?php
/***************************************************************************
 *  Copyright (C) 2004-2012 Binet Réseau                                   *
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

abstract class CasteFilterCondition extends FrankizFilterCondition
{
}

class CFC_Group extends CasteFilterCondition
{
    private $gids;
    private $rights = null;

    public function __construct($gs, $rights = null)
    {
        if ($gs instanceof Collection) {
            if ($gs->className() != 'Group') {
                throw new Exception('CFC_Group constructor takes a Collection<Group>');
            }
            $this->gids = $gs->ids();
        } else {
            $this->gids = Group::toIds(unflatten($gs));
        }
        $this->rights = (string) $rights;
    }

    public function buildCondition(PlFilter $f)
    {
        if (empty($this->rights)) {
            return XDB::format('c.`group` IN {?}', $this->gids);
        } else {
            return XDB::format('c.`group` IN {?} AND c.rights = {?}', $this->gids, $this->rights);
        }
    }
}

/** Retrieves castes that can hold datas (ie. everybody & restricted castes)
 */
class CFC_Holder extends CasteFilterCondition
{
    public function buildCondition(PlFilter $f)
    {
        return XDB::format('c.rights IN ({?}, {?})', (string) Rights::everybody(), (string) Rights::restricted());
    }
}

class CFC_Rights extends CasteFilterCondition
{
    private $rights;

    public function __construct($rights)
    {
        $this->rights = (string) $rights;
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('c.rights = {?}', $this->rights);
    }
}

class CFC_UserFilter extends CasteFilterCondition
{
    private $userfilter;

    public function __construct($userfilter = true)
    {
        $this->userfilter = $userfilter;
    }

    public function buildCondition(PlFilter $f)
    {
        $not = ($this->userfilter) ? 'NOT' : '';
        return "c.userfilter IS $not NULL";
    }
}

class CFC_User extends CasteFilterCondition
{
    private $uids;

    public function __construct($us)
    {
        $this->uids  = User::toIds(unflatten($us));
    }

    public function buildCondition(PlFilter $f)
    {
        $u = $f->addUserFilter();
        return XDB::format("$u.uid IN {?}", $this->uids);
    }
}

abstract class CasteFilterOrder extends FrankizFilterOrder
{
}

class CFO_Frequency extends CasteFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $f)
    {
        $sub = $f->addUserFilter();
        return "COUNT($sub.uid)";
    }
}

/***********************************
  *********************************
          CASTE FILTER CLASS
  *********************************
 ***********************************/

class CasteFilter extends FrankizFilter
{
    protected function schema()
    {
        return array('table' => 'castes',
                     'as'    => 'c',
                     'id'    => 'cid');
    }

    protected $with_user = false;

    public function addUserFilter()
    {
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

}
// }}}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
