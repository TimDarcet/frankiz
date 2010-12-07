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

abstract class ActivityInstanceFilterCondition extends FrankizFilterCondition
{
}

/** Filters instances based on their writer
 * @param $us A User, a Uid or an array of it
 */
class AIFC_Writer extends ActivityInstanceFilterCondition
{
    private $uids;

    public function __construct($us)
    {
        $this->uids = User::toIds(unflatten($us));
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('ai.writer IN {?}', $this->uids);
    }
}

/** Filters instances based on the origin group of the activity
 * @param $gs A Group, a Gid or an array of it
 */
class AIFC_Origin extends ActivityInstanceFilterCondition
{
    private $gids;

    public function __construct($gs)
    {
        $this->gids = Group::toIds(unflatten($gs));
    }

    public function buildCondition(PlFilter $f)
    {
        $sub = $f->addActivityFilter();
        return XDB::format("$sub.origin IN {?}", $this->gids);
    }
}

/** Filters instances based on the target group of the activity
 * @param $gs A Group, a Gid or an array of it
 */
class AIFC_Target extends ActivityInstanceFilterCondition
{
    private $gids;

    public function __construct($gs)
    {
        $this->gids = Group::toIds(unflatten($gs));
    }

    public function buildCondition(PlFilter $f)
    {
        $sub = $f->addActivityFilter();
        return XDB::format("$sub.target IN {?}", $this->gids);
    }
}

/** Returns instances for which the users are linked to the target group
 * @param $us     A User, a uid or an array
 * @param $rights The rights the user must have in the targeted group (member by default)
 */
class AIFC_User extends ActivityInstanceFilterCondition
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
        $f->addActivityFilter();
        $c = $f->addCasteFilter();
        $cu = $f->addUserFilter();
        return XDB::format("$c.rights = {?} AND $cu.uid IN {?}", (string) $this->rights, $this->uids);
    }
}

/** Returns instances that are between two datetimes
 */
class AIFC_Period extends ActivityInstanceFilterCondition
{
    private $begin;
    private $end;
    private $strict;

    public function __construct(FrankizDateTime $begin, FrankizDateTime $end, $strict = false)
    {
        $this->begin  = $begin;
        $this->end    = $end;
        $this->strict = $strict;
    }

    public function buildCondition(PlFilter $f)
    {
        if ($this->strict)
            return XDB::format('ai.begin >= {?} AND ai.end <= {?}',
                            $this->begin->format(), $this->end->format());

        return XDB::format('ai.end >= {?} AND ai.begin <= {?}',
                        $this->begin->format(), $this->end->format());
    }
}

/** Returns instances that begin after (or before) a specified datetime
 */
class AIFC_Begin extends ActivityInstanceFilterCondition
{
    const AFTER  = '>=';
    const BEFORE = '<=';
    const EGAL   = '=';

    private $sign;
    private $begin;

    public function __construct(FrankizDateTime $begin, $sign = self::AFTER)
    {
        $this->begin = $begin;
        $this->sign  = $sign;
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('ai.begin ' . $this->sign . ' {?}', $this->begin->format());
    }
}

/** Returns instances that end before (or after) a specified datetime
 */
class AIFC_End extends ActivityInstanceFilterCondition
{
    const AFTER  = '>=';
    const BEFORE = '<=';
    const EGAL   = '=';

    private $sign;
    private $end;

    public function __construct(FrankizDateTime $end, $sign = self::BEFORE)
    {
        $this->end  = $end;
        $this->sign = $sign;
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('ai.end ' . $this->sign . ' {?}', $this->end->format());
    }
}

/** Returns instances that are in progress with a specified accuracy
 *
 * To filter today's instances: new AIFC_Current(AIFC_Current::ACCURACY_DAY)
 *
 */
class AIFC_Current extends ActivityInstanceFilterCondition
{
    const ACCURACY_SECOND = 0x01;
    const ACCURACY_DAY    = 0x02;

    private $accuracy;

    public function __construct($accuracy = self::PRECISION_SECOND)
    {
        $this->accuracy = $accuracy;
    }

    public function buildCondition(PlFilter $f)
    {
        if ($this->accuracy & self::ACCURACY_DAY)
            return 'CURDATE() BETWEEN ai.begin AND ai.end';

        return 'NOW() BETWEEN ai.begin AND ai.end';
    }
}

/** Returns instances of private activities
 */
class AIFC_Private extends ActivityInstanceFilterCondition
{
    private $priv;

    public function __construct($priv = true)
    {
        $this->priv = $priv;
    }

    public function buildCondition(PlFilter $f)
    {
        $sub = $f->addActivityFilter();
        return $sub.'.priv = ' . (int) $this->priv;
    }
}

abstract class ActivityInstanceFilterOrder extends FrankizFilterOrder
{
}

class AIFO_Begin extends ActivityInstanceFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $f)
    {
        return "ai.begin";
    }
}

class AIFO_End extends ActivityInstanceFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $f)
    {
        return "ai.end";
    }
}

/***********************************
  *********************************
    ActivityInstance FILTER CLASS
  *********************************
 ***********************************/

class ActivityInstanceFilter extends FrankizFilter
{
    protected function schema()
    {
        return array('table' => 'activities_instances',
                     'as'    => 'ai',
                     'id'    => 'id');
    }

    private $with_activity = false;

    public function addActivityFilter()
    {
        $this->with_activity = true;
        return 'a';
    }

    protected function activityJoins()
    {
        $joins = array();
        if ($this->with_activity) {
            $joins['a'] = PlSqlJoin::inner('activities', '$ME.aid = ai.aid');
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
            $joins['c'] = PlSqlJoin::left('castes', '$ME.gid = a.target');
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
