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

interface ImageInterface
{
    /*******************************************************************************
         Constants

    *******************************************************************************/

    // Maxs for the original picture
    const MAX_WIDTH  = 800;
    const MAX_HEIGHT = 600;

    // Sizes for the miniature
    const SMALL_WIDTH   = 140;
    const SMALL_HEIGHT  = 105;
    const SMALL_QUALITY = 75;

    // Sizes for the stamp-size picture
    const MICRO_WIDTH   = 35;
    const MICRO_HEIGHT  = 35;
    const MICRO_QUALITY = 75;

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
