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

class FrankizDateTime extends DateTime implements Formatable
{
    public $isValid;

    public function __construct($time = null, DateTimeZone $timezone = null) {
        $this->isValid = !($time == '0000-00-00');
        parent::__construct($time, $timezone);
    }

    public function format($format = 'Y-m-d H:i:s')
    {
        return $this->isValid ? parent::format($format) : 'N/A';
    }

    public function toDb()
    {
        return $this->isValid ? $this->format('Y-m-d H:i:s') : '0000-00-00';
    }

    public function toJs()
    {
        return $this->isValid ? $this->format('Y/m/d H:i:s') : '0000/00/00 00:00:00';
    }

    public function __toString()
    {
        return $this->format('Y-m-d H:i:s');
    }

    public function age() {
        $now = new self();
        return $this->diff($now);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
