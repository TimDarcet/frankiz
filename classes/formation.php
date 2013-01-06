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

class FormationSchema extends Schema
{
    public function className() {
        return 'Formation';
    }

    public function table() {
        return 'formations';
    }

    public function id() {
        return 'formation_id';
    }

    public function fromKey() {
        return 'abbrev';
    }

    public function tableAs() {
        return 'form';
    }

    public function scalars() {
        return array('domain', 'label', 'abbrev', 'description');
    }

    public function flagsets() {
        return array('platalyears' => array('formations_platal', 'year'));
    }
}

class FormationSelect extends Select
{
    protected static $natives = array('domain', 'label', 'abbrev', 'description');

    public function className() {
        return 'Formation';
    }

    protected function handlers() {
        return array('main' => self::$natives,
              'platalyears' => array('platalyears'),
                   'promos' => array('promos'));
    }

    protected function handler_promos(Collection $formations, $fields) {
        $_formations = array();
        foreach ($formations as $f) {
            $_formations[$f->id()] = array();
        }
        $iter = XDB::iterRow('SELECT  formation_id AS id,
                                      GROUP_CONCAT(DISTINCT promo ORDER BY promo SEPARATOR ",") AS p
                                FROM  studies
                               WHERE  formation_id IN {?}
                            GROUP BY  formation_id',
                                      $formations->ids());
        while (list($id, $promos) = $iter->next()) {
            $promos = explode(',', $promos);
            foreach ($promos as &$p) {
                $p = (integer)$p;
            }
            sort($promos);
            $_formations[$id] = $promos;
        }
        foreach ($formations as $f) {
            $f->fillFromArray(array('promos' => $_formations[$f->id()]));
        }
    }

    protected function handler_platalyears(Collection $formations, $fields) {
        $this->helper_flagset($formations, 'platalyears');
    }

    public static function base() {
        return new FormationSelect(self::$natives, null);
    }

    /**
     * Select available promos
     */
    public static function promos() {
        return new FormationSelect(array('promos'), null);
    }

    /**
     * Select platal years
     */
    public static function on_platal() {
        return new FormationSelect(array_merge(self::$natives, array('platalyears')), null);
    }
}

class Formation extends Meta
{
    /*******************************************************************************
         Properties

    *******************************************************************************/

    protected $domain = null;
    protected $label  = null;
    protected $abbrev = null;
    protected $description = null;

    // Existing promos
    protected $promos = null;

    // Platal years
    protected $platalyears = null;

    /*******************************************************************************
         Getters & Setters

    *******************************************************************************/

    public function image() {
        return new StaticImage('formations/' . $this->abbrev() . '.png');
    }

    public function promos() {
        return $this->promos;
    }

    /**
     * Get associated group
     * @return Group
     */
    public function getGroup() {
        return Group::from('formation_' . $this->abbrev);
    }

    /**
     * Get associated group for this formation and promo
     * @param integer $promo
     * @return Group
     */
    public function getGroupForPromo($promo) {
        return Group::from('promo_' . $this->abbrev . sprintf('%04d', $promo));
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
