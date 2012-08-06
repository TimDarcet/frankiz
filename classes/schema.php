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

/*
 * @class Schema
 *
 * The *Schema classes serves two purposes :
 *
 * 1. Very simple description of the "main" table related with the object,
 *    in order to support automatic getters & setters for the "simple" fields
 *
 *2.  Describe of the type of the fields in a Meta object,
 *    in order for the Select class to know how to build fetched datas
 */
abstract class Schema {
    /**
     * @return string Meta class name
     */
    abstract public function className();

    /**
     * @return string Database table name
     */
    abstract public function table();

    /**
     * @return string Table alias in SQL queries
     */
    abstract public function tableAs();

    /**
     * @return string Column ID in the database
     */
    abstract public function id();

    /**
     * @return array List of scalar fields
     */
    public function scalars() {
        return array();
    }

    /**
     * @return array List of object fields
     */
    public function objects() {
        return array();
    }

    /**
     * Return for each FlagSet field an link array in the following format:
     * array($table, $column), where
     * * $table is the name of a table used  for the link
     * * $column is the column name
     *
     * @return array List of array fields
     */
    public function flagsets() {
        return array();
    }

    /**
     * @return array List of collection fields
     */
    public function collections() {
        return array();
    }

    protected static $schemas = array();

    public static function get($className) {
        if (empty(self::$schemas[$className])) {
            $schemaName = $className . 'Schema';
            self::$schemas[$className] = new $schemaName();
        }
        return self::$schemas[$className];
    }

    public static function __callStatic($name, $arguments)
    {
        return self::get($name);
    }

    public function fields() {
        $scalars = array_fill_keys($this->scalars(), true);
        $objects = $this->objects();
        return array_merge($scalars, $objects);
    }

    public function has($field) {
        return $this->isScalar($field) ||
            $this->isObject($field) ||
            $this->isFlagset($field) ||
            $this->isCollection($field);
    }

    public function isScalar($field) {
        if (in_array($field, $this->scalars())) {
            return true;
        }
        return false;
    }

    public function isObject($field) {
        if (array_key_exists($field, $this->objects())) {
            return true;
        }
        return false;
    }

    public function isFlagset($field) {
        if (array_key_exists($field, $this->flagsets())) {
            return true;
        }
        return false;
    }

    public function isCollection($field) {
        if (array_key_exists($field, $this->collections())) {
            return true;
        }
        return false;
    }

    public function objectType($field) {
        $objects = $this->objects();
        if (empty($objects[$field])) {
            throw new Exception("$field is not an Object");
        }
        return $objects[$field];
    }

    public function flagsetType($field) {
        $flagsets = $this->flagsets();
        if (empty($flagsets[$field])) {
            throw new Exception("$field is not a FlagSet");
        }
        return $flagsets[$field];
    }

    public function collectionType($field) {
        $collections = $this->collections();
        if (empty($collections[$field])) {
            throw new Exception("$field is not a Collection");
        }
        return $collections[$field];
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
