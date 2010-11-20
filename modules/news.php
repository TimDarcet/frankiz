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

class NewsModule extends PlModule
{
    public function handlers()
    {
        return array(
            "news"           => $this->make_hook("news"     , AUTH_PUBLIC),
            "news/ajax/show" => $this->make_hook("ajax_show", AUTH_COOKIE),
            "news/ajax/hide" => $this->make_hook("ajax_hide", AUTH_COOKIE),
        );
    }

    function handler_ajax_show($page, $id)
    {
        $news = new News($id);
        $news->hide(false);
        exit;
    }

    function handler_ajax_hide($page, $id)
    {
        $news = new News($id);
        $news->hide(true);
        exit;
    }

    function handler_news($page)
    {
        // News from the groups where you are member
        $member_news = new NewsFilter(new PFC_And(new NFC_Current(),
                                                  new NFC_User(S::user())));

        // News from the groups where you are friend and that are public
        $friend_news = new NewsFilter(new PFC_And(new NFC_Current(),
                                                  new PFC_And(new NFC_User(S::user(), 'friend'),
                                                              new NFC_Private(false))));

        $member_news = $member_news->get();
        $friend_news = $friend_news->get();

        // Temporary Collection to retrieve in one request all the datas
        $all_news = new Collection('News');
        $all_news = $all_news->merge($member_news)->merge($friend_news);
        $all_news->select();

        $page->assign('member_news', $member_news);
        $page->assign('friend_news', $friend_news);
        $page->addCssLink('news.css');
        $page->assign('title', 'Annonces');
        $page->changeTpl('news/news.tpl');
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
