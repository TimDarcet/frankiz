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
            "news/admin"        => $this->make_hook("admin"         , AUTH_MDP),
            "news/rss"          => $this->make_hook("rss"           , AUTH_PUBLIC, "user", NO_HTTPS),
            "news/ajax/read"    => $this->make_hook("ajax_read"     , AUTH_COOKIE),
            "news/ajax/star"    => $this->make_hook("ajax_star"     , AUTH_COOKIE),
            "news/ajax/admin"   => $this->make_hook("ajax_admin"    , AUTH_MDP),
            "news/ajax/modify"  => $this->make_hook("ajax_modify"   , AUTH_MDP),
        );
    }

    function handler_ajax_read($page, $ids, $state)
    {
        $ids = explode(',', $ids);
        $news = new Collection('News');
        $news->add($ids);
        $news->read(($state == 1));
        exit;
    }

    function handler_ajax_star($page, $id, $state)
    {
        $news = new News($id);
        $news->star(($state == 1));
        exit;
    }

    function handler_news($page)
    {
        // News from the groups where you are member
        $member_news = new NewsFilter(new PFC_And(new NFC_Current(),
                                                  new NFC_User(S::user(), Rights::member())));

        // News from the public groups where you are friend
        $friend_news = new NewsFilter(new PFC_And(new NFC_Current(),
                                                  new PFC_And(new NFC_User(S::user(), Rights::friend()),
                                                              new NFC_Private(false))));

        $member_news = $member_news->get();
        $friend_news = $friend_news->get()->remove($member_news);

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

    function handler_admin($page)
    {
        $c = new NewsFilter(new PFC_And(new NFC_Current(),
                                        new NFC_User(S::user(), 'admin')));
        $c = $c->get();
        $c->select();

        if (Env::has('admin_id'))
        {
            $id = Env::i('admin_id');
	        $n = $c->get($id);
	        if($n === false)
            {
                $page->assign('msg', 'Vous ne pouvez pas modifier cette activité.');
            }
            else
            {
	            if (Env::has('modify'))
	            {
                    try
                    {
                        $end = new FrankizDateTime(Env::t('end'));
                        $n->title(Env::t('title'));
                        $n->content(Env::t('content'));
                        $n->end($end);
                        $n->priv(Env::has('priv'));
                        $n->replace();
                        $page->assign('msg', 'L\'annonce a été modifiée.');
                    }
                    catch (Exception $e)
                    {
                        $page->assign('msg', 'La date n\'est pas correcte.');
                    }
	            }
                if (Env::has('delete'))
                {
                    $n->delete();
                    $page->assign('delete', true);
                }
                $page->assign('id', $id);
	            $page->assign('news', $n);
            }
        }
        $page->assign('all_news', $c);

        $page->assign('title', 'Modifier les annonces en cours');
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
        $n->select();
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
        $n->select();
        try
        {
            $end = new FrankizDateTime($json->end);
            $n->title($json->title);
            $n->content($json->content);
            $n->end($end);
            $n->priv($json->priv == 'on');
            $n->replace();
            $page->jsonAssign('success', true);
        }
        catch (Exception $e)
        {
            $page->jsonAssign('success', false);
        }
        return PL_JSON;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
