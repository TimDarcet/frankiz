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
            "news"              => $this->make_hook("news"      , AUTH_PUBLIC),
            "news/current"      => $this->make_hook("news_current"  , AUTH_PUBLIC),
            "news/new"          => $this->make_hook("news_new"      , AUTH_PUBLIC),
            "news/mine"         => $this->make_hook("news_mine"     , AUTH_PUBLIC),
            "news/admin"        => $this->make_hook("admin"         , AUTH_MDP),
            "news/rss"          => $this->make_hook("rss"           , AUTH_PUBLIC, "user", NO_HTTPS), // TODO
            "news/ajax/read"    => $this->make_hook("ajax_read"     , AUTH_COOKIE),
            "news/ajax/star"    => $this->make_hook("ajax_star"     , AUTH_COOKIE),
        );
    }

    function handler_ajax_read($page, $ids, $state)
    {
        S::assert_xsrf_token();
        $ids = explode(',', $ids);
        $news = new Collection('News');
        $news->add($ids);
        $news->read(($state == 1));

        return PL_JSON;
    }

    function handler_ajax_star($page, $id, $state)
    {
        S::assert_xsrf_token();
        $news = new News($id);
        $news->star(($state == 1));

        return PL_JSON;
    }

    function handler_news($page, $id = false)
    {
        if (S::logged()) {
            $this->handler_news_new($page, $id);
        } else {
            $this->handler_news_current($page, $id);
        }
    }

    function handler_news_current($page, $id = false)
    {
        if ($id) {
            $news = new News($id);
            $news->read(true);
        }

        $nf = new NewsFilter(new PFC_And(new NFC_Current(),
                                         new NFC_Target(S::user()->targetCastes())),
                             new NFO_Begin(true));

        $this->viewNews($page, $nf->get(), 'current', $id);
    }

    function handler_news_new($page, $id = false)
    {
        if ($id) {
            $news = new News($id);
            $news->read(true);
        }

        $nf = new NewsFilter(new PFC_And(new PFC_Or(new PFC_And(new NFC_Current(),
                                                                new PFC_Not(new NFC_Read(S::user()))),
                                                    new NFC_Star(S::user())),
                                         new NFC_Target(S::user()->targetCastes())));

        $this->viewNews($page, $nf->get(), 'new', $id);
    }

    function handler_news_mine($page)
    {
        // You may have written the news, but it has to be in a caste where you can see it too !
        $nf = new NewsFilter(new PFC_And(new NFC_Writer(S::user()),
                                         new NFC_CanBeSeen(S::user())),
                             new NFO_Begin(true));

        $this->viewNews($page, $nf->get(), 'mine');
    }

    function viewNews($page, $news, $view, $id = false) {
        if ($id !== false && !$news->get($id)) {
            $nf = new NewsFilter(new PFC_And(new NFC_Id($id), new NFC_CanBeSeen(S::user())));
            $news->merge($nf->get());
        }

        $news->select(NewsSelect::news());

        $page->assign('selected_id', $id);
        $page->assign('view', $view);
        $page->assign('user', S::user());
        $page->assign('news', $news);
        $page->addCssLink('news.css');
        $page->assign('title', 'Annonces');
        $page->changeTpl('news/news.tpl');
    }

    function handler_admin($page, $nid)
    {
        $nf = new NewsFilter(new PFC_And(new NFC_Id($nid)));
        $news = $nf->get(true);

        if ($news !== false) {
            $news->select(NewsSelect::news());
            if (S::user()->hasRights($news->target()->group(), Rights::admin()) || S::user()->isWeb()) {
                if (Env::has('modify') || Env::has('delete')) {
                    S::assert_xsrf_token();
                }

                if (Env::has('modify')) {
                    $news->title(Env::t('title'));
                    $news->content(Env::t('news_content'));
                    $news->begin(new FrankizDateTime(Env::t('begin')));
                    $news->end(new FrankizDateTime(Env::t('end')));
                    if (Env::has('image')) {
                        $image = new ImageFilter(new PFC_And(new IFC_Id(Env::i('image')), new IFC_Temp()));
                        $image = $image->get(true);
                        if (!$image) {
                            throw new Exception("This image doesn't exist anymore");
                        }
                        $image->select(FrankizImageSelect::caste());
                        $image->label($news->title());
                        $image->caste($news->target());
                        $news->image($image);
                    }
                    $page->assign('msg', "L'annonce a été modifiée.");
                }

                if (Env::has('delete')) {
                    $news->delete();
                    $page->assign('delete', true);
                }
            }
        }

        $page->assign('news', $news);
        $page->assign('title', "Modifier l'annonce");
        $page->addCssLink('validate.css');
        $page->changeTpl('news/admin.tpl');
    }

    function handler_rss($page, $user = null, $hash = null)
    {
        $feed = new NewsFeed();
        return $feed->run($page, $user, $hash);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
