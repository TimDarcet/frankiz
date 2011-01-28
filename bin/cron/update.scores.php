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

/*
 * This script updates the score field of the groups.
 * SCORE = | EVERYBODY |
 */

require '../connect.db.inc.php';

XDB::execute('UPDATE  groups AS g
                 SET  g.score = (SELECT  COUNT(cu.uid)
                                   FROM  castes_users AS cu
                             INNER JOIN  castes AS c ON c.cid = cu.cid
                                  WHERE  c.gid = g.gid AND c.rights = {?})',
                                            (string) Rights::everybody());

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
