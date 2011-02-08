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

function smarty_function_target_picker($params, &$smarty) {
    $fkz = new Collection('Group');
    $fkz->add(array('everybody', 'on_platal', 'public'));
    $fkz->select(new GroupSelect(array('description')));

    $gf = new GroupFilter(new PFC_And(new PFC_Not(new GFC_Namespace(Group::NS_USER)),
                                      new GFC_User(S::user(), Rights::admin())),
                          new GFO_Score());
    $gs = $gf->get()->diff($fkz);

    $temp = new Collection();
    $temp->merge($gs)->merge($fkz);
    $temp->select(GroupSelect::base());

    $smarty->assign($params['user_groups'], $gs);
    $smarty->assign($params['fkz_groups'], $fkz);
    $smarty->assign($params['own_group'], S::user()->group());
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
