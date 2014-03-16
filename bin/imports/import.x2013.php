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
$file = "/home/2012/momohawari/dev/frankiz/bin/imports/x2013-eleves.csv";
$sports_file = "/home/2012/momohawari/dev/frankiz/bin/imports/x2013-sports.csv";
$rooms_file = "/home/2011/thunder/TOS2012/eleves-x12-rooms.csv";
$photos_folder = "/home/2012/momohawari/dev/frankiz/bin/imports/PHOTOS_X2013";

//index of data
$lastname = 0;
$firstname = 1;
$nationality = 5;
$birthdate = 3;
$email = 6;
//$formation = 11;
$promo = 4;
//$year_in = 0;
//$year_out = 1;
$gender = 2;
//$room_id = 13;
//$sport = 7;
//$photo_file = 7;
$matricule=7;
// index of data for sports file
$firstname_sports = 1;
$lastname_sports = 0;
$section_sports = 2;

// index of data for rooms file
$firstname_rooms = 1;
$lastname_rooms = 0;
$casert_rooms = 2;


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
$fail_sports = 0;

for ($datas = fgetcsv($fic, 1024, ';'); !feof($fic); $datas = fgetcsv($fic, 1024, ';')) {
    $datas[$promo] = '2013';
    print_r($datas);
    $t = microtime(true);
    // Creating the User
    $u = new User();
    $login = str_replace('@polytechnique.edu','',$datas[$email]);
    
    // checking that the user doesn't already exist
    $res = XDB::query('SELECT uid FROM account WHERE hruid = {?}', $login);
    if ($res->numRows() == 0)
    {   
	$u -> insert();
	$new = 1;
    }
    else
    {   
        $u = User::fromId($res->fetchOneCell());
	$new = 0;
    }
    
    $u->login($login);
    $u->hruid($login);
    
//    $u->password($datas['passwd'], false);
    $u->firstname(ucwords(strtolower(conv($datas[$firstname]))));
    $u->lastname(ucwords(strtolower(conv($datas[$lastname]))));
//    $u->nickname(conv($datas['surnom']));
    if  ($datas[$birthdate] != null) $u->birthdate(new FrankizDateTime(preg_replace("`^([0-9]{2})/([0-9]{2})`", "$2/$1", trim($datas[$birthdate]))));
   // if($gender != null)
        $u->gender(($datas[$gender] == 'Mme') ? User::GENDER_FEMALE : User::GENDER_MALE);
    if (!empty($datas[$email])) {
        $u->email($datas[$email]);
    }

    if  ($new == 1)
    {
    	//setting default minimodules
    	$u->select(UserSelect::minimodules());
    	$u->copyMinimodulesFromUser(0);
    	$u->skin('default');
    }


/*    try {
        $u->cellphone(new Phone($datas['portable']));
    } catch(Exception $e) {
        echo 'Error for phone ' . $datas['portable'] . "\n";
    }*/
//    $u->poly($datas['login']);

    // Linking with the room
    //if($room_id != null){
        //$rooms_fic = fopen($rooms_file, 'rb');
        //$found = 0;
        //for ($rooms_datas = fgetcsv($rooms_fic, 1024, ';'); !feof($rooms_fic); $rooms_datas = fgetcsv($rooms_fic, 1024, ';')) {
            //print_r($rooms_datas);
          //  if  (conv_name($rooms_datas[$firstname_sports]) == conv_name($datas[$firstname]) && conv_name($rooms_datas[$lastname_sports]) == conv_name($datas[$lastname]))
            //{
              //  $casert = $rooms_datas[$casert_rooms];
                //$found = 1;
                //break;
            //}
        //}
        //fclose($rooms_fic);
        
        if  ($found == 1)
        {
            if (!empty($casert)) {
            //if (preg_match('/^[0-9]+[a-z]?$/', $casert)) {
                $casert = str_replace('.','', $casert);
                $casert = 'X' . $casert;
                echo "Casert : " . $casert . "\n";
            //}
            if ($casert = Room::from($casert)) {
                $u->addRoom($casert);
            } else {
                echo 'Error for room ' . $rooms_datas[$casert_rooms] . "\n";
            }
        }
           // echo "Sport : " . 'sport_' . conv_name($section)."\n";
        }
        else {
            echo "Fail room ! (". ++$fail_rooms .")\n";
        }
    //}

 /*   $login = "";
    if(preg_match('!@institutoptique.fr!',$datas[$email]))
        $login = str_replace('@institutoptique.fr','',$datas[$email]);
    else
        $login = str_replace('@polytechnique.edu','',$datas[$email]);
*/
        
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
    
    $u->addStudy($formation_id, ($year_in === null ? (int) $datas[$promo]: (int) $datas[$year_in]), ($year_out === null ? (int) $datas[$promo] + 4 : (int) $datas[$year_out]), $datas[$promo], $login);
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
    //if ($sport != null) {
        $sports_fic = fopen($sports_file, 'rb');
        $found = 0;
        for ($sports_datas = fgetcsv($sports_fic, 1024, ';'); !feof($sports_fic); $sports_datas = fgetcsv($sports_fic, 1024, ';')) {
            //print_r($sports_datas);
            if  (conv_name($sports_datas[$firstname_sports]) == conv_name($datas[$firstname]) && conv_name($sports_datas[$lastname_sports]) == conv_name($datas[$lastname]))
            {
                $section = $sports_datas[$section_sports];
                $found = 1;
                break;
            }
        }
        fclose($sports_fic);
        
        if  ($found == 1) {
        
            $nf = new GroupFilter(new GFC_Name('sport_' . conv_name($section)));
            $n = $nf->get(true);
            if($n){
                $n->select(GroupSelect::castes());
                $n->caste(Rights::member())->addUser($u);
            }
            echo "Sport : " . 'sport_' . conv_name($section)."\n";
        }
        else {
            echo "Fail sport ! (". ++$fail_sports .")\n";
        }
    //}

    //Adding the promo group
    if ($promo != null) {
        $nf = new GroupFilter(new GFC_Name('promo_' . conv_name($datas[$promo])));
        $n = $nf->get(true);
        if($n){
            $n->select(GroupSelect::castes());
            $n->caste(Rights::member())->addUser($u);
        }
    }

    //Adding polytechnicien as a formation *group* (sic)
        $nf = new GroupFilter(new GFC_Name('formation_x'));
        $n = $nf->get(true);
        if($n){
            $n->select(GroupSelect::castes());
            $n->caste(Rights::member())->addUser($u);
        }

    //Photo
    $works = false;
    $suffix = '';
    $folder = $photos_folder;
    $original = true;
    //$path = $folder . '/' . strtoupper(conv_name($datas[$lastname])) . '_' . strtoupper(conv_name($datas[$firstname])) . '.jpg';
    //$path_small = $folder . '/small/' . strtoupper(conv_name($datas[$lastname])) . '_' . strtoupper(conv_name($datas[$firstname])) . '.jpg';
    $path_small = $folder . '/small/' . $datas[$matricule]. '.jpg';
    $path=$folder . '/' . $datas[$matricule]. '.jpg';
    if (file_exists($path)) {
	$source = imagecreatefromjpeg($path); // La photo est la source
	$largeur_image = imagesx($source);
	$hauteur_image = imagesy($source);
	
	$destination = imagecreatetruecolor(800, 800 * $hauteur_image / $largeur_image); // On crée la miniature vide
	$largeur_destination = imagesx($destination);
	$hauteur_destination = imagesy($destination);
						
	// On crée la miniature
	imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeur_destination, $hauteur_destination, $largeur_image, $hauteur_image);
	
	// On enregistre la miniature
	imagejpeg($destination, $path_small);
        $upload = FrankizUpload::fromFile($path_small);
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

echo "\n".$k." users executed, ".$fail_sports." sports failed\n";

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
