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

/* contains all admin stuff */
class AdminModule extends PlModule
{
    function handlers()
    {
        return array(
            'admin/su'      => $this->make_hook('su'  , AUTH_MDP, 'admin'),
            'admin/tree'    => $this->make_hook('tree', AUTH_MDP, 'admin'),
        );
    }

    function handler_su(&$page, $uid=0)
    {
        if (S::has('suid')) {
            $page->kill("Déjà en SUID !!!");
        }
        $res = XDB::query("SELECT eleve_id
                             FROM compte_frankiz
                            WHERE eleve_id = {?}", $uid);
        if($res->numRows() == 1){
            if(!Platal::session()->startSUID($uid)) {
                $page->trigError('Impossible d\'effectuer un SUID sur ' . $uid);
            } else {
                $page->kill("SU ok");
                pl_redirect('');
            }
        }
    }

    function handler_tree(&$page)
    {
        $page->assign('title', 'Arbre des groupes');
        $page->assign('tree', $tree);
        $page->changeTpl('admin/tree.tpl');
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
