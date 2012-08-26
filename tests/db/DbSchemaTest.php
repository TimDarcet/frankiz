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

require_once(dirname(__FILE__) . '/../../include/test.inc.php');

class DbSchemaTest extends PHPUnit_Framework_TestCase
{
    /**
     * Get all *Schema classes
     */
    public function getSchemaClasses()
    {
        $files = glob(dirname(__FILE__) . '/../../classes/*.php');
         // Return an array of parameters
        $data = array();
        foreach ($files as $f) {
            $classname = substr(basename($f), 0, -4);
            if (class_exists($classname . 'Schema')) {
                $data[] = array(Schema::get($classname));
            }
        }
        return $data;
    }

    /**
     * @dataProvider getSchemaClasses
     */
    public function testSchema(Schema $schema) {
        // Known special keys : unique, columns
        $known_keys = array(
            'castes.gid_rights' => array(true, 'group', 'rights'),
            'formations.domain' => array(false, 'domain'),
            'ips.rid' => array(false, 'room')
        );
        // Retrieve current indexes
        $dbindexes = XDB::rawFetchAllAssoc('SHOW INDEX FROM ' . $schema->table());
        $primary_key = $schema->id();
        $secondary_key = $schema->fromKey();
        $hasPrimary = false;
        $hasSecondary = ($primary_key == $secondary_key);
        foreach ($dbindexes as $dbindex) {
            $keyname = $dbindex['Key_name'];
            $colname = $dbindex['Column_name'];
            $seqIndex = (integer)$dbindex['Seq_in_index'];
            $isUnique = ($dbindex['Non_unique'] == 0);
            $this->assertEquals($schema->table(), $dbindex['Table']);
            $this->assertNotEmpty($keyname);
            $this->assertNotEmpty($colname);
            $this->assertEquals('', $dbindex['Null'],
                "Key $keyname is nullables");
            $this->assertEquals('A', $dbindex['Collation'],
                "Key $keyname has not Collation A");
            $this->assertEquals('BTREE', $dbindex['Index_type'],
                "Key $keyname is not BTree");

            if ($keyname == 'PRIMARY') {
                // Primary key
                $hasPrimary = true;
                $this->assertEquals($primary_key, $colname,
                    "Invalid column for primary key");
                $this->assertEquals(1, $seqIndex, "Multi-column primary key");
                $this->assertTrue($isUnique, "Primary key is not unique");
            } elseif ($keyname == $secondary_key) {
                // Secondary key
                $hasSecondary = true;
                $this->assertEquals($secondary_key, $colname,
                    "Invalid column for secondary key");
                $this->assertEquals(1, $seqIndex, "Multi-column secondary key");
                $this->assertTrue($isUnique, "Secondary key is not unique");
            } else {
                $keyname = $schema->table() . '.' . $keyname;
                $this->assertArrayHasKey($keyname, $known_keys);
                $key = $known_keys[$keyname];
                $this->assertEquals($key[0], $isUnique);
                $this->assertArrayHasKey($seqIndex, $key);
                $this->assertEquals($key[$seqIndex], $colname);
            }
        }

        $this->assertTrue($hasPrimary, "No PRIMARY key found");
        $this->assertTrue($hasSecondary, "No secondary key '$secondary_key' found");
    }
}
