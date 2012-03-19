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

class NewsSchema extends Schema
{
    public function className() {
        return 'News';
    }

    public function table() {
        return 'news';
    }

    public function id() {
        return 'id';
    }

    public function tableAs() {
        return 'n';
    }

    public function scalars() {
        return array('title', 'content', 'comment');
    }

    public function objects() {
        return array('writer' => 'User',
                     'target' => 'Caste',
                      'image' => 'FrankizImage',
                     'origin' => 'Group',
                      'begin' => 'FrankizDateTime',
                        'end' => 'FrankizDateTime',
                       'read' => 'FrankizDateTime',
                       'star' => 'FrankizDateTime');
    }
}

class NewsSelect extends Select
{
    protected static $natives = array('title', 'content', 'comment', 'writer',
                                     'target', 'image', 'origin', 'begin', 'end');

    public function className() {
        return 'News';
    }

    public static function base($subs = null) {
        return new NewsSelect(array('title', 'origin', 'target'), $subs);
    }

    public static function head($subs = null) {
        return new NewsSelect(array('title', 'origin', 'target', 'writer', 'begin', 'end', 'read', 'star'),
                              array('origin' => GroupSelect::base(),
                                    'writer' => UserSelect::base()));
    }

    public static function news() {
        return new NewsSelect(array_merge(self::$natives, array('read', 'star')),
                              array('writer' => UserSelect::base(),
                                    'target' => CasteSelect::group(),
                                    'origin' => GroupSelect::base()));
    }

    protected function handlers() {
        return array('main' => self::$natives,
                     'read' => array('read'),
                     'star' => array('star'));
    }

    protected function handler_read(Collection $news, $fields) {
        foreach ($news as $n) {
            $n->fillFromArray(array('read' => false));
        }

        $iter = XDB::iterRow("SELECT  uid, news, time
                                FROM  news_read
                               WHERE  news IN {?} AND uid = {?}", $news->ids(), S::user()->id());

        while (list($uid, $nid, $time) = $iter->next()) {
            $news->get($nid)->fillFromArray(array('read' => new FrankizDateTime($time)));
        }
    }

    protected function handler_star(Collection $news, $fields) {
        foreach ($news as $n) {
            $n->fillFromArray(array('star' => false));
        }

        $iter = XDB::iterRow("SELECT  uid, news, time
                                FROM  news_star
                               WHERE  news IN {?} AND uid = {?}", $news->ids(), S::user()->id());

        while (list($uid, $nid, $time) = $iter->next()) {
            $news->get($nid)->fillFromArray(array('star' => new FrankizDateTime($time)));
        }
    }
}

class News extends meta
{
    /*******************************************************************************
         Properties

    *******************************************************************************/

    protected $writer  = null;
    protected $target  = null;
    protected $image   = null;
    protected $origin  = null;
    protected $title   = null;
    protected $content = null;
    protected $begin   = null;
    protected $end     = null;
    protected $comment = null;

    /*
     * /!\ read & star a special fields, as their value
     * depends on the current user
     */
    protected $read    = null;
    protected $star    = null;

    /*******************************************************************************
         Getters & Setters

    *******************************************************************************/

    public function read($read = null)
    {
        if ($read !== null) {
            self::batchRead(array($this), $read);
            $this->read = $read;
        }

        return $this->read;
    }

    public static function batchRead(array $news, $read = null)
    {
        if (empty($news) || $read === null) {
            return;
        }

        $ids = self::toIds($news);

        if ($read === true) {
            $values = array();
            foreach ($ids as $id)
                $values[] = XDB::format("({?}, {?}, NOW())", S::user()->id(), $id);

            XDB::execute('INSERT INTO  news_read (uid, news, time)
                               VALUES  ' . implode(',', $values) . '
              ON DUPLICATE KEY UPDATE  time = NOW()');
        }

        if ($read === false) {
            XDB::execute('DELETE FROM news_read WHERE uid = {?} AND news IN {?}', S::user()->id(), $ids);
        }
    }

    public function removeReadFlags()
    {
        XDB::execute('DELETE FROM news_read WHERE news = {?}', $this->id());
    }

    public function star($star = null)
    {
        if ($star === true) {
            XDB::execute('INSERT INTO  news_star
                                  SET  uid = {?}, news = {?}, time = NOW()
              ON DUPLICATE KEY UPDATE  time = NOW()', S::user()->id(), $this->id());
            $this->star = true;
        }
        if ($star === false) {
            XDB::execute('DELETE FROM news_star WHERE uid = {?} AND news = {?}', S::user()->id(), $this->id());
            $this->star = false;
        }
        return $this->star;
    }

    /**
    * Image getter & setter
    *
    * @param $image A FrankizImage or false
    */
    public function image($image = null)
    {
        if ($image != null) {
            if ($this->image) {
                $this->image->delete();
            }
            $this->image = $image;
            XDB::execute('UPDATE news SET image = {?} WHERE id = {?}',
                                          $image->id(), $this->id());
        }
        return $this->image;
    }

    public function delete()
    {
        XDB::execute('DELETE FROM news WHERE id = {?}', $this->id());
        XDB::execute('DELETE FROM news_read WHERE news = {?}', $this->id());
        XDB::execute('DELETE FROM news_star WHERE news = {?}', $this->id());
        if ($this->image()) {
            $this->image()->delete();
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
