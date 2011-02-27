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

class Image {
    private static $mimes =
        array(0 => 'image/jpeg',
              1 => 'image/png',
              2 => 'image/gif');

    public $data;
    public $mime;
    public $x;
    public $y;

    public function __construct($datas = array()) {
        foreach ($datas as $k => $v) {
            $this->$k = $v;
        }
    }

    public function mimeType() {
        return self::$mimes[$this->mime];
    }

    public static function mimeToCode($mime) {
        return array_search($mime, self::$mimes);
    }

    public static function fromImagick(Imagick $im) {
        $i = new Image();
        $i->data = $im->getImageBlob();
        $i->mime = self::mimeToCode($im->getImageMimeType());
        $i->x = $im->getImageWidth();
        $i->y = $im->getImageHeight();
        return $i;
    }

    public function send() {
        pl_cached_dynamic_content_headers($this->mimeType());
        echo $this->data;
        exit;
    }

    public function size() {
        return strlen($this->data);
    }
}

class ImageSize
{
    public $x; // Width
    public $y; // Height
    public $q; // Quality

    public function __construct($x, $y, $q = -1) {
        $this->x = $x;
        $this->y = $y;
        $this->q = $q;
    }

    public function export() {
        return array($this->x, $this->y, $this->q);
    }

    public static function fromExport($export) {
        return new self($export[0], $export[1], $export[2]);
    }

    private static $_MAX_SIZE = null;

    public final static function MAX() {
        global $globals;

        if (self::$_MAX_SIZE === null) {
            self::$_MAX_SIZE = ImageSize::fromExport(json_decode($globals->sizes->max));
        }

        return self::$_MAX_SIZE;
    }
    
    public final static function MAX_WIDTH() {
        return self::MAX()->x;
    }

    public final static function MAX_HEIGHT() {
        return self::MAX()->y;
    }
}

class ImageSizesSet
{
    public static $sizes = null;
    public static $order = null;

    protected static function loadConf() {
        global $globals;

        if (self::$sizes == null) {
            self::$sizes = array();
            self::$order = json_decode($globals->sizes->order);
            foreach (self::$order as $size) {
                self::$sizes[$size] = ImageSize::fromExport(json_decode($globals->sizes->$size));
            }
        }
    }

    public static function sizeToOrder($size) {
        self::loadConf();

        $order = array_search($size, self::$order);
        if ($order === false) {
            throw new Exception("This size ($size) of image doesn't exist");
        }
        return $order;
    }

    private static function setResize(Imagick $im, ImageSize $size, $force = false) {
        if ($im->getImageWidth() <= $size->x && $im->getImageHeight() <= $size->y) {
            if ($force) {
                return Image::fromImagick($im);
            } else {
                return null;
            }
        }

        $im->thumbnailImage($size->x, $size->y, true);
        $im->setImageCompressionQuality($size->q);
        //$im->stripImage(); Usefull ?

        return Image::fromImagick($im);
    }

    public static function resize($blob_image) {
        self::loadConf();

        $im = new Imagick();
        $im->readImageBlob($blob_image);

        $images = array();

        $first = true;
        foreach (self::$sizes as $size => $imsize) {
            $image = self::setResize($im, $imsize, $first);

            if ($image !== null) {
                $images[$size] = $image;
            }
            $first = false;
        }

        return $images;
    }
}

class ImageSizeException extends Exception
{
    private $size;
    private $allowed;

    public function size(ImageSize $size) {
        if ($size !== null) {
            $this->size = $size;
        }
        return $this->size;
    }

    public function allowed(ImageSize $allowed) {
        if ($allowed !== null) {
            $this->allowed = $allowed;
        }
        return $this->allowed;
    }
}

class ImageFormatException extends Exception
{
}

interface ImageInterface
{
    /**
    * Returns the src attribute to put into the img tag
    *
    * @param $bits  Size to use
    */
    public function src($size);

    public function send($size);
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
