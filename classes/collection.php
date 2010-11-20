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

class IdentifierNotFoundException extends Exception {

}

// Collection of Db-existent objects
// Objects need to have an unique id accessible by id()
class Collection extends PlAbstractIterable
{
    protected $collected = array();
    protected $className = null;

    protected $order = null;
    protected $desc  = true;

    /**
    *
    * @param $className The class name of the items
    * @param $order     The item's method to be used to sort the Collection
    * @param $desc      Boolean specifying if the sort is descending (default) or ascending
    */
    public function __construct($className = null, $order = null, $desc = true)
    {
        $this->className($className);
        $this->order($order, $desc);
    }

    /**
    * Set or get the class of the items
    *
    * @param $className The class name
    */
    public function className($className = null)
    {
        if ($className != null)
            $this->className = $className;
        return $this->className;
    }

    /**
    * Set or get the current order of the Collection
    *
    * @param $order The item's method to be used to sort the Collection
    * @param $desc Boolean specifying if the sort is descending (default) or ascending
    */
    public function order($order = null, $desc = true)
    {
        if ($order != null) {
            $this->order = $order;
            $this->desc  = $desc;
        }
        return $this->order;
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

    public function toArray($indexedBy = null)
    {
        if (empty($indexedBy))
            return $this->collected;

        $collec = array();
        foreach ($this->collected as $c)
            $collec[$c->$indexedBy()] = $c;

        return $collec;
    }

    /** Build an iterator for this Collection.
     */
    public function iterate()
    {
        $order = $this->order;

        $iterator = PlIteratorUtils::fromArray($this->collected, 1, true);

        if (empty($order))
            return $iterator;

        $desc = ($this->desc) ? 1 : -1;
        return PlIteratorUtils::sort($iterator, function($a, $b) use($order, $desc)
                                                {
                                                    $a = $a->$order();
                                                    $b = $b->$order();
                                                    if ($a == $b) return 0;
                                                    return ($a < $b) ? $desc : -$desc;
                                                });
    }

    /**
    * Fetch datas from the database for each items of the Collection
    *
    * @param $options Fields to be fetched by batchSelect()
    */
    public function select($options = null)
    {
        if ($this->count() == 0)
            return $this;

        $className = $this->className;
        $className::batchSelect($this->collected, $options);
        return $this;
    }

    /**
    * Returns an array containing the ids of the collection's items)
    */
    public function ids()
    {
        return array_keys($this->collected);
    }

    /**
    * Add an item only if it doesn't already exist in the Collection
    * Returns the added item
    *
    * @param $id The Id of the item
    */
    public function addget($id)
    {
        $id = intval($id);
        if (!empty($this->collected[$id]))
            return $this->collected[$id];

        $className = $this->className;
        $this->collected[$id] = new $className($id);
        return $this->collected[$id];
    }

    /**
    * Add Items in the Collection.
    * In order to pass an identifier, you must have specified the element's class
    *
    * @param $cs An Item, an id or any unique identifier supported by batchFrom()
    */
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
            else if (isId($c)) {
                if (empty($this->collected[$c]))
                    $this->collected[$c] = new $className($c);
            } else
                $mixed[] = $c;

        if (!empty($mixed)) {
            $collec = $className::batchFrom($mixed);
            $this->merge($collec);

            if (count($mixed) != $collec->count())
                throw new IdentifierNotFoundException('Identifiers passed: ' . implode(',', $mixed) . "\n" .
                                                      'Instances returned: ' . implode(',', array_keys($collec->toArray('name'))));
        }

        return $this;
    }

    /**
    * Merge another Collection with this one which is returned
    *
    * @param $collec Another Collection containing the same class of elements
    */
    public function merge(Collection $collec)
    {
        if (empty($this->className))
            $this->className = $collec->className();

        foreach ($collec->collected as $id => $c)
            $this->collected[$id] = $c;

        return $this;
    }

    /**
    * Gets an item from the Collection
    *
    * @param $mixed An Item, an id or any unique identifier supported by isMe()
    */
    public function get($mixed)
    {
        if (isId($mixed))
            return empty($this->collected[intval($mixed)]) ? false : $this->collected[intval($mixed)];
        elseif ($mixed instanceof Meta)
            return empty($this->collected[$mixed->id()]) ? false : $this->collected[$mixed->id()];
        else
            foreach ($this->collected as $c)
                if ($c->isMe($mixed))
                    return $c;

        return false;
    }

    /**
    * Remove items from the Collection
    *
    * @param $cs  An Item, an id or an array containing them
    */
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

    /**
    * Number of items in the Collection
    */
    public function count()
    {
        return count($this->collected);
    }

    /**
    * Returns an new Collection with the filtered items
    * You can give a closure as an argument too
    *
    * @param $methodName  The name of the method to call on each item
    * @param $value       Expected value to be returned by the method
    */
    public function filter()
    {
        $args = func_get_args();

        $filtered = new Collection($this->className);
        if (is_callable($args[0]))
        {
            $callback = $args[0];
            return $filtered->add(array_filter($this->collected, $callback));
        } else {
            $methodName = $args[0];
            $val        = $args[1];
            return $filtered->add(array_filter($this->collected, function ($i) use($val, $methodName) {return $i->$methodName() == $val;}));
        }
    }

    /**
    * Returns an array associating the possible values returned by
    * $item->$methodName() with a Collection of the correspnding items
    *
    * @param $methodName    The name of the method to call on each item
    */
    public function split($methodName)
    {
        $split = array();
        foreach ($this->collected as $c)
        {
            $key = $c->$methodName();
            if (!isset($split[$key]))
                $split[$key] = new Collection($this->className);

            $split[$key]->add($c);
        }
        return $split;
    }

    /**
    * Returns the first element of the Collection
    */
    public function first()
    {
        foreach ($this as $c)
            return $c;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
