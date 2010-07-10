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

class Group
{
    protected $gid;
    protected $type;
    protected $L;
    protected $R;
    protected $name;
    protected $long_name;
    protected $description;

    public function __construct($raw)
    {
        $this->fillFromArray($raw);
    }

    protected function fillFromArray(array $values)
    {
        foreach ($values as $key => $value) {
            if (property_exists($this, $key) && !isset($this->$key)) {
                $this->$key = $value;
            }
        }
    }

    public function gid()
    {
        return $this->gid;
    }

    public function type()
    {
        return $this->type;
    }

    public function L()
    {
        return $this->L;
    }

    public function R()
    {
        return $this->R;
    }

    public function name()
    {
        return $this->name;
    }

    public function long_name()
    {
        return $this->long_name;
    }

    public function description()
    {
        return $this->description;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
