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
 * This script creates and updates the studies
 */

require 'connect.db.inc.php';

$iter = XDB::iterRow('SELECT formation_id, abbrev, label FROM  formations');

while (list($formation_id, $abbrev, $label) = $iter->next()) {
    $name = 'formation_' . $abbrev;

    $gf = new GroupFilter(new GFC_Name($name));
    $g = $gf->get(true);
    if ($g instanceof Group) {
        $g->select(Group::SELECT_CASTES);
        $c = $g->caste(Rights::member());
        $c->select(Caste::SELECT_BASE)->compute();
        echo $label . ' (' . $g->id() . ") updated\n";
    } else {
        $f = new UserFilter(new UFC_Study(new Formation($formation_id)));

        $g = new Group();
        $g->insert();
        $c = $g->caste(Rights::member());
        $c->userfilter($f);
        $g->ns(Group::NS_STUDY);
        $g->name($name);
        $g->label($label);

        echo $label . " created\n";
    }
}

echo 'Fini' . "\n";

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
