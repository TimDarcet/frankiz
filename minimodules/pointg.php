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

class PointGMiniModule extends FrankizMiniModule
{
    public function auth()
    {
        return AUTH_PUBLIC;
    }

    public function perms()
    {
        return 'user';
    }

    public function tpl()
    {
        return 'minimodules/pointg/pointg.tpl';
    }

    public function js()
    {
        return 'minimodules/pointg.js';
    }

    public function css()
    {
        return 'minimodules/pointg.css';
    }

    public function title()
    {
        return 'Point Gamma';
    }

    private static function response() {

        static $url = "http://www.pointgamma.com/moduleFKZ/generation_xml.php";
        try {
            $api = new API($url, true);
            $xml = simplexml_load_string(utf8_decode($api->response()));
            if ($xml) {
                $response = $xml->bars->children();
                $response2 = $xml->annonces->children();
                $response3 = $xml->preventes->children();
            }
            else{
                $response = array();
                $response2 = array();
                $response3 = array();
            }
            $return = array();
            $bar = array();
            $annonce = array();
            $prevente = array();
            foreach($response as $r) {
                $bar[] = array('classement' => (string) $r->numero_bar,
                               'bars' => (string) $r->nom_bar,
                               'score' => (string) $r->score_bar);
            }
            foreach($response2 as $r) {
                $annonce[] = array('numero' => (string) $r->numero_annonce,
                                   'titre' => (string) $r->titre_annonce,
                                   'time' => (string) $r->time_annonce,
                                   'text' => (string) $r->text_annonce);
            }
            foreach($response3 as $r) {
                $prevente[] = array('ecole' => (string) $r->ecole_prevente,
                                    'time' => (string) $r->time_prevente);
            }
            $return[0] = $bar;
            $return[1] = $annonce;
            $return[2] = $prevente;
            return $return;
        }
        catch(Exception $e){
            return array();
        }
    }

    public static function getBars(){
        global $globals;

        if(!PlCache::hasGlobal('bar_array'))
            PlCache::setGlobal('bar_array', self::response(), 720);

        return PlCache::getGlobal('bar_array');
    }


    public function run()
    {
        if (!$pointg = self::getBars())
        {
            $bars = array();
            $annonces_pointg = array();
            $preventes_pointg = array();
        }
        else
        {
            $bars = $pointg[0];
            $annonces_pointg = $pointg[1];
            $preventes_pointg = $pointg[2];
        }
        $this->assign('bars',$bars);
        $this->assign('annonces_pointg',$annonces_pointg);
        $this->assign('preventes_pointg',$preventes_pointg);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
