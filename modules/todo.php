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

class TodoModule extends PlModule
{
    function handlers()
    {
        return array(
            'todo/ajax/add'     => $this->make_hook('ajax_todo_add',     AUTH_COOKIE),
            'todo/ajax/check'   => $this->make_hook('ajax_todo_check',   AUTH_COOKIE),
            'todo/ajax/uncheck' => $this->make_hook('ajax_todo_uncheck', AUTH_COOKIE),
            'todo/ajax/clear'   => $this->make_hook('ajax_todo_clear',   AUTH_COOKIE),
        );
    }

    function handler_ajax_todo_add($page)
    {
        S::assert_xsrf_token();

        if (Json::has('tobedone')) {
            XDB::execute('INSERT INTO  todo
                                  SET  uid = {?}, sent = NOW(), checked = 0, tobedone = {?}',
                                       S::user()->id(), Json::s('tobedone'));

            if (XDB::affectedRows() > 0) {
                $page->jsonAssign('todo_id', XDB::insertId());
            } else {
                $page->jsonAssign('error', "Impossible d'ajouter une nouvelle tâche");
            }
        } else {
            $page->jsonAssign('error', "Requête invalide");
        }

        return PL_JSON;
    }

    function handler_ajax_todo_check($page)
    {
        S::assert_xsrf_token();

        if (Json::has('todo_id')) {
            XDB::execute('UPDATE  todo
                             SET  checked = 1
                           WHERE  uid = {?} AND todo_id = {?}',
                                  S::user()->id(), Json::i('todo_id'));

            if (XDB::affectedRows() != 1) {
                $page->jsonAssign('error', "Impossible de cocher la tâche");
            }
        } else {
            $page->jsonAssign('error', "Requête invalide");
        }

        return PL_JSON;
    }

    function handler_ajax_todo_uncheck($page)
    {
        S::assert_xsrf_token();

        if (Json::has('todo_id')) {
            XDB::execute('UPDATE  todo
                             SET  checked = 0
                           WHERE  uid = {?} AND todo_id = {?}',
                                  S::user()->id(), Json::i('todo_id'));

            if (XDB::affectedRows() != 1) {
                $page->jsonAssign('error', "Impossible de décocher la tâche");
            }
        } else {
            $page->jsonAssign('error', "Requête invalide");
        }

        return PL_JSON;
    }

    function handler_ajax_todo_clear($page)
    {
        S::assert_xsrf_token();
        XDB::execute('DELETE FROM  todo
                            WHERE  uid = {?} AND checked = 1',
                                   S::user()->id());

        if (XDB::affectedRows() != 1) {
            $page->jsonAssign('error', "Impossible de nettoyer la liste des tâches");
        }

        return PL_JSON;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
