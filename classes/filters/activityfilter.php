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

abstract class ActivityFilterCondition extends FrankizFilterCondition
{
}

/** Filters activities based on their origin group
 * @param $gs A Group, a Gid or an array of it
 */
class AFC_Origin extends ActivityFilterCondition
{
    private $gids;

    public function __construct($gs)
    {
        $this->gids = Group::toIds(unflatten($gs));
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('a.origin IN {?}', $this->gids);
    }
}

/** Filters activities based on their target group
 * @param $gs A Group, a Gid or an array of it
 */
class AFC_Target extends ActivityFilterCondition
{
    private $gids;

    public function __construct($gs)
    {
        $this->gids = Group::toIds(unflatten($gs));
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('a.target IN {?}', $this->gids);
    }
}

class AFC_Title extends ActivityFilterCondition
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

    public function buildCondition(PlFilter $f)
    {
        $right = XDB::formatWildcards($this->mode, $this->text);

        return 'a.title' . $right;
    }
}

/** Returns activities that users are allowed to see
 * @param $us     A User, a uid or an array
 * @param $rights The rights the user must have in the targeted group (member by default)
 */
class AFC_User extends ActivityFilterCondition
{
    private $uids;
    private $rights;

    public function __construct($us, $rights = 'member')
    {
        $this->uids = User::toIds(unflatten($us));
        $this->rights = $rights;
    }

    public function buildCondition(PlFilter $f)
    {
        $sub = $f->addUserFilter();
        return XDB::format("$sub.uid IN {?} AND FIND_IN_SET({?}, $sub.rights) > 0", $this->uids, $this->rights);
    }
}

/** Returns news that are private
 */
class AFC_Private extends ActivityFilterCondition
{
    private $priv;

    public function __construct($priv = true)
    {
        $this->priv = $priv;
    }

    public function buildCondition(PlFilter $f)
    {
        return 'a.priv = ' . (int) $this->priv;
    }
}

abstract class NewsFilterOrder extends FrankizFilterOrder
{
}


/***********************************
  *********************************
          ACTIVITY FILTER CLASS
  *********************************
 ***********************************/

class ActivityFilter extends FrankizFilter
{
    protected function schema()
    {
        return array('table' => 'activities',
                     'as'    => 'a',
                     'id'    => 'aid');
    }

    private $with_user = false;

    public function addUserFilter()
    {
        $this->with_user = true;
        return 'ug';
    }

    protected function userJoins()
    {
        $joins = array();
        if ($this->with_user) {
            $joins['ug'] = PlSqlJoin::left('users_groups', '$ME.gid = a.target');
        }
        return $joins;
    }
}
// }}}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
