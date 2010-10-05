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

function __autoload($cls)
{
    if (!pl_autoload($cls)) {
        $cls = strtolower($cls);
        if (substr($cls, 0, 4) == 'ufc_' || substr($cls, 0, 4) == 'ufo_') {
            __autoload('userfilter');
            return;
        } else if (substr($cls, 0, 4) == 'pfc_' || substr($cls, 0, 4) == 'pfo_' || substr($cls, 0, 8) == 'plfilter') {
            __autoload('plfilter');
            return;
        }
        include "$cls.inc.php";
    }
}

function format_phone_number($tel)
{
    $tel = trim($tel);

    if (substr($tel, 0, 3) === '(0)')
        $tel = '33' . $tel;

    $tel = preg_replace('/\(0\)/',  '', $tel);
    $tel = preg_replace('/[^0-9]/', '', $tel);
    if (substr($tel, 0, 2) === '00') {
        $tel = substr($tel, 2);
    } else if(substr($tel, 0, 1) === '0') {
        $tel = '33' . substr($tel, 1);
    }
    return $tel;
}

function flatten($var)
{
    if (is_array($var) && count($var) <= 1)
        return array_pop($var);
    else
        return $var;
}

function unflatten($var)
{
    if (!is_array($var))
        return array($var);
    else
        return $var;
}

function isId($mixed)
{
    return (intval($mixed).'' == $mixed);
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
