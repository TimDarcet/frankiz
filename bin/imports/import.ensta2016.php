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
 * Import promos 2015 of ENSTA
 */
require_once dirname(__FILE__) . '/create.account.inc.php';
$photos_folder = dirname(__FILE__); 
$f = fopen(dirname(__FILE__) . '/ensta2016.csv', 'rb') or die("No data file\n");
$formation = Formation::from('ensta');
$accounts_studies = array();
$promo="2016";
while (!feof($f)) {
    $data = fgetcsv($f, 1024, ';');
    if ($data === false)
        continue;
    list($lastname, $firstname,$phone,$birthdate,$photo, $email) = $data;

    $promo = (int) $promo;
    $year_in = $promo - 3;
    $year_out = 2016;
    $hruid = preg_replace('! - !', '-', $firstname . "." . $lastname);
    $hruid = preg_replace('!\s+!', '-', $hruid);
    $hruid = conv_noaccent(mb_strtolower(($hruid), 'UTF-8'));
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
        echo("Error: user $hruid already exists !\n");
	$u = User::fromId($hruid);
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
	//Photo
    
    $works = false;
    $suffix = '';
$gf = new GroupFilter(new GFC_Name('tol'));
$group = $gf->get(true)->select(GroupSelect::castes());
$tol_caste = $group->caste(Rights::everybody());
    $folder = $photos_folder;
    $original = true;
    $path = $folder . '/' . $photo;
    if (file_exists($path)) {
        $upload = FrankizUpload::fromFile($path);
        if ($upload->size() > 0) {
            try {
                $i = new FrankizImage();
                $i->insert();
                $i->caste($tol_caste);
                $i->label($firstname . ' ' . $lastname);
                $i->image($upload, false);
                if ($original) {
                    $u->original($i);
                } else {
                    $u->photo($i);
                }
                $works = true;
            } catch (Exception $e) {
                echo 'Error:' . $e->getMessage() . "\n";
            }
        }
    }
    if (!$works) {
        echo 'Not done: ' . $u->id() . ' - ' . $u->displayname() . ' - '. $path . "\n";
    }
    $u->birthdate(new FrankizDateTime(preg_replace("`^([0-9]{2})/([0-9]{2})`", "$2/$1", trim($birthdate))));
	try {
        $u->cellphone(new Phone(preg_replace("`^(33)(.*)`","0$2",trim($phone))));
    } catch(Exception $e) {
        echo 'Error for phone ' . $phone . "\n";
    }
	$u->email($email);
    }

