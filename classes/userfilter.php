<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                        *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                      *
 *                                                                         *
 *  Copyright (C) 2003-2009 Polytechnique.org                              *
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

/******
 * Doc
 ******/
/* For joins, $ME is the key and $UID the user id (as in "INNER JOIN blah AS b ON ($ME.coin = $UID)" == "INNER JOIN blah AS b ON (b.coin = a.user_id))"
 * UFC stands for "UserFilterCondition", UFO for "UserFilterOrder"
 *
 * A UFC can use addEducationFilter or other such methods to notify UserFilter that education-related joins will be needed.
 *  This method returns a suffix which can be used to identify the version of the "edu" table which has been joined here
 *  (this functionality isn't used for the moment)
 *
 * In the query, short names are used :
 *  a       stands for  account
 *  edu     stands for  studies (left join on users)
 *  eduf    stands for  formations (left join on studies)
 */


/******************
 * CONDITIONS
 ******************/

// Those objects are used to set conditions to match users
interface UserFilterCondition
{
    const COND_TRUE  = 'TRUE';
    const COND_FALSE = 'FALSE';

    /** Check that the given user matches the rule.
     */
    public function buildCondition(UserFilter &$uf);
}

// Condition with one child (Not, ...)
abstract class UFC_OneChild implements UserFilterCondition
{
    protected $child;

    public function __construct($child = null)
    {
        if (!is_null($child) && ($child instanceof UserFilterCondition)) {
            $this->setChild($child);
        }
    }

    public function setChild(UserFilterCondition &$cond)
    {
        $this->child =& $cond;
    }
}

// Condition with several children (And, Or, ...)
abstract class UFC_NChildren implements UserFilterCondition
{
    protected $children = array();

    public function __construct()
    {
        $children = func_get_args();
        foreach ($children as &$child) {
            if (!is_null($child) && ($child instanceof UserFilterCondition)) {
                $this->addChild($child);
            }
        }
    }

    public function addChild(UserFilterCondition &$cond)
    {
        $this->children[] =& $cond;
    }

    protected function catConds(array $cond, $op, $fallback)
    {
        if (count($cond) == 0) {
            return $fallback;
        } else if (count($cond) == 1) {
            return $cond[0];
        } else {
            return '(' . implode(') ' . $op . ' (', $cond) . ')';
        }
    }
}

// True and False
class UFC_True implements UserFilterCondition
{
    public function buildCondition(UserFilter &$uf)
    {
        return self::COND_TRUE;
    }
}

class UFC_False implements UserFilterCondition
{
    public function buildCondition(UserFilter &$uf)
    {
        return self::COND_FALSE;
    }
}

// Not, And, Or
class UFC_Not extends UFC_OneChild
{
    public function buildCondition(UserFilter &$uf)
    {
        $val = $this->child->buildCondition($uf);
        if ($val == self::COND_TRUE) {
            return self::COND_FALSE;
        } else if ($val == self::COND_FALSE) {
            return self::COND_TRUE;
        } else {
            return 'NOT (' . $val . ')';
        }
    }
}

class UFC_And extends UFC_NChildren
{
    public function buildCondition(UserFilter &$uf)
    {
        if (empty($this->children)) {
            return self::COND_FALSE;
        } else {
            $true = self::COND_FALSE;
            $conds = array();
            foreach ($this->children as &$child) {
                $val = $child->buildCondition($uf);
                if ($val == self::COND_TRUE) {
                    $true = self::COND_TRUE;
                } else if ($val == self::COND_FALSE) {
                    return self::COND_FALSE;
                } else {
                    $conds[] = $val;
                }
            }
            return $this->catConds($conds, 'AND', $true);
        }
    }
}

class UFC_Or extends UFC_NChildren
{
    public function buildCondition(UserFilter &$uf)
    {
        if (empty($this->children)) {
            return self::COND_TRUE;
        } else {
            $true = self::COND_TRUE;
            $conds = array();
            foreach ($this->children as &$child) {
                $val = $child->buildCondition($uf);
                if ($val == self::COND_TRUE) {
                    return self::COND_TRUE;
                } else if ($val == self::COND_FALSE) {
                    $true = self::COND_FALSE;
                } else {
                    $conds[] = $val;
                }
            }
            return $this->catConds($conds, 'OR', $true);
        }
    }
}

// Frankiz-specific conditions (promo, formation, IP, casert, ...)

class UFC_Promo implements UserFilterCondition
{
    private $study;
    private $promo;
    private $comparison;

    public function __construct($comparison, $study, $promo)
    {
        // If $study is DISPLAY, match on displayed promo
        // Otherwise, match on promo of specified study (X, SUPOP, PHD, ...)
        $this->study = $study;
        $this->comparison = $comparison;
        $this->promo = $promo;
        // Check that 'study' is valid
        UserFilter::assertStudy($this->study);
    }

    public function buildCondition(UserFilter &$uf)
    {
        // Match on specific promo : we tell the userfilter that we want study info
        $sub = $uf->addEducationFilter();
        // So we know we can match on fields of versions $sub of edu table
        // promoYear returns the name of the field containing the right promo
        $promo_field = 'edu' . $sub . '.' . UserFilter::promoYear($this->study);
        $formation_field = 'eduf' . $sub . '.abbrev';
        $where = $formation_field . ' IS NOT NULL AND ' .
            $formation_field .' = ' . XDB::format('{?}', $this->study) . ' AND ' .
            $promo_field . ' ' . $this->comparison . ' ' . XDB::format('{?}', $this->promo);

        return $where;
    }
}

class UFC_Name implements UserFilterCondition
{
    // MODE_PREFIX for prefix search, MODE_SUFFIX for suffix search, MODE_CONTAINS = MODE_PREFIX & MODE_SUFFIX for both
    const MODE_PREFIX    = 1;
    const MODE_SUFFIX    = 2;
    const MODE_CONTAINS  = 3;

    // type is the type of name, to choose from User::$name_fields
    private $type;
    private $text;
    private $mode;

    public function __construct($type, $text, $mode)
    {
        $this->type = $type;
        $this->text = $text;
        $this->mode = $mode;
    }

    public function buildCondition(UserFilter &$uf)
    {
        $op   = ' LIKE ';
        if (($this->mode & self::MODE_CONTAINS) == 0) {
            $right = XDB::format('{?}', $this->text);
            $op    = ' = ';
        } else if (($this->mode & self::MODE_CONTAINS) == self::MODE_PREFIX) {
            $right = XDB::format('CONCAT({?}, \'%\')', $this->text);
        } else if (($this->mode & self::MODE_CONTAINS) == self::MODE_SUFFIX) {
            $right = XDB::format('CONCAT(\'%\', {?})', $this->text);
        } else {
            $right = XDB::format('CONCAT(\'%\', {?}, \'%\')', $this->text);
        }
        $cond = $op . $right;
        $types = User::getNameVariants($this->type);
        $conds = array();
        foreach($types as $ty) {
            $conds[] = UserFilter::getNameField($ty) . $cond;
        }
        return implode(' OR ', $conds);
    }
}

class UFC_Registered implements UserFilterCondition
{
    private $active;

    public function __construct($active = false)
    {
        $this->only_active = $active;
    }

    public function buildCondition(UserFilter &$uf)
    {
        if ($this->only_active) {
            $cond = 'a.uid IS NOT NULL AND a.state = \'active\'';
        } else {
            $cond = 'a.uid IS NOT NULL AND a.state != \'unregistered\'';
        }
        return $cond;
    }
}

class UFC_Birthday implements UserFilterCondition
{
    private $comparison;
    private $date;

    public function __construct($comparison = null, $date = null)
    {
        $this->comparison = $comparison;
        $this->date = $date;
    }

    public function buildCondition(UserFilter &$uf)
    {
        return 'a.next_birthday ' . $this->comparison . XDB::format(' {?}', date('Y-m-d', $this->date));
    }
}

class UFC_Sex implements UserFilterCondition
{
    private $sex;
    public function __construct($sex)
    {
        $this->sex = $sex;
    }

    public function buildCondition(UserFilter &$uf)
    {
        if ($this->sex != User::GENDER_MALE && $this->sex != User::GENDER_FEMALE) {
            return self::COND_FALSE;
        } else {
            return XDB::format('a.gender = {?}', $this->sex == User::GENDER_FEMALE ? 'woman' : 'man');
        }
    }
}


class UFC_Email implements UserFilterCondition
{
    private $email;
    public function __construct($email)
    {
        $this->email = $email;
    }

    public function buildCondition(UserFilter &$uf)
    {
        if (User::isForeignEmailAddress($this->email)) {
            return XDB::format('a.email IS NOT NULL AND a.email = {?}', $this->email);
        } else {
            // Email is a "formation" email, check for domain in formations and for user in studies
            @list($user, $domain) = explode('@', $this->email);
            $sub = $uf->addEducationFilter();
            return XDB::format('edu' . $sub . '.forlife = {?} AND eduf' . $sub . '.domain = {?}', $user, $domain);
        }
    }
}


/******************
 * ORDERS
 ******************/

// Those classes implements orders on users (by promo, ...)
abstract class UserFilterOrder
{
    protected $desc = false;
    public function __construct($desc = false)
    {
        $this->desc = $desc;
    }

    public function buildSort(UserFilter &$uf)
    {
        $sel = $this->getSortTokens($uf);
        if (!is_array($sel)) {
            $sel = array($sel);
        }
        if ($this->desc) {
            foreach ($sel as $k=>$s) {
                $sel[$k] = $s . ' DESC';
            }
        }
        return $sel;
    }

    abstract protected function getSortTokens(UserFilter &$uf);
}

class UFO_Promo extends UserFilterOrder
{
    private $study;

    public function __construct($study = null, $desc = false)
    {
        parent::__construct($desc);
        $this->study = $study;
    }

    protected function getSortTokens(UserFilter &$uf)
    {
        $sub = $uf->addEducationFilter();
        if (UserFilter::isValidStudy($this->study)) {
            return 'edu' . $sub . '.' . UserFilter::promoYear($this->study);
        } else {
            return 'edu' . $sub . '.' . UserFilter::promoYear(UserFilter::STUDY_X_INGE);
        }
    }
}

class UFO_Name extends UserFilterOrder
{
    private $type;

    public function __construct($type, $desc = false)
    {
        parent::__construct($desc);
        $this->type = $type;
    }

    protected function getSortTokens(UserFilter &$uf)
    {
        if ($field = UserFilter::getNameField($this->type)) {
            return 'a.' . $field;
        } else {
            return 'a.' . UserFilter::getNameField(User::LAST_NAME);
        }
    }
}

class UFO_Birthday extends UserFilterOrder
{
    protected function getSortTokens(UserFilter &$uf)
    {
        return 'a.next_birthday';
    }
}

/***********************************
  *********************************
          USER FILTER CLASS
  *********************************
 ***********************************/

class UserFilter
{
    static private $joinMethods = array();

    private $root;
    private $sort = array();
    private $query = null;
    private $orderby = null;

    private $lastcount = null;

    public function __construct($cond = null, $sort = null)
    {
        // Dynamically load joinMethods from class names
        if (empty(self::$joinMethods)) {
            $class = new ReflectionClass('UserFilter');
            foreach ($class->getMethods() as $method) {
                $name = $method->getName();
                if (substr($name, -5) == 'Joins' && $name != 'buildJoins') {
                    self::$joinMethods[] = $name;
                }
            }
        }
        if (!is_null($cond)) {
            if ($cond instanceof UserFilterCondition) {
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

    private function formatJoin(array $joins)
    {
        $str = '';
        foreach ($joins as $key => $infos) {
            $mode  = $infos[0];
            $table = $infos[1];
            if ($mode == 'inner') {
                $str .= 'INNER JOIN ';
            } else if ($mode == 'left') {
                $str .= 'LEFT JOIN ';
            } else {
                Platal::page()->kill("Join mode error");
            }
            $str .= $table . ' AS ' . $key;
            if (isset($infos[2])) {
                $str .= ' ON (' . str_replace(array('$ME', '$UID'), array($key, 'a.uid'), $infos[2]) . ')';
            }
            $str .= "\n";
        }
        return $str;
    }

    private function buildJoins()
    {
        $joins = array();
        foreach (self::$joinMethods as $method) {
            $joins = array_merge($joins, $this->$method());
        }
        return $this->formatJoin($joins);
    }

    private function getUIDList($uids = null, $count = null, $offset = null)
    {
        $this->buildQuery();
        $limit = '';
        if (!is_null($count)) {
            if (!is_null($offset)) {
                $limit = XDB::format('LIMIT {?}, {?}', (int)$offset, (int)$count);
            } else {
                $limit = XDB::format('LIMIT {?}', (int)$count);
            }
        }
        $cond = '';
        if (!is_null($uids)) {
            $cond = ' AND a.uid IN ' . XDB::formatArray($uids);
        }
        $fetched = XDB::fetchColumn('SELECT SQL_CALC_FOUND_ROWS  a.uid
                                    ' . $this->query . $cond . '
                                   GROUP BY  a.uid
                                    ' . $this->orderby . '
                                    ' . $limit);
        $this->lastcount = (int)XDB::fetchOneCell('SELECT FOUND_ROWS()');
        return $fetched;
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

    /** Filter a list of user to extract the users matching the rule.
     */
    public function filter(array $users, $count = null, $offset = null)
    {
        $this->buildQuery();
        $table = array();
        $uids  = array();
        foreach ($users as $user) {
            if ($user instanceof PlUser) {
                $uid = $user->id();
            } else {
                $uid = $user;
            }
            $uids[] = $uid;
            $table[$uid] = $user;
        }
        $fetched = $this->getUIDList($uids, $count, $offset);
        $output = array();
        foreach ($fetched as $uid) {
            $output[] = $table[$uid];
        }
        return $output;
    }

    public function getUIDs($count = null, $offset = null)
    {
        return $this->getUIDList(null, $count, $offset);
    }

    public function getUsers($count = null, $offset = null)
    {
        return User::getBulkUsersWithUIDs($this->getUIDs($count, $offset));
    }

    public function getTotalCount()
    {
        if (is_null($this->lastcount)) {
            $this->buildQuery();
            return (int)XDB::fetchOneCell('SELECT  COUNT(DISTINCT a.uid)
                                          ' . $this->query);
        } else {
            return $this->lastcount;
        }
    }

    public function setCondition(UserFilterCondition &$cond)
    {
        $this->root =& $cond;
        $this->query = null;
    }

    public function addSort(UserFilterOrder &$sort)
    {
        $this->sort[] = $sort;
        $this->orderby = null;
    }

    static public function sortByName()
    {
        return array(new UFO_Name(User::LAST_NAME), new UFO_Name(User::FIRST_NAME));
    }

    static public function sortByPromo()
    {
        return array(new UFO_Promo(), new UFO_Name(User::LAST_NAME), new UFO_Name(User::FIRST_NAME));
    }

    // Returns a stripped version of $string for use in a MySQL query
    static private function getDBSuffix($string)
    {
        return preg_replace('/[^a-z0-9]/i', '', $string);
    }

    // Adds an optional field to output of the query (for $val = 'val', will return '_val')
    // The generated name will be added to &$table in order to be able to access it when joining
    private $option = 0;
    private function register_optional(array &$table, $val)
    {
        if (is_null($val)) {
            $sub   = $this->option++;
            $index = null;
        } else {
            $sub   = self::getDBSuffix($val);
            $index = $val;
        }
        $sub = '_' . $sub;
        $table[$sub] = $index;
        return $sub;
    }

    /** NAMES
     */
    static private $name_fields = array(
        User::FIRST_NAME    => 'firstname',
        User::LAST_NAME     => 'lastname',
        User::NICK_NAME     => 'nickname',
    );
    static public function getNameField($name)
    {
        if (array_key_exists($name, self::$name_fields)) {
            return self::$name_fields[$name];
        }
        return false;
    }

    static public function assertName($name)
    {
        if (!self::getNameField($name)) {
            Platal::page()->kill('Invalid name type');
        }
    }

    static public function isDisplayName($name)
    {
        return $name == self::DN_FULL || $name == self::DN_YOURSELF;
    }

    /** EDUCATION
     */

    // const STUDY_* must reflect data in "formations" DB
    const STUDY_X_INGE  = 'X';
    const STUDY_MASTER  = 'MASTER';
    const STUDY_PHD     = 'DOC';
    const STUDY_SUPOP   = 'SUPOP';

    static public function isValidStudy($study)
    {
        return $study == self::STUDY_X_INGE || $study == self::STUDY_MASTER || $study == self::STUDY_PHD || $study == self::STUDY_SUPOP;
    }

    static public function assertStudy($study)
    {
        if (!self::isValidStudy($study)) {
            Platal::page()->killError("Formation non valide");
        }
    }

    static public function promoYear($study)
    {
        // Defines which year of study is called "promo"
        return ($study == UserFilter::STUDY_X_INGE) ? 'year_in' : 'year_out';
    }

    // Whether we want the list of studies per user
    private $with_edu = false;
    public function addEducationFilter()
    {
        $this->with_edu = true;
        // There are no special joins here.
        $sub = '';
        return $sub;
    }

    private function educationJoins()
    {
        $joins = array();
        if ($this->with_edu) {
            // Associate the list of valid studies per user
            $joins['edu'] = array('left', 'studies', '$ME.uid = $UID');
            $joins['eduf'] = array('left', 'formations', '$ME.formation_id = edu.formation_id');
        }
        return $joins;
    }
}
// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
