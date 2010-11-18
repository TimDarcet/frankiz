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

class WikiModule extends PlModule
{
    function handlers()
    {
        return array(
            'wiki/ajax/update' => $this->make_hook('ajax_update', AUTH_COOKIE, 'admin'),
        );
    }

    function handler_ajax_update($page)
    {
        $json = json_decode(Env::v('json'));
        $wiki = new Wiki($json->wid);

        $page->jsonAssign('success', true);
        try {
            $wiki->update($json->content);
            $html = $wiki->select(Wiki::SELECT_VERSION)->html();
            $page->jsonAssign('html', $html);
        } catch(Exception $e) {
            $page->jsonAssign('success', false);
        }

        return PL_JSON;
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
