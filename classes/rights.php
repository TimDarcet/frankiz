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
    const WEB    = 'web' ;
    const ADMIN  = 'admin';
    const MEMBER = 'member';
    const FRIEND = 'friend';

    public static function inheritances()
    {
        $inheritances = array(
            self::DESCENDING => array(self::PREZ, self::ADMIN),
            self::ASCENDING  => array(self::MEMBER),
            self::FIXED      => array(self::WEB, self::FRIEND)
        );

        return $inheritances;
    }

    public static function inheritance($searched_right)
    {
        $inheritances = self::inheritances();
        foreach ($inheritances as $inheritance => $rights)
            foreach ($rights as $right)
                if ($right == $searched_right)
                    return $inheritance;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>