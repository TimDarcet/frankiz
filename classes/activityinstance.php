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


class ActivityInstance extends meta
{    
    const SELECT_BASE = 0x01;
    
    protected $aid;
    protected $writer;
    protected $target;
    protected $title;
    protected $description;
    protected $comment;
    protected $begin;
    protected $end;
    protected $priv;
    
    protected $regular;
     

    public function aid()
    {
        return $this->aid;
    }
    
    public function writer()
    {
        return $this->writer;
    }

    public function target()
    {
        return $this->target;
    }

    public function title()
    {
        return $this->title;
    }
    
    public function description()
    {
        return $this->description;
    }

    public function comment(String $comment = null)
    {
        if (is_null($comment))
            return $this->comment;
        $this->comment = $comment;
    }

    public function begin($begin)
    {
        if (is_null($begin))
            return $this->begin;
        $this->begin = $begin;
    }

    public function end(String $end = null)
    {
        if (is_null($end))
            return $this->end;
        $this->end = $end;
    }

    public function priv()
    {
        return $this->priv;
    }
    
    public function regular()
    {
        return $this->regular;
    }

    public function delete()
    {  
        if ($this->id == null)
            throw new Exception("This activity doesn't exist.");
        XDB::execute('DELETE FROM activities_instances WHERE id={?}', $this->id);
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
        XDB::execute('UPDATE  activities_instances
                         SET  aid = {?}, writer = {?}, comment = {?},
                              begin = {?}, end = {?},
                       WHERE  id = {?}',
            $this->aid, $this->writer->id(), $this->comment,
            $this->begin, $this->end, $this->id);
    }
    
    public function insert()
    {
        XDB::execute('INSERT INTO  activities_instances
                         SET  aid = {?}, writer = {?}, comment = {?},
                              begin = {?}, end = {?}',
            $this->aid, $this->writer->id(), $this->comment,
            $this->begin, $this->end);
            
        $this->id = XDB::insertId();
    }
    
    public function fillFromArray(array $values)
    {        
        if (isset($values['days'])) {
            $this->regular = ($values['days'] != '');
            unset($values['days']);
        }

        parent::fillFromArray($values);
    
        if (isset($values['writer']) && !($values['writer'] instanceof User)) 
        {
            $this->writer = new User($values['writer']);
        }
        
        if (isset($values['target']) && !($values['target'] instanceof Group)) 
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
            
        $request = 'SELECT ai.id';
        if ($fields & self::SELECT_BASE)
            $request .= ', ai.aid, ai.writer, ai.comment, ai.begin, ai.end, a.target, a.title, a.description, a.priv, a.days';
        
        $iter = XDB::iterator($request .
                        ' FROM  activities_instances AS ai 
                    INNER JOIN  activities AS a 
                            ON  ai.aid = a.aid
                         WHERE  ai.id IN {?}',
                         array_keys($activities));

        while ($array_datas = $iter->next())
        {
            $activities[$array_datas['id']]->fillFromArray($array_datas);
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
