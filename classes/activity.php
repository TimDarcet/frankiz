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
    const SELECT_HEAD = 0x01;
    const SELECT_IMAGE = 0x02;
    
    protected $user;
    protected $group;
    protected $image;
    protected $title;
    protected $description;
    protected $begin;
    protected $end;
     
    public function user()
    {
        return $this->user;
    }

    public function group()
    {
        return $this->group;
    }

    public function image(FrankizImage $fi = null)
    {
        if (is_null($fi))
            return $this->image;
        $this->image = $fi;
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

    public function delete()
    {  
        if ($this->id == null)
            throw new Exception("This activity doesn't exist.");
        XDB::execute('DELETE FROM activities WHERE id={?}', $this->id);
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
                         SET  gid = {?}, uid = {?}, iid = {?},
                              title = {?}, description = {?}, begin = {?},
                              end = {?},
                       WHERE  id = {?}',
            $this->group->id(), $this->user->id(), $this->image, 
            $this->title, $this->description, $this->begin,
            $this->end, $this->id);
    }
    
    public function insert()
    {
        XDB::execute('INSERT INTO  activities
                              SET  gid = {?}, uid = {?}, iid = {?},
                                   title = {?}, description = {?}, begin = {?},
                                   end = {?}',
            $this->group->id(), $this->user->id(), $this->image,
            $this->title, $this->description, $this->begin,
            $this->end);
    }
    
    public function fillFromArray(array $values)
    {
        if (isset($values['uid'])) {
            $this->user = new User($values['uid']);
            $this->user->select(User::SELECT_BASE);
            unset($values['uid']);
        }

        if (isset($values['gid'])) {
            $this->group = new Group($values['gid']);
            $this->group->select(Group::SELECT_BASE);
            unset($values['gid']);
        }
        
        if (isset($values['iid'])) {
            /*$this->image = new FrankizImage($values['iid']);
            $this->image->select(FrankizImage::SELECT_FULL);*/
            $this->image = $values['iid'];
            unset($values['iid']);
        }

        parent::fillFromArray($values);
    }
    
    public static function batchSelect(array $activities, $fields)
    {
        if (empty($activities))
            return;

        $activities = array_combine(self::toIds($activities), $activities);
            
        $request = 'SELECT id';
        if ($fields & self::SELECT_HEAD)
            $request .= ', uid, gid, title, description, begin, end';
        if ($fields & self::SELECT_IMAGE)
            $request .= ', iid';
        
        $iter = XDB::iterator($request .
                        ' FROM activities
                         WHERE id IN {?}',
                         array_keys($activities));
                         
        while ($array_datas = $iter->next())
            $activities[$array_datas['id']]->fillFromArray($array_datas);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
