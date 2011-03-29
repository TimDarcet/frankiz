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
            "chat"              => $this->make_hook("chat"               , AUTH_COOKIE),
	    "chat/ajax/avatar"  => $this->make_hook("chat_ajax_avatar"   , AUTH_COOKIE) 
        );
    }

    function handler_chat($page, $group='br')
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

    function handler_chat_ajax_avatar($page)
    {
        $json = json_decode(Env::v('json'));
	$page->jsonAssign('json',$json);
        $hruid = $json->hruid;

	$filter = new UFC_Hruid($hruid);
        $uf = new UserFilter($filter);
        $user = $uf->get(true); //add boolean
	if (! $user) {
            $page->jsonAssign('error',"Inexistent user, or inconsistent database.");
            return PL_JSON;
	}
        
        $user->select(UserSelect::login());
        $image = $user->image();
        $src = $image->src("micro");
        $page->jsonAssign('src', $src);

        return PL_JSON;
    }
    
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
