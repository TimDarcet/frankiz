#!/usr/bin/php -q
<?php
/***************************************************************************
 *  Copyright (C) 2004-2012 Binet Réseau                                   *
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

require_once(dirname(__FILE__) . '/../connect.db.inc.php');

$execute_real = (!empty($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'real');

// Half moons data : 3rd IP number
$halfmoons = array(
    '09' => 220, // Maunoury
    '10' => 212, // Foch
    '11' => 232, // Fayolle
    '12' => 216);// Joffre


// Schaeffer and Lemonnier data
$squares = array(
    '70' => array(224, 128),
    '71' => array(224, 0),
    '72' => array(228, 128),
    '73' => array(225, 0),
    '74' => array(225, 128),
    '75' => array(226, 0),
    '76' => array(227, 0),
    '77' => array(227, 128),
    '78' => array(228, 0),
    '79' => array(229, 0),
    '80' => array(226, 128));

// BEM
$bem = array(
    'A' => array(203, 50),
    'D' => array(204, 50));

// PEM (X17)
$pem = array(
    '10' => array(205, 15),
    '20' => array(205, 70));


// Read all data
$iter = XDB::iterRow('SELECT ip, rid, comment FROM ips');
$ips2room = array();
while (list($ip, $rid, $comment) = $iter->next()) {
    if (isset($ips2room[$ip])) {
        echo "IP " . $ip . " is used for " . $ips2room[$ip] . " and " . $rid . "\n";
    } else {
        $ips2room[$ip] = array('r'=>$rid, 'c'=>$comment);
    }
}
$ips2roomUnkown = $ips2room;

// Associate information about rooms and IP addresses
function associate_room_ip($rid, $ip, $comment) {
    global $ips2room, $ips2roomUnkown;
    global $execute_real;
    if (empty($ips2room[$ip])) {
        echo "IP " . $ip . " is not yet associated with room " . $rid . " (" . $comment . ")\n";
        if ($execute_real) {
            XDB::execute('INSERT INTO ips SET ip = {?}, rid = {?}, comment = {?}',
                $ip, $rid, $comment);
            echo "... Inserted.\n";
        }
    } else {
        unset($ips2roomUnkown[$ip]);
        if ($ips2room[$ip]['r'] != $rid) {
            echo "IP " . $ip . " is associated with room " . $ips2room[$ip]['r']
                . " (" . $ips2room[$ip]['c'] . ") instead of " . $rid . " (" . $comment . ")\n";
            if ($execute_real) {
                XDB::execute('UPDATE ips SET rid = {?}, comment = {?} WHERE ip = {?}',
                    $rid, $comment, $ip);
                echo "... Changed.\n";
            }
        }
    }
}

// Get all rooms
$iter = XDB::iterRow('SELECT rid FROM rooms');
$matches = array();
while (list($rid) = $iter->next()) {
    if (preg_match('/^X(09|10|11|12)([1-4]0)([0-9][0-9])$/', $rid, $matches)) {
        array_shift($matches);
        list($moon, $floor, $number) = $matches;
        $ip3 = $halfmoons[$moon] + (intval($floor[0]) - 1);
        $ip4 = intval($number);
        associate_room_ip($rid, '129.104.' . $ip3 . '.' . $ip4, 'principale');
        associate_room_ip($rid, '129.104.' . $ip3 . '.' . (100+$ip4), 'secondaire');
    } elseif (preg_match('/^X17([12]0)([0-9][0-9])$/', $rid, $matches)) {
        array_shift($matches);
        list($floor, $number) = $matches;
        list($ip3, $ip4) = $pem[$floor];
        $ip4 += intval($number);
        associate_room_ip($rid, '129.104.' . $ip3 . '.' . $ip4, 'principale');
    } elseif (preg_match('/^X(7[0-9]|80)([1-9]0)([0-9][0-9])$/', $rid, $matches)) {
        array_shift($matches);
        list($square, $floor, $number) = $matches;
        list($ip3, $ip4) = $squares[$square];
        $ip4 += intval($number);
        associate_room_ip($rid, '129.104.' . $ip3 . '.' . $ip4, 'principale');
    } elseif (preg_match('/^(A|D)([0-9][0-9])([0-9][0-9])$/', $rid, $matches)) {
        array_shift($matches);
        list($bemletter, $bemid, $number) = $matches;
        list($ip3, $ip4) = $bem[$bemletter];
        $ip4 += intval($number);
        associate_room_ip($rid, '129.104.' . $ip3 . '.' . $ip4, 'principale');
        associate_room_ip($rid, '129.104.' . $ip3 . '.' . (100+$ip4), 'secondaire');
    } elseif (preg_match('/^X(09|10|11|12)([1-4]0)([0-9][0-9])\s*[ABb$]/', $rid, $matches)) {
        // Bis
    } elseif (preg_match('/^BATACL/', $rid, $matches)) {
        // Bataclan
    } elseif (in_array($rid, array('X120001', 'X192003'))) {
        // Other
    } else {
        echo "Unknown room ID " . $rid . "\n";
    }
}

// List all unknown rows
foreach ($ips2roomUnkown as $ip => $infos) {
    echo "Unknown IP " . $ip . " for room ID " . $infos['r'] . " (" . $infos['c'] . ")\n";
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
