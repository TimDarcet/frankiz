<?php
/***************************************************************************
 *  Copyright (C) 2004-2013 Binet Réseau                                   *
 *  http://br.binets.fr/                                                   *
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
    private $ids;

    public function ids(array $ids = null)
    {
        if ($ids !== null)
            $this->ids = $ids;

        return $this->ids;
    }
}

class UndefinedIdException extends Exception
{
}

class NotAnIdException extends Exception
{
}

class DataNotFetchedException extends Exception
{
}

abstract class Meta
{
    protected $id = null;
    protected $_autocommit = true; //TODO

    public function __call($method, $arguments)
    {
        $className = get_class($this);
        $schema = Schema::get($className);
        if ($schema->has($method)) {
            // Automatic field getter/setter
            if (!empty($arguments) && !is_null($arguments[0])) {
                $table = $schema->table();
                $field = $method;
                $id    = $schema->id();

                if ($schema->isScalar($field)) {
                    $data = ($arguments[0] === false) ? null : $arguments[0];
                } elseif ($schema->isObject($field)) {
                    $objectType = $schema->objectType($field);

                    $reflection = new ReflectionClass($objectType);

                    if ($reflection->isSubclassOf('Meta')) {
                        if ($arguments[0] === false) {
                            $data = null;
                        } elseif ($arguments[0] instanceof Meta) {
                            $data = $arguments[0]->id();
                        } else {
                            throw new Exception('The object ' . var_dump($arguments[0]) . 'is not of class Meta');
                        }
                    } else if ($reflection->implementsInterface('Formatable')) {
                        $data = ($arguments[0] === false) ? null : $arguments[0]->toDb();
                    } else {
                        throw new Exception('Unsupported object passed to the setter');
                    }
                } elseif ($schema->isFlagset($field)) {
                    // Autosetter for flag sets
                    return $this->helper_flagsetSet($field, $arguments[0], $schema);
                } elseif ($schema->isCollection($field)) {
                    // Autosetter for collections
                    return $this->helper_collectionSet($field, $arguments[0], $schema);
                } else {
                    throw new Exception("Auto setter for field $field is not supported yet");
                }

                XDB::execute("UPDATE  $table
                                 SET  `$field` = {?}
                               WHERE  $id = {?}", $data, $this->id());

                $this->$method = $arguments[0];
            }

            if ($this->$method === null) {
                throw new DataNotFetchedException("$method has not been fetched in $className(" . $this->id() . ")");
            }

            return $this->$method;
        } elseif (starts_with($method, 'add')) {
            // Add something, return true if the data was really added
            $field = strtolower(substr($method, 3)) . 's';
            if (!is_array($arguments) || count($arguments) != 1) {
                throw new Exception('Wrong parameter count for auto-adder');
            }
            if ($schema->isFlagset($field)) {
                return $this->helper_flagsetAdd($field, $arguments[0], $schema);
            } elseif ($schema->isCollection($field)) {
                return $this->helper_collectionAdd($field, $arguments[0], $schema);
            }
        } elseif (starts_with($method, 'remove')) {
            // Remove something, return true if the data was really removed
            $field = strtolower(substr($method, 6)) . 's';
            if (!is_array($arguments) || count($arguments) != 1) {
                throw new Exception('Wrong parameter count for auto-adder');
            }
            if ($schema->isFlagset($field)) {
                return $this->helper_flagsetRemove($field, $arguments[0], $schema);
            } elseif ($schema->isCollection($field)) {
                return $this->helper_collectionRemove($field, $arguments[0], $schema);
            }
        } elseif (starts_with($method, 'has')) {
            // Test if a set of objects has something
            $field = strtolower(substr($method, 3)) . 's';
            if (!is_array($arguments) || count($arguments) != 1) {
                throw new Exception('Wrong parameter count for auto-has');
            }
            if (isset($this->$field)) {
                if ($this->$field === null) {
                    throw new DataNotFetchedException("$method has not been fetched in $className(" . $this->id() . ")");
                }
                if ($schema->isFlagset($field)) {
                    return $this->$field->hasFlag($arguments[0]);
                } elseif ($schema->isCollection($field)) {
                    return $this->$field->has($arguments[0]);
                }
            }
        }

        throw new Exception("This object doesn't have '$method' as automatic field");
    }

    public function __construct($datas = null)
    {
        if ($datas === null) {
            return;
        }

        if (!is_array($datas)) {
            if (static::isId($datas)) {
                $this->id = $datas;
            } else {
                throw new NotAnIdException("$datas is not a correct Id for "  .get_class($this));
            }
        } else {
            $this->fillFromArray($datas);
        }
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

    public static function isId($mixed)
    {
        return !is_object($mixed) && (intval($mixed).'' == $mixed);
    }

    public function select($fields = null)
    {
        if ($fields instanceof Select) {
            $c = new Collection(get_class($this));
            $fields->select($c->add($this));
        } elseif (!is_null($fields)) {
            static::batchSelect(array($this), $fields);
        }
        return $this;
    }

    /**
     * Select all elements in the database
     * @param $fields select() parameter
     */
    public static function selectAll($fields = null)
    {
        $schema = Schema::get(get_called_class());
        $res = XDB::query('SELECT ' . $schema->id() . ' FROM ' . $schema->table());
        $col = new Collection($schema->className());
        $col->add($res->fetchColumn());
        return $col->select($fields);
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
        else if (static::isId($other))
            return $other == $this->id();
        else
            return null;
    }

    public function insert()
    {
        $schema = Schema::get(get_class($this));

        $table  = $schema->table();
        $id = $schema->id();

        XDB::execute("INSERT INTO $table SET `$id` = NULL");
        $this->id = XDB::insertId();
    }

    public function delete()
    {
        $schema = Schema::get(get_class($this));

        $table = $schema->table();
        $id    = $schema->id();

        XDB::execute("DELETE FROM $table WHERE $id = {?}", $this->id());
    }

    public function export($bits = null)
    {
        $export = array('id' => $this->id());
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
                                    if ($table == -1) {
                                        return $value;
                                    } else {
                                        return "`$table`.`$value`";
                                    }
                                }, $vals));

        return implode(', ', $sql_columns);
    }

    /**
     * Get an object by its ID or with a secondary key
     * @param mixed $id
     * @param boolean $tryKey try the other key if $id is not an index
     * @return false if nothing is found, a meta object otherwise
     *
     * @deprecated Please use from() for modules and IDs internally
     *
     * Note: This method is to be called when there is only one expected object.
     * For several objects, you should use FrankizFilter and Collection instead.
     */
    public static function fromId($id, $tryKey = true)
    {
        if (empty($id)) {
            return false;
        }
        // If $id is not an ID, return from result
        if (!static::isId($id)) {
            if (!$tryKey) {
                return false;
            }
            try {
                return static::from($id);
            } catch (ItemNotFoundException $e) {
                return false;
            }
        }

        // Get schema
        $schema = Schema::get(get_called_class());
        $className = $schema->className();
        $id_col = $schema->id();
        $key_col = $schema->fromKey();
        $mysql_key = (($key_col && $key_col != $id_col)
            ? XDB::format(' OR ' . $key_col . ' = {?}', (string)$id)
            : '');
        $res = XDB::fetchOneRow('SELECT  ' . $id_col . ' AS id
                                   FROM  ' . $schema->table() . '
                                  WHERE  ' . $id_col . ' = {?}' .
                                $mysql_key . '
                                  LIMIT  1', $id);
        return isset($res[0]) ? new $className($res[0]) : false;
    }

    /**
     * Returns the object corresponding to the specified name
     *
     * @param $mixed              A unique identifier ot the object to retrieve
     * @param $insertIfNotExists  Create the Object if the identifier doesn't exist ? (Use 'name' by default)
     * @return Meta an Meta object
     * @throws ItemNotFoundException if this object does not exist
     */
    public static function from($mixed, $insertIfNotExists = false)
    {
        if (!$mixed) {
            throw new ItemNotFoundException('Empty index');
        }
        try {
            $w = static::batchFrom(array($mixed))->first();
        } catch (ItemNotFoundException $e) {
            if ($insertIfNotExists) {
                throw new Exception('Feature not supported');
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
        // The schema may specify a key
        $schema = Schema::get(get_called_class());
        $className = $schema->className();
        $key = $schema->fromKey();
        if (empty($key))
            throw new Exception("batchFrom isn't implemented");

        $collec = new Collection($className);
        if (!empty($mixed)) {
            $iter = XDB::iterator('SELECT  ' . $schema->id() . ' AS id, ' . $key . '
                                     FROM  ' . $schema->table() . '
                                    WHERE  ' . $key . ' IN {?}', $mixed);
            while ($data = $iter->next()) {
                $collec->add(new $className($data));
            }
            if ($collec->count() == 0) {
                throw new ItemNotFoundException('Nothing found for ' . implode(', ', $mixed));
            }
            if (count($mixed) != $collec->count()) {
                throw new ItemNotFoundException('Asking for ' . implode(', ', $mixed) .
                    ' but only found ' . implode(', ', $collec->ids()));
            }
        }
        return $collec;
    }

    /**
    * Fetch datas from the database
    *
    * @param $metas    The objects to be filled
    * @param $options  Options defining the datas to load
    */
    public static function batchSelect(array $metas, $options = null)
    {
        throw new Exception("batchSelect isn't implemented in " . get_called_class());
    }

    /**
     * Helper to add a value into a FlagSet which is in the database
     *
     * @param string $field Flagset field
     * @param mixed $value the value to add
     * @param Schema|null $schema database schema of this metaobject
     * @return true if something has been modified, false otherwise
     */
    protected function helper_flagsetAdd($field, $value, Schema $schema = null) {
        if (!$schema) {
            $schema = Schema::get(get_class($this));
        }
        $id = $schema->id();
        list($table, $column) = $schema->flagsetType($field);
        XDB::execute("INSERT IGNORE  `$table`
                                SET  `$id` = {?}, `$column` = {?}",
                     $this->id(), $value);

        if (!(XDB::affectedRows() > 0))
            return false;

        if (empty($this->$field))
            $this->$field = new PlFlagSet();

        $this->$field->addFlag($value);
        return true;
    }

    /**
     * Helper to remove a value from a FlagSet which is in the database
     *
     * @param string $field Flagset field
     * @param mixed $value the value to remove
     * @param Schema|null $schema database schema of this metaobject
     * @return true if something has been modified, false otherwise
     */
    protected function helper_flagsetRemove($field, $value, Schema $schema = null) {
        if (!$schema) {
            $schema = Schema::get(get_class($this));
        }
        $id = $schema->id();
        list($table, $column) = $schema->flagsetType($field);
        XDB::execute("DELETE FROM  `$table`
                            WHERE  `$id` = {?} AND `$column` = {?}
                            LIMIT  1",
                     $this->id(), $value);

        if (!(XDB::affectedRows() > 0))
            return false;

        if (empty($this->$field))
            $this->$field = new PlFlagSet();

        $this->$field->rmFlag($value);
        return true;
    }

    /**
     * Helper to set values for a FlagSet
     *
     * @param string $field Flagset field
     * @param PlFlagSet $values
     * @param Schema|null $schema database schema of this metaobject
     * @return new value for PlFlagSet
     */
    protected function helper_flagsetSet($field, PlFlagSet $values = null, Schema $schema = null) {
        if (is_null($values)) {
            return $this->$field;
        }
        if (!$schema) {
            $schema = Schema::get(get_class($this));
        }

        foreach ($values as $v) {
            if (!$this->$field->hasFlag($v))
                $this->helper_flagsetAdd($field, $v, $schema);
        }
        foreach ($this->$field as $v) {
            if (!$values->hasFlag($v))
                $this->helper_flagsetRemove($field, $v, $schema);
        }
        return $this->$field;
    }

    /**
     * Helper to add an item to a Collection which is in the database
     *
     * @param string $field Collection field
     * @param Meta|null $value the value to add
     * @param Schema|null $schema database schema of this metaobject
     * @return true if something has been modified, false otherwise
     */
    protected function helper_collectionAdd($field, Meta $value = null, Schema $schema = null) {
        if (!$value) {
            // Nothing to add
            return false;
        }
        if (!$schema) {
            $schema = Schema::get(get_class($this));
        }
        list($l_className, $table, $l_id, $id) = $schema->collectionType($field);

        XDB::execute("INSERT IGNORE INTO  `$table`
                                     SET  `$l_id` = {?}, `$id` = {?}",
                     $value->id(), $this->id());

        if (XDB::affectedRows() <= 0) {
            return false;
        }
        if (empty($this->$field)) {
            $this->$field = new Collection($l_className);
        }
        $this->$field->add($value);
        return true;
    }

    /**
     * Helper to remove an item from a Collection which is in the database
     *
     * @param string $field Collection field
     * @param Meta|null $value the value to remove
     * @param Schema|null $schema database schema of this metaobject
     * @return true if something has been modified, false otherwise
     */
    protected function helper_collectionRemove($field, Meta $value = null, Schema $schema = null) {
        if (!$value) {
            // Nothing to add
            return false;
        }
        if (!$schema) {
            $schema = Schema::get(get_class($this));
        }
        list($l_className, $table, $l_id, $id) = $schema->collectionType($field);

        XDB::execute("DELETE FROM  `$table`
                            WHERE  `$l_id` = {?} AND `$id` = {?}
                            LIMIT  1",
                     $value->id(), $this->id());

        if (XDB::affectedRows() <= 0) {
            return false;
        }
        if (!empty($this->$field)) {
            $this->$field->remove($value);
        }
        return true;
    }

    /**
     * Helper to set values for a Collection
     *
     * @param string $field Collection field
     * @param Collection|null $values
     * @param Schema|null $schema database schema of this metaobject
     * @return new value for Collection
     *
     * If $values is null, this function does NOT reset anything and is a getter.
     */
    protected function helper_collectionSet($field, Collection $values = null, Schema $schema = null) {
        if (is_null($values)) {
            return $this->$field;
        }
        if (!$schema) {
            $schema = Schema::get(get_class($this));
        }

        $oldIds = $this->$field->ids();
        $newIds = $values->ids();
        // Remove no longer used items
        foreach (array_diff($oldIds, $newIds) as $id) {
            $this->helper_collectionRemove($field, $this->$field->get($id), $schema);
        }
        // Add new items
        foreach (array_diff($newIds, $oldIds) as $id) {
            $this->helper_collectionAdd($field, $values->get($id), $schema);
        }
        return $this->$field;
    }


}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
