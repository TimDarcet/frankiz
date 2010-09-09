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

class Sharer
{
    protected $id = null;

    static protected $shared = array();

    public function __construct($ns, $id)
    {
        $this->id = $ns . $id;

        if (!isset(self::$shared[$this->id]))
            self::$shared[$this->id] = array();
    }

    public function v($key, $default = null)
    {
        return isset(self::$shared[$this->id][$key]) ? self::$shared[$this->id][$key] : $default;
    }

    public function set($key, $value)
    {
        self::$shared[$this->id][$key] =& $value;
    }

    public function has($key, $value)
    {
        return isset(self::$shared[$this->id][$key]);
    }

    public function fill(array $datas)
    {
        foreach ($datas as $key => $value)
            $this->set($key, $value);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
