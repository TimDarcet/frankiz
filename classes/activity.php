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
 *  along with this program; if not, write to the Free Software            *
 *  Foundation, Inc.,                                                      *
 *  59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                *
 ***************************************************************************/


class ActivitySchema extends Schema
{
    public function className() {
        return 'Activity';
    }

    public function table() {
        return 'activities';
    }

    public function id() {
        return 'aid';
    }

    public function tableAs() {
        return 'a';
    }

    public function scalars() {
        return array('title', 'description', 'days', 'default_begin', 'default_end');
    }

    public function objects() {
        return array('target' => 'Caste',
                     'origin' => 'Group');
    }
}

class ActivitySelect extends Select
{
    protected static $natives = array('title', 'description', 'days', 'default_begin', 'default_end', 'target', 'origin');

    public function className() {
        return 'Activity';
    }

    public static function base() {
        return new ActivitySelect(self::$natives,
                              array('target' => CasteSelect::group(),
                                    'origin' => GroupSelect::base()));
    }

    protected function handlers() {
        return array('main' => self::$natives);
    }
}

class Activity extends meta
{
    protected $target;
    protected $origin;
    protected $title;
    protected $description;
    protected $days;
    protected $default_begin;
    protected $default_end;

    public function target_group() {
        return $this->target->group();
    }

    public function is_regular()
    {
        if (is_null($this->days))
            $this->select(SELECT_TIME);
        return ($this->days != '');
    }
    
    public function next_dates($number)
    {
        if (!$this->is_regular())
            return false;
        $a = explode(',', $this->days);
        $dates = array();
        foreach($a as $e)
        {
            $first_date = strtotime("next " . $e);
            $dates[$e] = array(date('Y-m-d', $first_date));
            for ($i = 1; $i < $number; $i++)
            {
                $dates[$e][] = date('Y-m-d', strtotime('+' . $i . 'week', $first_date));
            }
        }
        return $dates;
    }

    public function include_day($day)
    {
        $a = explode(',', $this->days);
        foreach ($a as $e) {
            if ($e == $day)
                return true;
        }
        return false;
    }
    
    public function insert()
    {
        XDB::execute('INSERT  activities
                         SET  target = {?}, origin = {?}, title = {?},
                              description = {?}, days = {?}, default_begin = {?},
                              default_end = {?}',
            $this->target->id(), $this->origin, $this->title,
            $this->description, $this->days, $this->default_begin,
            $this->default_end);
            
        $this->id = XDB::insertId();
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
