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

class FrankizImage extends Meta implements ImageInterface
{
    /*******************************************************************************
         Properties

    *******************************************************************************/

    protected $label;
    protected $description;

    // Size in Bytes
    protected $size;

    // Size in pixels
    protected $x;
    protected $y;

    protected $mime;

    // The group which owns the image
    protected $group;

    // Number of times the full image was seen
    protected $seen;
    // The last time the full image was seen
    protected $lastseen;

    // A miniature of the image (max: SMALL_WIDTH x SMALL_HEIGHT)
    protected $small = null;
    // The image
    protected $full = null;

    /*******************************************************************************
         Getters & Setters

    *******************************************************************************/

    public function label($label = null)
    {
        if ($label != null) {
            $this->label = $label;
            XDB::execute('UPDATE images SET label = {?} WHERE iid = {?}', $this->label, $this->id());
        }

        return $this->label;
    }

    public function description($description = null)
    {
        if ($description != null) {
            $this->description = $description;
            XDB::execute('UPDATE images SET description = {?} WHERE iid = {?}', $this->description, $this->id());
        }

        return $this->description;
    }

    public function group(Group $group = null)
    {
        if ($group != null) {
            $this->group = $group;
            XDB::execute('UPDATE images SET gid = {?} WHERE iid = {?}', $this->group->id(), $this->id());
        }

        return $this->group;
    }

    public function mime()
    {
        return $this->mime;
    }

    protected function smallMime()
    {
        return ($this->mime == 'image/png') ? 'image/png' : 'image/jpeg';
    }

    protected static function data_uri($content, $mime)
    {
        return 'data:' . $mime . ';base64,' . base64_encode($content);
    }

    public function x()
    {
        return $this->x;
    }

    public function y()
    {
        return $this->y;
    }

    public function size()
    {
        if (!empty($this->size))
            return $this->size;

        return strlen($this->full);
    }

    public function seen()
    {
        return $this->seen;
    }

    public function lastseen()
    {
        return $this->lastseen;
    }

    /*******************************************************************************
         Show the image

    *******************************************************************************/

    public function send($bits = self::SELECT_FULL)
    {
        global $globals;

        if (($bits & self::SELECT_FULL) && (!empty($this->full))) {
            XDB::execute('UPDATE images SET seen = seen + 1, lastseen = NOW() WHERE iid = {?}', $this->id());
            pl_cached_dynamic_content_headers($this->mime);
            echo $this->full;
            exit;
        }

        if (!empty($this->small)) {
            pl_cached_dynamic_content_headers($this->smallMime());
            echo $this->small;
            exit;
        }

        // Fallback image as specified in the configuration file
        $fallback = new FrankizImage($globals->image);
        $fallback->select($bits)->send($bits);
    }

    public function inline()
    {
        return self::data_uri($this->full, $this->mime);
    }

    public function inlineSmall()
    {
        return self::data_uri($this->small, $this->smallMime());
    }

    /**
    * Return the html code to print the image
    * Ex: {$image->html()|smarty:nodefaults}
    *
    * @param $bits  Size to use
    */
    public function html($bits = self::SELECT_SMALL)
    {
        $small = ($bits == self::SELECT_SMALL) ? '?small' : '';
        return '<a href="image/' . $this->id() . '"><img src="image/' . $this->id() . $small . '" /></a>';
    }

    /**
    * Return the src attribute to put into the img tag
    *
    * @param $bits  Size to use
    */
    public function src($bits = self::SELECT_SMALL)
    {
        $small = ($bits == self::SELECT_SMALL) ? '?small' : '';
        return 'image/' . $this->id() . $small;
    }

    /*******************************************************************************
         Build the image

    *******************************************************************************/

    protected function makeSmall($im = null)
    {
        if ($this->x <= self::SMALL_WIDTH && $this->y <= self::SMALL_HEIGHT)
            return null;

        if (empty($im)) {
            $im = new Imagick();
            $im->readImageBlob($this->full);
        }

        if ($im->getImageWidth() > self::SMALL_WIDTH)
            $im->thumbnailImage(self::SMALL_WIDTH, null, false);

        if ($im->getImageHeight() > self::SMALL_HEIGHT)
            $im->thumbnailImage(null, self::SMALL_HEIGHT, false);

        $im->setImageCompressionQuality(self::SMALL_QUALITY);
        $im->stripImage();

        return $im->getimageblob();
    }

    /**
    * Build the image from a FrankizUpload instance, stores it into the DB
    *
    * @param $fu  Instance of FrankizUpload
    * @param $rm  Should the temporary file be removed after ? (Default: yes)
    */
    public function image(FrankizUpload $fu, $rm = true)
    {
        $infos = getimagesize($fu->path());
        $this->mime = $infos['mime'];
        $this->x    = $infos[0];
        $this->y    = $infos[1];

        if ($this->x > self::MAX_WIDTH || $this->y > self::MAX_HEIGHT)
            throw new Exception('The picture is to big: ('.$this->x.'x'.$this->y.') > ('.self::MAX_WIDTH.'x'.self::MAX_HEIGHT.')');

        $this->full = file_get_contents($fu->path());
        $this->small = $this->makeSmall();
        if ($rm)
            $fu->rm();

        XDB::execute('UPDATE  images
                         SET  full = {?}, small = {?},
                              mime= {?}, x = {?}, y = {?}
                       WHERE  iid = {?}',
                             $this->full, $this->small, $this->mime,
                             $this->x, $this->y, $this->id());
    }

    /*******************************************************************************
         Data fetcher
             (batchFrom, batchSelect, fillFromArray, …)
    *******************************************************************************/

    public static function batchSelect(array $images, $options = null)
    {
        if (empty($options)) {
            $options = self::SELECT_BASE;
        }

        $bits = self::optionsToBits($options);
        $images = array_combine(self::toIds($images), $images);

        $cols = '';
        if ($bits & self::SELECT_BASE)
            $cols .= ', gid, mime, x, y, label, seen, lastseen, OCTET_LENGTH(full) size';
        if ($bits & self::SELECT_FULL)
            $cols .= ', gid, mime, full';
        if ($bits & self::SELECT_SMALL)
            $cols .= ', gid, mime, IFNULL(small, full) AS small';

        if ($cols != '') {
            $iter = XDB::iterator("SELECT  iid AS id $cols
                                    FROM  images
                                   WHERE  iid IN {?}", array_keys($images));

            $groups = new Collection('Group');
            while ($datas = $iter->next()) {
                $datas['group'] = $groups->addget($datas['gid']); unset($datas['gid']);
                $images[$datas['id']]->fillFromArray($datas);
            }

            if (isset($options[self::SELECT_BASE]))
                $groups->select($options[self::SELECT_BASE]);
        }
    }

    /**
    * Insert an empty image
    *
    */
    public function insert()
    {
        XDB::execute('INSERT INTO images SET seen = 0, lastseen = NOW()');
        $this->id = XDB::insertId();
    }

    public function delete()
    {
        XDB::execute('DELETE FROM images WHERE iid = {?}', $this->id());
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
