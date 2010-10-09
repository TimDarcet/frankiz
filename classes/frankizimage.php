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

class FrankizImage
{
    const MAX_WIDTH  = 600;
    const MAX_HEIGHT = 600;

    const SMALL_WIDTH = 100;
    const SMALL_HEIGHT = 100;
    const SMALL_QUALITY = 75;

    // the id of the image in the DB
    protected $id;
    // About the picture
    protected $comment;

    protected $size;
    protected $mime;
    protected $x;
    protected $y;

    // Number of times the full image was seen
    protected $seen;
    // The alst time the full image was seen
    protected $lastseen;
    // A miniature of the image (max: SMALL_WIDTH x SMALL_HEIGHT)
    protected $small = null;
    // The image
    protected $full = null;

    const SELECT_BASE  = 0x01;
    const SELECT_FULL  = 0x02;
    const SELECT_SMALL = 0x04;

    public function __construct($datas = null)
    {
        if (!is_array($datas))
            $this->id = $datas;
        else
            $this->fillFromArray($datas);
    }

    public function id()
    {
        return $this->id;
    }

    public function comment()
    {
        return $this->comment;
    }

    public function mime()
    {
        return $this->mime;
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

    public function fillFromArray(array $values)
    {
        foreach ($values as $key => $value)
            if (property_exists($this, $key))
                $this->$key = $value;
    }

    public static function toIds(array $images)
    {
        $result = array();
        foreach ($images as $n)
            if ($n instanceof FrankizImage)
                $result[] = $n->id;
            else
                $result[] = $n;
        return $result;
    }

    public function select($bits)
    {
        self::batchSelect(array($this), $bits);
        return $this;
    }

    public static function batchSelect(array $images, $bits)
    {
        $ids = self::toIds($images);

        $cols = '';
        if ($bits & self::SELECT_BASE)
            $cols .= ', mime, x, y, comment, seen, lastseen, OCTET_LENGTH(full) size';
        if ($bits & self::SELECT_FULL)
            $cols .= ', mime, full';
        if ($bits & self::SELECT_SMALL)
            $cols .= ', mime, IFNULL(small, full) AS small'; 

        if ($cols != '') {
            $res = XDB::query("SELECT  iid AS id $cols
                                 FROM  images
                                WHERE  iid IN {?}", $ids);

            $ids_datas = $res->fetchAllAssoc('id');
            foreach ($images as $i)
                $i->fillFromArray($ids_datas[$i->id]);
        }
    }

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

        /*$im->setImageCompression(Imagick::COMPRESSION_JPEG);
        Imagick::COMPRESSION_BZIP*/
        $im->setImageCompressionQuality(self::SMALL_QUALITY);
        $im->stripImage();

        return $im->getimageblob();

//        $source = imagecreatefromstring($this->data);
//
//        $nx = $this->x;
//        $ny = $this->y;
//        if ($nx > self::SMALL_WIDTH) {
//            $ny = intval($ny*self::SMALL_WIDTH/$nx);
//            $nx = self::SMALL_WIDTH;
//        }
//        if ($ny > self::SMALL_HEIGHT) {
//            $nx = intval($nx*self::SMALL_HEIGHT/$ny);
//            $ny = self::SMALL_HEIGHT;
//        }
//
//        $small = imagecreatetruecolor($nx, $ny);
//        imagealphablending($small, false);
//        imagesavealpha($small, true);
//        imagecopyresampled($small, $source, 0, 0, 0, 0, $nx, $ny, $this->x, $this->y);
//
//        ob_start();
//        if ($this->mime == "image/png")
//            imagepng($small, null, round(abs((100 - self::SMALL_QUALITY) / 11.111111)));
//        else
//            imagejpeg($small, null, self::SMALL_QUALITY);
//        return ob_get_clean();
    }

    public function insert()
    {
        XDB::execute('INSERT INTO  images
                              SET  mime = {?}, x = {?}, y = {?},
                                   seen = 0, lastseen = NOW(),
                                   small = {?}, full = {?}, comment = {?}',
                                   $this->mime, $this->x, $this->y,
                                   $this->small, $this->full, $this->comment);
        $this->iid = XDB::insertId();
    }
    
    public function loadPdf($path, $page = 0)
    {
        $im = new Imagick($path . '[' . $page . ']');
        $im->setImageFormat('jpg');

        if ($im->getImageWidth() > self::MAX_WIDTH)
            $im->thumbnailImage(self::MAX_WIDTH, null, false);

        if ($im->getImageHeight() > self::MAX_HEIGHT)
            $im->thumbnailImage(null, self::MAX_HEIGHT, false);

        $im->setImageCompression(Imagick::COMPRESSION_JPEG);
        $im->setImageCompressionQuality(self::SMALL_QUALITY);
        $im->stripImage();

        $this->full = $im->getimageblob();
        $this->mime = 'image/jpeg';
        $this->x    = $im->getImageWidth();
        $this->y    = $im->getImageHeight();
        $this->small = $this->makeSmall($im);

        return $this;
    }

    public function loadUpload(FrankizUpload $fu, $rm = true)
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

        return $this;
    }

    protected function smallMime()
    {
        return ($this->mime == 'image/png') ? 'image/png' : 'image/jpeg';
    }

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

    protected static function data_uri($content, $mime) 
    {
        return 'data:' . $mime . ';base64,' . base64_encode($content);
    }

    public function inline()
    {
        return self::data_uri($this->full, $this->mime);
    }

    public function inlineSmall()
    {
        return self::data_uri($this->small, $this->smallMime());
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
