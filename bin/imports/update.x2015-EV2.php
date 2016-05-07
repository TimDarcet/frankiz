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

/*
 * This script imports accounts from a SQL table
 * It's intended to be adaptated each time we have to create lots of accounts
 *
 */
set_time_limit(0);
$file = "/home/2014/varal7/X2015-EV2-part2.csv";
$photos_folder = "";

//index of data
$lastname = 1;
$firstname = 2;
$nationality = 4;
$birthdate = 3;
$email = 5;
//$formation = 11;
//$promo = 5;
//$year_in = 0;
//$year_out = 1;
$gender = 0;
//$room_id = 9;
//$sport = 7;
//$photo_file = 8;


require_once(dirname(__FILE__) . '/../connect.db.inc.php');
$globals->debug = 0;

function conv($str) {
    $str = html_entity_decode($str, ENT_QUOTES);
    $str = str_replace("&apos;", "'", $str);
    return $str;
}

function conv_name($str)
{
    $str = str_replace(array('É'), 'e', $str);
    $str = strtolower(conv($str));
    $str = str_replace(array('é', 'è', 'ë', 'ê'), 'e', $str);
    $str = str_replace(array('ü', 'û'), 'u', $str);
    $str = str_replace(array('à', 'ä', 'â', 'á'), 'a', $str);
    $str = str_replace(array('î', 'ï'), 'i', $str);
    $str = str_replace(array('ç','Ç'), 'c', $str);
    return preg_replace("/[^a-z0-9_-]/", "", $str);
}

$gf = new GroupFilter(new GFC_Name('tol'));
$group = $gf->get(true)->select(GroupSelect::castes());
$tol_caste = $group->caste(Rights::everybody());


$fic = fopen($file, 'rb');
$k = 0;

for ($datas = fgetcsv($fic, 1024, ','); !feof($fic); $datas = fgetcsv($fic, 1024, ',')) {
    print_r($datas);
    //echo preg_replace("`^([0-9]{1,2})/([0-9]{2})`", "$2/$1", trim($datas[$birthdate]));
    //exit();
    $t = microtime(true);
    // Creating the User
    $u = new User();
    $u->insert();
//    $u->password($datas['passwd'], false);
    $u->firstname(ucwords(strtolower(conv($datas[$firstname]))));
    $u->lastname(ucwords(strtolower(conv($datas[$lastname]))));
//    $u->nickname(conv($datas['surnom']));
    $u->birthdate(new FrankizDateTime(trim($datas[$birthdate])));
    if($gender != null)
        $u->gender(($datas[$gender] == 'Mlle') ? User::GENDER_FEMALE : User::GENDER_MALE);
    if (!empty($datas[$email])) {
        $u->email($datas[$email]);
    }
    $u->skin('default');

    //setting default minimodules
    $u->select(UserSelect::minimodules());
    $u->copyMinimodulesFromUser(0);


/*    try {
        $u->cellphone(new Phone($datas['portable']));
    } catch(Exception $e) {
        echo 'Error for phone ' . $datas['portable'] . "\n";
    }*/
//    $u->poly($datas['login']);

    // Linking with the room
    if($room_id != null){
        $room = $datas[$room_id];
        if (!empty($room)) {
            if (preg_match('/^[0-9]+[a-z]?$/', $room)) {
                $room = 'X' . $room;
            }
            if ($room = Room::from($room)) {
                $u->addRoom($room);
            } else {
                echo 'Error for room ' . $datas[$room_id] . "\n";
            }
        }
    }

    /*$login = "";
    if(preg_match('!@institutoptique.fr!',$datas[$email]))
        $login = str_replace('@institutoptique.fr','',$datas[$email]);
    else
        $login = str_replace('@polytechnique.edu','',$datas[$email]);
*/
        $login = str_replace('@polytechnique.edu','',$datas[$email]);
    $formation_id = 1;
    
    /*
    switch ($datas[$formation]) {
        case "X": // X
        $formation_id = 1;
        break;

        case "Doctorant": // Doctorant
        $formation_id = 4;
        break;

        case "PEI": // PEI
        $formation_id = 5;
        break;

        case "Supop": // Supop
        $formation_id = 6;
        break;

        default: // Master
        $formation_id = 3;
    }
    */
    // they are X2015
    $u->login($login);
    $u->hruid($login);
    // (int) $datas[$promo] = 2012
    $u->addStudy($formation_id, ($year_in === null ? 2015 : (int) $datas[$year_in]), ($year_out === null ? 2015 + 4 : (int) $datas[$year_out]), 2015, $login);
    // Linking with the nationality
    if($nationality != null){
        if (!empty($datas[$nationality])) {
            echo(conv_name($datas[$nationality]));
            $nf = new GroupFilter(new GFC_Name('nation_' . conv_name($datas[$nationality])));
            $n = $nf->get(true);
            if($n){
                $n->select(GroupSelect::castes());
                $n->caste(Rights::member())->addUser($u);
            }
        }
    }

    // Linking with the sport
    /*
    if ($sport != null) {
        $nf = new GroupFilter(new GFC_Name('sport_' . conv_name($datas[$sport])));
        $n = $nf->get(true);
        if($n){
            $n->select(GroupSelect::castes());
            $n->caste(Rights::member())->addUser($u);
        }
    }*/

    //Adding the promo group
        $nf = new GroupFilter(new GFC_Name('promo_' . conv_name(2015)));
        $n = $nf->get(true);
        if($n){
            $n->select(GroupSelect::castes());
            $n->caste(Rights::member())->addUser($u);
        }
    
    //Adding polytechnicien as a formation *group* (sic)
        $nf = new GroupFilter(new GFC_Name('formation_x'));
        $n = $nf->get(true);
        if($n){
            $n->select(GroupSelect::castes());
            $caste = $n->caste(Rights::member());
            $caste->addSimpleUser($u);
        }

    //Photo
    /*
    $works = false;
    $suffix = '';
    $folder = $photos_folder;
    $original = true;
    $path = $folder . '/' . $datas[$photo_file];
    if (file_exists($path)) {
        $upload = FrankizUpload::fromFile($path);
        if ($upload->size() > 0) {
            try {
                $i = new FrankizImage();
                $i->insert();
                $i->caste($tol_caste);
                $i->label($u->firstname() . ' ' . $u->lastname());
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
    }*/

    $k++;
    echo 'User ' . str_pad($k, 4, '0', STR_PAD_LEFT) . '/xx : '
         . str_pad($u->id(), 5, '0', STR_PAD_LEFT) . ' - 2015 - '
         . substr(microtime(true) - $t, 0, 5) . '   ' . $u->login() . "\n";
}
$nf = new GroupFilter(new GFC_Name('formation_x'));
$n = $nf->get(true);
if($n){
    $n->select(GroupSelect::castes());
    $caste = $n->caste(Rights::member());
    $caste->bubble();
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
