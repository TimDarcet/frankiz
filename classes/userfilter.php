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
interface UserFilterCondition extends PlFilterCondition
{
}
// }}}

// {{{ class UFC_Hruid
/** Filters users based on their hruid
 * @param $val Either an hruid, or a list of those
 */
class UFC_Hruid implements UserFilterCondition
{
    private $hruids;

    public function __construct($val)
    {
        if (!is_array($val)) {
            $val = array($val);
        }
        $this->hruids = $val;
    }

    public function buildCondition(PlFilter &$uf)
    {
        return XDB::format('a.hruid IN {?}', $this->hruids);
    }
}
// }}}

// {{{ class UFC_Ip
/** Filters users based on one of their last IPs
 * @param $ip IP from which connection are checked
 */
class UFC_Ip implements UserFilterCondition
{
    private $ip;

    public function __construct($ip)
    {
        $this->ip = $ip;
    }

    public function buildCondition(PlFilter &$uf)
    {
        $sub = $uf->addIpFilter();
        return XDB::format($sub . '.ip LIKE {?}', $ip);
    }
}
// }}}

// {{{ class UFC_Comment
class UFC_Comment implements UserFilterCondition
{
    private $text;

    public function __construct($text)
    {
        $this->text = $text;
    }

    public function buildCondition(PlFilter &$uf)
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
class UFC_Promo implements UserFilterCondition
{
    private $comparison;
    private $promo;
    private $formation_id;

    public function __construct($comparison, $promo, $formation_id)
    {
        $this->formation_id = $formation_id;
        $this->comparison = $comparison;
        $this->promo = $promo;
    }

    public function buildCondition(PlFilter &$uf)
    {
        $sub = $uf->addStudiesFilter();

        $formation_cond = '';
        if ($this->formation_id != 0)
            $formation_cond = ' AND ' . $sub . '.formation_id = ' . $this->formation_id;

        return '(' . $sub . '.promo ' . $this->comparison . ' ' . $this->promo . $formation_cond . ')';
    }
}
// }}}

// {{{ class UFC_Formation
/** Filters users by formation
 * @param $val The formation to search (either ID or array of IDs)
 */
class UFC_Formation implements UserFilterCondition
{
    private $val;

    public function __construct($val)
    {
        if (!is_array($val)) {
            $val = array($val);
        }
        $this->val = $val;
    }

    public function buildCondition(PlFilter &$uf)
    {
        $sub = $uf->addStudiesFilter();
        return XDB::format($sub . '.formation_id IN {?}', $this->val);
    }
}
// }}}

// {{{ class UFC_Name
/** Filters users based on name(s)
 * @param $type Type of name field on which filtering is done (firstname, lastname, both, ...)
 * @param $text Text on which to filter
 * @param $mode Flag indicating search type (prefix, suffix ...)
 */
class UFC_Name implements UserFilterCondition
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

    public function buildCondition(PlFilter &$uf)
    {
        $right = XDB::formatWildcards($this->mode, $this->text);

        $conds = array();
        if (($this->type & self::LASTNAME) == $this->type)
            $conds[] = 'a.lastname' . $right;

        if (($this->type & self::FIRSTNAME) == $this->type)
            $conds[] = 'a.firstname' . $right;

        if (($this->type & self::NICKNAME) == $this->type)
            $conds[] = 'a.nickname' . $right;

        return $cond = implode(' OR ', $conds);
    }
}
// }}}

// {{{ class UFC_Nationality
/** Filters users based on their nationality
 * @param $val Nation's Id
 */
class UFC_Nationality implements UserFilterCondition
{
    private $val;

    public function __construct($val)
    {
        if (!is_array($val)) {
            $val = array($val);
        }
        $this->val = $val;
    }

    public function buildCondition(PlFilter &$uf)
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
class UFC_Birthday implements UserFilterCondition
{
    private $comparison;
    private $date;

    public function __construct($comparison = null, $date = null)
    {
        $this->comparison = $comparison;
        $this->date = $date;
    }

    public function buildCondition(PlFilter &$uf)
    {
        return 'p.next_birthday ' . $this->comparison . XDB::format(' {?}', date('Y-m-d', $this->date));
    }
}
// }}}

// {{{ class UFC_Sex
/** Filters users based on sex
 * @param $sex One of User::GENDER_MALE or User::GENDER_FEMALE, for selecting users
 */
class UFC_Sex implements UserFilterCondition
{
    private $sex;

    public function __construct($sex)
    {
        $this->sex = $sex;
    }

    public function buildCondition(PlFilter &$uf)
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
 * @param $type Level of membership (Cluster::LOBBY, Cluster::MEMBER, ...)
 */
class UFC_Group implements UserFilterCondition
{
    private $group;
    private $type;

    public function __construct($group, $type = Cluster::LOBBY)
    {
        $this->group = intval($group);
        $this->type = $type;
    }

    public function buildCondition(PlFilter &$uf)
    {
        // Check for members at least "sympathisants"
        if ($this->type == Cluster::LOBBY) {
            $sub = $uf->addGroupFilter();
            return $sub . '.gid = ' . $this->group;

        // Check for active members
        } else if ($this->type == Cluster::MEMBER) {
            return 'EXISTS ( 
            SELECT * FROM user_clusters AS sub_uc 
            INNER JOIN clusters AS sub_c ON sub_c.cid = sub_uc.cid 
            WHERE sub_uc.uid = a.uid AND sub_c.type != ' . Cluster::LOBBY . ' AND sub_c.gid = ' . $this->group . ' )';

        // Check for writers
        } else if ($this->type == Cluster::MEMBER) {
            return 'EXISTS ( 
                SELECT * FROM user_clusters AS sub_uc 
                INNER JOIN clusters AS sub_c ON sub_c.cid = sub_uc.cid 
                WHERE sub_uc.uid = a.uid AND (sub_c.type == ' . Cluster::WRITER . ' OR sub_c.type == ' . Cluster::ADMIN . ') AND sub_c.gid = ' . $this->group . ' )';

        // Check for admins
        } else if ($this->type == Cluster::ADMIN) {
            return 'EXISTS ( 
                SELECT * FROM user_clusters AS sub_uc 
                INNER JOIN clusters AS sub_c ON sub_c.cid = sub_uc.cid 
                WHERE sub_uc.uid = a.uid AND sub_c.type == ' . Cluster::ADMIN . ' AND sub_c.gid = ' . $this->group . ' )';
        }

    }
}
// }}}

// {{{ class UFC_Casert
/** Filters users based on group membership
 * @param $val Room's Id
 */
abstract class UFC_Casert implements UserFilterCondition
{
    private $val;

    public function __construct($val)
    {
        $this->val = $val;
    }

    public function buildCondition(PlFilter &$uf)
    {
        $sub = $uf->addRoomFilter();
        return XDB::format($sub . '.owner_id = {?}', $this->val);
    }
}
// }}}

// {{{ class UFC_Cellphone
class UFC_Cellphone implements UserFilterCondition
{
    private $number;

    public function __construct($number)
    {
        $this->number = $number;
    }

    public function buildCondition(PlFilter &$uf)
    {
        return XDB::format('a.cellphone = {?}', $this->number);
    }
}
// }}}

// {{{ class UFC_Casertphone
class UFC_Casertphone implements UserFilterCondition
{
    private $number;

    public function __construct($number)
    {
        $this->number = $number;
    }

    public function buildCondition(PlFilter &$uf)
    {
        return XDB::format('a.cellphone = {?}', $this->number);
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
abstract class UserFilterOrder extends PlFilterOrder
{
    /** This function must return the tokens to use for ordering
     * @param &$uf The UserFilter whose results must be ordered
     * @return The name of the field to use for ordering results
     */
//    abstract protected function getSortTokens(UserFilter &$uf);
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

    protected function getSortTokens(PlFilter &$uf)
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

    protected function getSortTokens(PlFilter &$uf)
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
    protected function getSortTokens(PlFilter &$uf)
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
class UserFilter extends PlFilter
{
    protected $joinMethods = array();

    protected $joinMetas = array(
                                '$UID' => 'a.uid',
                                );

    private $root;
    private $sort = array();
    private $query = null;
    private $orderby = null;

    private $lastcount = null;

    public function __construct($cond = null, $sort = null)
    {
        if (empty($this->joinMethods)) {
            $class = new ReflectionClass('UserFilter');
            foreach ($class->getMethods() as $method) {
                $name = $method->getName();
                if (substr($name, -5) == 'Joins' && $name != 'buildJoins') {
                    $this->joinMethods[] = $name;
                }
            }
        }
        if (!is_null($cond)) {
            if ($cond instanceof PlFilterCondition) {
                $this->setCondition($cond);
            }
        }
        if (!is_null($sort)) {
            if ($sort instanceof UserFilterOrder) {
                $this->addSort($sort);
            } else if (is_array($sort)) {
                foreach ($sort as $s) {
                    $this->addSort($s);
                }
            }
        }
    }

    private function buildQuery()
    {
        if (is_null($this->orderby)) {
            $orders = array();
            foreach ($this->sort as $sort) {
                $orders = array_merge($orders, $sort->buildSort($this));
            }
            if (count($orders) == 0) {
                $this->orderby = '';
            } else {
                $this->orderby = 'ORDER BY  ' . implode(', ', $orders);
            }
        }
        if (is_null($this->query)) {
            $where = $this->root->buildCondition($this);
            $joins = $this->buildJoins();
            $this->query = 'FROM  account AS a
                               ' . $joins . '
                           WHERE  (' . $where . ')';
        }
    }

    private function getUIDList($uids = null, PlLimit &$limit)
    {
        $this->buildQuery();
        $lim = $limit->getSql();
        $cond = '';
        if (!is_null($uids)) {
            $cond = XDB::format(' AND a.uid IN {?}', $uids);
        }
        $fetched = XDB::fetchColumn('SELECT SQL_CALC_FOUND_ROWS  a.uid
                                    ' . $this->query . $cond . '
                                   GROUP BY  a.uid
                                    ' . $this->orderby . '
                                    ' . $lim);
        $this->lastcount = (int)XDB::fetchOneCell('SELECT FOUND_ROWS()');
        return $fetched;
    }

    private static function defaultLimit($limit) {
        if ($limit == null) {
            return new PlLimit();
        } else {
            return $limit;
        }
    }

    /** Check that the user match the given rule.
     */
    public function checkUser(PlUser &$user)
    {
        $this->buildQuery();
        $count = (int)XDB::fetchOneCell('SELECT  COUNT(*)
                                        ' . $this->query . XDB::format(' AND a.uid = {?}', $user->id()));
        return $count == 1;
    }

    public function getUIDs($limit = null)
    {
        $limit = self::defaultLimit($limit);
        return $this->getUIDList(null, $limit);
    }

    public function getUID($pos = 0)
    {
        $uids =$this->getUIDList(null, new PlFilter(1, $pos));
        if (count($uids) == 0) {
            return null;
        } else {
            return $uids[0];
        }
    }

    public function getUsers($limit = null)
    {
        return User::getBulkUsersWithUIDs($this->getUIDs($limit));
    }

    public function getUser($pos = 0)
    {
        $uid = $this->getUID($pos);
        if ($uid == null) {
            return null;
        } else {
            return User::getWithUID($uid);
        }
    }

    public function get($limit = null)
    {
        return $this->getUsers($limit);
    }

    public function getTotalCount()
    {
        if (is_null($this->lastcount)) {
            $this->buildQuery();
            if ($this->with_accounts) {
                $field = 'a.uid';
            } else {
                $field = 'p.pid';
            }
            return (int)XDB::fetchOneCell('SELECT  COUNT(DISTINCT ' . $field . ')
                                          ' . $this->query);
        } else {
            return $this->lastcount;
        }
    }

    public function setCondition(PlFilterCondition &$cond)
    {
        $this->root =& $cond;
        $this->query = null;
    }

    public function addSort(PlFilterOrder &$sort)
    {
        $this->sort[] = $sort;
        $this->orderby = null;
    }

    static public function sortByName()
    {
        return array(new UFO_Name(Profile::LASTNAME), new UFO_Name(Profile::FIRSTNAME));
    }

    static public function sortByPromo()
    {
        return array(new UFO_Promo(), new UFO_Name(Profile::LASTNAME), new UFO_Name(Profile::FIRSTNAME));
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
        addIpFilter();
        $this->with_ip = true;
        return 'ri';
    }
    
    protected function roomJoins()
    {
        $joins = array();
        if ($this->with_room) {
            $joins['r'] = PlSqlJoin::left('rooms_owners', '$ME.owner_id = a.uid AND $ME.owner_type = "user"');
        }
        return $joins;
    }

    protected function ipJoins()
    {
        $joins = array();
        if ($this->with_ip) {
            $joins['ri'] = PlSqlJoin::left('rooms_ip', '$ME.rid = r.uid');
        }
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

    /** GROUPS and CLUSTERS
     */
    private $with_groups = false;
    private $with_clusters = true;

    public function addGroupFilter()
    {
        $this->with_groups = true;
        return 'ug';
    }

    public function addClusterFilter()
    {
        $this->with_clusters = true;
        return 'uc';
    }

    protected function groupJoins()
    {
        $joins = array();
        if ($this->with_groups) {
            $joins['ug'] = PlSqlJoin::inner('users_groups', '$ME.uid = a.uid');
        }
        return $joins;
    }

    protected function clusterJoins()
    {
        $joins = array();
        if ($this->with_groups) {
            $joins['uc'] = PlSqlJoin::inner('users_clusters', '$ME.uid = a.uid');
        }
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
}
// }}}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
