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

// Collection of Db-existent objects
// Objects need to have an unique id accessible by id()
class Collection
{
    protected $collected = array();
    protected $c_class   = null;

    public function __construct($collected_class)
    {
        $this->c_class = $collected_class;
    }

    public function toJson()
    {
        $json = array();
        foreach ($this->collected as $c)
            $json[] = $c->toJson();
        return $json;
    }

    public function select($fields)
    {
        $c_class = $this->c_class;
        $c_class::batchSelect($this->collected, $fields);
        return $this;
    }

    public function add($cs)
    {
        $c_class = $this->c_class;
        $cs = unflatten($cs);
        foreach ($cs as $c)
            if ($c instanceof $c_class)
                $this->collected[$c->id()] = $c;
            else
                $this->collected[$c] = new $c_class($c);

        return $this;
    }

    public function get($id)
    {
        return $this->collected[$id];
    }

    public function remove($cs)
    {
        $cs = unflatten($cs);
        foreach ($cs as $c)
            if ($c instanceof $c_class)
                unset($this->collected[$c->id()]);
            else
                unset($this->collected[$c]);

        return $this;
    }

    public function count()
    {
        return count($this->collected);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
