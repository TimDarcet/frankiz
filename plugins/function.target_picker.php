<?php
/***************************************************************************
 *  Copyright (C) 2004-2012 Binet Réseau                                   *
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

function smarty_function_target_picker($params, &$smarty) {
    // Get user groups
    $everybody_groups = S::user()->castes(Rights::everybody())->groups();

    // Get Frankiz special groups
    $fkz = new Collection('Group');
    $fkz->add(array('everybody', 'public'));
    $fkz->select(new GroupSelect(array('description')));

    // BDE, study and promo groups
    $study_groups = $everybody_groups->filter('ns', Group::NS_BDE);
    $study_groups->merge($everybody_groups->filter('ns', Group::NS_PROMO));
    $study_groups->merge($everybody_groups->filter('ns', Group::NS_STUDY));

    // Get all groups user is admin, without the user one
    $gs = S::user()->castes(Rights::admin())->groups();
    $gs->diff($fkz);
    $gs->filter(function ($g) {return $g->ns() != Group::NS_USER;});

    // Collection of Group objects to be selected
    $selected_groups = new Collection('Group');
    $selected_groups->merge($gs)->merge($fkz);

    $gso = false;
    if ($params['even_only_friend']) {
        $gfo = new GroupFilter(
            new PFC_And(
                new GFC_Namespace(array(Group::NS_BINET, Group::NS_FREE)),
                new GFC_User(S::user(), Rights::everybody())),
            new GFO_Score());
        $gso = $gfo->get()->diff($gs)->diff($fkz);
        $selected_groups->merge($gso);
    }
    $selected_groups->select(GroupSelect::base());

    $smarty->assign($params['user_groups'], $gs);
    $smarty->assign($params['fkz_groups'], $fkz);
    $smarty->assign($params['study_groups'], $study_groups);
    $smarty->assign($params['own_group'], S::user()->group());
    $smarty->assign('only_friend', $gso);
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
