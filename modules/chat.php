<?php
/***************************************************************************
 *  Copyright (C) 2010 Binet Réseau                                       *
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

class ChatModule extends PlModule
{
    public function handlers()
    {
        return array(
            "chat"              => $this->make_hook("chat"               , AUTH_COOKIE, ''),
            "chat/avatar"       => $this->make_hook("chat_avatar"        , AUTH_COOKIE, ''),
        );
    }

    // Adds value to the javascripts variables in the template
    function handler_chat($page, $group='platal')
    {
        $page->assign('jabber_hruid', S::user()->login());
        $page->assign('jabber_nick', S::user()->displayName());
        $page->assign('jabber_cookie', $_SERVER['HTTP_COOKIE']);
        if ($group)
            $page->assign('jabber_room', $group);
        else
            $page->assign('jabber_room', 'br');
        $page->changeTpl('chat/chat.tpl');
    }

    function handler_chat_avatar($page, $hruid)
    {
        global $globals;

        $filter = new UFC_Hruid($hruid);
        $uf = new UserFilter($filter);
        $user = $uf->get(true); //add boolean
        if (! $user) {
            header($_SERVER['SERVER_PROTOCOL'] . '404 Not Found');
            $image = new StaticImage($globals->images->man);
            // for some reason mime isn't picked up: for valid images mime == null is enough to be displayed correctly
            // for $globals->images->man neither 1 nor null does the trick
        } else {
            $user->select(UserSelect::login());
            $image = $user->image();
        }

        $image->send("micro");
        exit;
    }

    // Rather than a method taking json input, it would make sense to use instead a method like chat/ajax/picture?hruid=jid.
    // The outgoing payload need only be the picture itself. No question asked. Then the resolution need not be done is js.
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker:
?>
