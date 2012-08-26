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
    private $content_table = 'forum_content';

    public function __construct($datas = null)
    {
        if($datas === null) return;
        if (!is_array($datas)) {
            $this->id = $datas;
            $this->load();
        }
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

    public function root_id()
    {
        return $this->root_id;
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
         $res = XDB::query("SELECT L, R, depth, root_id
                             FROM ".$this->table."
                             WHERE id=".$this->id);

         $fixMe = $res->fetchAllAssoc();
         $this->fillFromArray($fixMe[0]);
    }

    public function getChildren()
    {
        $res = XDB::query("SELECT c.id, c.L, c.R, c.depth, c.content_id, n.last_modification_date
                             FROM ".$this->table." AS n
                             LEFT JOIN ".$this->content_table." AS c ON c.node_id=n.id
                             WHERE root_id=".$this->root_id." AND L BETWEEN 1+".$this->L." AND ".$this->R);
        return $res->fetchAllAssoc();
    }

    public function getDescendants()
    {
        $res = XDB::query("SELECT n.id, n.L, n.R, n.depth, n.content_id, c.last_modification_date
                             FROM ".$this->table." as n
                             LEFT JOIN ".$this->content_table." AS c ON c.node_id=n.id
                             WHERE root_id=".$this->root_id." AND L BETWEEN 1+".$this->L." AND ".$this->R." AND depth=1+".$this->depth);
        return $res->fetchAllAssoc();
    }

    public function getAncestors()
    {
        $res = XDB::query("SELECT nparents.id, nparents.L, nparents.R, nparents.depth, nparents.content_id
                             FROM ".$this->table." as n
                             JOIN ".$this->table." as nparents ON MBRWithin(Point(0, n.L), nparents.box) AND n.root_id=nparents.root_id
                             WHERE n.id=".$this->id);
        return $res->fetchAllAssoc();
    }

    public function insert()
    {
        XDB::query("CALL forum_nodes_insert(".($this->parent_id ? $this->parent_id : "NULL").", ".$this->content_id.", @unusedID)");
    }

    public function delete()
    {
        if(empty($this->id)) return false;
        XDB::query("CALL forum_nodes_remove(".$this->id.")");
    }
}

?>
