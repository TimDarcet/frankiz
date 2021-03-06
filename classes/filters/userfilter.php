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


/******************
 * CONDITIONS
 ******************/


/** This interface describe objects which filter users based
 *      on various parameters.
 * The parameters of the filter must be given to the constructor.
 * The buildCondition function is called by UserFilter when
 *     actually building the query. That function must call
 *     $uf->addWheteverFilter so that the UserFilter makes
 *     adequate joins. It must return the 'WHERE' condition to use
 *     with the filter.
 */
abstract class UserFilterCondition extends FrankizFilterCondition
{
}

/** Filters users based on their hruid
 * @param $val Either an hruid, or a list of those
 */
class UFC_Uid extends UserFilterCondition
{
    private $uids;

    public function __construct($us)
    {
        $this->uids = User::toIds(unflatten($us));
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('a.uid IN {?}', $this->uids);
    }

    public function export()
    {
        return array('type' => 'uid', 'uids' => $this->uids);
    }
}

/** Filters users based on their hruid
 * @param $val Either an hruid, or a list of those
 */
class UFC_Hruid extends UserFilterCondition
{
    private $hruids;

    public function __construct($val)
    {
        if (!is_array($val)) {
            $val = array($val);
        }
        $this->hruids = $val;
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('a.hruid IN {?}', $this->hruids);
    }
}

/** Filters users based on their IPs
 * @param $ip IP from which connection are checked
 */
class UFC_Ip extends UserFilterCondition
{
    private $ip;

    public function __construct($ip)
    {
        $this->ip = $ip;
    }

    public function buildCondition(PlFilter $uf)
    {
        $sub = $uf->addIpFilter();
        $right = XDB::formatWildcards(XDB::WILDCARD_CONTAINS, $this->ip);
        return $sub . '.ip ' . $right;
    }
}

class UFC_Poly extends UserFilterCondition
{
    private $poly;

    public function __construct($poly)
    {
        $this->poly = $poly;
    }

    public function buildCondition(PlFilter $uf)
    {
        $sub = $uf->addPolyFilter();
        return XDB::format($sub . '.poly = {?}', $this->poly);
    }
}

class UFC_Comment extends UserFilterCondition
{
    private $text;

    public function __construct($text)
    {
        $this->text = $text;
    }

    public function buildCondition(PlFilter $uf)
    {
        return 'a.comment ' . XDB::formatWildcards(XDB::WILDCARD_CONTAINS, $this->text);
    }
}

/** Filters users based on promotion
 * @param $comparison Comparison operator (>, =, ...)
 * @param $promo Promotion on which the filter is based
 * @param $study Formation Id on which to restrict, 0 for "any formation"
 */
class UFC_Promo extends UserFilterCondition
{
    private $comparison;
    private $promo;
    private $formation_id;

    public function __construct($promo, $comparison = '=', $formation_id = 0)
    {
        $this->promo = $promo;
        $this->comparison = $comparison;
        $this->formation_id = $formation_id;
    }

    public function buildCondition(PlFilter $uf)
    {
        $sub = $uf->addStudiesFilter();
        if ($this->formation_id > 0) {
            return XDB::format("$sub.promo $this->comparison {?} AND $sub.formation_id = {?}",
                $this->promo, $this->formation_id);
        } else {
            return XDB::format("$sub.promo $this->comparison {?}", $this->promo);
        }
    }

    public function export()
    {
        $export = array('type' => 'promo', 'comparison' => $this->comparison, 'promo' => $this->promo);
        if ($this->formation_id > 0)
            $export['formation_id'] = $this->formation_id;
        return $export;
    }
}

/** Filters users by studies
 * @param $formation_id The id of the study
 */
class UFC_Study extends UserFilterCondition
{
    private $formation_ids = null;

    public function __construct($formations)
    {
        if ($formations instanceof Collection) {
            $this->formation_ids = $formations->ids();
        } elseif ($formations instanceof Formation) {
            $this->formation_ids = unflatten($formations->id());
        } else {
            $this->formation_ids = unflatten($formations);
        }
    }

    public function buildCondition(PlFilter $uf)
    {
        $sub = $uf->addStudiesFilter();
        return XDB::format($sub . '.formation_id IN {?}', $this->formation_ids);
    }

    public function export()
    {
        return array('type' => 'study', 'formation_ids' => $this->formation_ids);
    }
}

/** Filters users based on name(s)
 * @param $type Type of name field on which filtering is done (firstname, lastname, both, ...)
 * @param $text Text on which to filter
 * @param $mode Flag indicating search type (prefix, suffix ...)
 */
class UFC_Name extends UserFilterCondition
{
    // Modes
    const PREFIX   = XDB::WILDCARD_PREFIX;   // 0x001
    const SUFFIX   = XDB::WILDCARD_SUFFIX;   // 0x002
    const CONTAINS = XDB::WILDCARD_CONTAINS; // 0x003

    // Types (can be combined with &)
    const LASTNAME  = 0x01;
    const FIRSTNAME = 0x02;
    const NICKNAME  = 0x04;

    private $type;
    private $text;
    private $mode;

    public function __construct($text, $type, $mode)
    {
        $this->type = $type;
        $this->text = $text;
        $this->mode = $mode;
    }

    public function buildCondition(PlFilter $uf)
    {
        $right = XDB::formatWildcards($this->mode, $this->text);

        $conds = array();
        if ($this->type & self::LASTNAME)
            $conds[] = 'a.lastname' . $right;

        if ($this->type & self::FIRSTNAME)
            $conds[] = 'a.firstname' . $right;

        if ($this->type & self::NICKNAME)
            $conds[] = 'a.nickname' . $right;

        return implode(' OR ', $conds);
    }
}

/** Filters users based on their forlives
 * @param $forlife
 * @param $domain
 */
class UFC_Forlife extends UserFilterCondition
{
    private $forlife;
    private $domain;

    public function __construct($forlife, $domain)
    {
        $this->forlife = $forlife;
        $this->domain = $domain;
    }

    public function buildCondition(PlFilter $uf)
    {
        $s = $uf->addStudiesFilter();
        $f = $uf->addFormationsFilter();
        return XDB::format("$s.forlife = {?} AND $f.domain = {?}", $this->forlife, $this->domain);
    }
}

/** Filters users based on their nationality
 * @param $val Nation's Id
 */
class UFC_Nationality extends UserFilterCondition
{
    private $val;

    public function __construct($val)
    {
        if (!is_array($val)) {
            $val = array($val);
        }
        $this->val = $val;
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('a.nation IN {?}', $this->val);
    }
}

/** Filters users based on next birthday date
 * @param $comparison Comparison operator
 * @param $date Date to which users next birthday date should be compared
 */
class UFC_Birthday extends UserFilterCondition
{
    private $comparison;
    private $date;

    public function __construct($comparison = null, FrankizDateTime $date = null)
    {
        $this->comparison = $comparison;
        $this->date = $date;
    }

    public function buildCondition(PlFilter $uf)
    {
        return 'a.next_birthday ' . $this->comparison . XDB::format(' {?}', $this->date->format('Y-m-d'));
    }
}

/** Filters users based on gender
 * @param $sex One of User::GENDER_MALE or User::GENDER_FEMALE, for selecting users
 */
class UFC_Gender extends UserFilterCondition
{
    private $gender;

    public function __construct($gender)
    {
        $this->gender = $gender;
    }

    public function buildCondition(PlFilter $uf)
    {
        if ($this->gender != User::GENDER_MALE && $this->gender != User::GENDER_FEMALE) {
            return self::COND_FALSE;
        } else {
            return XDB::format('a.gender = {?}', $this->gender);
        }
    }
}

/** Filters users based on group membership
 * The groups&rights are converted to castes when the filter is executed or exported
 * @param $group Group
 * @param $rights Rights level in the group
 */
class UFC_Group extends UserFilterCondition
{
    static protected $instances = array();

    protected $gids;
    protected $rights;
    protected $cids = null;

    public function __construct($gs, Rights $rights = null)
    {
        if ($gs instanceof Collection) {
            $this->gids = $gs->ids();
        } else {
            $this->gids = Group::toIds(unflatten($gs));
        }
        $this->rights = (empty($rights)) ? Rights::member() : $rights;
        self::$instances[] = $this;
    }

    private function fetchCids()
    {
        if ($this->cids === null)
        {
            $groupsrights = array();
            foreach (self::$instances as $instance) {
                foreach ($instance->gids as $gid)
                    $groupsrights[] = array('group' => $gid, 'rights' => $instance->rights);
            }

            $castes = Caste::batchFrom($groupsrights);

            foreach (self::$instances as $instance) {
                $gids   = $instance->gids;
                $rights = $instance->rights;
                $filtered = $castes->filter(
                    function ($c) use($gids, $rights) {
                        return (in_array($c->group()->id(), $gids)) && ($c->rights()->isMe($rights));
                    }
                );

                $instance->cids = $filtered->ids();
            }
        }

        return $this->cids;
    }

    public function buildCondition(PlFilter $f)
    {
        $cids = $this->fetchCids();
        if (!empty($cids)) {
            $sub = $f->addCasteFilter();
            return XDB::format($sub . '.cid IN {?}', $cids);
        }
        return '0';
    }

    public function export()
    {
        return array("type" => 'caste', "children" => $this->fetchCids());
    }
}

/** Filters users based on caste membership
 * @param $caste Caste whose members we are selecting
 */
class UFC_Caste extends UserFilterCondition
{
    private $cids;

    public function __construct($cs)
    {
        if ($cs instanceof Collection) {
            $this->cids = $cs->ids();
        } else {
            $this->cids = Caste::toIds(unflatten($cs));
        }
    }

    public function buildCondition(PlFilter $f)
    {
        $sub = $f->addCasteFilter();
        return XDB::format($sub . '.cid IN {?}', $this->cids);
    }

    public function export()
    {
        return array("type" => 'caste', "children" => $this->cids);
    }
}

/** Filters users based on their room'sid
 * @param $val Room's Id
 */
class UFC_Room extends UserFilterCondition
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

class UFC_Cellphone extends UserFilterCondition
{
    private $number;

    public function __construct($number)
    {
        $this->number = $number;
    }

    public function buildCondition(PlFilter $uf)
    {
        $right = XDB::formatWildcards(XDB::WILDCARD_CONTAINS, $this->number);
        return 'a.cellphone' . $right;
    }
}

class UFC_Roomphone extends UserFilterCondition
{
    private $number;

    public function __construct($number)
    {
        $this->number = $number;
    }

    public function buildCondition(PlFilter $uf)
    {
        $sub = $uf->addRoomFilter();
        $right = XDB::formatWildcards(XDB::WILDCARD_CONTAINS, $this->number);
        return $sub . '.phone ' . $right;
    }
}

/** Filters users based on participation in an activity
 * @param $ais ActivityInstances whose participants we are selecting
 */
class UFC_ActivityInstance extends UserFilterCondition
{
    private $aids;

    public function __construct($ais)
    {
        $this->aids = ActivityInstance::toIds(unflatten($ais));
    }

    public function buildCondition(PlFilter $f)
    {
        $sub = $f->addActivityInstanceFilter();
        return XDB::format($sub . '.id IN {?}', $this->aids);
    }

    public function export()
    {
        return array("type" => 'activityInstance', "children" => $this->aids);
    }
}



/******************
 * ORDERS
 ******************/

/** Base class for ordering results of a query.
 * Parameters for the ordering must be given to the constructor ($desc for a
 *     descending order).
 * The getSortTokens function is used to get actual ordering part of the query.
 */
abstract class UserFilterOrder extends FrankizFilterOrder
{
}

/** Orders users by promotion
 * @param $grade Formation whose promotion users should be sorted by (restricts results to users of that formation)
 * @param $desc Whether sort is descending
 */
class UFO_Promo extends UserFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $uf)
    {
        $sub = $uf->addStudiesFilter();
        return $sub . '.promo';
    }
}

/** Sorts users by name
 * @param $type Type of name on which to sort (firstname...)
 * @param $desc If sort order should be descending
 */
class UFO_Name extends UserFilterOrder
{
    private $type;

    const LASTNAME  = 0x01;
    const FIRSTNAME = 0x02;
    const NICKNAME  = 0x04;

    public function __construct($type, $desc = false)
    {
        parent::__construct($desc);
        $this->type = $type;
    }

    protected function getSortTokens(PlFilter $uf)
    {
        if ($this->type == self::LASTNAME)
            return 'a.lastname';

        if ($this->type == self::FIRSTNAME)
            return 'a.firstname';

        if ($this->type == self::NICKNAME)
            return 'a.nickname';
    }
}

/** Sorts users based on next birthday date
 */
class UFO_Birthday extends UserFilterOrder
{
    protected function getSortTokens(PlFilter $uf)
    {
        return 'a.next_birthday';
    }
}

/***********************************
  *********************************
          USER FILTER CLASS
  *********************************
 ***********************************/

/** This class provides a convenient and centralized way of filtering users.
 *
 * Usage:
 * $uf = new UserFilter(new UFC_Blah($x, $y), new UFO_Coin($z, $t));
 *
 * Resulting UserFilter can be used to:
 * - get a list of User objects matching the filter
 * - get a list of UIDs matching the filter
 * - get the number of users matching the filter
 * - check whether a given User matches the filter
 * - filter a list of User objects depending on whether they match the filter
 *
 * Usage for UFC and UFO objects:
 * A UserFilter will call all private functions named XXXJoins.
 * These functions must return an array containing the list of join
 * required by the various UFC and UFO associated to the UserFilter.
 * Entries in those returned array are of the following form:
 *   'join_tablealias' => array('join_type', 'joined_table', 'join_criter')
 * which will be translated into :
 *   join_type JOIN joined_table AS join_tablealias ON (join_criter)
 * in the final query.
 *
 * In the join_criter text, $ME is replaced with 'join_tablealias' and $UID with accounts.uid.
 *
 * For each kind of "JOIN" needed, a function named addXXXFilter() should be defined;
 * its parameter will be used to set various private vars of the UserFilter describing
 * the required joins ; such a function shall return the "join_tablealias" to use
 * when referring to the joined table.
 *
 * For example, if data from profile_job must be available to filter results,
 * the UFC object will call $uf-addJobFilter(), which will set the 'with_pj' var and
 * return 'pj', the short name to use when referring to profile_job; when building
 * the query, calling the jobJoins function will return an array containing a single
 * row:
 *   'pj' => array('left', 'profile_job', '$ME.pid = $UID');
 *
 * The 'register_optional' function can be used to generate unique table aliases when
 * the same table has to be joined several times with different aliases.
 */
class UserFilter extends FrankizFilter
{
    protected $joinMetas = array('$UID' => 'a.uid');

    protected function schema()
    {
        return array('table' => 'account',
                     'as'    => 'a',
                     'id'    => 'uid');
    }

    /** ROOM (casert, ip, phone)
     */
    private $with_room = false;
    private $with_ip = false;

    public function addRoomFilter()
    {
        $this->with_room = true;
        return 'r';
    }

    public function addIpFilter()
    {
        $this->with_room = true;
        $this->with_ip = true;
        return 'tips';
    }

    protected function roomJoins()
    {
        $joins = array();
        if ($this->with_room) {
            $joins['ru'] = PlSqlJoin::left('rooms_users', '$ME.uid = a.uid');
            $joins['r']  = PlSqlJoin::left('rooms', '$ME.rid = ru.rid');
        }
        return $joins;
    }

    protected function ipJoins()
    {
        $joins = array();
        if ($this->with_ip)
            $joins['tips'] = PlSqlJoin::left('ips', '$ME.room = r.rid');

        return $joins;
    }

    /** EDUCATION
     */
    private $with_studies = false;

    public function addStudiesFilter()
    {
        $this->with_studies = true;
        return 's';
    }

    protected function studiesJoins()
    {
        $joins = array();
        if ($this->with_studies) {
            $joins['s'] = PlSqlJoin::inner('studies', '$ME.uid = a.uid');
        }
        return $joins;
    }

    private $with_formations = false;

    public function addFormationsFilter()
    {
        $this->with_formations = true;
        $this->addStudiesFilter();
        return 'f';
    }

    protected function formationsJoins()
    {
        $joins = array();
        if ($this->with_formations) {
            $joins['f'] = PlSqlJoin::inner('formations', '$ME.formation_id = s.formation_id');
        }
        return $joins;
    }

    /** POLY
     */
    private $with_poly = false;

    public function addPolyFilter()
    {
        $this->with_poly = true;
        return 'p';
    }

    protected function polyJoins()
    {
        $joins = array();
        if ($this->with_poly) {
            $joins['p'] = PlSqlJoin::inner('poly', '$ME.uid = a.uid');
        }
        return $joins;
    }

    /** CASTES
     */
    private $with_castes = 0;

    public function addCasteFilter()
    {
        $this->with_castes++;
        return 'cu' . $this->with_castes;
    }

    protected function casteJoins()
    {
        $joins = array();
        if ($this->with_castes > 0) {
            for ($i = 1; $i <= $this->with_castes; $i++) {
                $joins['cu' . $i] = PlSqlJoin::inner('castes_users',
                    '$ME.uid = a.uid AND ($ME.visibility IN {?} OR $ME.uid = {?})',
                    S::user()->visibleGids(), S::user()->id());
            }
        }
        return $joins;
    }

    /** ACTIVITY INSTANCES
     */
    private $with_activityinstances = 0;

    public function addActivityInstanceFilter()
    {
        $this->with_activityinstances++;
        return 'ai' . $this->with_activityinstances;
    }

    protected function activityInstanceJoins()
    {
        $joins = array();
        if ($this->with_activityinstances > 0) {
            for ($i = 1; $i <= $this->with_activityinstances; $i++) {
                $joins['ai' . $i] = PlSqlJoin::inner('activities_participants', '$ME.participant = a.uid');
            }
        }
        return $joins;
    }

    /**
     * EXPORT & IMPORT
     */
    public function export()
    {
        $export = array('type' => 'user');
        if (!empty($this->root))
            $export['condition'] =  $this->root->export();
        if (!empty($this->sort))
            $export['sort'] =  $this->sort->export();
        return $export;
    }

    public static function fromExport(array $export) {
        $condition = null;
        $sort = null;

        if (!empty($export['condition']))
            $condition = self::importCondition($export['condition']);
        if (!empty($export['sort']))
            $sort = self::importSort($export['sort']);

        return new UserFilter($condition, $sort);
    }

    public static function importCondition($export)
    {
        $obj = null;
        switch ($export['type']) {
            case 'true':
                $obj = new PFC_True();
                break;

            case 'and':
                $obj = new PFC_And();
                break;

            case 'or':
                $obj = new PFC_Or();
                break;

            case 'not':
                $obj = new PFC_Not();
                break;

            case 'study':
                $obj = new UFC_Study($export['formation_ids']);
                break;

            case 'uid':
                $obj = new UFC_Uid($export['uids']);
                break;

            case 'caste':
                $obj = new UFC_Caste($export['children']);
                break;

            case 'promo':
                $obj = new UFC_Promo($export['promo'],
                    empty($export['comparison']) ? '=' : $export['comparison'],
                    empty($export['formation_id']) ? 0 : $export['formation_id']);
                break;

            case 'activityInstance':
                $obj = new UFC_ActivityInstance($export['children']);
                break;
        }

        if ($obj == null)
         throw new Exception("Object ".$export['type']." doesn't exist");

        if ($obj instanceof PFC_OneChild)
            $obj->setChild(self::importCondition($export['child']));
        elseif ($obj instanceof PFC_NChildren)
            foreach ($export['children'] as $child)
                $obj->addChild(self::importCondition($child));

        return $obj;
    }

    public function dependencies($root = null)
    {
        if ($root === null)
            $root = $this->root->export();

        $castes = array();

        if ($root['type'] == 'and' || $root['type'] == 'or') {
            foreach ($root['children'] as $child) {
                $castes = array_merge($castes, $this->dependencies($child));
            }
        }

        if ($root['type'] == 'not') {
            $castes = array_merge($castes, $this->dependencies($root['child']));
        }

        if ($root['type'] == 'caste') {
            $castes = array_merge($castes, $root['children']);
        }

        return array_unique($castes);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
