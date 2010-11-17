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

abstract class ValidateFilterCondition implements PlFilterCondition
{
    public function export()
    {
        throw new Exception('Not implemented');
    }
}

/** Filters Validate based on the user asking for it
 * @param $user A User, a Uid or an array of it
 */
class VFC_Asker extends ValidateFilterCondition
{
    private $uids;

    public function __construct($us)
    {
        $this->uids = User::toIds(unflatten($us));
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('v.uid IN {?}', $this->uids);
    }
}

/** Filters Validate based on the group validating it
 * @param $gs A Group, a Gid or an array of it
 */
class VFC_Group extends ValidateFilterCondition
{
    private $gids;

    public function __construct($gs)
    {
        $this->gids = Group::toIds(unflatten($gs));
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('v.gid IN {?}', $this->gids);
    }
}

/** Filters Validate based on their types
 * @param $types A type or an array of types
 */
class VFC_Type extends ValidateFilterCondition
{
    private $types;

    public function __construct($types)
    {
        $this->types = unflatten($types);
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('v.type IN {?}', $this->types);
    }
}

/** Returns Validates that users are allowed to see because they
 * are admin of the targeted groups
 * @param $us     A User, a uid or an array
 */
class VFC_User extends NewsFilterCondition
{
    private $uids;

    public function __construct($us)
    {
        $this->uids = User::toIds(unflatten($us));
    }

    public function buildCondition(PlFilter $uf)
    {
        $sub = $uf->addUserFilter();
        return XDB::format("$sub.uid IN {?} AND FIND_IN_SET('admin', $sub.rights) > 0", $this->uids);
    }
}

abstract class ValidateFilterOrder extends PlFilterOrder
{
    public function export()
    {
        throw new Exception('Not implemented');
    } 
}

class VFO_Created extends ValidateFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $gf)
    {
        return "v.created";
    }
}

/***********************************
  *********************************
        VALIDATE FILTER CLASS
  *********************************
 ***********************************/

class ValidateFilter extends PlFilter
{
    protected $joinMethods = array();

    protected $joinMetas = array(
                                '$VID' => 'v.id',
                                );
    private $root = null;
    private $sort = array();
    private $query = null;
    private $orderby = null;

    private $lastcount = null;

    public function __construct($cond = null, $sort = null)
    {
        if (empty($this->joinMethods)) {
            $class = new ReflectionClass('ValidateFilter');
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
            if ($sort instanceof ValidateFilterOrder) {
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
            $this->query = 'FROM  validate AS v
                               ' . $joins . '
                           WHERE  (' . $where . ')';
        }
    }

    private function getIDList($ids = null, PlLimit $limit)
    {
        $this->buildQuery();
        $lim = $limit->getSql();
        $cond = '';
        if (!is_null($ids)) {
            $cond = XDB::format(' AND v.id IN {?}', $ids);
        }
        $fetched = XDB::fetchColumn('SELECT SQL_CALC_FOUND_ROWS  v.id
                                    ' . $this->query . $cond . '
                                   GROUP BY  v.id
                                    ' . $this->orderby . '
                                    ' . $lim);
        $this->lastcount = (int) XDB::fetchOneCell('SELECT FOUND_ROWS()');
        return $fetched;
    }

    private static function defaultLimit($limit) {
        if ($limit == null) {
            return new PlLimit();
        } else {
            return $limit;
        }
    }

    public function getIDs($limit = null)
    {
        $limit = self::defaultLimit($limit);
        return $this->getIDList(null, $limit);
    }

    public function get($limit = null)
    {
        $c = new Collection('Validate');
        return $c->add($this->getIDs($limit));
    }

    public function getTotalCount()
    {
        if (is_null($this->lastcount)) {
            $this->buildQuery();
            return (int)XDB::fetchOneCell('SELECT COUNT(DISTINCT v.id)' . $this->query);
        } else {
            return $this->lastcount;
        }
    }

    public function setCondition(PlFilterCondition $cond)
    {
        $this->root = $cond;
        $this->query = null;
    }

    public function addSort(PlFilterOrder $sort)
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
            $joins['ug'] = PlSqlJoin::left('users_groups', '$ME.gid = v.gid');
        }
        return $joins;
    }

    // Not implemented
    public function hasGroups()
    {
        throw new Exception('Not implemented');
    }

    public function getGroups() 
    {
        throw new Exception('Not implemented');
    }

    public function filter(array $objects, $limit = null) {
        throw new Exception('Not implemented');
    }

    public function export()
    {
        throw new Exception('Not implemented');
    }

}
// }}}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
