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

abstract class SurveyFilterCondition extends FrankizFilterCondition
{
}

/** Filters surveys based on their id
 * @param $val
 */
class SFC_Id extends SurveyFilterCondition
{
    private $ids;

    public function __construct($mixed)
    {
        $this->ids = Survey::toIds(unflatten($mixed));
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('s.sid IN {?}', $this->ids);
    }
}

/** Filter based on the writer
 * @param $us A User, a Uid or an array of it
 */
class SFC_Writer extends SurveyFilterCondition
{
    private $uids;

    public function __construct($us)
    {
        $this->uids = User::toIds(unflatten($us));
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('s.writer IN {?}', $this->uids);
    }
}

/** Filter based on the origin group
 * @param $gs A Group, a Gid or an array of it
 */
class SFC_Origin extends SurveyFilterCondition
{
    private $gids;

    public function __construct($gs)
    {
        $this->gids = Group::toIds(unflatten($gs));
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('s.origin IN {?}', $this->gids);
    }
}

/** Filter based on the target group
 * @param $gs A Group, a Gid or an array of it
 */
class SFC_Target extends SurveyFilterCondition
{
    private $gids;

    public function __construct($gs)
    {
        $this->gids = Group::toIds(unflatten($gs));
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('s.target IN {?}', $this->gids);
    }
}

/** Returns news that users are allowed to see
 * @param $us     A User, a uid or an array
 * @param $rights The rights the user must have in the targeted group (member by default)
 */
class SFC_User extends SurveyFilterCondition
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

/** Filter those out-of-date
 */
class NFC_Current extends SurveyFilterCondition
{
    public function buildCondition(PlFilter $uf)
    {
        return 'CAST(NOW() AS DATE) BETWEEN s.begin AND s.end';
    }
}

/** Returns surveys that are in private groups
 */
class SFC_Private extends SurveyFilterCondition
{
    private $priv;

    public function __construct($priv = true)
    {
        $this->priv = $priv;
    }

    public function buildCondition(PlFilter $f)
    {
        $g = $f->addGroupFilter();
        return $g.'.priv = ' . (int) $this->priv;
    }
}

abstract class SurveyFilterOrder extends FrankizFilterOrder
{
}

class SFO_Begin extends SurveyFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $gf)
    {
        return "s.begin";
    }
}

class SFO_End extends SurveyFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $gf)
    {
        return "s.end";
    }
}

/***********************************
  *********************************
          SURVEY FILTER CLASS
  *********************************
 ***********************************/

class SurveyFilter extends FrankizFilter
{
    protected function schema()
    {
        return array('table' => 'surveys',
                     'as'    => 's',
                     'id'    => 'sid');
    }

    private $with_group = false;

    public function addGroupFilter()
    {
        $this->with_group = true;
        return 'g';
    }

    protected function groupJoins()
    {
        $joins = array();
        if ($this->with_caste) {
            $joins['g'] = PlSqlJoin::left('groups', '$ME.gid = s.target');
        }
        return $joins;
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
            $joins['c'] = PlSqlJoin::left('castes', '$ME.gid = s.target');
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
            $joins['cu'] = PlSqlJoin::left('castes_users',
                '$ME.cid = c.cid AND ($ME.visibility IN {?} OR $ME.uid = {?})',
                S::user()->visibleGids(), S::user()->id());
        }
        return $joins;
    }
}
// }}}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
