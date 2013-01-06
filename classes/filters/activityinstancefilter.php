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

abstract class ActivityInstanceFilterCondition extends FrankizFilterCondition
{
}

class AIFC_Id extends ActivityInstanceFilterCondition
{
    private $ids;

    public function __construct($ns)
    {
        $this->ids = ActivityInstance::toIds(unflatten($ns));
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('ai.id IN {?}', $this->ids);
    }
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

/** Retrieves instances where the target is owned by the specified groups
 * @param $gs Collection of Groups
 */
class AIFC_TargetGroup extends ActivityInstanceFilterCondition
{
    private $cids;

    public function __construct(Collection $groups)
    {
        $cf = new CasteFilter(new PFC_And(new CFC_Holder(), new CFC_Group($groups)));
        $this->cids = $cf->get()->ids();
    }

    public function buildCondition(PlFilter $f)
    {
        $sub = $f->addActivityFilter();
        return XDB::format("$sub.target IN {?}", $this->cids);
    }
}

/** Filters instances based on the target caste of the activity
 * @param $gs A Caste, a cid or an array of it
 */
class AIFC_Target extends ActivityInstanceFilterCondition
{
    private $cids;

    public function __construct($cs)
    {
        if ($cs instanceof Collection) {
            if ($cs->className() != 'Caste') {
                throw new Exception('AIFC_Target constructor takes a Collection<Caste>');
            }
            $this->cids = $cs->ids();
        } else {
            $this->cids = Caste::toIds(unflatten($cs));
        }
    }

    public function buildCondition(PlFilter $f)
    {
        $sub = $f->addActivityFilter();
        return XDB::format("$sub.target IN {?}", $this->cids);
    }
}

/** Returns instances for which the users are linked to the target group
 * @param $us     A User, a uid or an array
 * @param $rights The rights the user must have in the targeted group (everybody by default)
 */
class AIFC_User extends ActivityInstanceFilterCondition
{
    private $uids;
    private $rights;

    public function __construct($us, $rights)
    {
        $this->uids = User::toIds(unflatten($us));
        $this->rights = (empty($rights)) ? Rights::everybody() : $rights;
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

/** Returns activities that the users are allowed to see
 * (ie the user is in the group or the news is public)
 * @param $us     A User, a uid or an array
 */
class AIFC_CanBeSeen extends ActivityInstanceFilterCondition
{
    private $uids;

    public function __construct($us)
    {
        $this->uids = User::toIds(unflatten($us));
    }

    public function buildCondition(PlFilter $f)
    {
        $f->addActivityFilter();
        $c = $f->addCasteFilter();
        $cu = $f->addUserFilter();
        return XDB::format("$c.rights = {?} OR ($c.rights = {?} AND $cu.uid IN {?})",
                        (string) Rights::everybody(), (string) Rights::restricted(), $this->uids);
    }
}

/** Filters instances based on the fact that the given users participate
 * @param $us A User, a Uid or an array of it
 */
class AIFC_Participants extends ActivityInstanceFilterCondition
{
    private $uids;

    public function __construct($us)
    {
        $this->uids = User::toIds(unflatten($us));
    }

    public function buildCondition(PlFilter $f)
    {
        $p = $f->addParticipantsFilter();
        return XDB::format("$p.participant IN {?}", $this->uids);
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
            $joins['a'] = PlSqlJoin::inner('activities', '$ME.aid = ai.activity');
        }
        return $joins;
    }

    private $with_participants = false;

    public function addParticipantsFilter()
    {
        $this->with_participants = true;
        return 'ap';
    }

    protected function participantsJoins()
    {
        $joins = array();
        if ($this->with_participants) {
            $joins['ap'] = PlSqlJoin::inner('activities_participants', '$ME.id = ai.id');
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
            $joins['cu'] = PlSqlJoin::left('castes_users',
                '$ME.cid = c.cid AND ($ME.visibility IN {?} OR $ME.uid = {?})',
                S::user()->visibleGids(), S::user()->id());
        }
        return $joins;
    }
}
// }}}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
