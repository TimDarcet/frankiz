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

class ReceptionModule extends PLModule
{

    public function handlers()
    {
        return array(
            "reception/news" => $this->make_hook("news", AUTH_PUBLIC),
        );
    }

    function handler_news($page)
    {
        
        $nf1 = new NewsFilter(new PFC_And(new NFC_Current(),
                                          new NFC_User(S::user())));

        $nf2 = new NewsFilter(new PFC_And(new NFC_Current(),
                                          new PFC_And(new NFC_User(S::user(), 'friend'),
                                                      new NFC_Private(false))));

        $news_array = array(
            'member' => $nf1->get(),
            'friend' => $nf2->get());
        
        foreach ($news_array as $key => $o)
        {
            $news_array[$key]->select(News::SELECT_HEAD | News::SELECT_BODY);
            $news_array[$key] = $o->split('order');
            ksort($news_array[$key]);
        }
        krsort($news_array);

        $page->assign('news_array', $news_array);
        $page->addJsLink('news.js');
        $page->addCssLink('news.css');
        $page->changeTpl('reception/news.tpl');
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
