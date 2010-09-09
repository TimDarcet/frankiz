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

abstract class TreeInfo
{
    protected static $instance = null;
    protected static $root = null;

    protected function __construct()
    {
    }

    public static function get()
    {
        if (self::$instance == null)
            self::$instance = new static();

        return self::$instance;
    }

    protected function loadRoot()
    {
        $id = $this->idName();
        $ta = $this->table();
        $res = XDB::query("SELECT  $id AS id, L, R, depth
                             FROM  $ta
                            WHERE  (R - L + 1) / 2 = (SELECT COUNT(*) FROM $ta)");
        return $this->node($res->fetchOneAssoc());
    }

    public function root()
    {
        if (self::$root === null)
            self::$root = $this->loadRoot();

        return self::$root;
    }

    // maxDepth of the table
    abstract public function maxDepth();
    // name of the table containing the nodes
    abstract public function table();
    // name of the column containing the nodes id
    abstract public function idName();
    // The node builder
    abstract public function buildNode($datas);
    // The fields loader
    abstract public function batchLoad($nodes, $fields);
}

class GroupsTreeInfo extends TreeInfo
{
    protected static $root = null;

    public function maxDepth()
    {
        return 666;
    }
    
    public function table()
    {
        return 'groups';
    }

    public function idName()
    {
        return 'gid';
    }

    public function root()
    {
        global $globals;

        if (self::$root === null)
            if (isset($globals->root) && ($globals->root != ''))
                self::$root = self::buildNode(array('id' => $globals->root));
            else
                self::$root = $this->loadRoot();

        return self::$root;
    }

    public function buildNode($datas)
    {
        return new Group($datas);
    }

    public function batchLoad($nodes, $fields)
    {
        return Group::batchLoad($nodes, $fields);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
