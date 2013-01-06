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

abstract class FrankizFilterCondition implements PlFilterCondition
{
    public function export()
    {
        throw new Exception('Not implemented');
    }
}

abstract class FrankizFilterOrder extends PlFilterOrder
{
    public function export()
    {
        throw new Exception('Not implemented');
    } 
}


/***********************************
  *********************************
          Frankiz FILTER CLASS
  *********************************
 ***********************************/

abstract class FrankizFilter extends PlFilter
{
    protected $joinMethods = array();

    protected $root = null;
    protected $sort = array();
    protected $query = null;
    protected $orderby = null;

    protected $lastcount = null;

    protected function className() {
        return substr(get_class($this), 0, -6);
    }

    abstract protected function schema();

    public function __construct($cond = null, $sort = null)
    {
        if (is_string($cond)) {
            $export = json_decode($cond, true);

            if (!empty($export['condition'])) {
                $cond = static::importCondition($export['condition']);
            }
            if (!empty($export['sort'])) {
                $sort = static::importSort($export['sort']);
            }
        }

        if (empty($this->joinMethods)) {
            $class = new ReflectionClass(get_class($this));
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
            } else {
                throw new Exception("FrankizFilter's constructor only accept PlFilterCondition or a json string");
            }
        }
        if (!is_null($sort)) {
            if ($sort instanceof PlFilterOrder) {
                $this->addSort($sort);
            } else if (is_array($sort)) {
                foreach ($sort as $s) {
                    $this->addSort($s);
                }
            }
        }
    }

    protected static function defaultLimit($limit) {
        if ($limit == null) {
            return new PlLimit();
        } else {
            return $limit;
        }
    }

    protected function buildQuery()
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

            $schema  = $this->schema();
            $table  = $schema['table'];
            $as     = $schema['as'];

            $this->query = "FROM  $table AS $as
                                  $joins
                           WHERE  ( $where )";
        }
    }

    protected function getIDList($ids = null, PlLimit $limit)
    {
        $schema = $this->schema();
        $as     = $schema['as'];
        $id     = $schema['id'];

        $this->buildQuery();
        $lim = $limit->getSql();
        $cond = '';

        if (!is_null($ids))
            $cond = XDB::format(" AND $as.$id IN {?}", $ids);

        $fetched = XDB::fetchColumn("SELECT  SQL_CALC_FOUND_ROWS $as.$id
                                             $this->query
                                             $cond
                                   GROUP BY  $as.$id
                                             $this->orderby
                                             $lim");

        $this->lastcount = (int) XDB::fetchOneCell('SELECT FOUND_ROWS()');
        return $fetched;
    }

    public function getIDs($limit = null)
    {
        $limit = self::defaultLimit($limit);
        return $this->getIDList(null, $limit);
    }

    public function get($limit = null)
    {
        $className = $this->className();
        if ($limit === true)
        {
            $ids = $this->getIDList(null, new PlLimit(1));
            return (count($ids) != 1) ? false : new $className(array_pop($ids));
        } else {
            $c = new Collection($className);
            return $c->add($this->getIDs($limit));
        }
    }

    public function getTotalCount()
    {
        if (is_null($this->lastcount)) {
            $schema = $this->schema();
            $as     = $schema['as'];
            $id     = $schema['id'];

            $this->buildQuery();
            return (int) XDB::fetchOneCell("SELECT COUNT(DISTINCT $as.$id) $this->query");
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
