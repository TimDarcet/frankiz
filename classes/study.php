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

class Study
{
    protected $formation  = null;
    protected $year_in    = null;
    protected $year_out   = null;
    protected $promo      = null;
    protected $forlife    = null;

    public function __construct($datas)
    {
        $this->fillFromArray($datas);
    }

    public function fillFromArray(array $values)
    {
        if (empty($values))
            return;

        foreach ($values as $key => $value)
            if (property_exists($this, $key))
                $this->$key = $value;
    }

    public function formation() {
        return $this->formation;
    }

    public function year_in() {
        return $this->year_in;
    }

    public function year_out() {
        return $this->year_out;
    }

    public function promo() {
        return $this->promo;
    }

    public function forlife() {
        return $this->forlife;
    }

    /**
     * Get the associated promotion group name
     */
    public function groupName() {
        return 'promo_' . $this->formation->abbrev() . $this->promo;
    }

    /**
     * Get the associated promotion group name
     */
    public function studyGoupName() {
        return 'formation_' . $this->formation->abbrev();
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
