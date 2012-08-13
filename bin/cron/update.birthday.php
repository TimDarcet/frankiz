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

require_once(dirname(__FILE__) . '/../connect.db.inc.php');

XDB::execute('UPDATE  account
                 SET  next_birthday = birthdate
               WHERE  (birthdate != 0 AND birthdate IS NOT NULL AND next_birthday = 0)');

$it = 0;
do {
    XDB::execute('UPDATE  account
                     SET  next_birthday = DATE_ADD(next_birthday, INTERVAL 1 YEAR)
                   WHERE  (next_birthday != 0 AND next_birthday IS NOT NULL AND next_birthday < CURDATE())');
    ++$it;
    $affected = XDB::affectedRows();
    // echo "Iteration $it => $affected changes\n";
} while ($affected > 0);

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
