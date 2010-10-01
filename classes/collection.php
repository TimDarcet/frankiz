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
class Collection extends PlAbstractIterable
{
    protected $collected = array();
    protected $className = null;

    /* Feel free to do whatever you want with
     * the constructor in the subclasses.
     */
    public function __construct()
    {
    }

    public static function fromClass($className)
    {
        $c = new Collection();
        $c->className($className);
        return $c;
    }

    public function className($className = null)
    {
        if ($className != null)
            $this->className = $className;
        return $this->className;
    }

    public function __call($method, $arguments)
    {
        $className = $this->className;
        $inferedMethod = 'batch' . ucfirst($method);
        array_unshift($arguments, $this->collected);

        if (method_exists($className, $inferedMethod)) {
            $r = forward_static_call_array(array($className, $inferedMethod), $arguments);
            if (!is_array($r))
                return $r;

            $c = new Collection($className);
            if (!empty($r))
                $c->add($r);
            return $c;
        }

        throw new Exception("The method $className::$inferedMethod doesn't exist");
    }

    public function toJson($stringify = false)
    {
        $json = array();
        foreach ($this->collected as $c)
            $json[] = $c->toJson();

        return ($stringify) ? json_encode($json) : $json;
    }

    public function toArray()
    {
        return $this->collected;
    }

    /** Build an iterator for this Collection.
     */
    public function iterate()
    {
        return PlIteratorUtils::fromArray($this->collected, 1, true);
    }

    public function select($fields)
    {
        $className = $this->className;
        $className::batchSelect($this->collected, $fields);
        return $this;
    }

    public function ids()
    {
        $ids = array();
        foreach ($this->collected as $c)
            $ids = array_merge($ids, $c->ids());

        return $ids;
    }

    public static function isId($mixed)
    {
        return (intval($mixed).'' == $mixed);
    }

    public function add($cs)
    {
        $cs = unflatten($cs);

        // If the class hasn't been specified yet
        if (empty($this->className))
            $this->className = get_class(current($cs));

        $mixed = array();
        $className = $this->className;
        foreach ($cs as $c)
            if ($c instanceof $className)
                $this->collected[$c->id()] = $c;
            else if (self::isId($c))
                if (empty($this->collected[$c]))
                    $this->collected[$c] = new $className($c);
            else
                $mixed[] = $c;

        if (!empty($mixed)) {
            $instances = $className::batchFrom($mixed);
            foreach ($instances as $c)
                $this->collected[$c->id()] = $c;
        }

        return $this;
    }

    public function merge(Collection $collec)
    {
        if (empty($this->className))
            $this->className = $collec->className();

        foreach ($collec->collected as $id => $c)
            $this->collected[$id] = $c;

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
            if ($c instanceof $className)
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
