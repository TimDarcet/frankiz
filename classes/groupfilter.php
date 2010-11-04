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

abstract class GroupFilterCondition implements PlFilterCondition
{
    public function export()
    {
        throw new Exception('Not implemented');
    }
}

class GFC_Name extends GroupFilterCondition
{
    private $name;

    public function __construct($val)
    {
        $this->name = unflatten($val);
    }

    public function buildCondition(PlFilter &$uf)
    {
        return XDB::format('g.name IN {?}', $this->name);
    }
}

class GFC_Namespace extends GroupFilterCondition
{
    private $ns;

    public function __construct($ns)
    {
        $this->ns = $ns;
    }

    public function buildCondition(PlFilter &$uf)
    {
        return XDB::format('g.ns = {?}', $this->ns);
    }
}

class GFC_Label extends GroupFilterCondition
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

    public function buildCondition(PlFilter &$uf)
    {
        $right = XDB::formatWildcards($this->mode, $this->text);

        return 'g.label' . $right;
    }
}

class GFC_User extends GroupFilterCondition
{
    private $uids;
    private $right;

    public function __construct($us, $right = null)
    {
        $this->right = $right;
        $this->uids  = User::toIds(unflatten($us));
    }

    public function buildCondition(PlFilter &$uf)
    {
        $sub = $uf->addUserFilter();
        if ($this->right === null)
            return XDB::format($sub . '.uid IN {?}', $this->uids);
        else
            return XDB::format("( $sub.uid IN {?} AND FIND_IN_SET({?}, $sub.rights) ", $this->uids, $this->right);
    }
}

abstract class GroupFilterOrder extends PlFilterOrder
{
    public function export()
    {
        throw new Exception('Not implemented');
    } 
}

class GFO_Frequency extends GroupFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter &$gf)
    {
        $sub = $gf->addUserFilter();
        return "COUNT($sub.uid)";
    }
}

class GFO_Name extends GroupFilterOrder
{

    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter &$gf)
    {
        return 'g.name';
    }
}

/***********************************
  *********************************
          GROUP FILTER CLASS
  *********************************
 ***********************************/

class GroupFilter extends PlFilter
{
    protected $joinMethods = array();

    protected $joinMetas = array(
                                '$GID' => 'g.gid',
                                );
    private $root = null;
    private $sort = array();
    public $query = null;
    private $orderby = null;

    private $lastcount = null;

    public function __construct($cond = null, $sort = null)
    {
        if (empty($this->joinMethods)) {
            $class = new ReflectionClass('GroupFilter');
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
            if ($sort instanceof GroupFilterOrder) {
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
            if ($this->root === null)
                $where = '1';
            else
                $where = $this->root->buildCondition($this);
            $joins = $this->buildJoins();
            $this->query = 'FROM  groups AS g
                               ' . $joins . '
                           WHERE  (' . $where . ')';
        }
    }

    private function getGIDList($gids = null, PlLimit &$limit)
    {
        $this->buildQuery();
        $lim = $limit->getSql();
        $cond = '';
        if (!is_null($gids)) {
            $cond = XDB::format(' AND g.gid IN {?}', $gids);
        }
        $fetched = XDB::fetchColumn('SELECT SQL_CALC_FOUND_ROWS  g.gid
                                    ' . $this->query . $cond . '
                                   GROUP BY  g.gid
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

    public function getGIDs($limit = null)
    {
        $limit = self::defaultLimit($limit);
        return $this->getGIDList(null, $limit);
    }

    public function getGID($pos = 0)
    {
        $gids = $this->getGIDList(null, new PlLimit(1, $pos));
        if (count($gids) == 0) {
            return null;
        } else {
            return $gids[0];
        }
    }

    public function getGroup($pos = 0)
    {
        $uid = $this->getGID($pos);
        if ($uid == null) {
            return null;
        } else {
            return new User($uid);
        }
    }

    public function get($limit = null)
    {
        $c = new Collection('Group');
        return $c->add($this->getGIDs($limit));
    }

    public function getTotalCount()
    {
        if (is_null($this->lastcount)) {
            $this->buildQuery();
            return (int)XDB::fetchOneCell('SELECT COUNT(DISTINCT g.gid)' . $this->query);
        } else {
            return $this->lastcount;
        }
    }

    public function hasGroups()
    {
    }

    public function getGroups() 
    {
    }

    public function setCondition(PlFilterCondition &$cond)
    {
        $this->root = $cond;
        $this->query = null;
    }

    public function addSort(PlFilterOrder &$sort)
    {
        $this->sort[] = $sort;
        $this->orderby = null;
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
            $joins['ug'] = PlSqlJoin::left('users_groups', '$ME.gid = g.gid AND FIND_IN_SET("member", $ME.rights)>0');
        }
        return $joins;
    }

    // Temporary
    public function filter(array $objects, $limit = null) {}

    public function export()
    {
        throw new Exception('Not implemented');
    }

}
// }}}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
