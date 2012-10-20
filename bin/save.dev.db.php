#!/usr/bin/php -q
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
 * This script saves database information into outputed SQL lines
 */

require_once(dirname(__FILE__) . '/connect.db.inc.php');

// DELETE confidential data
echo XDB::format('UPDATE account SET password="", hash="", hash_rss="";') . PHP_EOL;
echo 'DELETE FROM log_events;' . PHP_EOL;
echo 'DELETE FROM log_sessions;' . PHP_EOL;
echo 'DELETE FROM log_last_sessions;' . PHP_EOL;
echo 'DELETE FROM mails;' . PHP_EOL;
echo 'DELETE FROM msdnaa_keys;' . PHP_EOL;
echo 'DELETE FROM remote;' . PHP_EOL;
echo 'DELETE FROM remote_groups;' . PHP_EOL;

// Get this data from bdd
$iter = XDB::iterRow('SELECT  hruid, password
                        FROM  account
                       WHERE  password != {?}', '');
while (list($hruid, $password) = $iter->next()) {
    echo XDB::format('UPDATE account SET password = {?} WHERE hruid = {?};', $password, $hruid) . PHP_EOL;
}

// Save dev's remote sites
$remote_cols = array('site', 'privkey', 'label', 'rights');
$remotes = Remote::selectAll(RemoteSelect::groups());
foreach ($remotes as $r) {
    $query = XDB::format('INSERT INTO remote SET remid = {?}', $r->id());
    foreach ($remote_cols as $c) {
        $query .= XDB::format(', ' . $c . ' = {?}', $r->$c());
    }
    echo $query . ';' . PHP_EOL;
    foreach ($r->groups() as $g) {
        echo XDB::format('INSERT INTO remote_groups SET remid = {?}, gid = {?};', $r->id(), $g->id()) . PHP_EOL;
    }
}
