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

abstract class NewsFilterCondition implements PlFilterCondition
{
    public function export()
    {
        throw new Exception('Not implemented');
    }
}

/** Filters news based on their writer
 * @param $us A User, a Uid or an array of it
 */
class NFC_Writer extends NewsFilterCondition
{
    private $uids;

    public function __construct($us)
    {
        $this->uids = User::toIds(unflatten($us));
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('n.id IN {?}', $this->uids);
    }
}

/** Filters news based on their origin group
 * @param $gs A Group, a Gid or an array of it
 */
class NFC_Origin extends NewsFilterCondition
{
    private $gids;

    public function __construct($gs)
    {
        $this->gids = Group::toIds(unflatten($gs));
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('n.origin IN {?}', $this->gids);
    }
}

/** Filters news based on their target group
 * @param $gs A Group, a Gid or an array of it
 */
class NFC_Group extends NewsFilterCondition
{
    private $gids;

    public function __construct($gs)
    {
        $this->gids = Group::toIds(unflatten($gs));
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('n.gid IN {?}', $this->gids);
    }
}

class NFC_Title extends NewsFilterCondition
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

    public function buildCondition(PlFilter $uf)
    {
        $right = XDB::formatWildcards($this->mode, $this->text);

        return 'n.title' . $right;
    }
}

/** Returns news that users are allowed to see
 * @param $us     A User, a uid or an array
 * @param $rights The rights the user must have in the targeted group (member by default)
 */
class NFC_User extends NewsFilterCondition
{
    private $uids;
    private $rights;

    public function __construct($us, $rights = 'member')
    {
        $this->uids = User::toIds(unflatten($us));
        $this->rights = $rights;
    }

    public function buildCondition(PlFilter $uf)
    {
        $sub = $uf->addUserFilter();
        return XDB::format("$sub.uid IN {?} AND FIND_IN_SET({?}, $sub.rights) > 0", $this->uids, $this->rights);
    }
}

/** Returns news that are not out-of-date
 */
class NFC_Current extends NewsFilterCondition
{
    public function buildCondition(PlFilter $uf)
    {
        return 'CAST(NOW() AS DATE) BETWEEN n.begin AND n.end';
    }
}

/** Returns news that are private
 */
class NFC_Private extends NewsFilterCondition
{
    private $priv;

    public function __construct($priv = true)
    {
        $this->priv = $priv;
    }

    public function buildCondition(PlFilter $uf)
    {
        return 'n.priv = ' . (int) $this->priv;
    }
}

abstract class NewsFilterOrder extends PlFilterOrder
{
    public function export()
    {
        throw new Exception('Not implemented');
    } 
}

class GFO_Begin extends NewsFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $gf)
    {
        return "n.begin";
    }
}

class GFO_End extends NewsFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $gf)
    {
        return "n.end";
    }
}

/***********************************
  *********************************
          GROUP FILTER CLASS
  *********************************
 ***********************************/

class NewsFilter extends PlFilter
{
    protected $joinMethods = array();

    protected $joinMetas = array(
                                '$ID' => 'n.id',
                                );
    private $root = null;
    private $sort = array();
    private $query = null;
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
            $this->query = 'FROM  news AS n
                               ' . $joins . '
                           WHERE  (' . $where . ')';
        }
    }

    private function getIDList($gids = null, PlLimit $limit)
    {
        $this->buildQuery();
        $lim = $limit->getSql();
        $cond = '';
        if (!is_null($gids)) {
            $cond = XDB::format(' AND n.id IN {?}', $gids);
        }
        $fetched = XDB::fetchColumn('SELECT SQL_CALC_FOUND_ROWS  n.id
                                    ' . $this->query . $cond . '
                                   GROUP BY  n.id
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

    public function getIDs($limit = null)
    {
        $limit = self::defaultLimit($limit);
        return $this->getIDList(null, $limit);
    }

    public function get($limit = null)
    {
        if ($limit === true)
        {
            $ids = $this->getIDList(null, new PlLimit(1));
            return (count($ids) != 1) ? null : new News(array_pop($ids));
        } else {
            $c = new Collection('News');
            return $c->add($this->getIDs($limit));
        }
    }

    public function getTotalCount()
    {
        if (is_null($this->lastcount)) {
            $this->buildQuery();
            return (int)XDB::fetchOneCell('SELECT COUNT(DISTINCT n.id)' . $this->query);
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
            $joins['ug'] = PlSqlJoin::left('users_groups', '$ME.gid = n.gid');
        }
        return $joins;
    }

    // Not implemented
    public function filter(array $objects, $limit = null) {
        throw new Exception('Not implemented');
    }

    public function export()
    {
        throw new Exception('Not implemented');
    }

    public function hasGroups()
    {
        throw new Exception('Not implemented');
    }

    public function getGroups()
    {
        throw new Exception('Not implemented');
    }

}
// }}}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
