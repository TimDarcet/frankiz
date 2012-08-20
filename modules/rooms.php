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

class RoomsModule extends PlModule
{
    function handlers()
    {
        return array(
            'rooms'     => $this->make_hook('rooms', AUTH_COOKIE),
            'rooms/see' => $this->make_hook('room_see',  AUTH_COOKIE)
        );
    }

    function handler_rooms($page)
    {
        $rf = new RoomFilter(new RFC_Prefix('BATACL'));
        $rooms = $rf->get();
        $rooms->select(RoomSelect::see(array('groups' => GroupSelect::base())));
        $page->assign('rooms', $rooms);
        $page->changeTpl('rooms/rooms.tpl');
    }

    function handler_room_see($page, $rid = null)
    {
        $room = Room::fromId($rid);
        if (!$room) {
            $page->changeTpl('rooms/no_room.tpl');
            return;
        }
        $room->select(RoomSelect::see());
        $page->assign('room', $room);
        $page->assign('title', $room->id());
        $page->changeTpl('rooms/room.tpl');
    }
}
