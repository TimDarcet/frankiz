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

class StaticImage implements ImageInterface
{
    const BASE_PATH = '/htdocs/static/0/';

    private $path = null;
    private $x    = null;
    private $y    = null;
    private $mime = null;

    public function StaticImage($path)
    {
        global $globals;

        $basepath = $globals->spoolroot . self::BASE_PATH;

        if (file_exists($basepath . $path)) {
            $truepath = $path;
        } else {
            $parents = explode('/', $path);
            while (count($parents) > 0) {
                array_pop($parents);

                if (file_exists($basepath . implode('/', $parents) . '/_.png')) {
                    $truepath = implode('/', $parents) . '/_.png';
                    break;
                }
            }
        }

        $infos = getimagesize($basepath . $truepath);

        $this->path = $truepath;
        $this->mime = $infos['mime'];
        $this->x    = $infos[0];
        $this->y    = $infos[1];
    }

    public function path()
    {
        return $this->path;
    }

    public function x()
    {
        return $this->x;
    }

    public function y()
    {
        return $this->y;
    }

    public function mime()
    {
        return $this->mime;
    }

    /**
    * Return the src attribute to put into the img tag
    *
    * @param $bits  Size to use
    */
    public function src($size)
    {
        global $globals;

        $order = ImageSizesSet::sizeToOrder($size);

        while ($order >= 0) {
            if (file_exists($globals->spoolroot . '/htdocs/static/' . $order .'/' . $this->path())) {
                return 'static/' . $order . '/' . $this->path();
            }
            $order--;
        }

        throw new Exception('No such image');
    }

    public function send($size)
    {
        global $globals;

        $path = $globals->spoolroot . '/htdocs/' . $this->src($size);
        $infos = getimagesize($path);

        $image = new Image(array('data' => file_get_contents($path),
                                 'x' => $infos[0], 'y' => $infos[1], 'mime' => $infos['mime']));
        $image->send();
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
