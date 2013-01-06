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

class ConcertLoader
{
    //loader la page xml pour parser
    static $url = "http://x-live/pages/cache/generation_concert_xml.php";

    private static function response() {
        try {
            $api = new API(self::$url, false);
            $xml = simplexml_load_string(utf8_decode($api->response()));
            if ($xml) {
                $response = $xml->concerts->children();
                $response2 = $xml->concerts_later->children();
            }
            else
                $response = array();
            $return = array();
            foreach($response as $r) {
            //withpic true signifie avec les photos, sinon false. cela depend de la date de concerts <= 30 ou pas
                    $return[] = array('withpic' =>"true",
                                      'group' => (string) $r->nom_groupe, 'link_group' => (string) $r->lien_nom_groupe,
                                      'place' => (string) $r->nom_salle,  'link_place' => (string) $r->lien_nom_salle,
                                      'date' => (string) $r->date,        'price' => (string) $r->prix.' euro',
                                      'pict' => (string) $r->img_groupe,  'link_pict' => (string) $r->lien_img_groupe);

            }
            foreach($response2 as $r) {
            //withpic true signifie avec les photos, sinon false. cela depend de la date de concerts <= 30 ou pas
                    $return[] = array('withpic' =>"false",
                                      'group' => (string) $r->nom_groupe, 'link_group' => (string) $r->lien_nom_groupe,
                                      'place' => (string) $r->nom_salle,  'link_place' => (string) $r->lien_nom_salle,
                                      'date' => (string) $r->date,        'price' => (string) $r->prix.' euro',
                                      'pict' => (string) $r->img_groupe,  'link_pict' => (string) $r->lien_img_groupe);

            }

            return $return;
        }
        catch(Exception $e){
        return array();
        }
    }

    public static function get() {
        global $globals;

        if (!PlCache::hasGlobal('concert_array'))
            PlCache::setGlobal('concert_array', self::response(), 3600);

        return PlCache::getGlobal('concert_array');
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
