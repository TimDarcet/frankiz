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

class NewsFeed extends FrankizFeed
{
    public function __construct()
    {
        global $globals;
        parent::__construct('Frankiz : Annonces',
                            $globals->baseurl ,
                            'Frankiz : le serveur des élèves de l\'École polytechnique',
                            $globals->baseurl . '/css/' . FrankizPage::getCssPath('images/logo.png'),
                            FrankizPage::getTplPath('news/rss.tpl'));
    }

    protected function fetch(User $user)
    {
        global $globals;

        $nf = new NewsFilter(new PFC_And(new NFC_Current(),
                                         new NFC_Target($user->targetCastes())),
                             new NFO_Begin(true));

        $all_news = $nf->get();
        $all_news->select(NewsSelect::news());
        $data = array();
        foreach ($all_news as $item)
        {
            $e = array();
            $e['id'] = $item->id();
            $e['title'] = '[' . $item->target()->group()->label() . '] ' . $item->title();
            $e['news'] = $item;
            $e['publication'] = $item->begin()->format();
            $auth = ($item->origin() != false)?'[' . $item->origin()->label() . '] ':'';
            $e['author'] = $auth .$item->writer()->displayName();
            $e['link'] = $globals->baseurl . '/news';
            $data[] = $e;
        }
        return PlIteratorUtils::fromArray($data, 1, true);
    }
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
