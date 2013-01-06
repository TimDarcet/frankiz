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

class WikiModule extends PlModule
{
    function handlers()
    {
        return array(
            'wiki/ajax/update' => $this->make_hook('ajax_update', AUTH_COOKIE, 'web'),
            'wiki/ajax/get'    => $this->make_hook('ajax_get'   , AUTH_COOKIE, 'web'),
            'wiki/ajax/see'    => $this->make_hook('ajax_see'   , AUTH_PUBLIC),
            'wiki/admin'       => $this->make_hook('admin'      , AUTH_COOKIE, 'web'),
            'wiki/see'         => $this->make_hook('see'        , AUTH_PUBLIC),
        );
    }

    function handler_see($page)
    {
        // Get a wikiid, which contains slashes (and so is exploded)
        $mixed = func_get_args();
        array_shift($mixed);
        $mixed = implode('/', $mixed);
        $wiki = Wiki::fromId($mixed);
        if ($wiki) {
            $wiki->select(Wiki::SELECT_VERSION);
            $page->assign('wiki', $wiki);
            $page->assign('admin', S::user()->checkPerms('admin'));
            $page->assign('title', 'FAQ: ' . $wiki->name());
        }
        $page->changeTpl('wiki/see.tpl');
    }

    function handler_ajax_update($page)
    {
        $wiki = Wiki::fromId(Json::i('wid'));
        if ($wiki) {
            $wiki->select(Wiki::SELECT_VERSION);
            $content = Json::t('content');
            if ($content != $wiki->content()) {
                $wiki->update($content);
                $wiki->select(Wiki::SELECT_VERSION);
            }
            $page->jsonAssign('html', $wiki->html());
            $page->jsonAssign('success', true);
        } else {
            $page->jsonAssign('success', false);
        }
        return PL_JSON;
    }

    function handler_ajax_get($page)
    {
        $wiki = Wiki::fromId(Json::i('wid'));
        $versions = Json::v('versions', array('last'));
        if (!$wiki) {
            $page->jsonAssign('error', "ID not found");
            return PL_JSON;
        }

        try {
            $wiki->select(array(Wiki::SELECT_VERSION => array('versions' => $versions,
                                                              'options' => UserSelect::base())));
            $page->jsonAssign('wiki', $wiki->export());
        } catch(Exception $e) {
            $page->jsonAssign('error', $e->getMessage());
        }
        return PL_JSON;
    }

    function handler_ajax_see($page)
    {
        $mixed = func_get_args();
        array_shift($mixed);
        $mixed = implode('/', $mixed);
        $wiki = Wiki::fromId($mixed);
        if ($wiki) {
            $wiki->select(Wiki::SELECT_VERSION);
            echo $wiki->html();
        }
        if (S::user()->perms()->hasFlag('admin')) {
            echo '<a class="edit" href="wiki/admin/' . $mixed . '" >Modifier</a>';
        }
        exit;
    }

    function handler_admin($page)
    {
        $page->addCssLink('wiki.css');
        $mixed = func_get_args();
        array_shift($mixed);
        $mixed = implode('/', $mixed);

        if (empty($mixed)) {
            $wikis = Wiki::selectAll(Wiki::SELECT_BASE | Wiki::SELECT_COUNT);
            $page->assign('wikis', $wikis);
            $page->assign('title', 'Admin Wiki');
            $page->changeTpl('wiki/list.tpl');
        } else {
            if (Wiki::isId($mixed)) {
                $wiki = new Wiki($mixed);
            } else {
                $wiki = Wiki::from($mixed, true); // Create the Wiki if it doesn't exist
            }

            if (Env::has('newcontent')) {
                $wiki->update(Env::s('newcontent'));
            }

            $wiki->select(Wiki::SELECT_BASE | Wiki::SELECT_COUNT);
            $wiki->select(array(Wiki::SELECT_VERSION => array('versions' => array('last'),
                                                              'options' => UserSelect::base())));

            $page->assign('wiki', $wiki);
            $page->assign('title', 'Admin Wiki: ' . $wiki->name());
            $page->changeTpl('wiki/admin.tpl');
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
