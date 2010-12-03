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
    const SELECT_PARTICIPANTS = 0x02;
    
    protected $aid;
    protected $writer;
    protected $target;
    protected $origin;
    protected $title;
    protected $description;
    protected $comment;
    protected $begin;
    protected $end;
    protected $priv;

    protected $participants;
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

    public function origin()
    {
        return $this->origin;
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

    public function date()
    {
        return $this->begin->format("Y-m-d");
    }

    public function hour_begin()
    {
        return $this->begin->format("H:i");
    }

    public function hour_end()
    {
        return $this->end->format("H:i");
    }

    public function priv()
    {
        return $this->priv;
    }

    public function participants()
    {
        return $this->participants;
    }
    
    public function regular()
    {
        return $this->regular;
    }
    
    // may be an array or a single element, id or User
    public function add_participants($p)
    {
        $users = unflatten($p);
        $values = array();
        foreach ($users as $user)
        {
            if ($user instanceof User)
                $values[] = '(' . $this->id. ',' . $user->id() . ')';
            else if (isId($user))
                $values[] = '(' . $this->id. ',' . $user . ')';
        }
        if (!empty($values))
        {
            $values = implode(',', $values);
            XDB::execute('REPLACE INTO  activities_participants
                                VALUES  ' . $values);
        }
    }

    // may be an array or a single element, id or User
    public function delete_participants($p)
    {
        $users = unflatten($p);
        $values = array();
        foreach ($users as $user)
        {
            if ($user instanceof User)
                $values[] = '(' . $this->id. ',' . $user->id() . ')';
            else if (isId($user))
                $values[] = '(' . $this->id. ',' . $user . ')';
        }
        if (!empty($values))
        {
            $values = '(' . implode(',', $values) .')';
            XDB::execute('DELETE FROM  activities_participants
                                WHERE  (id,participant) IN ' . $values);
        }
    }

    public function export() {
        $a = array();
        $a['id'] = $this->id;
        $a['aid'] = $this->aid;
        $a['writer'] = array('displayName'  => $this->writer->displayName(),
                             'id'           => $this->writer->id());
        $a['target'] = array('name'         => $this->target->name(),
                             'label'        => $this->target->label());
        $a['title'] = $this->title;
        $a['description'] = $this->description;
        $a['comment'] = $this->comment;
        $a['begin'] = $this->begin->format("m/d/Y H:i");
        $a['end'] = $this->end->format("m/d/Y H:i");
        $a['priv'] = $this->priv;
        $a['participants'] = array();
        foreach ($this->participants as $user)
        {
            $a['participants'][$user->id()] = array('displayName'  => $user->displayName(),
                                                    'id'           => $user->id());
            if($user->id() == s::user()->id())
                $a['participate'] = true;
        }
        if (!isset( $a['participate']))
            $a['participate'] = false;
        $a['regular'] = $this->regular;
        return $a;
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
                              begin = {?}, end = {?}
                       WHERE  id = {?}',
            $this->aid, $this->writer->id(), $this->comment,
            $this->begin->format('Y-m-d H:i:s'), $this->end->format('Y-m-d H:i:s'), $this->id);
    }
    
    public function insert()
    {
        XDB::execute('INSERT INTO  activities_instances
                         SET  aid = {?}, writer = {?}, comment = {?},
                              begin = {?}, end = {?}',
            $this->aid, $this->writer->id(), $this->comment,
            $this->begin->format('Y-m-d H:i:s'), $this->end->format('Y-m-d H:i:s'));
            
        $this->id = XDB::insertId();
    }
    
    public function fillFromArray(array $values)
    {
        if (!isset($values['participants']))
            $this->participants = array();

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
        }
    }
    
    public static function batchSelect(array $activities, $options = null)
    {
        if (empty($activities))
            return;


        if (empty($options)) {
            $options = array();
            $options[self::SELECT_BASE] = array('groups' => Group::SELECT_BASE,
                                                 'users' => User::SELECT_BASE);
        }

        $bits = self::optionsToBits($options);

        $activities = array_combine(self::toIds($activities), $activities);
            
        $request = 'SELECT ai.id';
        if ($bits & self::SELECT_BASE)
            $request .= ', ai.aid, ai.writer, ai.comment, ai.begin, ai.end, a.target, a.origin, a.title, a.description, a.priv, a.days';

        $iter = XDB::iterator($request . 
                        ' FROM  activities_instances AS ai 
                    INNER JOIN  activities AS a
                            ON  ai.aid = a.aid
                         WHERE  ai.id IN {?}',
                         array_keys($activities));


        $groups = new Collection('Group');
        $users = new Collection('User');

        while ($datas = $iter->next()) {
            $datas['writer'] = $users->addget($datas['writer']);
            $datas['target'] = $groups->addget($datas['target']);
            $datas['begin']  = new FrankizDateTime($datas['begin']);
            $datas['end']    = new FrankizDateTime($datas['end']);
            $activities[$datas['id']]->fillFromArray($datas);
        }


        if ($bits & self::SELECT_PARTICIPANTS)
        {
            $iter = XDB::iterator('SELECT  id , participant
                                      FROM  activities_participants
                                     WHERE  id IN {?}', array_keys($activities));

            while ($datas = $iter->next()) {
                $activities[$datas['id']]->participants[] = $users->addget($datas['participant']);
            }
        }


        if (!empty($options[self::SELECT_BASE]['groups']))
            $groups->select($options[self::SELECT_BASE]['groups']);

        if (!empty($options[self::SELECT_BASE]['users']))
            $users->select($options[self::SELECT_BASE]['users']);

    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
