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
    const EXTERNAL = 0;   // External IP
    const AUTRES   = 1;   // Ni casert ni local => pits, salles infos ...
    const CASERT   = 2;   // Student's rooms
    const LOCAL    = 3;   // Binets et bars d'étage

    private static $originCache = array();
    
    public static function get() {
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            // CLI
            $ip = '127.0.0.1';
        }

        if ($ip === '129.104.30.4') {
            // C'est l'adresse du portail w3x
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $listeIPs = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                
                // Le dernier de cette liste est celui ajoute par w3x, qui est un
                // proxy fiable. Toute cette verification a pour objectif de ne pas
                // permettre l'ip spoofing
                // (trim : le séparateur entre les ips dans $headers['X-Forwarded-For'] est ', ')
                $ipForwardee = trim(end($listeIPs));
                
                if (preg_match("/([0-9]{1,3}\.){3}[0-9]{1,3}/", $ipForwardee)) {
                    $ip = $ipForwardee;
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
        if (array_key_exists($ip, self::$originCache))
        {
            return self::$originCache[$ip];
        }
        else
        {
            $origin = -1;
            if($ip == '127.0.0.1' || (substr($ip, 0, 8) == '129.104.' && $ip != '129.104.30.4')) 
            {
                $res=XDB::query('SELECT ro.owner_type
                                   FROM ips
                             INNER JOIN rooms_owners AS ro
                                     ON ro.rid = ips.rid
                                  WHERE ips.ip = {?}', $ip);
                $cell = $res->fetchOneCell();
                switch($cell)
                {
                    case 'user':
                        $origin = self::CASERT;
                        break;
                        
                    case 'group':
                        $origin = self::LOCAL;
                        break;
                        
                    default:
                        $origin = self::AUTRES;
                }
            } 
            else
            {
                $origin = self::EXTERNAL;
            }
            
            self::$originCache[$ip] = $origin;
            return $origin;
        }
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
