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
$rooms_file = "/home/2012/momohawari/dev/frankiz/bin/imports/x2013-casert.csv";

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


    // Linking with the room
$rooms_fic = fopen($rooms_file, 'rb');
$found = 0;
for ($rooms_datas = fgetcsv($rooms_fic, 1024, ';'); !feof($rooms_fic); $rooms_datas = fgetcsv($rooms_fic, 1024, ';')) 
{
	print_r($rooms_datas);
	$u = new User();
	$res = XDB::query('SELECT uid FROM account WHERE firstname = {?} AND lastname = {?}', $rooms_datas[$firstname_rooms],$rooms_datas[$lastname_rooms]);
	if ($res->numRows()==0)
	{
		echo 'FAILED, user not found !';
	}
	else
	{
		$u = User::fromId($res->fetchOneCell());
		$casert = $rooms_datas[$casert_rooms];
		if (!empty($casert)) 
    		{
    			$casert = str_replace('.','', $casert);
                	$casert = 'X' . $casert;
                	echo "Casert : " . $casert . "\n";

            		if ($casert = Room::from($casert)) 
            		{
                		$u->addRoom($casert);
            		} 
            		else 
            		{
                		echo 'Error for room ' . $rooms_datas[$casert_rooms] . "\n";
            		}
        	}
	}
	
}
fclose($rooms_fic);

