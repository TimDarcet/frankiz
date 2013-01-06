<?php
/***************************************************************************
 *  Copyright (C) 2004-2013 Binet Réseau                                   *
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

class LinkSchema extends Schema
{
    public function className() {
        return 'Link';
    }

    public function table() {
        return 'links';
    }

    public function id() {
        return 'id';
    }

    public function tableAs() {
        return 'li';
    }

    public function scalars() {
        return array('link', 'label', 'description', 'ns', 'comment', 'rank');
    }

    public function objects() {
        return array('image'    => 'FrankizImage');
    }
}

class LinkSelect extends Select
{
    protected static $natives = array('link', 'label', 'description', 'ns', 'comment', 'rank', 'image');

    public function className() {
        return 'Link';
    }

    protected function handlers() {
        return array('main' => self::$natives);
    }


    public static function all() {
        return new LinkSelect(self::$natives);
    }


    public static function link() {
        return new LinkSelect(array('link', 'label', 'description', 'ns', 'comment'));
    }
}

class Link extends Meta
{
    protected $link;
    protected $label;
    protected $description;
    protected $ns;
    protected $comment;
    protected $image;
    protected $rank;

    // @param $rm_prev If the previous image must ba deleted
    public function image(FrankizImage $image = null, $rm_prev = true) 
    {
        if ($image != null) {
            if ($rm_prev && $this->image != false)
                $this->image->delete();
            $this->image = $image;
            XDB::execute('UPDATE  links
                             SET  image = {?}
                           WHERE  id = {?}', $this->image->id(), $this->id);
        }
        return $this->image;
    }

    public function insert($type = '')
    {
        XDB::execute('INSERT INTO  links
                              SET  id = NULL');
        $r = XDB::query('SELECT  MAX(rank)
                           FROM  links
                          WHERE  ns = {?}', $type)->fetchOneCell();
        $this->id = XDB::insertId();
        $this->ns($type);
        $this->rank($r+1);
    }

    // @param $rm_img If the image must be deleted
    public function delete($rm_img = true)
    {
        if ($rm_img) {
            $this->image->delete();
        }
        parent::delete();
    }

    // @param $ns The namespace on wich to filter the links
    public static function all($ns = false)
    {
        if ($ns === false) {
            $res = XDB::query('SELECT  id
                                 FROM  links');
        } else {
            $res = XDB::query('SELECT  id
                                 FROM  links
                                WHERE  ns = {?}', $ns);
        }

        $collec = new Collection('Link');
        $collec->add($res->fetchColumn());

        return $collec;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
