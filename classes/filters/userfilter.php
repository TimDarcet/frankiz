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


/******************
 * CONDITIONS
 ******************/

// {{{ interface UserFilterCondition
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
// }}}

// {{{ class UFC_Uid
/** Filters users based on their hruid
 * @param $val Either an hruid, or a list of those
 */
class UFC_Uid extends UserFilterCondition
{
    private $uids;

    public function __construct($us)
    {
        $this->uids = User::toIds(unflatten($gs));
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
// }}}

// {{{ class UFC_Hruid
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
// }}}

// {{{ class UFC_Ip
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
// }}}

// {{{ class UFC_Comment
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
// }}}

// {{{ class UFC_Promo
/** Filters users based on promotion
 * @param $comparison Comparison operator (>, =, ...)
 * @param $promo Promotion on which the filter is based
 * @param $study Formation Id on which to restrict, 0 for "any formation"
 */
class UFC_Promo extends UserFilterCondition
{
    private $comparison;
    private $promo;

    public function __construct($promo, $comparison = '=')
    {
        $this->promo = $promo;
        $this->comparison = $comparison;
    }

    public function buildCondition(PlFilter $uf)
    {
        $sub = $uf->addStudiesFilter();
        return XDB::format("$sub.promo $this->comparison {?}", $this->promo);
    }

    public function export()
    {
        return array('type' => 'promo', 'comparison' => $this->comparison, 'promo' => $this->promo);
    }
}
// }}}

// {{{ class UFC_Study
/** Filters users by studies
 * @param $formation_id The id of the study
 */
class UFC_Study extends UserFilterCondition
{
    private $formation_ids = null;

    public function __construct($formation_ids)
    {
        $this->formation_ids = unflatten($formation_ids);
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
// }}}

// {{{ class UFC_Name
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

        return $cond = implode(' OR ', $conds);
    }
}
// }}}

// {{{ class UFC_Bestalias
/** Filters users based on their mail adresse
 * @param $mail Mail adresse
 */
class UFC_Bestalias extends UserFilterCondition
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
        return XDB::format('a.bestalias IN {?}', $this->val);
    }
}
// }}}

// {{{ class UFC_Nationality
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
// }}}

// {{{ class UFC_Birthday
/** Filters users based on next birthday date
 * @param $comparison Comparison operator
 * @param $date Date to which users next birthday date should be compared
 */
class UFC_Birthday extends UserFilterCondition
{
    private $comparison;
    private $date;

    public function __construct($comparison = null, $date = null)
    {
        $this->comparison = $comparison;
        $this->date = $date;
    }

    public function buildCondition(PlFilter $uf)
    {
        return 'p.next_birthday ' . $this->comparison . XDB::format(' {?}', date('Y-m-d', $this->date));
    }
}
// }}}

// {{{ class UFC_Sex
/** Filters users based on sex
 * @param $sex One of User::GENDER_MALE or User::GENDER_FEMALE, for selecting users
 */
class UFC_Sex extends UserFilterCondition
{
    private $sex;

    public function __construct($sex)
    {
        $this->sex = $sex;
    }

    public function buildCondition(PlFilter $uf)
    {
        if ($this->sex != User::GENDER_MALE && $this->sex != User::GENDER_FEMALE) {
            return self::COND_FALSE;
        } else {
            return XDB::format('a.sex = {?}', $this->sex == User::GENDER_FEMALE ? 'woman' : 'man');
        }
    }
}
// }}}

// {{{ class UFC_Group
/** Filters users based on group membership
 * @param $group Group whose members we are selecting
 * @param $right Level of membership (Rights::FRIEND, Rights::MEMBER, ...)
 */
class UFC_Group extends UserFilterCondition
{
    private $gids;
    private $right;

    public function __construct($gs, $right = Rights::MEMBER)
    {
        $this->right = $right;
        $this->gids  = Group::toIds(unflatten($gs));
    }

    public function buildCondition(PlFilter $uf)
    {
        $sub = $uf->addGroupFilter();
        return XDB::format($sub . '.gid IN {?}', $this->gids);
    }

    public function export()
    {
        return array("type" => 'group', "children" => $this->gids);
    }
}
// }}}

// {{{ class UFC_Room
/** Filters users based on their room'sid
 * @param $val Room's Id
 */
class UFC_Room extends UserFilterCondition
{
    private $val;

    public function __construct($val)
    {
        $this->val = $val;
    }

    public function buildCondition(PlFilter $uf)
    {
        $sub = $uf->addRoomFilter();
        $right = XDB::formatWildcards(XDB::WILDCARD_CONTAINS, $this->val);
        return $sub . '.rid ' . $right;
    }
}
// }}}

// {{{ class UFC_Cellphone
class UFC_Cellphone extends UserFilterCondition
{
    private $number;

    public function __construct($number)
    {
        $this->number = $number;
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('a.cellphone = {?}', $this->number);
    }
}
// }}}

// {{{ class UFC_Casertphone
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
// }}}



/******************
 * ORDERS
 ******************/

// {{{ class UserFilterOrder
/** Base class for ordering results of a query.
 * Parameters for the ordering must be given to the constructor ($desc for a
 *     descending order).
 * The getSortTokens function is used to get actual ordering part of the query.
 */
abstract class UserFilterOrder extends FrankizFilterOrder
{
    /** This function must return the tokens to use for ordering
     * @param &$uf The UserFilter whose results must be ordered
     * @return The name of the field to use for ordering results
     */
//    abstract protected function getSortTokens(UserFilter &$uf);

    public function export()
    {
        throw new Exception();
    }
}
// }}}

// {{{ class UFO_Promo
/** Orders users by promotion
 * @param $grade Formation whose promotion users should be sorted by (restricts results to users of that formation)
 * @param $desc Whether sort is descending
 */
class UFO_Promo extends UserFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
        $this->grade = $grade;
    }

    protected function getSortTokens(PlFilter $uf)
    {
        $sub = $uf->addStudiesFilter();
        return $sub . '.promo';
    }
}
// }}}

// {{{ class UFO_Name
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
// }}}

// {{{ class UFO_Birthday
/** Sorts users based on next birthday date
 */
class UFO_Birthday extends UserFilterOrder
{
    protected function getSortTokens(PlFilter $uf)
    {
        return 'a.next_birthday';
    }
}
// }}}

/***********************************
  *********************************
          USER FILTER CLASS
  *********************************
 ***********************************/

// {{{ class UserFilter
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
            $joins['ro'] = PlSqlJoin::left('rooms_owners', '$ME.owner_id = a.uid AND $ME.owner_type = "user"');
            $joins['r']  = PlSqlJoin::left('rooms', '$ME.rid = ro.rid');
        }
        return $joins;
    }

    protected function ipJoins()
    {
        $joins = array();
        if ($this->with_ip)
            $joins['tips'] = PlSqlJoin::left('ips', '$ME.rid = r.rid');

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

    /** GROUPS
     */
    private $with_groups = array();

    public function addGroupFilter()
    {
        $table_uid = 'ug_' . uniqid();
        $this->with_groups[$table_uid] = true;
        return $table_uid;
    }

    protected function groupJoins()
    {
        $joins = array();
        foreach ($this->with_groups as $table_uid => $bool)
            if ($bool)
                $joins[$table_uid] = PlSqlJoin::inner('users_groups', '$ME.uid = a.uid');

        return $joins;
    }

    /** PHONE
     */
    private $with_ptel = false;

    public function addPhoneFilter()
    {
        $this->with_ptel = true;
        return 'ptel';
    }

    protected function phoneJoins()
    {
        $joins = array();
        if ($this->with_ptel) {
            $joins['ptel'] = PlSqlJoin::left('profile_phones', '$ME.pid = $PID');
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
            case 'and':
                $obj = new PFC_And();
                break;

            case 'study':
                $obj = new UFC_Study($export['formation_ids']);
                break;

            case 'promo':
                if (empty($export['comparison']))
                    $obj = new UFC_Promo($export['promo']);
                else
                    $obj = new UFC_Promo($export['promo'], $export['comparison']);
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

    /* /!\ UserFilter's id isn't numeric, but a hash
    */
    public function id()
    {
        return md5(json_encode($this->export()));
    }

    public function insert()
    {
        XDB::execute('INSERT IGNORE  userfilters
                                SET  ufid = {?}, userfilter = {?}',
                                     $this->id(), json_encode($this->export()));
        XDB::execute('DELETE FROM userfilters_dependencies WHERE ufid = {?}', $this->id());
    }

    public function delete()
    {
        XDB::execute('DELETE FROM userfilters WHERE ufid = {?}', $this->id());
        XDB::execute('DELETE FROM userfilters_dependencies WHERE ufid = {?}', $this->id());
    }

}
// }}}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
