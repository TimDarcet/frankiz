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

    public function __construct($className = null, $order = null, $desc = true)
    {
        $this->className($className);
        $this->order($order, $desc);
    }

    public function className($className = null)
    {
        if ($className != null)
            $this->className = $className;
        return $this->className;
    }

    public function order($order = null, $desc = true)
    {
        if ($order != null)
            $this->order = $order;
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

    public function select($fields)
    {
        $className = $this->className;
        $className::batchSelect($this->collected, $fields);
        return $this;
    }

    public function ids()
    {
        return array_keys($this->collected);
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

    public function merge(Collection $collec)
    {
        if (empty($this->className))
            $this->className = $collec->className();

        foreach ($collec->collected as $id => $c)
            $this->collected[$id] = $c;

        return $this;
    }

    public function get($mixed)
    {
        if (isId($mixed))
            return $this->collected[intval($mixed)];
        elseif ($mixed instanceof Meta)
            return $this->collected[$mixed->id()];
        else
            foreach ($this->collected as $c)
                if ($c->isMe($mixed))
                    return $c;

        return false;
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

    public function filter()
    {
        $args = func_get_args();

        $filtered = new Collection($this->className);
        if (is_callable($args[0]))
        {
           return $filtered->add(array_filter($this->collected, $args[0]));
        }
    }

    public function first()
    {
        return reset($this->collected);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
