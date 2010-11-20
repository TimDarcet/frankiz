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
 *  You should have received a copy of the GNU General Public License      *
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
    const SELECT_BASE = 0x01;
    const SELECT_BODY = 0x02;

    protected $writer  = null;
    protected $target  = null;
    protected $image   = null;
    protected $origin  = null;
    protected $title   = null;
    protected $content = null;
    protected $begin   = null;
    protected $end     = null;
    protected $comment = null;
    protected $priv    = null;

    public function writer()
    {
        return $this->writer;
    }

    public function target()
    {
        return $this->target;
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

    public function delete()
    {  
	    XDB::execute('DELETE FROM news WHERE id={?}', $this->id());
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
                         SET  target = {?}, writer = {?}, iid = {?}, origin = {?},
                              title = {?}, content = {?}, end = {?},
                              comment = {?}, priv = {?}
                       WHERE  id = {?}',
        $this->target->id(), $this->writer->id(), $this->image, is_null($this->origin)?null:$this->origin->id(),
        $this->title, $this->content, $this->end,
        $this->comment, $this->priv, $this->id());
    }

    public function insert()
    {
        XDB::execute('INSERT INTO news SET id = NULL');
        $this->id = XDB::insertId();
        $this->update();
    }

    public static function batchSelect(array $news, $options = null)
    {
        if (empty($news))
            return;

        if (empty($options)) {
            $options = array(self::SELECT_BODY => null);
            $options[self::SELECT_BASE] = array('writers' => User::SELECT_BASE,
                                                'groups' => Group::SELECT_BASE);
        }

        $bits = self::optionsToBits($options);
        $news = array_combine(self::toIds($news), $news);

        $request = 'SELECT id';
        if ($bits & self::SELECT_BASE)
            $request .= ', writer, target, title, origin, begin, end, priv';
        if ($bits & self::SELECT_BODY)
            $request .= ', content, iid, comment';

        $iter = XDB::iterator($request .
                        ' FROM news
                         WHERE id IN {?}',
                         array_keys($news));

        $users  = new Collection('User');
        $groups = new Collection('Group');
        $images = new Collection('FrankizImage');
        while ($datas = $iter->next())
        {
            $datas['writer'] = $users->addget($datas['writer']);
            $datas['target'] = $groups->addget($datas['target']);
            $datas['origin'] = $groups->addget($datas['origin']);
            $datas['image']  = $images->addget($datas['iid']); unset($datas['iid']);
            $news[$datas['id']]->fillFromArray($datas);
        }

        if (!empty($options[self::SELECT_BASE]['writers']))
            $users->select($options[self::SELECT_BASE]['writers']);

        if (!empty($options[self::SELECT_BASE]['groups']))
            $groups->select($options[self::SELECT_BASE]['groups']);

        if (!empty($options[self::SELECT_BASE]['images']))
            $groups->select($options[self::SELECT_BASE]['images']);
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
        return 'other';
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
