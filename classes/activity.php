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


class Activity extends meta
{    
    const SELECT_BASE = 0x01;
    const SELECT_TIME = 0x02;
    
    protected $target;
    protected $title;
    protected $description;
    protected $days;
    protected $default_begin;
    protected $default_end;
    protected $priv;

    public function target()
    {
        return $this->target;
    }

    public function title(String $title = null)
    {
        if (is_null($title))
            return $this->title;
        $this->title = $title;
    }

    public function description(String $description = null)
    {
        if (is_null($description))
            return $this->description;
        $this->description = $description;
    }

    public function days(String $days = null)
    {
        if (is_null($days))
            return $this->days;
        $this->days = $days;
    }
    
    //must be like in database, eg : 'Monday'
    public function include_day(String $day)
    {
        return strpos($this->days, $day) !== false;
    }
    
    public function default_begin($begin)
    {
        if (is_null($begin))
            return $this->default_begin;
        $this->default_begin = $begin;
    }

    public function default_end(String $end = null)
    {
        if (is_null($end))
            return $this->default_end;
        $this->default_end = $end;
    }
    
    public function priv($priv = null)
    {
        if (is_null($priv))
            return $this->priv;
        $this->priv = $priv;
    }

    public function delete()
    {  
        if ($this->id == null)
            throw new Exception("This activity doesn't exist.");
        XDB::execute('DELETE FROM activities WHERE aid={?}', $this->id);
    }
    
    public function is_regular()
    {
        if (is_null($this->days))
            $this->select(SELECT_TIME);
        return ($this->days != '');
    }
    
    public function next_dates(Integer $number)
    {
        if (!$this->is_regular())
            return false;
        $a = split(',', $this->days);
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

    public function replace()
    {
        // TO DO
        //if (!$this->valid())
        //    throw new Exception("This activity is not valid.");
        if (is_null($this->id))
            $this->insert();
        else 
            $this->update();
    }
    
    public function update()
    {        
        XDB::execute('UPDATE  activities
                         SET  target = {?}, title = {?}, description = {?}, 
                              days = {?}, default_begin = {?}, default_end = {?},
                              priv = {?}
                       WHERE  aid = {?}',
            $this->target->id(), $this->title, $this->description, 
            $this->days, $this->default_begin, $this->default_end,
            $this->priv, $this->id);
    }
    
    public function insert()
    {
        XDB::execute('INSERT  activities
                         SET  target = {?}, title = {?}, description = {?}, 
                              days = {?}, default_begin = {?}, default_end = {?},
                              priv = {?}',
            $this->target->id(), $this->title, $this->description, 
            $this->days, $this->default_begin, $this->default_end,
            $this->priv);
            
        $this->id = XDB::insertId();
    }
    
    public function fillFromArray(array $values)
    {
        if (isset($values['aid'])) 
        {
            $this->id = $values['aid'];
            unset($values['aid']);
        }
        
        parent::fillFromArray($values);
    
        if (isset($values['target']) && (!$values['target'] instanceof Group)) 
        {
            $this->target = new Group($values['target']);
            $this->target->select(Group::SELECT_BASE);
        }
    }
    
    public static function batchSelect(array $activities, $fields)
    {
        if (empty($activities))
            return;

        $activities = array_combine(self::toIds($activities), $activities);
            
        $request = 'SELECT aid';
        if ($fields & self::SELECT_BASE)
            $request .= ', target, title, description, priv';
        if ($fields & self::SELECT_TIME)
            $request .= ', days, default_begin, default_end';
        
        $iter = XDB::iterator($request .
                        ' FROM activities
                         WHERE aid IN {?}',
                         array_keys($activities));
                         
        while ($array_datas = $iter->next())
            $activities[$array_datas['aid']]->fillFromArray($array_datas);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
