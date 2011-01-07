<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                       *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                     *
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

class Room extends Meta
{
    const SELECT_BASE      = 0x01;
    const SELECT_IPS       = 0x02;

    protected $phone    = null;
    protected $comment  = null;
    protected $ips      = null;

    public static function isId($mixed)
    {
        return !is_object($mixed) && (preg_match('/^[A-Z]*[0-9]*[a-z]*$/', $mixed));
    }

    public function phone()
    {
        return $this->phone;
    }

    public function comment()
    {
        return $this->comment;
    }

    public function ips()
    {
        return $this->ips;
    }

    /*******************************************************************************
         Data fetcher
             (batchFrom, batchSelect, fillFromArray, …)
    *******************************************************************************/

    public static function batchSelect(array $rooms, $options = null)
    {
        if (empty($rooms))
            return;

        if (empty($rooms)) {
            $options = self::SELECT_BASE;
        }

        $bits = self::optionsToBits($options);
        $rooms = array_combine(self::toIds($rooms), $rooms);

        if (!empty($cols)) {
            $iter = XDB::iterator('SELECT  rid AS id, phone, comment
                                     FROM  rooms
                                    WHERE  rid IN {?}', self::toIds($rooms));

            while ($datas = $iter->next())
                $castes[$datas['id']]->fillFromArray($datas);
        }

        if ($bits & self::SELECT_IPS) {
            foreach($rooms as $room)
                $room->ips = array();

            $iter = XDB::iterRow("SELECT  ip, rid
                                    FROM  ips
                                   WHERE  rid IN {?}", self::toIds($rooms));

            while (list($ip, $rid) = $iter->next())
                array_push($rooms[$rid]->ips, $ip);
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
