<?php
/***************************************************************************
 *  Copyright (C) 2004-2013 Binet Réseau                                   *
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

class RerLoader
{
    static $urls = array(
        'toParis'   => "http://ratp-bridge.fabernovel.com/ratp.schedule?reseau=4&direction=52&station=881&%20H",
        'toStRemy'  => "http://ratp-bridge.fabernovel.com/ratp.schedule?reseau=4&direction=55&station=836&%20H"
    );
    
    private static function destination($dest){
        $dest = str_replace("AEROPORT ", "", $dest); 
        $dest = str_replace("Charles-de-Gaulle", "CDG", $dest);
        return strtoupper(substr($dest, 0, 8));
    }
    
    private static function response($destination){
        try{
            $api = new API(self::$urls[$destination]);
            $xml = simplexml_load_string(utf8_decode($api->response()));
            if($xml && isset($xml->schedules->schedule->liste))
                $response = $xml->schedules->schedule->liste->children();
            else 
                $response = array();
            $return = array();
            foreach($response as $r){
                if(preg_match('![0-9]{2}:[0-9]{2}!',(string) $r->texte2))
                    $return[] = array(
                        'name' => (string) $r->texte3,
                        'desc' => self::destination((string) $r->texte1),
                        'time' => (string) $r->texte2);
            }
            return $return;
        }
        catch(Exception $e){
            return array();
        }
    }
    
    public static function get($destination = 'toParis') {
        global $globals;

        if (!PlCache::hasGlobal('rer_' . $destination))
            PlCache::setGlobal('rer_' . $destination, self::response($destination), $globals->cache->rer);

        return PlCache::getGlobal('rer_' . $destination);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
