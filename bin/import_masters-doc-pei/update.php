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


$uf = new UserFilter(new PFC_And(new UFC_Study(new Formation(1)), new UFC_Promo(2010)));
$us = $uf->get()->select(UserSelect::tol());

            $nf = new GroupFilter(new GFC_Name('sport_judo'));
            $n = $nf->get(true);
            $n->select(GroupSelect::castes());

/*
XDB::execute('DELETE FROM users_minimodules WHERE uid = 0 AND col = "COL_FLOAT"');
XDB::execute('INSERT INTO users_minimodules (uid,name,col,row) VALUES
                          (0, "activate_account",  "COL_FLOAT",  0 )');
XDB::execute('INSERT INTO users_minimodules (uid,name,col,row) VALUES
                          (0, "quicksearch",       "COL_FLOAT",  1 )');
XDB::execute('INSERT INTO users_minimodules (uid,name,col,row) VALUES
                          (0, "links",             "COL_FLOAT",  2 )');
*/

$users = $us->count();
$k = 0;
foreach($us as $u) {
    $t = microtime(true);
    // Creating the User

//    $u->birthdate(new FrankizDateTime($datas['date_nais']));
    /*
    $u->skin('default');
    
    XDB::execute('DELETE FROM users_minimodules WHERE uid = {?}',$u->id());
                              
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
                              ({?}, "activate_account",  "COL_FLOAT",  0 )',$u->id());
    XDB::execute('INSERT INTO users_minimodules (uid,name,col,row) VALUES
                              ({?}, "quicksearch",  "COL_FLOAT",  1 )',$u->id());
    XDB::execute('INSERT INTO users_minimodules (uid,name,col,row) VALUES
                              ({?}, "activity",     "COL_FLOAT",  2 )',$u->id());
    XDB::execute('INSERT INTO users_minimodules (uid,name,col,row) VALUES
                              ({?}, "news",         "COL_FLOAT",  3 )',$u->id());
    XDB::execute('INSERT INTO users_minimodules (uid,name,col,row) VALUES
                              ({?}, "todo",         "COL_FLOAT",  4 )',$u->id());
                          
    */
    
    
    $iter = XDB::iterator('SELECT  compagnie, section, datedeN, bat, chambre
                         FROM  dev.test_tol2k10
                         WHERE email = {?}', $u->hruid() . '@polytechnique.edu');
                         
    $datas = array();
    if($iter->total() == 1){
        $datas = $iter->next();
    }
    else { echo "erreur : " . $u->hruid() . '\n';}
    
    $u->birthdate(new FrankizDateTime(DateTime::createFromFormat('d/m/Y',$datas['datedeN'])->format('Y-m-d')));
    
/*    try {
        $u->cellphone(new Phone($datas['portable']));
    } catch(Exception $e) {
        echo 'Error for phone ' . $datas['portable'] . "\n";
    }*/
//    $u->poly($datas['login']);

    // Linking with the room
    $room = str_replace('.','',$datas['chambre']);
    if (!empty($room)) {
        if (preg_match('/^[0-9]+[a-z]?$/', $room)) {
            $room = ($datas['bat'] == "Marié Bât D" ? 'D' :'X') . $room;
        }
        if ($room = Room::from($room)) {
            $u->addRoom($room);
        } else {
            echo 'Error for room ' . $datas['chambre'] . "\n";
        }
    }


    // Linking with the sport
    if (!empty($datas['section'])) {
        $nf = new GroupFilter(new GFC_Name('sport_' . conv_name($datas['section'])));
        $n = $nf->get(true);
        $n->select(GroupSelect::castes());
        $n->caste(Rights::member())->addUser($u);
    }


    //Photo
/*    $works = false;
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
*/
    $k++;
    echo 'User ' . str_pad($k, 4, '0', STR_PAD_LEFT) . '/' . $users . ' : '
         . str_pad($u->id(), 5, '0', STR_PAD_LEFT) . ' - ' . $datas['promo'] . ' - '
         . substr(microtime(true) - $t, 0, 5) . '   ' . $u->login() . "\n";
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
