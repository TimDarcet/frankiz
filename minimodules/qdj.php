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

class QdjMiniModule extends FrankizMiniModule
{
    const auth  = AUTH_COOKIE;
    const perms = 'user';
    const js    = 'minimodules/qdj.js';

    public function __construct()
    {
        $res=XDB::query('SELECT qdj_id, date, question, answer1, answer2, count1, count2
                           FROM qdj
                          ORDER BY date DESC
                          LIMIT 1');
        $array_qdj = $res->fetchOneAssoc();

        $this->assign('date', $array_qdj['date']);          // Limited support for browser with javascript disabled
        $this->assign('question', $array_qdj['question']);
        $this->assign('answer1', $array_qdj['answer1']);
        $this->assign('answer2', $array_qdj['answer2']);

        $this->tpl = "minimodules/qdj/qdj.tpl";
        $this->titre = "QDJ";
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
