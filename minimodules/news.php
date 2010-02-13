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

    public function __construct()
    {
        // Fetch news ot the 24 last hours
        $res=XDB::query('SELECT nc.cid cid,
                                n.nid nid, n.uid uid, n.begin begin, n.end end, n.title title,
                                a.uid uid, a.firstname firstname, a.lastname lastname, a.nickname nickname,
                                g.name group_name
                           FROM news_clusters AS nc
                     INNER JOIN news AS n
                             ON n.nid = nc.nid
                     INNER JOIN account AS a
                             ON a.uid = n.uid
                      LEFT JOIN groups AS g
                             ON g.gid = n.gid                             
                          WHERE NOW() < n.end AND n.begin < NOW() < n.begin + 24*3600
                            AND nc.cid IN ' . Cluster::inline(S::v('clusters')) . '
                          GROUP BY nc.nid
                          ORDER BY n.begin DESC');
        $news = $res->fetchAllAssoc();

        $this->assign('news', $news);
        $this->tpl = "minimodules/news/news.tpl";
        $this->titre = "Nouvelles fraîches";
    }

}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
