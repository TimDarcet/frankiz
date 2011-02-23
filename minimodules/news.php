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

class NewsMiniModule extends FrankizMiniModule
{
    public function tpl()
    {
        return 'minimodules/news/news.tpl';
    }

    public function css()
    {
        return 'minimodules/news.css';
    }

    public function title()
    {
        return 'Annonces non lues';
    }

    public function run()
    {
        $target_castes = new Collection();
        $target_castes->merge(S::user()->castes(Rights::restricted()));
        $target_castes->merge(S::user()->castes(Rights::everybody()));

        $nf = new NewsFilter(new PFC_And(new NFC_Current(),
                                         new PFC_Or(new PFC_Not(new NFC_Read(S::user())), new NFC_Star(S::user())),
                                         new NFC_Target($target_castes)));
        $news = $nf->get()->select(NewsSelect::head());

        $this->assign('news', $news);
    }

}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
