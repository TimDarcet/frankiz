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
 * This script imports accounts from a SQL table
 * It's intended to be adaptated each time we have to create lots of accounts
 * 
 */
 
$file = "/home/2009/matthieu/import/data.csv";
$photos_folder = "/home/2009/matthieu/import/photos";

//index of data
$lastname = 0;
$firstname = 1;
$nationality = 2;
$birthdate = 3;
$email = 4;
$formation = 5;
$promo = 6;
$year_in = 7;
$year_out = 8;
$gender = 11;
$room_id = 9;
$sport = 10;


require dirname(__FILE__) . '/../connect.db.inc.php';
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
    $t = microtime(true);
    // Creating the User
    $u = new User();
    $u->insert();
//    $u->password($datas['passwd'], false);
    $u->firstname(ucwords(strtolower(conv($datas[$firstname]))));
    $u->lastname(ucwords(strtolower(conv($datas[$lastname]))));
//    $u->nickname(conv($datas['surnom']));
    $u->birthdate(new FrankizDateTime($datas[$birthdate]));
    if($gender != null)
        $u->gender(($datas[$gender] == 'F') ? User::GENDER_FEMALE : User::GENDER_MALE);
    if (!empty($datas[$email])) {
        $u->email($datas[$email]);
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
    
    $login = "";
    if(preg_match('!@institutoptique.fr!',$datas[$email]))
        $login = str_replace('@institutoptique.fr','',$datas[$email]);
    else
        $login = str_replace('@polytechnique.edu','',$datas[$email]);
    
        
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
        
        case "Supop": // PEI
        $formation_id = 6;
        break;

        default: // Master
        $formation_id = 3; 
    }

    $u->login($login);
    $u->hruid($login);
    $u->addStudy($formation_id, ($year_in == null ? (int) $datas[$promo]: (int) $datas[$year_in]), ($year_out == null ? (int) $datas[$promo] + 4 : (int) $datas[$year_out]), $datas[$promo], $login);
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
    if ($sport != null) {
        $nf = new GroupFilter(new GFC_Name('sport_' . conv_name($datas[$sport])));
        $n = $nf->get(true);
        if($n){
            $n->select(GroupSelect::castes());
            $n->caste(Rights::member())->addUser($u);
        }
    }


    //Photo
    /*$works = false;
    $suffix = '';
    $folder = $photos_folder;
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
*/
    $k++;
    echo 'User ' . str_pad($k, 4, '0', STR_PAD_LEFT) . '/' . $users . ' : '
         . str_pad($u->id(), 5, '0', STR_PAD_LEFT) . ' - ' . $datas['promo'] . ' - '
         . substr(microtime(true) - $t, 0, 5) . '   ' . $u->login() . "\n";
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
