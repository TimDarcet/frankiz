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

class Group extends Node
{
    const MAX_DEPTH = 666;

    const SELECT_BASE        = 0x01;
    const SELECT_DESCRIPTION = 0x02;

    protected $type = null;
    protected $name = null;
    protected $label = null;
    protected $description = null;

    static protected $root = null;

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
        if (empty($nodes))
            return;

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
            $flattened = self::batchFlatten($nodes, false);

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
        $collec = new Collection();
        if (!empty($mixed)) {
            $iter = XDB::iterator('SELECT  gid AS id, name
                                     FROM  groups
                                    WHERE  name IN {?}', $mixed);
            while ($g = $iter->next())
                $collec->add(new self($g));
        }

        return $collec;
    }

    public function isMe($mixed)
    {
        $isMe = parent::isMe($mixed);
        if ($isMe !== null)
            return $isMe;

        return $mixed == $this->name();
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

    public function toJson($stringify = false)
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

        return ($stringify) ? json_encode($json) : $json;
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
