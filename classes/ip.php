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

class IpSchema extends Schema
{
    public function className() {
        return 'Ip';
    }

    public function table() {
        return 'ips';
    }

    public function id() {
        return 'ip';
    }

    public function fromKey() {
        return 'rid';
    }

    public function tableAs() {
        return 'ip';
    }

    public function scalars() {
        return array('plug', 'comment');
    }

    public function objects() {
        return array('room' => 'Room');
    }
}

class IpSelect extends Select
{
    public function className() {
        return 'Ip';
    }

    public static function base($subs = null) {
        return new IpSelect(array('plug', 'comment', 'room'), $subs);
    }

    protected function handlers() {
        return array('main' => array('plug', 'comment', 'room'));
    }
}

class Ip extends Meta
{
    // (deprecated) Plug ID
    protected $plug    = null;
    // A simple one-line comment for the IP address
    protected $comment = null;
    // Associated room
    protected $room    = null;

    /**
     * Allow ID matching an IP address
     * @param mixed $mixed ID to test
     * @return bool
     */
    public static function isId($mixed)
    {
        return !is_object($mixed) && !empty($mixed)
            && (preg_match('/^[0-9a-f][0-9a-f.:]+$/', $mixed));
    }

    /**
     * Represent an IP address by its ID
     */
    public function __toString()
    {
        return $this->id();
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
