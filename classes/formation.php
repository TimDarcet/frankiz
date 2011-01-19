<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                        *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                      *
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

class Formation extends Meta
{
    /*******************************************************************************
         Constants

    *******************************************************************************/

    const SELECT_BASE         = 0x01;
    const SELECT_DESCRIPTION  = 0x02;

    /*******************************************************************************
         Properties

    *******************************************************************************/

    protected $domain = null;
    protected $label  = null;
    protected $abbrev = null;
    protected $description = null;

    /*******************************************************************************
         Getters & Setters

    *******************************************************************************/

    public function domain() {
        return $this->domain;
    }

    public function label() {
        return $this->label;
    }

    public function abbrev() {
        return $this->abbrev;
    }

    public function description() {
        return $this->description;
    }

    /*******************************************************************************
         Data fetcher
             (batchFrom, batchSelect, fillFromArray, …)
    *******************************************************************************/

    public static function batchSelect(array $formations, $options = null)
    {
        if (empty($formations))
            return;

        if (empty($options)) {
            $options = self::SELECT_BASE;
        }

        $bits = self::optionsToBits($options);
        $formations = array_combine(self::toIds($formations), $formations);

        if ($bits & self::SELECT_BASE) {
            $iter = XDB::iterator('SELECT  formation_id AS id, domain, label, abbrev
                                     FROM  formations
                                    WHERE  formation_id IN {?}', self::toIds($formations));

            while ($datas = $iter->next()) {
                $formations[$datas['id']]->fillFromArray($datas);
            }
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
