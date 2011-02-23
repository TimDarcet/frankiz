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
            "news"              => $this->make_hook("news"          , AUTH_PUBLIC),
            "news/see"          => $this->make_hook("see"           , AUTH_PUBLIC),
            "news/admin"        => $this->make_hook("admin"         , AUTH_MDP),
            "news/rss"          => $this->make_hook("rss"           , AUTH_PUBLIC, "user", NO_HTTPS), // TODO
            "news/ajax/read"    => $this->make_hook("ajax_read"     , AUTH_COOKIE),
            "news/ajax/star"    => $this->make_hook("ajax_star"     , AUTH_COOKIE),
            "news/ajax/admin"   => $this->make_hook("ajax_admin"    , AUTH_MDP),
            "news/ajax/modify"  => $this->make_hook("ajax_modify"   , AUTH_MDP),
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

    function handler_news($page)
    {
        $target_castes = new Collection();
        $target_castes->merge(S::user()->castes(Rights::restricted()));
        $target_castes->merge(S::user()->castes(Rights::everybody()));

        $nf = new NewsFilter(new PFC_And(new NFC_Current(),
                                         new NFC_Target($target_castes)));
        $news = $nf->get()->select(NewsSelect::news());

        $page->assign('user', S::user());
        $page->assign('news', $news);
        $page->addCssLink('news.css');
        $page->assign('title', 'Annonces');
        $page->changeTpl('news/news.tpl');
    }

    function handler_see($page, $id)
    {
        $nf = new NewsFilter(new PFC_And(new NFC_Id($id),
                                         new NFC_CanBeSeen(S::user())));
        $news = $nf->get(true);

        if ($news) {
            $news->read(true);
            $news->select(NewsSelect::news());
        }

        $page->assign('news', $news);
        $page->addCssLink('news.css');
        $page->assign('title', 'Annonce: ' . $news->title());
        $page->changeTpl('news/see.tpl');
    }
    

    function handler_admin($page, $nid)
    {
        $nf = new NewsFilter(new PFC_And(new NFC_Id($nid),
                                        new NFC_TargetGroup(S::user()->castes(Rights::admin())->groups())));
        $news = $nf->get(true);

        if ($news !== false) {
            $news->select(NewsSelect::news());

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

    function handler_ajax_admin($page)
    {
        $json = json_decode(Env::v('json'));
        $id = $json->id;
        $n = new News($id);
        $n->select(NewsSelect::news());
        
        if (!S::user()->hasRights($n->target()->group(), Rights::admin())) {
            throw new Exception("Invalid credentials");
        }
        S::assert_xsrf_token();
        
        $page->assign('news', $n);
        $result = $page->fetch(FrankizPage::getTplPath('news/modify.tpl'));
        $page->jsonAssign('success', true);
        $page->jsonAssign('news', $result);
        return PL_JSON;
    }

    function handler_ajax_modify($page, $type)
    {
        $json = json_decode(Env::v('json'));
        $id = $json->admin_id;
        $n = new News($id);
        $n->select(NewsSelect::news());
        
        if (!S::user()->hasRights($n->target()->group(), Rights::admin())) {
            throw new Exception("Invalid credentials");
        }
        S::assert_xsrf_token();
        
        $page->jsonAssign('err', S::v('xsrf_token'));
        $page->jsonAssign('err', Env::v('token'));
        
        
        try
        {
            $begin = new FrankizDateTime($json->begin);
            $end = new FrankizDateTime($json->end);
            $n->title($json->title);
            $n->content($json->news_content);
            $n->begin($begin);
            $n->end($end);
            if (Env::has('image'))
                $n->image(new FrankizImage(Env::i('image')));
            $page->jsonAssign('success', true);
        }
        catch (Exception $e)
        {
            $page->jsonAssign('success', false);
            $page->jsonAssign('error', $e->getMessage());
        }
        return PL_JSON;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
