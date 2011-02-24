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

class Phone implements Formatable
{
    protected $phone = null;

    public function __construct($data = null)
    {
        $data = trim($data);
        $data = str_replace(array('.', ' ', '-'), '', $data);
        if (!preg_match('/^\+?[0-9]*$/', $data)) {
            throw new Exception("This doesn't look like a phone number");
        }
        $this->phone = $data;
    }

    public function toDb()
    {
        return $this->phone;
    }

    public function format()
    {
        if ($this->phone[0] == '+') {
            $duplets = str_split(substr($this->phone, 2), 2);
            $duplets[0] = '+' . $this->phone[1] . $duplets[0];
        } else {
            $duplets = str_split($this->phone, 2);
        }
        return implode(' ', $duplets);
    }

    public function __toString()
    {
        return $this->format();
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
