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
}

class ImageSizesSet
{
    const TOL   = 'tol';
    const GROUP = 'group';
    const NEWS  = 'news';

    public $full;
    public $small;
    public $micro;

    public function __construct($full, $small, $micro) {
        $this->full  = $full;
        $this->small = $small;
        $this->micro = $micro;
    }

    protected static function fromExport($export) {
        return new self(ImageSize::fromExport($export->full),
                        ImageSize::fromExport($export->small),
                        ImageSize::fromExport($export->micro));
    }

    public static function __callStatic($name, $arguments) {
        global $globals;

        $export = json_decode($globals->sizes->$name);
        return self::fromExport($export);
    }

    private static function setResize(Imagick $im, ImageSize $size, $force = false) {
        if (!$force && $im->getImageWidth()  <= $size->x
                    && $im->getImageHeight() <= $size->y) {
            return null;
        }

        if ($im->getImageWidth() > $size->x) {
            $im->thumbnailImage($size->x, null, false);
        }

        if ($im->getImageHeight() > $size->y) {
            $im->thumbnailImage(null, $size->y, false);
        }

        $im->setImageCompressionQuality($size->q);
        $im->stripImage();

        return $im->getImageBlob();
    }

    public function resize($blob_image) {
        $im = new Imagick();
        $im->readImageBlob($blob_image);

        $images = array();

        $images['full']  = self::setResize($im, $this->full, true);
        $images['small'] = self::setResize($im, $this->small);
        $images['micro'] = self::setResize($im, $this->micro);

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

interface ImageInterface
{
    /*******************************************************************************
         Constants

    *******************************************************************************/

    const MAX_WIDTH  = 1024;
    const MAX_HEIGHT = 1024;

    const SELECT_BASE  = 0x01;
    const SELECT_FULL  = 0x02;
    const SELECT_SMALL = 0x04;
    const SELECT_MICRO = 0x08;

    /*******************************************************************************
         Getters & Setters

    *******************************************************************************/

    /**
    * Returns the width of the original picture
    */
    public function x();

    /**
    * Returns the height of the original picture
    */
    public function y();

    /**
    * Returns the src attribute to put into the img tag
    *
    * @param $bits  Size to use
    */
    public function src($bits = self::SELECT_SMALL);
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
