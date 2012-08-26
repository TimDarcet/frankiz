<?php
/***************************************************************************
 *  Copyright (C) 2004-2012 Binet Réseau                                   *
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

/**
 * @brief Database schema structure
 *
 * The *Schema classes serves two purposes:
 *
 * 1. Very simple description of the "main" table related with the object,
 *    in order to support automatic getters & setters for the "simple" fields
 *
 * 2. Describe of the type of the fields in a Meta object,
 *    in order for the Select class to know how to build fetched datas
 */
abstract class Schema {
    /**
     * Meta class name
     *
     * @return string
     */
    abstract public function className();

    /**
     * Database table name
     *
     * @return string
     */
    abstract public function table();

    /**
     * Table alias in SQL queries
     *
     * @return string
     */
    abstract public function tableAs();

    /**
     * Column ID in the database
     *
     * @return string
     */
    abstract public function id();

    /**
     * Key to be used with batchFrom to retrieve data
     *
     * @return string $this->id() by default
     */
    public function fromKey() {
        return $this->id();
    }

    /**
     * List of scalar fields
     *
     * @return array
     */
    public function scalars() {
        return array();
    }

    /**
     * List of object fields
     *
     * @return array
     */
    public function objects() {
        return array();
    }

    /**
     * List of array fields
     *
     * Return for each FlagSet field a link array in the following format:
     * array($table, $column), where
     * * $table is the name of a table used  for the link
     * * $column is the column name
     *
     * @return array
     */
    public function flagsets() {
        return array();
    }

    /**
     * List of collection fields
     *
     * Return for each Collection field a link array in the following format:
     * array($classname, $table, $column [, $myid]), where
     * * $classname is the name of the meta class of the collected object
     * * $table is the name of a table used  for the link
     * * $column is the column name
     * * $myid is the name of the column id which is used, default is self::id()
     *
     * @return array
     */
    public function collections() {
        return array();
    }

    protected static $schemas = array();

    /**
     * Get a Schema instance associated with a class
     *
     * @param string $className
     * @return Schema A singleton instance of the Schema associated with $className
     */
    public static function get($className) {
        if (empty(self::$schemas[$className])) {
            $schemaName = $className . 'Schema';
            self::$schemas[$className] = new $schemaName();
        }
        return self::$schemas[$className];
    }

    /**
     * Get a Schema instance by calling Schema::get($name)
     *
     * @see Schema::get
     * @param string $name
     * @param mixed $arguments
     * @return Schema self::get($name)
     */
    public static function __callStatic($name, $arguments)
    {
        return self::get($name);
    }

    /**
     * Test wether this Schem has a field
     *
     * @param string $field
     * @return boolean
     */
    public function has($field) {
        return $this->isScalar($field) ||
            $this->isObject($field) ||
            $this->isFlagset($field) ||
            $this->isCollection($field);
    }

    /**
     * Test wether $field is a scalar field of this schema
     *
     * @param type $field
     * @return boolean
     */
    public function isScalar($field) {
        return in_array($field, $this->scalars());
    }

    /**
     * Test wether $field is an object field of this schema
     *
     * @param type $field
     * @return boolean
     */
    public function isObject($field) {
        return array_key_exists($field, $this->objects());
    }

    /**
     * Test wether $field is a flagset field of this schema
     *
     * @param type $field
     * @return boolean
     */
    public function isFlagset($field) {
        return array_key_exists($field, $this->flagsets());
    }

    /**
     * Test wether $field is a collection field of this schema
     * @param type $field
     * @return boolean
     */
    public function isCollection($field) {
        return array_key_exists($field, $this->collections());
    }

    /**
     * Give the object type of a field
     * @param type $field
     * @return string typeof($field)
     */
    public function objectType($field) {
        $objects = $this->objects();
        if (empty($objects[$field])) {
            throw new Exception("$field is not an Object");
        }
        return $objects[$field];
    }

    /**
     * Give the flagset type of a field
     * @see Schema::flagsets()
     * @param type $field
     * @return array
     */
    public function flagsetType($field) {
        $flagsets = $this->flagsets();
        if (empty($flagsets[$field])) {
            throw new Exception("$field is not a FlagSet");
        }
        return $flagsets[$field];
    }

    /**
     * Give the collection type of a field
     * @param type $field
     * @return array
     * @see collections()
     */
    public function collectionType($field) {
        $collections = $this->collections();
        if (empty($collections[$field])) {
            throw new Exception("$field is not a Collection");
        }
        $coltype = $collections[$field];
        if (!is_array($coltype)) {
            throw new Exception("$field has a bad collection schema");
        }
        if (count($coltype) == 3) {
            // Add 4th field
            $coltype[3] = $this->id();
        }
        if (count($coltype) != 4) {
            throw new Exception("Invalid collection schema for $field");
        }
        return $coltype;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
