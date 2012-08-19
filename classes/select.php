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
 * The *Select classes intend to make the data fetching for a Meta object shorter in lines.
 *
 * You define "handlers" who handle specific fields, and depdending of the requestes fields to be fetched,
 * the handlers() method shall call the correct handler.
 *
 * In order to factorize code, a default handler_main() is already present (but can be overriden)
 * and tries to fetch datas for the "main" table linked to the object in the corresponding *Schema.
 *
 * You will find 2 helper_* metods, aiming to factorise code when fetching 1-line = 1-object datas
 * for helper_main() and when fetching a Collection of related object for helper_collection().
 *
 */
abstract class Select
{
    protected $fields = null;
    protected $subs = null;
    protected $callback = null;

    protected $schema = null;

    abstract public function className();
    abstract protected function handlers();

    public function __construct(array $fields, $subs = null, $callback = null)
    {
        $this->fields = $fields;
        $this->subs = $subs;
        $this->callback = $callback;

        $this->schema = Schema::get($this->className());
    }

    /**
     * Convert an array('table'=>array(columns)) to a sql columns list
     * @param array $cols
     * @return string comma-separated columns
     */
    protected static function arrayToSqlCols(array $cols)
    {
        $sql_columns = array();
        foreach($cols as $table => $fields) {
            foreach ($fields as $field) {
                $sql_columns[] = (($table == -1) ? $field : ($table . '.' . $field));
            }
        }
        return implode(', ', $sql_columns);
    }

    /**
     * Build an unique hash for the select
     * @deprecated
     * @return type
     */
    public function hash() {
        $str = implode($this->fields);
        if (!empty($this->subs)) {
            $str .= serialize($this->subs);
        }
        return md5($str);
    }

    /**
     * Main entry point to select fields
     * @param array|Collection $metas metaobjects to be selected
     * @return Collection upated $metas
     * @throws Exception if an error happened
     */
    public function select($metas) {
        if (empty($metas))
            return;

        if (is_array($metas)) {
            $metas = Collection::fromArray($metas);
        }

        $tobefetched = $this->fields;
        $handlers = $this->handlers();
        foreach ($handlers as $handler => $fields) {
            $intersect = array_intersect($fields, $tobefetched);
            if (!empty($intersect)) {
                $tobefetched = array_diff($tobefetched, $intersect);
                $handler = 'handler_' . $handler;
                $this->$handler($metas, $intersect);
            }
        }

        if (!empty($tobefetched)) {
            throw new Exception("Some fields (" . implode(', ', $tobefetched) . ")"
                . " couldn't be fetched in class " . $this->className());
        }

        if (is_callable($this->callback)) {
            $cb = $this->callback;
            $cb($metas);
        }
        return $metas;
    }

    /**
     * Default handler for basic fields
     * @param Collection $metas
     * @param array $fields
     */
    protected function handler_main(Collection $metas, array $fields) {
        $table = $this->schema->table();
        $as = $this->schema->tableAs();
        $cols = array($as => $fields);
        $joins = array();

        $this->helper_main($metas, $cols, $joins);
    }

    protected function handler_collections(Collection $metas, array $fields) {
        foreach ($fields as $field) {
            if (!$this->schema->isCollection($field)) {
                throw new Exception("Select collections on something not a collections");
            }
            // Call help for each field
            $this->helper_collection($metas, $field);
        }
    }

    protected function helper_main(Collection $metas, array $cols, array $joins) {
        $table = $this->schema->table();
        $as = $this->schema->tableAs();
        $id = $this->schema->id();

        $sql_fields = self::arrayToSqlCols($cols);
        $sql_joins = PlSqlJoin::formatJoins($joins, array());

        $collections = array();
        foreach ($cols as $fields) {
            foreach ($fields as $field) {
                if ($this->schema->isCollection($field)) {
                    // TODO: is this code used ?
                    //$collections[$field] = new Collection($this->schema->collectionType($field));
                    throw new Exception("Oops, there is a main handler for collections now ?");
                } elseif (!empty($this->subs) && array_key_exists($field, $this->subs)) {
                    $collections[$field] = new Collection($this->schema->objectType($field));
                }
            }
        }

        $iter = XDB::iterator("SELECT  $as.$id AS id, $sql_fields
                                 FROM  $table AS $as
                                       $sql_joins
                                WHERE  $as.$id IN {?}", $metas->ids());

        while ($datas = $iter->next()) {
            foreach ($datas as $key => $value) {
                if ($this->schema->isObject($key)) {
                    $class = $this->schema->objectType($key);
                    $datas[$key] = new $class($value);
                }
                if (array_key_exists($key, $collections) && $value !== null) {
                    $datas[$key] = $collections[$key]->addget($value);
                }
                if ($value === null) {
                    /*
                     * /!\ Null in the DB means false in here.
                     * Therefore Boolean fields must *not* be nullable !
                     */
                    $datas[$key] = false;
                }
            }
            $metas->get($datas['id'])->fillFromArray($datas);
        }

        foreach ($collections as $field => $collection) {
            $collection->select($this->subs[$field]);
        }
    }

    /**
     * Select a collection from the database
     *
     * @param Collection $metas The objects to select
     * @param string $field collection field
     */
    protected function helper_collection(Collection $metas, $field) {
        // Retrieve link attributes
        list($l_className, $table, $l_id, $id) = $this->schema->collectionType($field);

        $_metas = array();
        foreach($metas as $meta) {
            $_metas[$meta->id()] = new Collection($l_className);
        }

        // Get collection ids
        $iter = XDB::iterRow("SELECT  $id, $l_id
                                FROM  $table
                               WHERE  $id IN {?}", $metas->ids());

        $linkeds = new Collection($l_className);
        while (list($id, $l_id) = $iter->next()) {
            $linked = $linkeds->addget($l_id);
            $_metas[$id]->add($linked);
        }

        // Update metas
        foreach ($metas as $meta) {
            $meta->fillFromArray(array($field => $_metas[$meta->id()]));
        }

        if (!empty($this->subs[$field])) {
            $linkeds->select($this->subs[$field]);
        }
    }

    /**
     * Select an FlagSet from the database into a field
     *
     * @param Collection $metas The objects to select
     * @param string $field An FlagSet field
     */
    protected function helper_flagset(Collection $metas, $field) {
        $_metas = array();
        foreach($metas as $meta) {
            $_metas[$meta->id()] = new PlFlagSet();
        }

        // Query database according to the schema
        $id = $this->schema->id();
        list($table, $column) = $this->schema->flagsetType($field);
        $iter = XDB::iterRow("SELECT  $id, $column
                                FROM  $table
                               WHERE  $id IN {?}", $metas->ids());

        // Put database result in a id => array(l_col) fashion
        while (list($id, $value) = $iter->next()) {
            $_metas[$id]->addFlag($value);
        }

        // Fill metas
        foreach ($metas as $meta) {
            $meta->fillFromArray(array($field => $_metas[$meta->id()]));
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
