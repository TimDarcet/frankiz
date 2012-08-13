<?php
/***************************************************************************
 *  Copyright (C) 2004-2012 Binet Réseau                                   *
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
 *  along with this program; if not, write to the Free Software            *
 *  Foundation, Inc.,                                                      *
 *  59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                *
 ***************************************************************************/

class ActivityInstanceSchema extends Schema
{
    public function className() {
        return 'ActivityInstance';
    }

    public function table() {
        return 'activities_instances';
    }

    public function id() {
        return 'id';
    }

    public function tableAs() {
        return 'ai';
    }

    public function scalars() {
        return array('comment');
    }

    public function objects() {
        return array('writer' => 'User',
                      'begin' => 'FrankizDateTime',
                        'end' => 'FrankizDateTime',
                   'activity' => 'Activity');
    }
}

class ActivityInstanceSelect extends Select
{
    protected static $natives = array('comment', 'writer', 'begin', 'end', 'activity');

    public function className() {
        return 'ActivityInstance';
    }

    public static function base($subs = null) {
        return new ActivityInstanceSelect(self::$natives,
                              array('writer' => UserSelect::base(),
                                  'activity' => ActivitySelect::base()));
    }

    public static function participants() {
        return new NewsSelect(array('participants'),
                              array('participants' => UserSelect::base()));
    }

    public static function all($subs = null) {
        return new ActivityInstanceSelect(array_merge(self::$natives, array('participants')),
                              array('writer' => UserSelect::base(),
                                  'activity' => ActivitySelect::base(),
                              'participants' => UserSelect::base()));
    }

    protected function handlers() {
        return array('main' => self::$natives,
             'participants' => array('participants'));
    }

    protected function handler_participants(Collection $activities, $fields) {
        $res = XDB::iterator('SELECT  id, participant
                                FROM  activities_participants
                               WHERE  id IN {?}', $activities->ids());

        $users = new Collection('User');
        $part = array();

        while($datas = $res->next()) {
            $part[$datas['id']][] = $users->addget($datas['participant']);
        }

        foreach ($part as $key => $obj) {
            $activities->get($key)->participants($obj);
        }

        $users->select($this->subs['participants']);
    }
}

class ActivityInstance extends meta
{
    protected $activity;
    protected $writer;
    protected $comment;
    protected $begin;
    protected $end;

    protected $participants;

    public function target()
    {
        return $this->activity->target();
    }

    public function target_group()
    {
        return $this->activity->target_group();
    }

    public function origin()
    {
        return $this->activity->origin();
    }

    public function title()
    {
        return $this->activity->title();
    }
    
    public function description()
    {
        return $this->activity->description();
    }
    
    public function date()
    {
        return $this->begin->format("Y-m-d");
    }

    public function hour_begin($day = false)
    {
        if ($day === false || ($day instanceof FrankizDateTime && $this->begin->format("Y-m-d") == $day->format("Y-m-d")))
            return $this->begin->format("H:i");
        else
            return false;
    }

    public function hour_end($day = false)
    {
        if ($day === false || ($day instanceof FrankizDateTime && $this->end->format("Y-m-d") == $day->format("Y-m-d")))
            return $this->end->format("H:i");
        else
            return false;
    }

    public function participants($part = null)
    {
        if ($part != null) {
            $this->participants = $part;
        }
        return $this->participants;
    }
    
    public function participate()
    {
        if (is_null($this->participants))
            return false;
        foreach ($this->participants as $user) {
            if($user->id() == s::user()->id())
                return true;
        }
        return false;
    }
    
    public function regular()
    {
        return ($this->activity->days() != '');
    }
    
    // may be an array or a single element, id or User
    public function add_participants($p)
    {
        $users = unflatten($p);
        $values = array();
        foreach ($users as $user) {
            if ($user instanceof User)
                $values[] = '(' . $this->id. ',' . $user->id() . ')';
            else if (self::isId($user))
                $values[] = '(' . $this->id. ',' . $user . ')';
        }
        if (!empty($values)) {
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
        foreach ($users as $user) {
            if ($user instanceof User)
                $values[] = '(' . $this->id. ',' . $user->id() . ')';
            else if (self::isId($user))
                $values[] = '(' . $this->id. ',' . $user . ')';
        }
        if (!empty($values)) {
            $values = '(' . implode(',', $values) .')';
            XDB::execute('DELETE FROM  activities_participants
                                WHERE  (id,participant) IN ' . $values);
        }
    }

    public function export($bits = null) 
    {
        $a = parent::export($bits);
        $a['aid'] = $this->activity->id();
        $a['writer'] = array('displayName'  => $this->writer->displayName(),
                             'id'           => $this->writer->id(),
                             'login'        => $this->writer->login(),
                             'photo'        => $this->writer->image()->src('micro'));
        $a['target'] = array('name'         => $this->activity->target_group()->name(),
                             'label'        => $this->activity->target_group()->label());
        if ($this->activity->origin() != false) {
            $a['origin'] = array('name'         => $this->activity->origin()->name(),
                                 'label'        => $this->activity->origin()->label());
            if ($this->activity->origin()->image() != false)
                $a['origin']['image'] = $this->activity->origin()->image()->src('micro');
        }

        $a['title'] = $this->activity->title();
        $a['description'] = MiniWiki::wikiToHTML($this->activity->description(), false);
        $a['comment'] = MiniWiki::wikiToHTML($this->comment, false);

        $a['begin'] = $this->begin->format("m/d/Y H:i");
        $a['end'] = $this->end->format("m/d/Y H:i");

        $a['participants'] = array();
        foreach ($this->participants as $user) {
            $a['participants'][$user->id()] = array('displayName'  => $user->displayName(),
                                                    'id'           => $user->id());
            if($user->id() == s::user()->id())
                $a['participate'] = true;
        }
        if (!isset( $a['participate']))
            $a['participate'] = false;
        
        $a['regular'] = $this->regular();
        $a['canEdit'] = S::user()->hasRights($this->activity->target()->group(), Rights::admin());
        $a['isWriter'] = S::user()->id() == $this->writer->id();
        return $a;
    }
    
    public function insert()
    {
        XDB::execute('INSERT INTO  activities_instances
                              SET  activity = {?}, writer = {?}, comment = {?},
                                   begin = {?}, end = {?}',
            $this->activity->id(), $this->writer->id(), $this->comment,
            $this->begin->toDb(), $this->end->toDb());
            
        $this->id = XDB::insertId();
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
