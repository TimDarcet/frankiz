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
    private $path = null;
    private $x    = null;
    private $y    = null;
    private $mime = null;

    public function StaticImage($path)
    {
        global $globals;
        
        $infos = getimagesize($globals->spoolroot . '/htdocs/static/' . $path);

        $this->path = $path;
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
    * Return the html code to print the image
    * Ex: {$image->html()|smarty:nodefaults}
    *
    * @param $bits  Size to use
    */
    public function html($bits = self::SELECT_SMALL)
    {
        $small = ($bits == self::SELECT_SMALL) ? '?small' : '';
        return '<img src="static/' . $this->path() . '" />';
    }

    /**
    * Return the src attribute to put into the img tag
    *
    * @param $bits  Size to use
    */
    public function src($bits = self::SELECT_SMALL)
    {
        $small = ($bits == self::SELECT_SMALL) ? '?small' : '';
        return 'static/' . $this->path();
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
