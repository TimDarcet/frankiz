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

class IP
{
    const EXTERNAL = 'ip_external';  // External IP
    const INTERNAL = 'ip_internal';  // Ni casert ni local => pits, salles infos ...
    const STUDENT  = 'ip_student';   // Student's rooms
    const PREMISE  = 'ip_premise';   // Binets et bars d'étage

    private static $originCache = array();

    public static function get() {
        global $globals;

    if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '127.0.0.1';
        }

        if ($ip === '129.104.201.51') {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ipList = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);

                // This is w3c, a trusted reverse proxy
                if (end($ipList) === '129.104.30.4') {
                    // This is a trusted remote reverse proxy
                    if (prev($ipList) === $globals->core->remoteproxy || in_array(prev($ipList), $globals->core->remoteproxy)) {
                        prev($ipList);
                    }
                }
                $forwardedIp = current($ipList);

                if (preg_match("/([0-9]{1,3}\.){3}[0-9]{1,3}/", $forwardedIp)) {
                    $ip = $forwardedIp;
                }
            }
        }

        return $ip;
    }

    public static function is_internal($ip = null)
    {
        $ip = ($ip == null) ? self::get() : $ip;
        return (self::origin($ip) > self::EXTERNAL);
    }

    public static function is_casert($ip = null)
    {
        $ip = ($ip == null) ? self::get() : $ip;
        return (self::origin($ip) == self::CASERT);
    }

    public static function is_local($ip = null)
    {
        $ip = ($ip == null) ? self::get() : $ip;
        return (self::origin($ip) == self::LOCAL);
    }

    public static function is_autres($ip = null)
    {
        $ip = ($ip == null) ? self::get() : $ip;
        return (self::origin($ip) == self::AUTRES);
    }

    // Where is the IP from ?
    public static function origin($ip = null)
    {
        $ip = ($ip == null) ? self::get() : $ip;
        if (array_key_exists($ip, self::$originCache)) {
            return self::$originCache[$ip];
        } else {
            $origin = self::EXTERNAL;
            if ($ip == '127.0.0.1' || (substr($ip, 0, 8) == '129.104.' && $ip != '129.104.30.4' && $ip != '129.104.30.90'))
            {
                if($ip == '127.0.0.1') {
                    return self::INTERNAL;
                }

                $res = XDB::iterator('SELECT  rg.rid
                                    FROM  ips
                              INNER JOIN  rooms_groups AS rg ON rg.rid = ips.rid
                                   WHERE  ips.ip = {?}
                                   LIMIT  1', $ip);
                if ($res->total() >= 1) {
                    $origin = self::PREMISE;
                    self::$originCache[$ip] = $origin;
                    return $origin;
                }

                $res = XDB::iterator('SELECT  ru.rid
                                    FROM  ips
                              INNER JOIN  rooms_users AS ru ON ru.rid = ips.rid
                                   WHERE  ips.ip = {?}
                                   LIMIT  1', $ip);
                if ($res->total() >= 1) {
                    $origin = self::STUDENT;
                    self::$originCache[$ip] = $origin;
                    return $origin;
                }

                $origin = self::INTERNAL;
                self::$originCache[$ip] = $origin;
                return $origin;
            }
        }
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
