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

class TodoMiniModule extends FrankizMiniModule
{
    const auth  = AUTH_COOKIE;
    const perms = 'user';
    const js    = 'minimodules/todo.js';

    public function __construct()
    {
        $res=XDB::query('SELECT todo_id, sent, checked, tobedone
                           FROM todo
                          WHERE uid = {?}
                          ORDER BY sent DESC',
                          S::user()->id());
        $array_todo = $res->fetchAllAssoc();

        $this->assign('list', $array_todo);
        $this->tpl = "minimodules/todo/todo.tpl";
        $this->titre = "To-Do";
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
