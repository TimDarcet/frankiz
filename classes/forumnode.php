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

class ForumNode
{
    protected $id = null;

    protected $L = null;
    protected $R = null;
    protected $depth = null;

    protected $root_id = null;
    protected $parent_id = null;
    protected $content_id = null;

    private $table = 'forum_nodes';

    public function __construct($datas)
    {
        if (!is_array($datas))
            $this->id = $datas;
        else
            $this->fillFromArray($datas);
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

    public function root()
    {
        return $this->root;
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

    private function load()
    {
         $res = XDB::query("SELECT L, R, depth
                             FROM ".$this->table."
                             WHERE id=".$this->id);
    }

    public function getChildren()
    {
        $res = XDB::query("SELECT id, L, R, depth, content_id
                             FROM ".$this->table."
                             WHERE root_id=".$this->root." AND L BETWEEN 1+".$this->L." AND ".$this->R);
        return $res->fetchAllAssoc();
    }

    public function getDescendants()
    {
        $res = XDB::query("SELECT id, L, R, depth, content_id
                             FROM ".$this->table."
                             WHERE root_id=".$this->root." AND L BETWEEN 1+".$this->L." AND ".$this->R."AND depth=1+".$this->depth);
        return $res->fetchAllAssoc();
    }

    public function getAncestors()
    {
        $res = XDB::query("SELECT nparents.id, nparents.L, nparents.R, nparents.depth, nparents.content_id
                             FROM ".$this->table." as n
                             JOIN ".$this->table." as nparents ON MBRWithin(Point(0, n.L), ndparents.box)
                             WHERE n.id=".$this->id);
        return $res->fetchAllAssoc();
    }

    public static function insert($parent, $content, $title)
    {
        XDB::query("CALL forum_nodes_insert(".($this->parent_id ? $this->parent_id : "NULL").", ".$this->content_id.")");
    }

    public function delete()
    {
        if(empty($this->id)) return false;
        XDB::query("CALL forum_nodes_remove(".$this->id.")");
    }
}

?>
