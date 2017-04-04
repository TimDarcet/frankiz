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

class IkMiniModule extends FrankizMiniModule
{
    public function auth()
    {
        return AUTH_INTERNAL;
    }

    public function css()
    {
        return 'minimodules/ik.css';
    }

    public function tpl()
    {
        return 'minimodules/ik/last_ik.tpl';
    }

    public function title()
    {
        return 'IK électronique';
    }

    public function run()
    {
        global $globals;

        if (!PlCache::hasGlobal('ik')) {
            $ikapi = new API('https://ik.frankiz.net/ajax/last', false);
            $json = json_decode($ikapi->response(), true);

            if (isset($json['ik']['id']) && $json['ik']['id'] != '') {
                $json = $json ['ik'];

                $filename = $globals->spoolroot . '/htdocs/data/ik/' . $json['id'] . '.jpg';
                file_put_contents($filename, base64_decode($json['base64']));
                $ik = array('id' => $json['id'], 'title' => $json['title'], 'url' => $json['url']);

                PlCache::setGlobal('ik', $ik, $globals->cache->ik);
            }
        }

        $this->assign('ik', PlCache::getGlobal('ik'));
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
