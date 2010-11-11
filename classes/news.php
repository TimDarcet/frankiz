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
 *  You should have received a copy of the GNU General Public License      *
 *  along with this program; if not, write to the Free Software            *
 *  Foundation, Inc.,                                                      *
 *  59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                *
 ***************************************************************************/

/*
 * uid : people who has written the news
 * gid : group where it is publicated
 * iid : image id
 * origin : group at the origin of the news
 * private : only people in the group can see the news
 */

class News extends meta
{    
    const SELECT_HEAD = 0x01;
    const SELECT_BODY = 0x02;
    
    public $uid;
    public $gid;
    public $iid;
    public $origin;
    public $title;
    public $content;
    public $begin;
    public $end;
    public $comment;
    public $private;
    public $important;
     
    public function delete()
    {  
	    if ($this->id == null)
	        throw new Exception("This news doesn't exist.");
	    XDB::execute('DELETE FROM news WHERE id={?}', $this->id);
    }

    public function replace()
    {
        if (!$this->valid())
            throw new Exception("This news is not valid.");
        if (is_null($this->id))
            $this->insert();
        else 
            $this->update();
    }
    
    public function update()
    {        
            XDB::execute('UPDATE  news
                             SET  gid = {?}, uid = {?}, iid = {?}, origin = {?},
                                  title = {?}, content = {?}, begin = {?}, end = {?},
                                  comment = {?}, private = {?}, important = {?}
                           WHERE  id = {?}',
            $this->gid, $this->uid, $this->iid, is_null($this->origin)?0:$this->origin, 
            $this->title, $this->content, $this->begin, $this->end,
            $this->comment, $this->private, $this->important, $this->id);
    }
    
    public function insert()
    {
        XDB::execute('INSERT INTO  news
                                  SET  gid = {?}, uid = {?}, iid = {?}, origin = {?},
                                       title = {?}, content = {?}, begin = {?}, end = {?},
                                       comment = {?}, private = {?}, important = {?}',
            $this->gid, $this->uid, $this->iid, is_null($this->origin)?0:$this->origin, 
            $this->title, $this->content, $this->begin, $this->end,
            $this->comment, $this->private, $this->important);
    }
    
    public static function batchSelect(array $news, $fields)
    {
        if (empty($news))
            return;

        $news = array_combine(self::toIds($news), $news);
            
        $request = 'SELECT id';
        if ($fields & SELECT_HEAD)
            $request .= ', uid, gid, title, origin, begin, end, private, important';
        if ($fields & SELECT_BODY)
            $request .= ', content, iid, comment';
        
        $iter = XDB::iterator($request .
                        ' FROM news
                         WHERE id IN {?}',
                         array_keys($news));
                         
        while ($array_datas = $iter->next())
            $news[$array_datas['id']]->fillFromArray($array_datas);
    }
    
    public function valid() {
    	return true;
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
