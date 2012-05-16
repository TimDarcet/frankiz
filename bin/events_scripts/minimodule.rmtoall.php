#!/usr/bin/php -q
<?php
/***************************************************************************
 *  Copyright (C) 2012 Binet Réseau                                        *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                      *
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
/**
 * Activate timeleft inimodue for every user
 */

require_once(dirname(__FILE__) . '/../connect.db.inc.php');

// Get all users
$userfilter = new UserFilter(new UFC_Group(Group::from('on_platal')));
$users = $userfilter->get();
$users->select(UserSelect::minimodules());
$users->select(UserSelect::base());

// Get timeleft minimodules
$m = FrankizMinimodule::get($argv[1]);

foreach ($users as $u) {
    $minimodules = $u->minimodules();
    if (in_array($argv[1], $minimodules)) {
        echo 'Remove minimodule for ' . $u->login() . PHP_EOL;
        $u->removeMinimodule($m);
    }
}
?>
