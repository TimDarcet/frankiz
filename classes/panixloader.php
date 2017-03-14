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

class PanixLoader
{
    static $url = "https://panix.binets.fr/dashboard/panel/render/8/";
    static $post_fields= "parentPanelPHIDs=5&__ajax__=true";

    private static function response(){
        try{
            $api = new API(self::$url, false, self::$post_fields);
	    $resp = substr($api->response(), 9); //This trims the unknown prefix "for (;;);"
            $json = json_decode(utf8_decode($resp));
	    $content = "";
            if($json && isset($json->payload) && isset($json->payload->panelMarkup)) {
                $string = $json->payload->panelMarkup;
                $dom = new DOMDocument;
		$dom->loadXML($string);
		if ($dom && isset($dom->textContent)) {
		   $content = $dom->textContent;
		   $content = str_replace("Statut des serveurs - problèmes en cours", "", $content);
		}
		//$s = simplexml_import_dom($dom);
		//var_dump($s); die;
            }
            return $content;
        }
        catch(Exception $e){
            return "";
        }
    }

    public static function get() {
        global $globals;

//        if (!PlCache::hasGlobal('panix_status'))
            PlCache::setGlobal('panix_status', self::response(), $globals->cache->panix);

        return PlCache::getGlobal('panix_status');
    }

}
