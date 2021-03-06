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

class RoomSchema extends Schema
{
    public function className() {
        return 'Room';
    }

    public function table() {
        return 'rooms';
    }

    public function id() {
        return 'rid';
    }

    public function tableAs() {
        return 'r';
    }

    public function scalars() {
        return array('phone', 'comment', 'open');
    }

    public function collections() {
        return array('ips' => array('Ip', 'ips', 'ip', 'room'),
                     'users' => array('User', 'rooms_users', 'uid'),
                     'groups' => array('Group', 'rooms_groups', 'gid'));
    }
}

class RoomSelect extends Select
{
    public function className() {
        return 'Room';
    }

    public static function base($subs = null) {
        return new RoomSelect(array('phone', 'comment'), $subs);
    }

    public static function ips($subs = null) {
        return new RoomSelect(array('ips'), $subs);
    }

    public static function premise($subs = null) {
        return new RoomSelect(array('phone', 'comment', 'ips', 'open'), $subs);
    }

    public static function all($subs = null) {
        return new RoomSelect(array('phone', 'comment', 'open', 'ips', 'users', 'groups'), $subs);
    }

    public static function see() {
        return self::all(array(
            'ips' => IpSelect::base(),
            'users' => UserSelect::base(),
            'groups' => GroupSelect::base()));
    }

   protected function handlers() {
        return array('main' => array('phone', 'comment', 'open'),
                     'collections' => array('ips', 'users', 'groups'));
    }
}

class Room extends Meta
{
    // string, phone number
    protected $phone = null;
    // string, comment about the room
    protected $comment = null;
    // boolean, open state
    protected $open = null;
    // Collection of IPs
    protected $ips = null;
    // Collection of users
    protected $users = null;
    // Collection of groups
    protected $groups = null;

    /**
     * Allow ID matching ^[A-Z]*[0-9\/]*[a-z]*$
     * @param mixed $mixed ID to test
     * @return bool
     */
    public static function isId($mixed)
    {
        return !is_object($mixed) && !empty($mixed)
            && (preg_match('/^[A-Z]*[0-9\/]*[a-z]*$/', $mixed));
    }

    /**
     * Get a nice string, with room ID if it can be shown
     * @return string
     */
    public function __toString()
    {
        $comment = $this->comment();
        if (!$comment)
            return $this->id();

        // Some IDs are hidden for artistic reasons
        $rid = $this->id();
        if (starts_with($rid, 'BATACL') || starts_with($rid, 'BINETS'))
            return $comment;
        else
            return $comment . ' ' . $rid;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
