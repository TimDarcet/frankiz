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

class Tree
{
    protected $treeInfo;

    protected $nodes = array();
    protected $roots = array();

    public function __construct(TreeInfo $treeInfo)
    {
        $this->treeInfo = $treeInfo;
    }

    public function toJson($visibility = 0)
    {
        $json = array();
        $roots = $this->roots();
        foreach ($roots as $root)
            $json[] = $root->toJson($visibility);
        return $json;
    }

    public function load($fields)
    {
        $results = $this->treeInfo->batchLoad($this->nodes, $fields);
        foreach ($results as $id => $datas)
            $this->nodes[$id]->fillFromArray($datas);

        return $this;
    }

    public function roots()
    {
        if (empty($this->roots))
            $this->roots = Node::roots($this->nodes);

        return $this->roots;
    }

    public function behead()
    {
        $roots = $this->roots();
        $newRoots = array();
        foreach ($roots as $root)
            $newRoots = $newRoots + $root->children();

        $this->roots = $newRoots;

        return $this;
    }

    /**
    * Load the descending tree originating from $nodes
    *
    * @param $nodes an array of nodes or ids
    * @param $depth is the depth of the search for children
    */
    public function descending(array $nodes, $depth = 1)
    {
        $id = $this->treeInfo->idName();
        $ta = $this->treeInfo->table();
        $iter = XDB::iterator("SELECT  n.$id AS id, n.L, n.R, n.depth
                                 FROM  $ta AS n
                           INNER JOIN  $ta AS current ON current.$id IN {?}
                                WHERE       n.L >= current.L
                                       AND  n.R <= current.R
                                       AND  n.depth <= current.depth + {?}",
                                       Node::toIds($nodes), $depth);

        while ($node = $iter->next())
            $this->nodes[$node['id']] = $this->treeInfo->buildNode($node);

        Node::buildLinks($this->nodes);

        return $this;
    }

    /**
    * Load the acending tree originating from $nodes
    *
    * @param $nodes an array of nodes
    * @param $depth is the depth of the search for fathers
    */
    public function ascending(array $nodes, $depth = 1)
    {
        $id = $this->treeInfo->idName();
        $ta = $this->treeInfo->table();
        $iter = XDB::iterator("SELECT  n.$id AS id, n.L, n.R, n.depth
                                 FROM  $ta AS n
                           INNER JOIN  $ta AS current ON current.$id IN {?}
                                WHERE       n.L <= current.L
                                       AND  n.R >= current.R
                                       AND  n.depth >= current.depth - {?}",
                                       Node::toIds($nodes), $depth);

        while ($node = $iter->next())
            $this->nodes[$node['id']] = $this->treeInfo->buildNode($node);

        Node::buildLinks($this->nodes);
        
        return $this;
    }

    /**
    * Load the only the given nodes
    *
    * @param $nodes an array of nodes
    */
    public function fixed(array $nodes)
    {
        $id = $this->treeInfo->idName();
        $ta = $this->treeInfo->table();
        $iter = XDB::iterator("SELECT  n.$id AS id, n.L, n.R, n.depth
                                 FROM  $ta AS n
                                WHERE  n.$id IN {?}", Node::toIds($nodes));

        while ($node = $iter->next())
            $this->nodes[$node['id']] = $this->treeInfo->buildNode($node);

        Node::buildLinks($this->nodes);

        return $this;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
