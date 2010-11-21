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

abstract class NewsFilterCondition extends FrankizFilterCondition
{
}

/** Filters news based on their writer
 * @param $us A User, a Uid or an array of it
 */
class NFC_Writer extends NewsFilterCondition
{
    private $uids;

    public function __construct($us)
    {
        $this->uids = User::toIds(unflatten($us));
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('n.writer IN {?}', $this->uids);
    }
}

/** Filters news based on their origin group
 * @param $gs A Group, a Gid or an array of it
 */
class NFC_Origin extends NewsFilterCondition
{
    private $gids;

    public function __construct($gs)
    {
        $this->gids = Group::toIds(unflatten($gs));
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('n.origin IN {?}', $this->gids);
    }
}

/** Filters news based on their target group
 * @param $gs A Group, a Gid or an array of it
 */
class NFC_Target extends NewsFilterCondition
{
    private $gids;

    public function __construct($gs)
    {
        $this->gids = Group::toIds(unflatten($gs));
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('n.target IN {?}', $this->gids);
    }
}

class NFC_Title extends NewsFilterCondition
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

        return 'n.title' . $right;
    }
}

/** Returns news that users are allowed to see
 * @param $us     A User, a uid or an array
 * @param $rights The rights the user must have in the targeted group (member by default)
 */
class NFC_User extends NewsFilterCondition
{
    private $uids;
    private $rights;

    public function __construct($us, $rights = 'member')
    {
        $this->uids = User::toIds(unflatten($us));
        $this->rights = $rights;
    }

    public function buildCondition(PlFilter $uf)
    {
        $sub = $uf->addUserFilter();
        return XDB::format("$sub.uid IN {?} AND FIND_IN_SET({?}, $sub.rights) > 0", $this->uids, $this->rights);
    }
}

/** Returns news that are not out-of-date
 */
class NFC_Current extends NewsFilterCondition
{
    public function buildCondition(PlFilter $uf)
    {
        return 'CAST(NOW() AS DATE) BETWEEN n.begin AND n.end';
    }
}

/** Returns news that are private
 */
class NFC_Private extends NewsFilterCondition
{
    private $priv;

    public function __construct($priv = true)
    {
        $this->priv = $priv;
    }

    public function buildCondition(PlFilter $uf)
    {
        return 'n.priv = ' . (int) $this->priv;
    }
}

abstract class NewsFilterOrder extends FrankizFilterOrder
{
}

class NFO_Begin extends NewsFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $gf)
    {
        return "n.begin";
    }
}

class NFO_End extends NewsFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $gf)
    {
        return "n.end";
    }
}

/***********************************
  *********************************
          NEWS FILTER CLASS
  *********************************
 ***********************************/

class NewsFilter extends FrankizFilter
{
    protected function schema()
    {
        return array('table' => 'news',
                     'as'    => 'n',
                     'id'    => 'id');
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
            $joins['ug'] = PlSqlJoin::left('users_groups', '$ME.gid = n.target');
        }
        return $joins;
    }
}
// }}}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
