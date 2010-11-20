<?php
/***************************************************************************
 *  Copyright (C) 2010 Binet Réseau                                       *
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

/*
 * user : people who has written the news
 * group : group where it is publicated
 * image : image to display
 * origin : group at the origin of the news, may be null
 * priv : only people in the group can see the news
 * begin, end : dates
 */

class News extends meta
{    
    const SELECT_HEAD = 0x01;
    const SELECT_BODY = 0x02;
    
    protected $user;
    protected $group;
    protected $image;
    protected $origin = null;
    protected $title;
    protected $content;
    protected $begin;
    protected $end;
    protected $comment;
    protected $priv;
    protected $important;
     
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

    public function origin()
    {
        return $this->origin;
    }

    public function title(String $title = null)
    {
        if (is_null($title))
            return $this->title;
        $this->title = $title;
    }

    public function content(String $content = null)
    {
        if (is_null($content))
            return $this->content;
        $this->content = $content;
    }

    public function begin()
    {
        return $this->begin;
    }

    public function end(String $end = null)
    {
        if (is_null($end))
            return $this->end;
        $this->end = $end;
    }

    public function comment()
    {
        return $this->comment;
    }

    public function priv($priv = null)
    {
        if (is_null($priv))
            return $this->priv;
        $this->priv = $priv;
    }

    public function important($important = null)
    {
        if (is_null($important))
            return $this->important;
        $this->important = $important;
    }
    
    public function delete()
    {  
	    if ($this->id == null)
	        throw new Exception("This news doesn't exist.");
	    XDB::execute('DELETE FROM news WHERE id={?}', $this->id);
    }

    public function replace()
    {
        // TO DO
        //if (!$this->valid())
        //    throw new Exception("This news is not valid.");
        if (is_null($this->id))
            $this->insert();
        else 
            $this->update();
    }
    
    public function update()
    {        
            XDB::execute('UPDATE  news
                             SET  gid = {?}, uid = {?}, iid = {?}, oid = {?},
                                  title = {?}, content = {?}, end = {?},
                                  comment = {?}, priv = {?}, important = {?}
                           WHERE  id = {?}',
            $this->group->id(), $this->user->id(), $this->image, is_null($this->origin)?null:$this->origin->id(), 
            $this->title, $this->content, $this->end,
            $this->comment, $this->priv, $this->important, $this->id);
    }
    
    public function insert()
    {
        $this->begin = date("Y-m-d");
        XDB::execute('INSERT INTO  news
                              SET  gid = {?}, uid = {?}, iid = {?}, oid = {?},
                                   title = {?}, content = {?}, begin = {?}, end = {?},
                                   comment = {?}, priv = {?}, important = {?}',
            $this->group->id(), $this->user->id(), $this->image, is_null($this->origin)?null:$this->origin->id(),
            $this->title, $this->content, $this->begin, $this->end,
            $this->comment, $this->priv, $this->important);
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
    
        if (isset($values['oid'])) {
            $this->origin = new Group($values['oid']);
            $this->origin->select(Group::SELECT_BASE);
            unset($values['oid']);
        }

        parent::fillFromArray($values);
    }
    
    public static function batchSelect(array $news, $fields = null)
    {
        if (empty($news))
            return;

        $news = array_combine(self::toIds($news), $news);
            
        $request = 'SELECT id';
        if ($fields & self::SELECT_HEAD)
            $request .= ', uid, gid, title, oid, begin, end, priv, important';
        if ($fields & self::SELECT_BODY)
            $request .= ', content, iid, comment';
        
        $iter = XDB::iterator($request .
                        ' FROM news
                         WHERE id IN {?}',
                         array_keys($news));
                         
        while ($array_datas = $iter->next())
            $news[$array_datas['id']]->fillFromArray($array_datas);
    }
    
    public function order()
    {
        $d = date("Y-m-d");
        if ($this->important)
            return 'important';
        if ($this->begin == $d)
            return 'new';
        if ($this->end == $d)
            return 'old';
        else return 'other';
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
