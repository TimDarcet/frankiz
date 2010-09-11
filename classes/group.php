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

    public function __construct($datas)
    {
        if (!is_array($datas))
            $this->id =$datas;

        if (is_array($datas))
            $this->fillFromArray($datas);
    }

    abstract public function treeInfo();

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
        return ($this->L() + 1 != $this->R());
    }

    public function children()
    {
        return $this->children;
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

    static public function roots(array $nodes)
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
    public function remove()
    {
        if ($this->id == null)
            throw new Exception("This node doesn't exist");

        $table = $this->treeInfo()->table();
        XDB::execute('CALL '.$table.'_remove({?})', $this->id);
    }

    /**
    * Create the node in the Db
    * Require a stored procedure called {table}_addTo()
    * Example with a table called group :
        CREATE PROCEDURE groups_addTo(IN parent_gid INT, OUT new_id INT)
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
    public function addTo(Node $parent)
    {
        if ($this->id != null)
            throw new Exception('This node already exists');

        $table = $this->treeInfo()->table();
        XDB::execute('CALL '.$table.'_addTo({?}, @new_id)', $parent->id());

        $this->id = XDB::query('SELECT @new_id')->fetchOneCell();
    }

    /**
    * Move the node and its sub-tree in the Db
    * Require a stored procedure called {table}_moveTo()
    * Example with a table called group :
        CREATE PROCEDURE groups_moveTo(IN g_gid INT, IN t_gid INT)
        BEGIN
            DECLARE g_R INT DEFAULT NULL;
            DECLARE g_L INT DEFAULT NULL;
            DECLARE g_depth INT DEFAULT NULL;
            DECLARE t_L INT DEFAULT NULL;
            DECLARE t_depth INT DEFAULT NULL;
            DECLARE delta INT DEFAULT NULL;
            DECLARE zone_min INT DEFAULT NULL;
            DECLARE zone_max INT DEFAULT NULL;
            DECLARE zone_sign INT DEFAULT NULL;
            DECLARE init_shift INT DEFAULT NULL;
            DECLARE end_shift INT DEFAULT NULL;

            START TRANSACTION;
                SELECT R, L, depth INTO g_R, g_L, g_depth FROM groups WHERE gid = g_gid;
                SELECT L, depth INTO t_L, t_depth FROM groups WHERE gid = t_gid;

                IF !ISNULL(g_R) AND !ISNULL(t_L) THEN
                    SET delta = g_R - g_L;
                    SET zone_min  = LEAST(g_R, t_L);
                    SET zone_max  = GREATEST(g_R, t_L);
                    SET zone_sign = SIGN(g_R - t_L);
                    SET init_shift = g_R + 1000;
                    SET end_shift = init_shift + IF(zone_sign = 1, t_L - g_L + 1, t_L - g_R);

                    UPDATE groups SET R = R - init_shift, L = L - init_shift WHERE L >= g_L AND R <= g_R;
                    UPDATE groups SET L = L + (zone_sign * (delta + 1)) WHERE L > zone_min AND L <= zone_max;
                    UPDATE groups SET R = R + (zone_sign * (delta + 1)) WHERE R > zone_min AND R < zone_max;
                    UPDATE groups SET R = R + end_shift, L = L + end_shift, depth = depth + (t_depth - g_depth + 1) WHERE L >= (g_L - init_shift) AND R <= (g_R - init_shift);
                END IF;
            COMMIT;
        END|
    *
    * @param $parent parent node
    */
    public function moveTo(Node $parent)
    {
        if ($this->id == null || $parent->id == null)
            throw new Exception("This node doesn't exist");

        $table = $this->treeInfo()->table();
        XDB::execute('CALL '.$table.'_moveTo({?}, {?})', $this->id(), $parent->id());
    }
}

class Group extends Node
{
    const BASE        = 0x01;
    const DESCRIPTION = 0x02;

    protected $type = null;
    protected $name = null;
    protected $label = null;
    protected $description = null;

    public static function batchLoad($nodes, $bits)
    {
        $fields = '';
        if ($bits & self::BASE)
            $fields .= ', name, label';
        if ($bits & self::DESCRIPTION)
            $fields .= ', description';

        $res = XDB::query("SELECT  gid $fields
                             FROM  groups
                            WHERE  gid IN {?}",
                             Node::toIds($nodes));
        return $res->fetchAllAssoc('gid');
    }

    public function load($bits)
    {
        $datas = batchLoad($this, $bits);
        $this->fillFromArray($datas[$this->id]);
    }

    public function __construct($datas)
    {
        parent::__construct($datas);
    }

    public function treeInfo()
    {
        return GroupsTreeInfo::get();
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

    public function addTo(Node $parent)
    {
        parent::addTo($parent);

        XDB::execute('UPDATE  groups
                         SET  name = {?}, label = {?}, description = {?}
                       WHERE  gid = {?}',
                   $this->name(), $this->label(), $this->description(), $this->id);
    }

    public function toJson($visibility = 0)
    {
        $json = array("data"  => array(
                                        "title" => $this->label()
                                      ),
                      "attr"  => array(
                                        "gid"   => $this->gid(),
                                        "name"  => $this->name(),
                                        "title" => $this->name(),
                                        "label" => $this->label()
                                      )
                       );

        if ($this->hasChildren())
        {
            $json['state'] = ($visibility > 0) ? "open" : "closed";
            $json['children'] = array();
            foreach($this->children as $child)
                $json['children'][] = $child->toJson($visibility - 1);
        }

        return $json;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
