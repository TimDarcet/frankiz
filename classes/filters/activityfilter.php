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

/** Retrieves instances where the target is owned by the specified groups
 * @param $gs Collection of Groups
 */
class AFC_TargetGroup extends ActivityFilterCondition
{
    private $cids;

    public function __construct(Collection $groups)
    {
        $cf = new CasteFilter(new PFC_And(new CFC_Holder(), new CFC_Group($groups)));
        $this->cids = $cf->get()->ids();
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format("a.target IN {?}", $this->cids);
    }
}

/** Filters activities based on their target caste
 * @param $gs A Caste, a Cid or an array of it
 */
class AFC_Target extends ActivityFilterCondition
{
    private $cids;

    public function __construct($cs)
    {
        $this->cids = Caste::toIds(unflatten($cs));
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('a.target IN {?}', $this->cids);
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

    public function __construct($us, $rights)
    {
        $this->uids = User::toIds(unflatten($us));
        $this->rights = (string) (empty($rights)) ? Rights::member() : $rights;
    }

    public function buildCondition(PlFilter $f)
    {
        $c = $f->addCasteFilter();
        $cu = $f->addUserFilter();
        return XDB::format("$c.rights = {?} AND $cu.uid IN {?}", (string) $this->rights, $this->uids);
    }
}

/** Returns activities that are private
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

/** Returns activities that are regular
 */
class AFC_Regular extends ActivityFilterCondition
{
    private $regular;

    public function __construct($regular = true)
    {
        $this->regular = $regular;
    }

    public function buildCondition(PlFilter $f)
    {
        $not = ($this->regular) ? 'NOT ' : '';
        return "a.default_begin IS $not NULL";
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
            $joins['c'] = PlSqlJoin::left('castes', '$ME.cid = a.target');
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
