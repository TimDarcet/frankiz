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
            'wiki/ajax/get'    => $this->make_hook('ajax_get'   , AUTH_COOKIE, 'admin'),
            'wiki/admin'       => $this->make_hook('admin'      , AUTH_COOKIE, 'admin'),
            'faq'              => $this->make_hook('show'       , AUTH_COOKIE),
        );
    }
    
    function handler_show($page, $id)
    {
//    	trace($id);
//        trace(s::user()->checkPerms('admin'));
    	$wiki = new Wiki($id);
    	$wiki->select(Wiki::SELECT_VERSION);
//    	if($id == 42)
//    	{
        $page->assign('title', 'sudo filsdepute');
//    	}
//        else 
//        {
//        $page-> assign('title',"FAQ, Article " + $id); //ça marche pas ce truc
//        }
        $page->assign('wiki', $wiki);
//        $page->assign('leftVersion', $leftVersion);
        $page->assign('admin', s::user()->checkPerms('admin'));
        $page->changeTpl('wiki/faq.tpl');
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

    function handler_ajax_get($page)
    {
        $json = json_decode(Env::v('json'));

        $wiki     = new Wiki($json->wid);
        $versions = isset($json->versions) ? $json->versions : array('last');

        try {
            $wiki->select(array(Wiki::SELECT_VERSION => array('versions' => $versions, 'options' => User::SELECT_BASE)));
            $page->jsonAssign('wiki', $wiki->export());
        } catch(Exception $e) {
            $page->jsonAssign('error', $e->getMessage());
        }

        return PL_JSON;
    }

    function handler_admin($page)
    {
        $mixed = func_get_args();
        array_shift($mixed);
        $mixed = implode('/', $mixed);

        // Create the Wiki if it doesn't exist
        if (isId($mixed))
            $wiki = new Wiki($mixed);
        else
            $wiki = Wiki::from($mixed, true);

        $wiki->select(Wiki::SELECT_BASE | Wiki::SELECT_COUNT);

        $leftVersion = $wiki->count() - 1;

        $wiki->select(array(Wiki::SELECT_VERSION => array('versions' => array('last', $leftVersion), 'options' => User::SELECT_BASE)));

        $page->addCssLink('wiki.css');
        $page->assign('title', 'Administration Wiki');
        $page->assign('wiki', $wiki);
        $page->assign('leftVersion', $leftVersion);
        $page->changeTpl('wiki/admin.tpl');
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
