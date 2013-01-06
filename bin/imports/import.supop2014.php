#!/usr/bin/php -q
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

/**
 * Import promos 2013 and 2014 of IOGS
 */

require_once dirname(__FILE__) . '/create.account.inc.php';

$f = fopen(dirname(__FILE__) . '/supops.csv', 'rb') or die("No data file\n");
$formation = Formation::from('iogs');
$accounts_studies = array();

while (!feof($f)) {
    $data = fgetcsv($f, 1024, ';');
    if ($data === false)
        continue;
    list($promo, $hruid, $lastname, $firstname, $email) = $data;

    $promo = (int) $promo;
    $year_in = $promo - 2;
    $year_out = $promo;

    if (isset($accounts_studies[$hruid])) {
        // Extend study
        list($u, $year_in2, $year_out2, $promo2) = $accounts_studies[$hruid];
        $year_in = min($year_in, $year_in2);
        $year_out = max($year_out, $year_out2);
        $promo = max($promo, $promo2);
        echo "Update study for " . $u->hruid() . ": $year_in to $year_out\n";
        $u->updateStudy($formation, $hruid, $year_in, $year_out, $promo);
        $accounts_studies[$hruid] = array($u, $year_in, $year_out, $promo);
    } elseif (User::fromId($hruid) !== false) {
        die("Error: user $hruid already exists !\n");
    } else {
        // Create user
        $u = create_user(array(
            'hruid' => $hruid,
            'lastname' => $lastname,
            'firstname' => $firstname,
            'email' => $email));
        if ($u === null) {
            echo "Unable to create user $hruid\n";
        } else {
            $u->addStudy($formation, $year_in, $year_out, $promo, $hruid);
        }
        $accounts_studies[$hruid] = array($u, $year_in, $year_out, $promo);
    }
}
