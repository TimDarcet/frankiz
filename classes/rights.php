<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                        *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                      *
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

class Rights
{
    // Types of inheritance for the rights
    const ASCENDING  = 'ascending';
    const DESCENDING = 'descending';
    const FIXED      = 'fixed';

    // Existing rights
    const PREZ   = 'prez';
    const WEB    = 'web';
    const ADMIN  = 'admin';
    const SUPER  = 'super';
    const MEMBER = 'member';
    const FRIEND = 'friend';

    static $rights =
            array(
                self::PREZ   => self::DESCENDING,
                self::ADMIN  => self::DESCENDING,
                self::SUPER  => self::DESCENDING,
                self::MEMBER => self::ASCENDING,
                self::WEB    => self::FIXED,
                self::FRIEND => self::FIXED
            );

    public static function inheritance($right = null)
    {
        return ($right == null) ? self::$rights : self::$rights[$right];
    }

    public static function emptyLayout()
    {
        $emptyLayout = array();
        $rights = array_keys(self::$rights);
        foreach ($rights as $right)
            $emptyLayout[$right] = Collection::fromClass('Group');

        return $emptyLayout;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
