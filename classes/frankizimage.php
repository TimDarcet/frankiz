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

class FrankizImageSchema extends Schema
{
    public function className() {
        return 'FrankizImage';
    }

    public function table() {
        return 'images';
    }

    public function id() {
        return 'iid';
    }

    public function tableAs() {
        return 'i';
    }

    public function scalars() {
        return array('label', 'seen');
    }

    public function objects() {
        return array('caste' => 'Caste',
                  'lastseen' => 'FrankizDateTime',
                   'created' => 'FrankizDateTime');
    }
}

class FrankizImageSelect extends Select
{
    public function className() {
        return 'FrankizImage';
    }

    public static function base() {
        return new self(array('label', 'seen', 'lastseen', 'created'));
    }

    public static function caste() {
        return new self(array('caste'), array('caste' => CasteSelect::base()));
    }

    public static function image($size) {
        return new self(array('images'), $size);
    }

    protected function handlers() {
        return array('main' => array('label', 'seen', 'lastseen', 'created', 'caste'),
                   'images' => array('images'));
    }

    protected function handler_images(Collection $frankizimages, $size) {
        $_frankizimages = array();
        foreach($frankizimages as $frankizimage) {
            $_frankizimages[$frankizimage->id()] = array();
        }

        $iter = XDB::iterRow("SELECT  iid, size, x, y, data
                                FROM  images_sizes
                               WHERE  size IN {?} AND iid IN {?}",
                                      $fields, $frankizimages->ids());

        while ($datas = $iter->next()) {
            $_frankizimages[$datas['iid']][$datas['size']] = new Image($datas);
        }

        foreach ($frankizimages as $frankizimage) {
            $frankizimage->fillFromArray(array('images' => $_frankizimages[$frankizimage->id()]));
        }
    }
}

class FrankizImage extends Meta implements ImageInterface
{
    protected $label = null;

    // The caste which owns the image
    protected $caste = null;

    // Number of times the full image was seen
    protected $seen = null;
    // The last time the full image was seen
    protected $lastseen = null;
    // When the image has been uploaded
    protected $created = null;

    // Differents levels of sizes
    protected $images = null;

    public function size()
    {
        return reset($this->images->size());
    }

    /*
     * /!\ send() is independant of the data fetcher !
     * If fetches the data best fiting to the asked $size.
     */
    public function send($size)
    {
        global $globals;

        $size_order = ImageSizesSet::sizeToOrder($size);

        // Update the counter if we are sending the 'full' image
        if ($size_order === 0) {
            XDB::execute('UPDATE images SET seen = seen + 1, lastseen = NOW() WHERE iid = {?}', $this->id());
        }

        $res = XDB::query("SELECT  mime, data
                             FROM  images_sizes
                            WHERE  size <= {?} AND iid = {?}
                         ORDER BY  size DESC
                            LIMIT  1", $size_order, $this->id());

        if ($res->numRows() != 1) {
            throw new Exception("The image (" . $this->id() . ") couldn't be fetched in size < $size");
        }

        $image = new Image($res->fetchOneAssoc());
        $image->send();
    }

    /**
    * Return the src attribute to put into the img tag
    *
    * @param $bits  Size to use
    */
    public function src($size)
    {
        return "image/$size/" . $this->id();
    }

    /**
    * Build the image from a FrankizUpload instance, stores it into the DB
    *
    * @param $fu      Instance of FrankizUpload
    * @param $rm      Should the temporary file be removed after ? (Default: yes)
    */
    public function image(FrankizUpload $fu, $rm = true)
    {
        $infos = getimagesize($fu->path());
        $this->mime = $infos['mime'];
        $this->x    = $infos[0];
        $this->y    = $infos[1];

        if ($this->x > self::MAX_WIDTH || $this->y > self::MAX_HEIGHT) {
            $e = new ImageSizeException('The picture is to big: ('.$this->x.'x'.$this->y.') > ('.self::MAX_WIDTH.'x'.self::MAX_HEIGHT.')');
            $e->size(new ImageSize($this->x, $this->y));
            $e->allowed(new ImageSize(self::MAX_WIDTH, self::MAX_HEIGHT));
            throw $e;
        }

        $this->images = ImageSizesSet::resize(file_get_contents($fu->path()));

        if ($rm) {
            $fu->rm();
        }

        foreach ($this->images as $size => $image) {
            $size_order = ImageSizesSet::sizeToOrder($size);
            XDB::execute('INSERT INTO  images_sizes
                                  SET  iid = {?}, size = {?}, mime = {?}, x = {?}, y = {?}, data = {?}
              ON DUPLICATE KEY UPDATE  mime = {?}, x = {?}, y = {?}, data = {?}',
                                       $this->id(), $size_order, $image->mime, $image->x, $image->y, $image->data,
                                       $image->mime, $image->x, $image->y, $image->data);
        }
    }

    public function insert()
    {
        XDB::execute('INSERT INTO images SET seen = 0, lastseen = NOW()');
        $this->id = XDB::insertId();
    }

    public function delete()
    {
        XDB::execute('DELETE FROM images WHERE iid = {?}', $this->id());
        XDB::execute('DELETE FROM images_sizes WHERE iid = {?}', $this->id());
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
