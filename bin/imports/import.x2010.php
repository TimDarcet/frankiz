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
    $str = str_replace(array('à', 'ä', 'â'), 'a', $str);
    $str = str_replace(array('î', 'ï'), 'i', $str);
    $str = str_replace(array('ç'), 'c', $str);
    return preg_replace("/[^a-z0-9_-]/", "", $str);
}

$gf = new GroupFilter(new GFC_Name('tol'));
$group = $gf->get(true)->select(GroupSelect::castes());
$tol_caste = $group->caste(Rights::everybody());


$iter = XDB::iterator('SELECT  nom, prenom, sexe, nationalite,
                               promo, email,
                               SUBSTR(email, 1, LENGTH(email) - 18) AS hruid
                         FROM  dev.temp_tol_2k10');

$users = $iter->total();
$k = 0;
while ($datas = $iter->next()) {
    $t = microtime(true);
    // Creating the User
    $u = new User();
    $u->insert();
//    $u->password($datas['passwd'], false);
    $u->firstname(ucwords(strtolower(conv($datas['prenom']))));
    $u->lastname(ucwords(strtolower(conv($datas['nom']))));
//    $u->nickname(conv($datas['surnom']));
//    $u->birthdate(new FrankizDateTime($datas['date_nais']));
    $u->gender(($datas['sexe'] == 'F') ? User::GENDER_FEMALE : User::GENDER_MALE);
    if (!empty($datas['email'])) {
        $u->email($datas['email']);
    }
    $u->skin('default');
    
    //setting default minimodules
    XDB::execute('INSERT INTO users_minimodules (uid,name,col,row) VALUES
                              ({?}, "birthday",     "COL_LEFT",   0 )',$u->id());
    XDB::execute('INSERT INTO users_minimodules (uid,name,col,row) VALUES
                              ({?}, "days",         "COL_LEFT",   1 )',$u->id());
    XDB::execute('INSERT INTO users_minimodules (uid,name,col,row) VALUES
                              ({?}, "ik",           "COL_LEFT",   2 )',$u->id());
    XDB::execute('INSERT INTO users_minimodules (uid,name,col,row) VALUES
                              ({?}, "qdj",          "COL_MIDDLE", 0 )',$u->id());
    XDB::execute('INSERT INTO users_minimodules (uid,name,col,row) VALUES
                              ({?}, "jtx",          "COL_MIDDLE", 1 )',$u->id());
    XDB::execute('INSERT INTO users_minimodules (uid,name,col,row) VALUES
                              ({?}, "groups",       "COL_RIGHT",  0 )',$u->id());
    XDB::execute('INSERT INTO users_minimodules (uid,name,col,row) VALUES
                              ({?}, "quicksearch",  "COL_FLOAT",  0 )',$u->id());
    XDB::execute('INSERT INTO users_minimodules (uid,name,col,row) VALUES
                              ({?}, "activity",     "COL_FLOAT",  1 )',$u->id());
    XDB::execute('INSERT INTO users_minimodules (uid,name,col,row) VALUES
                              ({?}, "news",         "COL_FLOAT",  2 )',$u->id());
    XDB::execute('INSERT INTO users_minimodules (uid,name,col,row) VALUES
                              ({?}, "todo",         "COL_FLOAT",  3 )',$u->id());
                          
                          
/*    try {
        $u->cellphone(new Phone($datas['portable']));
    } catch(Exception $e) {
        echo 'Error for phone ' . $datas['portable'] . "\n";
    }*/
//    $u->poly($datas['login']);

    // Linking with the room
/*    $room = $datas['piece_id'];
    if (!empty($room)) {
        if (preg_match('/^[0-9]+[a-z]?$/', $room)) {
            $room = 'X' . $room;
        }
        if ($room = Room::from($room)) {
            $u->addRoom($room);
        } else {
            echo 'Error for room ' . $datas['piece_id'] . "\n";
        }
    }*/

    if (!empty($datas['hruid'])) {
        $login = $datas['hruid'];
//        switch ($datas['programme']) {
//            case 1: // X
            $formation_id = 1;
/*            break;

            case 4: // Doctorant
            $formation_id = 4;
            break;

            case 5: // PEI
            $formation_id = 5;
            break;

            default: // Master
            $formation_id = 3; 
        }*/
    } else {
        $login = $datas['login'] . '.' . $datas['promo'];
        $formation_id = 2;
    }
    $u->login($login);
    $u->addStudy($formation_id, $datas['promo'], (int) $datas['promo'] + 4, $datas['promo'], $login);

    // Linking with the nationality
    if (!empty($datas['nationalite'])) {
        $nf = new GroupFilter(new GFC_Name('nation_' . conv_name($datas['nationalite'])));
        $n = $nf->get(true);
        if($n){
            $n->select(GroupSelect::castes());
            $n->caste(Rights::member())->addUser($u);
        }
    }

    // Linking with the sport
/*    if (!empty($datas['sport'])) {
        $nf = new GroupFilter(new GFC_Name('sport_' . conv_name($datas['sport'])));
        $n = $nf->get(true);
        $n->select(GroupSelect::castes());
        $n->caste(Rights::member())->addUser($u);
    }
*/

    //Photo
    $works = false;
    $suffix = '_original';
    $folder = '/home/2009/matthieu/photos';
    $original = true;
    $path = $folder . '/' . $u->hruid() . $suffix . '.jpg';
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
    }

    $k++;
    echo 'User ' . str_pad($k, 4, '0', STR_PAD_LEFT) . '/' . $users . ' : '
         . str_pad($u->id(), 5, '0', STR_PAD_LEFT) . ' - ' . $datas['promo'] . ' - '
         . substr(microtime(true) - $t, 0, 5) . '   ' . $u->login() . "\n";
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
