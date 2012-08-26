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

class Json
{
    private static $json = null;

    private static function _json()
    {
        if (is_null(self::$json)) {
            self::$json = json_decode(Env::v('json'));
        }
        return self::$json;
    }

    public static function _get($key, $default)
    {
        $json = self::_json();
        return isset(self::$json->$key) ? self::$json->$key : $default;
    }

    public static function has($key)
    {
        $json = self::_json();
        return isset($json->$key);
    }

    public static function v($key, $default = null)
    {
        return self::_get($key, $default);
    }

    public static function s($key, $default = '')
    {
        return (string)self::_get($key, $default);
    }

    public static function t($key, $default = '')
    {
        return trim(self::s($key, $default));
    }

    public static function blank($key, $strict = false)
    {
        if (!self::has($key)) {
            return true;
        }
        $var = $strict ? self::s($key) : self::t($key);
        return empty($var);
    }

    public static function b($key, $default = false)
    {
        return (bool)self::_get($key, $default);
    }

    public static function i($key, $default = 0)
    {
        $i = to_integer(self::_get($key, $default));
        return $i === false ? $default : $i;
    }

    public static function l(array $keys)
    {
        return array_map(array('Json', 'v'), $keys);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
