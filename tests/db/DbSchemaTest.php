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

require_once(dirname(__FILE__) . '/../../include/test.inc.php');

class DbSchemaTest extends PHPUnit_Framework_TestCase
{
    /**
     * Get all Schema indexes which should be in the database
     * @return array array($indexes, $foreign_keys)
     */
    public function testSchemaIndexes()
    {
        $files = glob(dirname(__FILE__) . '/../../classes/*.php');
        // Known special keys
        $indexes = array(
            // PRIMARY keys
            'activities_participants.PRIMARY' => 'U id participant',
            'castes_dependencies.PRIMARY' => 'U cid type id',
            'castes_users.PRIMARY' => 'U cid uid',
            'formations_platal.PRIMARY' => 'U formation_id year',
            'images_sizes.PRIMARY' => 'U iid size',
            'log_actions.PRIMARY' => 'U id',
            'log_last_sessions.PRIMARY' => 'U uid',
            'log_sessions.PRIMARY' => 'U id',
            'mails.PRIMARY' => 'U id',
            'news_read.PRIMARY' => 'U uid news',
            'news_star.PRIMARY' => 'U uid news',
            'poly.PRIMARY' => 'U uid',
            'qdj_scores.PRIMARY' => 'U uid',
            'qdj_votes.PRIMARY' => 'U vote_id',
            'remote_groups.PRIMARY' => 'U remid gid',
            'rooms_groups.PRIMARY' => 'U rid gid',
            'rooms_users.PRIMARY' => 'U rid uid',
            'studies.PRIMARY' => 'U uid formation_id',
            'users_comments.PRIMARY' => 'U uid gid',
            'users_defaultfilters.PRIMARY' => 'U uid',
            'users_minimodules.PRIMARY' => 'U uid name',
            'wiki_version.PRIMARY' => 'U wid version',

            // Tuple unique keys
            'castes.gid_rights' => 'U group rights',
            'studies.forlife_formation' => 'U forlife formation_id',

            // Special keys which don't appear in schemas
            'account.skin' => 'skin',
            'users_minimodules.name' => 'name'
        );
        // Nullable indexes
        $nullables = array(
            'account.group',
            'account.original',
            'account.photo',
            'activities.origin',
            'groups.image',
            'msdnaa_keys.uid',
            'msdnaa_keys.gid',
            'links.image',
            'news.image',
            'news.origin',
        );
        // Foreign keys
        $foreign = array(
            // These keys don't appear in schemas
            'fk_account_skin' => 'account.skin skins.name',
            'fk_users_minimodules_name' => 'users_minimodules.name minimodules.name'
        );

        foreach ($files as $f) {
            $classname = substr(basename($f), 0, -4);
            if (!class_exists($classname . 'Schema')) {
                continue;
            }
            $schema = Schema::get($classname);

            // Read schema information
            $table = $schema->table();
            $primary_key = $schema->id();
            $secondary_key = $schema->fromKey();
            $this->assertNotEmpty($table);
            $this->assertNotEmpty($primary_key);
            $this->assertNotEmpty($secondary_key, "Secondary key of $table is Null");

            // U means Unique
            $indexes[$table . '.PRIMARY'] = 'U ' . $primary_key;
            if ($primary_key != $secondary_key) {
                $indexes[$table . '.' . $secondary_key] = 'U ' . $secondary_key;
            }

            // Objects
            foreach ($schema->objects() as $field => $objtype) {
                $fullname = $table . '.' . $field;
                $objtype = $schema->objectType($field);
                if (!strcasecmp($objtype, 'array')) {
                    continue;
                }
                $reflection = new ReflectionClass($objtype);
                if (!$reflection->isSubclassOf('Meta')) {
                    continue;
                }

                // Guess nullability of the field
                $flags = (in_array($fullname, $nullables) ? 'N ' : '');
                $indexes[$fullname] = $flags . $field;
                $objSchema = Schema::get($objtype);
                $foreign['fk_' . $table . '_' . $field] = $fullname . ' ' .
                    $objSchema->table() . '.' . $objSchema->id();
            }

            // Flagsets
            foreach ($schema->flagsets() as $field => $flagtype) {
                $flagtype = $schema->flagsetType($field);
                list($l_table, $l_col) = $flagtype;
                $indexes[$l_table . '.' . $primary_key] = $primary_key;
                $foreign['fk_' . $l_table . '_' . $primary_key] =
                    $l_table . '.' . $primary_key . ' ' .
                    $table . '.' . $primary_key;
            }


            // Collections
            foreach ($schema->collections() as $field => $coltype) {
                $coltype = $schema->collectionType($field);
                list($l_className, $l_table, $l_id, $id) = $coltype;
                $l_schema = Schema::get($l_className);

                // Add indexes, without overwriting existing ones,
                // if the link table is an index of the linked object table
                $l_schema_ids = array($l_schema->id(), $l_schema->fromKey());
                if (!in_array($id, $l_schema_ids)) {
                    $indexes[$l_table . '.' . $id] = $id;
                }
                if (!in_array($l_id, $l_schema_ids)) {
                    $indexes[$l_table . '.' . $l_id] = $l_id;
                }

                // Foreign keys
                $foreign['fk_' .  $l_table . '_' . $id] =
                    $l_table . '.' . $id . ' ' .
                    $table . '.' . $primary_key;
                if ($l_table != $l_schema->table()) {
                    $foreign['fk_' . $l_table . '_' . $l_id] =
                        $l_table . '.' . $l_id . ' ' .
                        $l_schema->table() . '.' . $l_schema->id();
                }
            }
        }
        ksort($indexes);
        ksort($foreign);
        return array($indexes, $foreign);
    }

    /**
     * Get all indexes and foreign keys
     * @return array array($indexes, $foreign_keys)
     */
    public function testDbIndexes()
    {
        global $globals;
        $tables = XDB::rawIterRow('SHOW TABLES');
        $indexes = array();
        $foreign = array();
        while (list($table) = $tables->next()) {
            $this->assertNotEmpty($table);
            $indexes_list = XDB::rawFetchAllAssoc('SHOW INDEX FROM ' . $table);
            $res_indexes = array();
            foreach ($indexes_list as $index) {
                $keyname = $index['Key_name'];
                // Strip foreign key format
                $fkprefix = 'fk_' . $table . '_';
                if (starts_with($keyname, $fkprefix)) {
                    $keyname = substr($keyname, strlen($fkprefix));
                }

                $colname = $index['Column_name'];
                $seqIndex = (integer)$index['Seq_in_index'];
                $this->assertEquals($table, $index['Table']);
                $this->assertNotEmpty($keyname);
                $this->assertNotEmpty($colname);
                $keyname = $table . '.' . $keyname;
                $this->assertTrue($seqIndex > 0,
                    "Invalid sequence for $keyname");
                $this->assertEquals('A', $index['Collation'],
                    "Key $keyname has not Collation A");
                $this->assertEquals('BTREE', $index['Index_type'],
                    "Key $keyname is not BTree");

                // Null, Unique flags
                $flags = (($index['Null'] != '') ? 'N' : '') .
                    (($index['Non_unique'] == 0) ? 'U' : '');
                if (!array_key_exists($keyname, $indexes)) {
                    $indexes[$keyname] = array(0 => $flags);
                } else {
                    $this->assertEquals($indexes[$keyname][0], $flags,
                        "Key $keyname has multiple flags");
                }
                $this->assertArrayNotHasKey($seqIndex, $indexes[$keyname],
                    "Bad key $keyname, col $seqIndex");
                $indexes[$keyname][$seqIndex] = $colname;
            }
        }
        foreach ($indexes as $keyname => $data) {
            $this->assertArrayHasKey(0, $data);
            $this->assertArrayHasKey(1, $data);
            if (count($data) == 2 && !ends_with($keyname, '.PRIMARY')) {
                $this->assertStringEndsWith('.' . $data[1], $keyname,
                    "Invalid key $keyname for " . $data[1]);
            }
            // Flatten data
            $indexes[$keyname] = trim(implode(' ', $data));
        }

        // Foreign keys
        $fkeys = XDB::iterRow(
            "SELECT  `constraint_name`,
                     CONCAT(`table_name`, '.', `column_name`) AS `key`,
                     CONCAT(`referenced_table_name`, '.', `referenced_column_name`) AS `rkey`
              FROM  `information_schema`.`key_column_usage` AS `kcu`
             WHERE  `table_schema` = {?} AND `referenced_table_name` IS NOT NULL",
            $globals->dbdb);
        while (list($name, $key, $rkey) = $fkeys->next()) {
            $foreign[$name] = $key . ' ' . $rkey;
        }

        ksort($indexes);
        ksort($foreign);
        return array($indexes, $foreign);
    }

    /**
     * @depends testSchemaIndexes
     * @depends testDbIndexes
     */
    public function testIndexes(array $schema, array $db) {
        // If there are more indexes than expected, just mark the test as incomplete
        if (count(array_diff($db[0], $schema[0])) && !count(array_diff($schema[0], $db[0]))) {
            $this->markTestIncomplete("There are unknown indexes in database.");
        }
        $this->assertEquals($schema[0], $db[0],
            "Invalid indexes");
    }

    /**
     * @depends testSchemaIndexes
     * @depends testDbIndexes
     */
    public function testForeignKeys(array $schema, array $db) {
        if (count(array_diff($db[1], $schema[1])) && !count(array_diff($schema[1], $db[1]))) {
            $this->markTestIncomplete("There are unknown foreign keys in database.");
        }
        $this->assertEquals($schema[1], $db[1],
            "Invalid foreign keys");
    }
}
