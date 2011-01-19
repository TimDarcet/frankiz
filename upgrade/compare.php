#!/usr/bin/php -q
<?php
/***************************************************************************
 *  Copyright (C) 2003-2010 Polytechnique.org                              *
 *  http://opensource.polytechnique.org/                                   *
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

require '../bin/connect.db.inc.php';

echo "Comparing actual DB '" . $globals->dbdb . "' with the last theoric DB \n";

// Fetch the tables of the actual DB
$a_tables = XDB::query('SHOW TABLES');
$a_tables = $a_tables->fetchColumn();


// Clean up the temporary 'compare' database and populate it
try { XDB::execute('DROP DATABASE compare'); } catch (Exception $e) {}
XDB::execute('CREATE DATABASE compare DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci');
XDB::execute('USE compare');
$tables = glob("current/sql/*.sql");
foreach ($tables as $table) {
    echo exec('mysql -h ' . $globals->dbhost . ' -u ' . $globals->dbuser .
              ' -p' . $globals->dbpwd . ' compare < ' . $table);
}

// Compare the actual and the theoric databases
$t_tables = XDB::query('SHOW TABLES');
$t_tables = $t_tables->fetchColumn();
foreach ($t_tables as $table) {
    echo 'Table ' . $table;
    if (!in_array($table, $a_tables)) {
        echo ' missing';
    }
    echo "\n";

    $theoric = XDB::query('DESCRIBE ' . $table)->fetchAllAssoc('Field');
    $actual  = XDB::query('DESCRIBE ' . $globals->dbdb . '.' . $table)->fetchAllAssoc('Field');

    foreach($theoric as $field => $infos) {
        if (!array_key_exists($field, $actual)) {
            echo '- "' . $field . '" missing' . "\n";
        } else {
            if ($infos['Type'] != $actual[$field]['Type'])
            echo '- "' . $field . '" differs: ' . $infos['Type'] . ' VS ' . $actual[$field]['Type'] . "\n";
        }
    }

    foreach($actual as $field => $infos) {
        if (!array_key_exists($field, $theoric)) {
            echo '- "' . $field . '" supernumerary' . "\n";
        }
    }
}

