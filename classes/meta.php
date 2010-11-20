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

class ItemNotFoundException extends Exception
{
}

class UndefinedIdException extends Exception
{
}

abstract class Meta
{
    protected $id = null;

    public function __construct($datas = null)
    {
        if (!is_array($datas))
            $this->id = $datas;
        else
            $this->fillFromArray($datas);
    }

    public function fillFromArray(array $values)
    {
        if (empty($values))
            return;

        foreach ($values as $key => $value)
            if (property_exists($this, $key))
                $this->$key = $value;
    }

    public function id()
    {
        if ($this->id === null)
            throw new UndefinedIdException();

        return $this->id;
    }

    public function select($fields = null)
    {
        static::batchSelect(array($this), $fields);
        return $this;
    }

    public static function toIds(array $objects)
    {
        $ids = array();
        foreach ($objects as $o)
            if ($o instanceof static)
                $ids[] = $o->id;
            else
                $ids[] = $o;
        return $ids;
    }

    public static function toId($object)
    {
        return flatten(static::toIds(unflatten($object)));
    }

    public function isMe($other)
    {
        if ($other instanceof $this)
            return $other->id() == $this->id();
        else if (isId($other))
            return $other == $this->id();
        else
            return null;
    }

    public function delete()
    {
        if ($this->id == null)
            throw new Exception("This " . get_class($this) . " doesn't appear to exist in the DB and therefore can't be deleted.");
    }

    public function export()
    {
        $export = array('id'    => $this->id(),
                        'class' => get_class($this));
        return $export;
    }

    protected static function optionsToBits($options)
    {
        $bits = 0;
        if (is_array($options))
            foreach($options as $bit => $args)
                $bits |= $bit;
        else
            $bits = $options;
        return $bits;
    }

    protected static function arrayToSqlCols($cols)
    {
        $sql_columns = array();
        foreach($cols as $table => $vals)
            $sql_columns[] = implode(', ', array_map(
                                function($value) use($table) {
                                    if ($table == -1)
                                        return $value;
                                    else
                                        return $table . '.' . $value;
                                }, $vals));

        return implode(', ', $sql_columns);
    }

    /**
    * Returns the object corresponding to the specified name
    *
    * @param $mixed              A unique identifier ot the object to retrieve
    * @param $insertIfNotExists  Create the Object if the identifier doesn't exist ? (Use 'name' by default)
    */
    public static function from($mixed, $insertIfNotExists = false)
    {
        try {
            $w = static::batchFrom(array($mixed))->first();
        } catch (ItemNotFoundException $e) {
            if ($insertIfNotExists) {
                $w = new Wiki(array('name' => $mixed));
                $w->insert();
            } else {
                throw $e;
            }
        }
        return $w;
    }

    /**
    * Try to retrieve the id and return a collection of the corresponding objects
    *  thanks to another unique identifier passed as argument
    * If the function can't find an object for each identifier passed, it must throw
    *  an ItemNotFoundException
    *
    * @param $mixed  An array containing a unique identifier (often it's a unique name)
    */
    public static function batchFrom(array $mixed)
    {
        throw new Exception("batchFrom isn't implemented");
    }

    /**
    * Fetch datas from the database
    *
    * @param $metas    An array containing the objects to fill
    * @param $options  Options defining the datas to load
    */
    public static function batchSelect(array $metas, $options = null)
    {
        throw new Exception("batchSelect isn't implemented");
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
