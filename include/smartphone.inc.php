<?php
/***************************************************************************
 *  Copyright (C) 2004-2012 Binet Réseau                                   *
 *  http://br.binets.fr/                                                   *
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

function isSmartphone()
{
    if (!isset($_SERVER["HTTP_USER_AGENT"]))
        return false;

    $agents = array('Android',
                    'BlackBerry',
                    'iPhone',
                    'Palm',
                    'HTC',
                    'Mobile');

    foreach ($agents as $a)
        if (stripos($_SERVER["HTTP_USER_AGENT"], $a) !== false )
            return true;

     return false;
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
