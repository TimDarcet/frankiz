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



if (!empty($argv[1]) && !empty($argv[2])) {
    $dba = $argv[1];
    $dbb = $argv[2];
    echo "Comparing DB '$dba' with '$dbb' \n";
} else {
    $dba = $globals->dbdb;
    $dbb = 'compare';
    // Clean up the temporary 'compare' database and populate it
    try { XDB::execute('DROP DATABASE compare'); } catch (Exception $e) {}
    XDB::execute('CREATE DATABASE compare DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci');
    XDB::execute('USE compare');
    $tables = glob("2.0.0_to_3.0.0/sql/*.sql");
    foreach ($tables as $table) {
        echo exec('mysql -h ' . $globals->dbhost . ' -u ' . $globals->dbuser .
                  ' -p' . $globals->dbpwd . ' compare < ' . $table);
    }
    echo "Comparing actual DB '$dba' with the theoritical one \n";
}



// Fetch the tables names from the DBs
XDB::execute("USE $dba");
$a_tables = XDB::query('SHOW TABLES');
$a_tables = $a_tables->fetchColumn();
XDB::execute("USE $dbb");
$b_tables = XDB::query('SHOW TABLES');
$b_tables = $b_tables->fetchColumn();



// Compare the tables
foreach ($b_tables as $table) {
    echo "Table '$table' ";

    if (!in_array($table, $a_tables)) {
        echo "missing \n";
    } else {
        echo "\n";

        $theoric = XDB::query('DESCRIBE ' . $table)->fetchAllAssoc('Field');
        $actual  = XDB::query('DESCRIBE ' . $globals->dbdb . '.' . $table)->fetchAllAssoc('Field');

        foreach($theoric as $field => $infos) {
            if (!array_key_exists($field, $actual)) {
                echo "- '$field' " . $infos['Type'] . " \n";
            } else {
                if ($infos['Type'] != $actual[$field]['Type']) {
                    echo "* '$field' " . $infos['Type'] . " differs from " . $actual[$field]['Type'] . " \n";
                }
                if ($infos['Null'] != $actual[$field]['Null']) {
                    echo "* '$field' " . $infos['Type'] . " differs for NULL \n";
                }
                if ($infos['Key'] != $actual[$field]['Key']) {
                    echo "* '$field' " . $infos['Type'] . " differs for KEY \n";
                }
                if ($infos['Default'] != $actual[$field]['Default']) {
                    echo "* '$field' " . $infos['Type'] . " differs for Default \n";
                }
                if ($infos['Extra'] != $actual[$field]['Extra']) {
                    echo "* '$field' " . $infos['Type'] . " differs for Extra \n";
                }
            }
        }

        foreach($actual as $field => $infos) {
            if (!array_key_exists($field, $theoric)) {
                echo "+ '$field' " . $infos['Type'] . " \n";
            }
        }

    }
}

foreach($a_tables as $table) {
    if (!in_array($table, $b_tables)) {
        echo "Table '$table' supernumerary \n";
    }
}