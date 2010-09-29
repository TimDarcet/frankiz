<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                       *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                     *
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

    static public function table()
    {
        throw new Exception('Not implemented');
    }

    static public function idName()
    {
        throw new Exception('Not implemented');
    }

    public static function root()
    {
        $id = static::idName();
        $ta = static::table();
        $res = XDB::query("SELECT  $id AS id, L, R, depth
                             FROM  $ta
                            WHERE  (R - L + 1) / 2 = (SELECT COUNT(*) FROM $ta)");
        return new static($res->fetchOneAssoc());
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

    protected function hasChildren()
    {
        return ($this->L != null) && ($this->L != null) &&($this->L + 1 != $this->R);
    }

    public function children()
    {
        $c = new Collection();
        return $c->add($this->children);;
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

    /**
    * Try to find and build the family links with the nodes passed as a paramater
    *
    * @param $gs is an array of Nodes
    */
    protected function _buildLinks(array $nodes)
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
    }

    /**
    * Try to find and build the family links within the nodes passed as a paramater
    *
    * @param $toBeConsummed is an array of Nodes
    */
    static public function buildLinks(array $toBeConsummed)
    {
        while (($n = array_pop($toBeConsummed)) != null)
            $n->_buildLinks($toBeConsummed);
    }

    protected function _root()
    {
        return ($this->father === null) ? $this : $this->father->_root();
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
        $roots = array();
        foreach ($nodes as $n) {
            $root = $n->_root();
            $roots[$root->gid()] = $root;
        }
        return $roots;
    }

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

    public static function fromIds(array $ids)
    {
        $nodes = array();
        foreach ($ids as $id)
            $nodes[] = new Group($id);
        return $nodes;
    }

    public function select($fields)
    {
        static::batchSelect(array($this), $fields);
        return $this;
    }

    protected function flattenedChildren()
    {
        $nodes = $this->children;
        foreach ($this->children as $c)
            $nodes = $nodes + $c->flattenedChildren();

        return $nodes;
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
                                           AND  n.depth <= current.depth + {?}",
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
                                           AND  n.depth >= current.depth - {?}",
                                           array_keys($nodes), $depth);
            $fetched = $fetched + self::iterToNodes($iter, $nodes);
        }

        self::buildLinks($fetched);
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

class Group extends Node
{
    const MAX_DEPTH = 666;

    const SELECT_BASE        = 0x01;
    const SELECT_DESCRIPTION = 0x02;

    protected $type = null;
    protected $name = null;
    protected $label = null;
    protected $description = null;

    protected $root = null;

    static public function table()
    {
        return 'groups';
    }

    static public function idName()
    {
        return 'gid';
    }

    public static function batchSelect(array $nodes, $fields)
    {
        $bits = 0;
        if (is_array($fields))
            foreach($fields as $bit => $args)
                $bits |= $bit;
        else
            $bits = $fields;

        parent::batchSelect($nodes, $fields);

        $cols = '';
        if ($bits & self::SELECT_BASE)
            $cols .= ', name, label';
        if ($bits & self::SELECT_DESCRIPTION)
            $cols .= ', description';

        if ($cols != '') {
            $flattened = $nodes;
            foreach ($nodes as $n)
                $flattened = $flattened + $n->flattenedChildren();

            $res = XDB::query("SELECT  gid AS id $cols
                                 FROM  groups
                                WHERE  gid IN {?}", array_keys($flattened));
            $ids_datas = $res->fetchAllAssoc('id');

            foreach ($flattened as $n)
                $n->fillFromArray($ids_datas[$n->id]);
        }
    }

    public function __construct($datas)
    {
        parent::__construct($datas);
    }

    public function gid()
    {
        return $this->id();
    }

    public function type()
    {
        return $this->type;
    }

    public function name()
    {
        return $this->name;
    }

    public function label($label = null)
    {
        if ($label != null)
        {
            $this->label = $label;
            XDB::execute('UPDATE groups SET label = {?} WHERE gid = {?}', $label, $this->id);
        }
        return $this->label;
    }

    public function description()
    {
        return $this->description;
    }

    public static function batchFrom(array $mixed)
    {
        $instances = array();
        if (!empty($mixed)) {
            $iter = XDB::iterator('SELECT  gid AS id, name
                                     FROM  groups
                                    WHERE  name IN {?}', $mixed);
            while ($g = $iter->next())
                $instances[$g['name']] = new self($g);
        }
        return $instances;
    }

    public static function fromNames(array $names)
    {
        $iter = XDB::iterator("SELECT  gid AS id, name
                                 FROM  groups
                                WHERE  name IN {?}", $names);
        $groups = array();
        while ($node = $iter->next())
            $groups[$node['name']] = new Group($node);

        return $groups;
    }

    public static function root()
    {
        global $globals;

        if (self::$root === null)
            if (isset($globals->root) && ($globals->root != ''))
                self::$root = new self($globals->root);
            else
                self::$root = parent::root();

        return self::$root;
    }

    public function insert(Node $parent)
    {
        parent::insert($parent);

        XDB::execute('UPDATE  groups
                         SET  name = {?}, label = {?}, description = {?}
                       WHERE  gid = {?}',
                   $this->name(), $this->label(), $this->description(), $this->id);
    }

    public function toJson()
    {
        $json = array("id"    => $this->gid(),
                      "L"     => $this->L(),
                      "name"  => $this->name(),
                      "label" => $this->label());

        if ($this->hasChildren())
            if (empty($this->children)) {
                $json['children'] = true;
            } else {
                $children = $this->sortedChildren();
                $json['children'] = array();
                foreach($children as $child)
                    $json['children'][] = $child->toJson();
            }

        return $json;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
