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

/**
 * Useful functions to make account importation easier
 */

// PHP configuration
set_time_limit(0);
require_once(dirname(__FILE__) . '/../connect.db.inc.php');
$globals->debug = 0;

function conv($str)
{
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

/**
 * Update an user
 *
 * @param User $user
 * @param array $user_data
 *
 * Required fields:
 *    hruid, string: the login
 *    firstname, string
 *    lastname, string
 *    email, string
 *
 * Optional fields:
 *    birthdate, FrankizDateTime
 *    gender, User::GENDER_FEMALE or User::GENDER_MALE
 *    cellphone, string
 *    nationality, string
 *    sport, string
 */
function update_user(User $user, array $user_data)
{
    $hruid = strtolower($user_data['hruid']);
    $firstname = ucwords(strtolower(conv($user_data['firstname'])));
    $lastname = ucwords(strtolower(conv($user_data['lastname'])));
    $email = strtolower($user_data['email']);

    if (!$hruid || !$firstname || !$lastname || !$email) {
        echo "Some fields are missing for user " . $user->id() . ":\n";
        print_r($user_data);
        return false;
    }
    $user->hruid($hruid);
    $user->firstname($firstname);
    $user->lastname($lastname);
    $user->email($email);

    if (!empty($user_data['birthdate'])) {
        $user->birthdate($user_data['birthdate']);
    }
    if (!empty($user_data['gender'])) {
        $user->gender($user_data['gender']);
    }
    if (!empty($user_data['cellphone'])) {
        $user->gender($user_data['cellphone']);
    }
    if (!empty($user_data['nationality'])) {
        $nation = conv_name($user_data['nationality']);
        try {
            $g = Group::from('nation_' . $nation);
            $g->select(GroupSelect::castes());
            $g->caste(Rights::member())->addUser($u);
        } catch (ItemNotFoundException $e) {
            echo "No nationality $nation for $hruid\n";
            return false;
        }
    }
    if (!empty($user_data['sport'])) {
        $sport = conv_name($user_data['sport']);
        try {
            $g = Group::from('sport_' . $sport);
            $g->select(GroupSelect::castes());
            $g->caste(Rights::member())->addUser($u);
        } catch (ItemNotFoundException $e) {
            echo "No sport $sport for $hruid\n";
            return false;
        }
    }
    return true;
}

/**
 * Create an user
 * @see update_user
 */
function create_user(array $user_data)
{
    XDB::startTransaction();
    try {
        $u = new User();
        $u->insert();
        if (!update_user($u, $user_data)) {
            $u->delete();
            return null;
        }
        $u->skin('default');
        $u->select(UserSelect::minimodules());
        $u->copyMinimodulesFromUser(0);
        echo "Created user " . $u->id() . ": " . $u->hruid() . "\n";
        XDB::commit();
        return $u;
    } catch (Exception $e) {
        XDB::rollback();
        throw $e;
    }
    XDB::rollback();
    return null;
}

/**
 * Create a FrankizImage from a path
 * @return FrankizImage or null
 */
function create_image($path, $label = null)
{
    if (!file_exists($path)) {
        return null;
    }
    $upload = FrankizUpload::fromFile($path);
    if ($upload->size() == 0) {
        echo "Unable to upload $path\n";
        return null;
    }
    try {
        $group = Group::from('tol')->select(GroupSelect::castes());
        $i = new FrankizImage();
        $i->insert();
        $i->caste($group->caste(Rights::everybody()));
        $i->image($upload, false);
        if ($label) {
            $i->label($label);
        }
        return $i;
    } catch (Exception $e) {
        echo 'create_image error:' . $e->getMessage() . "\n";
        return null;
    }
    return null;
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
