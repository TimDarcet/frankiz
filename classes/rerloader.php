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
        'toParis'   => "http://ratp.varal7.fr/api.php?line=RB&station=Lozere&direction=A",
        'toStRemy'  => "http://ratp.varal7.fr/api.php?line=RB&station=Lozere&direction=B"
    );
    
    private static function response($destination){
        try{
            $api = new API(self::$urls[$destination]);
            $json = json_decode(utf8_decode($api->response()));
            if($json && isset($json->missions))
                $missions = $json->missions;
            else 
                $response = array();
            $return = array();
            foreach($missions as $mission){
                    $return[] = array(
                      'name' => $mission->id,
                      'desc' => $mission->stations[1]->name,
                      'time' => $mission->stationsMessages);
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
