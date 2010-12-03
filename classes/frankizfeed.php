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

require_once('../core/classes/plfeed.php');

/*
 * nearly the same as PlFeed but User doesn't extend PlUser
 */
abstract class FrankizFeed implements PlIterator
{
    public $title;
    public $link;
    public $description;

    public $img_link;

    private $article_tpl;
    private $iterator;

    /**
    * @param $title         The name of the flow
    * @param $link          The link to the website
    * @param $desc          The description of the flow
    * @param $img_link      The link to get the logo
    * @param $article_tpl   The template to use to show an item
    */
    public function __construct($title, $link, $desc, $img_link,
                                $article_tpl) {
        $this->title        = $title;
        $this->link         = $link;
        $this->description  = $desc;
        $this->img_link     = $img_link;
        $this->article_tpl  = $article_tpl;
    }

    /**
    * Fetch the feed for the given user.
    * Must return a PlIterator
    *
    * @param $user the user requesting the rss feed
    */
    abstract protected function fetch(User $user);

    public function next()
    {
        $data = $this->iterator->next();
        if (!empty($data)) {
            return new PlFeedArticle($this->article_tpl, $data);
        }
        return null;
    }

    public function total()
    {
        return $this->iterator->total();
    }

    public function first()
    {
        return $this->iterator->first();
    }

    public function last()
    {
        return $this->iterator->last();
    }

    /**
    * @param $page      The page
    * @param $login     The hruid of the user
    * @param $token     The hash_rss for identification
    */
    public function run(FrankizPage $page, $login, $token)
    {
        $uf = new UserFilter(new UFC_Hruid($login));
        $user = $uf->get(true);
        if (!$user) {
            return PL_FORBIDDEN;
        }
        $user->select(User::SELECT_BASE);
        if ($user->hash_rss() != $token) {
            return PL_FORBIDDEN;
        }

        $page->assign('rss_hash', $token);
        pl_content_headers("application/rss+xml");
        $this->iterator = $this->fetch($user);
        $page->coreTpl('feed.rss2.tpl', NO_SKIN);
        $page->assign_by_ref('feed', $this);
        $page->run();
    }
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
