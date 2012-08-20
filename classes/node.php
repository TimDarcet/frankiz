<?php
/***************************************************************************
 *  Copyright (C) 2004-2012 Binet Réseau                                   *
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

abstract class Node
{
    protected $id = null;
    protected $root_id = null;
    protected $content_id = null;

    protected $L = null;
    protected $R = null;
    protected $depth = null;

    protected $children = array();
    protected $father = null;

    const MAX_DEPTH = 1;

    const SELECT_CHILDREN = 0x04;
    const SELECT_FATHERS  = 0x08;

    public function __construct($datas)
    {
        if (!is_array($datas))
            $this->id = $datas;
        else
            $this->fillFromArray($datas);
    }
/*
    public function __clone() {
//        $this->unlink();
    }
*//*
    static public function table()
    {
        throw new Exception('Not implemented');
    }
*//*
    static public function idName()
    {
        throw new Exception('Not implemented');
    }
*/
    public static function root()
    {
/*        $id = static::idName();
        $ta = static::table();
        $res = XDB::query("SELECT  $id AS id, L, R, depth
                             FROM  $ta
                            WHERE  (R - L + 1) / 2 = (SELECT COUNT(*) FROM $ta)");
        return new static($res->fetchOneAssoc());*/
        return $this->root_id;
    }

    public function fillFromArray(array $values)
    {
        foreach ($values as $key => $value)
            if (property_exists($this, $key))
                $this->$key = $value;
    }

    public function id()
    {
        return $this->id;
    }
/*
    protected function hasChildren()
    {
        return ($this->L != null) && ($this->L != null) &&($this->L + 1 != $this->R);
    }

    public function children()
    {
        $c = new Collection('Node');
        return $c->add($this->children);
    }

    static protected function _sort($a, $b)
    {
        $a = intval($a->L());
        $b = intval($b->L());
        if ($a == $b)
            return 0;
        return ($a < $b) ? -1 : 1;
    }

    public function sortedChildren()
    {
        $sorted_children = $this->children;
        usort($sorted_children, array('self', '_sort'));
        return $sorted_children;
    }

    public function father()
    {
        return $this->father;
    }

    protected function L()
    {
        return $this->L;
    }

    protected function R()
    {
        return $this->R;
    }

    protected function depth()
    {
        return $this->depth;
    }

    protected function isFatherOf($n)
    {
        return ($this->L() < $n->L()) && ($this->R() > $n->R()) && ($this->depth() + 1 == $n->depth());
    }

    public function unlink()
    {
        $this->children = array();
        $this->father = null;
    }
 */
    /**
    * Try to find and build the family links with the nodes passed as a paramater
    *
    * @param $gs is an array of Nodes
    */
/*    protected function _buildLinks(array $nodes)
    {
        foreach ($nodes as $n) {
            if ($this->isFatherOf($n)) {
                $this->children[$n->id] = $n;
                $n->father = $this;
            } else if ($n->isFatherOf($this)) {
                $n->children[$this->id] = $this;
                $this->father = $n;
            }
        }
    }*/

    /**
    * Try to find and build the family links within the nodes passed as a paramater
    *
    * @param $toBeConsummed is an array of Nodes
    */
    /*static public function batchBuildLinks(array $toBeConsummed)
    {
        while (($n = array_pop($toBeConsummed)) != null)
            $n->_buildLinks($toBeConsummed);
    }

    protected function _root()
    {
        return ($this->father === null) ? $this : $this->father->_root();
    }

    public function leaves()
    {
        $leaves = new Collection('Node');
        if (empty($this->children))
            return $leaves->add($this);

        foreach ($this->children as $c)
            $leaves->merge($c->leaves());

        return $leaves;
    }

    public static function batchLeaves(array $nodes)
    {
        $leaves = new Collection('Node');
        foreach ($nodes as $n)
            $leaves->merge($n->leaves());

        return $leaves;
    }

    public static function batchChildren(array $nodes)
    {
        $children = array();
        foreach ($nodes as $n)
            $children = $children + $n->children;

        return $children;
    }

    public static function batchRoots(array $nodes)
    {
        $roots = new Collection('Node');
        foreach ($nodes as $n)
            $roots->add($n->_root());

        return $roots;
    }

    protected function cloneIfFathersOf($nodes)
    {
        foreach ($nodes as $id => $n) {
            if (($this->L() <= $n->L()) && ($this->R() >= $n->R())) {
                $fathers = array($this->id() => clone $this);
                foreach ($this->children as $c)
                    $fathers = array_merge($fathers, $c->cloneIfFathersOf($nodes));
                return $fathers;
            }
        }
        return array();
    }

    public static function batchFathersOf(array $roots, $nodes)
    {
        $fathers = array();

        foreach ($roots as $r)
            $fathers = array_merge($fathers, $r->cloneIfFathersOf($nodes));

        self::batchBuildLinks($fathers);
        return self::batchRoots($fathers);
    }

    protected function cloneChildren()
    {
        $children = array($this->id() => clone $this);
        foreach ($this->children as $c)
            $children = array_merge($children, $c->cloneChildren());
        return $children;
    }

    protected function isChildrenOf($nodes)
    {
        foreach ($nodes as $n)
            if (($this->L() >= $n->L()) && ($this->R() <= $n->R()))
                return true;
        return false;
    }

    protected function cloneIfChildrenOf($nodes)
    {
        if ($this->isChildrenOf($nodes))
            return $this->cloneChildren();

        $children = array();
        foreach ($this->children as $c)
            $children = array_merge($children, $c->cloneIfChildrenOf($nodes));

        return $children;
    }

    public static function batchChildrenOf(array $roots, $nodes)
    {
        $children = array();
        foreach ($roots as $r)
            $children = array_merge($children, $r->cloneIfChildrenOf($nodes));

        self::batchBuildLinks($children);
        return self::batchRoots($children);
    }*/

    public static function toIds(array $nodes)
    {
        $result = array();
        foreach ($nodes as $n)
            if ($n instanceof Node)
                $result[] = $n->id;
            else
                $result[] = $n;
        return $result;
    }

    public function ids()
    {
        $ids = array($this->id());
        foreach ($this->children as $c)
            $ids = array_merge($ids, $c->ids());

        return $ids;
    }

    public function select($fields)
    {
        static::batchSelect(array($this), $fields);
        return $this;
    }

    public function isMe($other)
    {
        if ($other instanceof $this)
            return $other->id() == $this->id();
        else if (isId($other))
            return $other == $this->id();
        else
            return null;
    }

    protected function flatten()
    {
        $nodes = $this->children;
        foreach ($this->children as $c)
            $nodes = $nodes + $c->flatten();

        return $nodes;
    }

    public static function batchFlatten(array $nodes, $unlink = true)
    {
        $flattened = array();
        foreach ($nodes as $n) {
            $flattened = $flattened + $n->flatten();
            $flattened[$n->id()] = $n;
        }

        if ($unlink)
            foreach ($flattened as $n)
                $n->unlink();

        return $flattened;
    }

    protected static function iterToNodes($iter, $nodes)
    {
        $fetched = array();
        while ($node = $iter->next()) {
            if (isset($nodes[$node['id']])) {
                $fetched[$node['id']] = $nodes[$node['id']];
                $fetched[$node['id']]->fillFromArray($node);
            } else {
                $fetched[$node['id']] = new static($node);
            }
        }
        return $fetched;
    }

    public static function batchSelect(array $nodes, $fields)
    {
        $bits = 0;
        if (is_array($fields))
            foreach($fields as $bit => $args)
                $bits |= $bit;
        else
            $bits = $fields;

        // Index the array
        $nodes = array_combine(self::toIds($nodes), $nodes);
        $fetched = array();

        // TODO : merge requests below
        if ($bits & self::SELECT_CHILDREN)
        {
            $depth = (isset($fields[self::SELECT_CHILDREN])) ? $fields[self::SELECT_CHILDREN] : 1;
            $id = static::idName();
            $ta = static::table();

            $iter = XDB::iterator("SELECT  n.$id AS id, n.L, n.R, n.depth
                                     FROM  $ta AS n
                               INNER JOIN  $ta AS current ON current.$id IN {?}
                                    WHERE       n.L >= current.L
                                           AND  n.R <= current.R
                                           AND  n.depth <= current.depth + {?}
                                 GROUP BY  n.$id",
                                           array_keys($nodes), $depth);
            $fetched = $fetched + self::iterToNodes($iter, $nodes);
        }

        if ($bits & self::SELECT_FATHERS)
        {
            $depth = (isset($fields[self::SELECT_FATHERS])) ? $fields[self::SELECT_FATHERS] : 1;
            $id = static::idName();
            $ta = static::table();

            $iter = XDB::iterator("SELECT  n.$id AS id, n.L, n.R, n.depth
                                     FROM  $ta AS n
                               INNER JOIN  $ta AS current ON current.$id IN {?}
                                    WHERE       n.L <= current.L
                                           AND  n.R >= current.R
                                           AND  n.depth >= current.depth - {?}
                                 GROUP BY  n.$id",
                                           array_keys($nodes), $depth);
            $fetched = $fetched + self::iterToNodes($iter, $nodes);
        }

        self::batchBuildLinks($fetched);
    }

    /**
    * Remove the node and its sub-tree from the Db
    * Require a stored procedure called {table}_remove()
    * Example with a table called group :
        CREATE PROCEDURE groups_remove(IN g_gid INT)
        BEGIN
            DECLARE g_R INT DEFAULT NULL;
            DECLARE g_L INT DEFAULT NULL;
            DECLARE delta INT DEFAULT NULL;

            START TRANSACTION;
                SELECT R, L INTO g_R, g_L FROM groups WHERE gid = g_gid;
                IF !ISNULL(g_R) THEN
                    SET delta = g_R - g_L + 1;
                    DELETE FROM groups WHERE  L >= g_L AND R <= g_R;
                    UPDATE groups SET L = L - delta WHERE L >= g_L;
                    UPDATE groups SET  R = R - delta WHERE R >= g_L;
                END IF;
            COMMIT;
        END|
    *
    * @param $parent parent node
    */
    public function delete()
    {
        if ($this->id == null)
            throw new Exception("This node doesn't exist");

        $table = static::table();
        XDB::execute('CALL '.$table.'_delete({?})', $this->id);
    }

    /**
    * Insert a node last in the Db
    * Require a stored procedure called {table}_insert()
    * Example with a table called group :
        CREATE PROCEDURE groups_insert(IN parent_gid INT, OUT new_id INT)
        BEGIN
            DECLARE parent_R INT DEFAULT NULL;
            DECLARE parent_depth INT DEFAULT NULL;

            START TRANSACTION;
                SELECT R, depth INTO parent_R, parent_depth FROM groups WHERE gid = parent_gid;

                IF ISNULL(parent_R) THEN
                    SET new_id = NULL;
                ELSE
                    UPDATE groups SET R = R + 2 WHERE R >= parent_R;
                    UPDATE groups SET L = L + 2 WHERE L >= parent_R;
                    INSERT INTO groups SET L = parent_R, R = (parent_R + 1), depth = (parent_depth + 1);
                    SELECT LAST_INSERT_ID() INTO new_id;
                END IF;
            COMMIT;
        END|
    *
    * @param $parent parent node
    */
    public function insert(Node $parent)
    {
        if ($this->id != null)
            throw new Exception('This node already exists');

        $table = static::table();
        XDB::execute('CALL '.$table.'_insert({?}, @new_id)', $parent->id());

        $this->id = XDB::query('SELECT @new_id')->fetchOneCell();
    }

    /**
    * Move the node and its sub-tree in the Db
    * Require a stored procedure called {table}_moveUnder()
    * Example with a table called group :
        CREATE PROCEDURE groups_moveUnder(IN g_gid INT, IN p_gid INT)
        BEGIN
            DECLARE g_ns ENUM('temp','group') DEFAULT NULL;
            DECLARE g_R INT DEFAULT NULL;
            DECLARE g_L INT DEFAULT NULL;
            DECLARE g_depth INT DEFAULT NULL;
            DECLARE p_R INT DEFAULT NULL;
            DECLARE p_depth INT DEFAULT NULL;
            DECLARE delta INT DEFAULT NULL;
            DECLARE zone_min INT DEFAULT NULL;
            DECLARE zone_max INT DEFAULT NULL;
            DECLARE zone_sign INT DEFAULT NULL;
            DECLARE shift INT DEFAULT NULL;

            START TRANSACTION;
                SELECT ns, R, L, depth INTO g_ns, g_R, g_L, g_depth FROM groups WHERE gid = g_gid;
                SELECT        L, depth INTO p_R, p_depth            FROM groups WHERE gid = p_gid;

                IF !ISNULL(g_R) AND !ISNULL(p_R) THEN
                    SET delta = g_R - g_L + 1;
                    SET zone_min  = LEAST(g_R, p_R);
                    SET zone_max  = GREATEST(g_R, p_R);
                    SET zone_sign = SIGN(g_R - p_R);
                    SET shift = p_R - g_R + IF(zone_sign = 1, delta, 0);

                    UPDATE groups SET ns = 'temp' WHERE ns = g_ns AND L >= g_L AND R <= g_R;
                    UPDATE groups SET L = L + (zone_sign * delta) WHERE ns = g_ns AND L >  zone_min AND L <= zone_max;
                    UPDATE groups SET R = R + (zone_sign * delta) WHERE ns = g_ns AND R >= zone_min AND R <  zone_max;
                    UPDATE groups SET R = R + shift, L = L + shift, depth = depth + (p_depth - g_depth + 1), ns = g_ns WHERE ns = 'temp';
                END IF;
            COMMIT;
        END|
    *
    * @param $parent parent node
    */
    public function moveUnder(Node $parent)
    {
        if ($this->id == null || $parent->id == null)
            throw new Exception("This node doesn't exist");

        $table = static::table();
        XDB::execute('CALL '.$table.'_moveUnder({?}, {?})', $this->id(), $parent->id());
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
